// Constants and configurations
const CONFIG = {
    MAX_IMPORT_RECORDS: 10000,
    TOAST_DURATION: 4000,
    VALIDATION_RULES: {
        name: { min: 3, max: 50 },
        email: { max: 50 },
        phone: { min: 10, max: 20 }
    }
};

const VALIDATION_FIELDS = [
    { id: '#nameInput', name: 'İsim, Soyisim', rules: ['required', 'min:3', 'max:50'] },
    { id: '#emailInput', name: 'E-posta', rules: ['required', 'email', 'max:50'] },
    { id: '#phoneInput', name: 'Telefon', rules: ['required', 'phone', 'min:10', 'max:20'] },
    { id: '#companyInput', name: 'Şirket Adı', rules: ['max:50'] },
];

// Utility functions
const Validators = {
    email: (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email),
    phone: (phone) => /^\+?[0-9]{10,14}$/.test(phone)
};

const UI = {
    toggleLoadingState: (button, isLoading, loadingText = 'Kaydediliyor...', defaultText = 'Kaydet') => {
        button.prop('disabled', isLoading);
        button.html(isLoading ?
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}` :
            defaultText
        );
    },

    showToast: (type, message) => {
        const toastr = $('<div>')
            .addClass('toastr ' + type)
            .html(`
                <span>${message}</span>
                <button class="close-btn" onclick="$(this).parent().remove()">×</button>
            `);

        $('#toastr-container').append(toastr);
        setTimeout(() => toastr.remove(), CONFIG.TOAST_DURATION);
    }
};

// Form validation
class FormValidator {
    static validateField(field, value, rowNumber = null) {
        const { name, rules } = field;

        if (name !== 'Şirket Adı' && !value) {
            UI.showToast('error', rowNumber ?
                `${rowNumber}. satırda ${name} alanı zorunludur.` :
                `${name} alanı zorunludur.`
            );
            return false;
        }

        if (name === 'Şirket Adı' && !value) return true;

        for (const rule of rules) {
            const [ruleName, ruleValue] = rule.split(':');

            if (!this.validateRule(ruleName, value, ruleValue, name)) {
                return false;
            }
        }

        return true;
    }

    static validateRule(ruleName, value, ruleValue, fieldName) {
        const validationMessages = {
            required: `${fieldName} alanı zorunludur.`,
            min: `${fieldName} alanı en az ${ruleValue} karakter olmalıdır.`,
            max: `${fieldName} alanı en fazla ${ruleValue} karakter olmalıdır.`,
            email: 'Geçersiz e-posta adresi.',
            phone: 'Geçersiz telefon numarası.'
        };

        const isValid = (() => {
            switch(ruleName) {
                case 'required': return !!value;
                case 'min': return value.length >= ruleValue;
                case 'max': return value.length <= ruleValue;
                case 'email': return Validators.email(value);
                case 'phone': return Validators.phone(value);
                default: return true;
            }
        })();

        if (!isValid) {
            UI.showToast('error', validationMessages[ruleName]);
            return false;
        }

        return true;
    }
}

// Customer management
class CustomerManager {
    static async create() {
        const isValid = VALIDATION_FIELDS.every(field =>
            FormValidator.validateField(field, $(field.id).val())
        );

        if (!isValid) return;

        const $saveButton = $('#saveButton');
        UI.toggleLoadingState($saveButton, true);

        try {
            await $.ajax({
                url: '/store-customer',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: $('#nameInput').val(),
                    email: $('#emailInput').val(),
                    phone: $('#phoneInput').val(),
                    company: $('#companyInput').val() || null,
                    customerId: $('#customerId').val() || null
                }
            });

            UI.showToast('success', "Müşteri başarıyla oluşturuldu.");
            this.resetForm();
            $("#customersTable").DataTable().ajax.reload();

        } catch (error) {
            Object.values(error.responseJSON.errors).forEach(err =>
                UI.showToast('error', err)
            );
        } finally {
            UI.toggleLoadingState($saveButton, false);
        }
    }

    static resetForm() {
        $('.createInput').val('');
        $('#customerModal').modal('hide');
    }

    static async edit(id) {
        try {
            const response = await $.ajax({
                url: `/customer/${id}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                }
            });

            $('#nameInput').val(response.name);
            $('#emailInput').val(response.email);
            $('#phoneInput').val(response.phone);
            $('#companyInput').val(response.company || '');
            $('#customerId').val(response.id);
            $('#customerModal').modal('show');

        } catch (error) {
            UI.showToast('error', 'Müşteri bilgileri yüklenirken hata oluştu.');
        }
    }

    static async delete(id) {
        const $deleteButton = $('#deleteCustomerBtn');
        UI.toggleLoadingState($deleteButton, true, 'Siliniyor...', 'Sil');

        try {
            await $.ajax({
                url: `/delete-customer/${id}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                }
            });

            UI.showToast('success', 'Müşteri başarıyla silindi.');
            $("#customersTable").DataTable().ajax.reload();
            $('#confirmModal').modal('hide');

        } catch (error) {
            UI.showToast('error', 'Müşteri silinirken hata oluştu.');
        } finally {
            UI.toggleLoadingState($deleteButton, false, 'Siliniyor...', 'Sil');
        }
    }
}

// Import functionality
class ImportManager {
    static async processFile() {
        const file = $('#fileInput').prop('files')[0];
        if (!file) {
            UI.showToast('error', 'Lütfen bir dosya seçin.');
            return;
        }

        try {
            const data = await this.readExcelFile(file);
            if (!this.validateImportData(data)) return;

            await this.sendImportData(data);

            UI.showToast('success', 'Aktarım başladı, Müşterileriniz en kısa sürede listelenecektir.');
            setTimeout(() => window.location.reload(), 5000);

        } catch (error) {
            UI.showToast('error', 'Dosya işlenirken hata oluştu.');
        }
    }

    static readExcelFile(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                let jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                jsonData = jsonData.filter(row => row.length > 0);
                jsonData.shift(); // Remove header row
                resolve(jsonData);
            };
            reader.readAsArrayBuffer(file);
        });
    }

    static validateImportData(data) {
        if (data.length > CONFIG.MAX_IMPORT_RECORDS) {
            UI.showToast('error', `Maksimum ${CONFIG.MAX_IMPORT_RECORDS} kayıt yükleyebilirsiniz.`);
            return false;
        }

        return data.every((row, index) =>
            VALIDATION_FIELDS.every(field =>
                FormValidator.validateField(
                    { ...field, index: VALIDATION_FIELDS.indexOf(field) },
                    row[VALIDATION_FIELDS.indexOf(field)],
                    index + 1
                )
            )
        );
    }

    static async sendImportData(data) {
        await $.ajax({
            url: "/import",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                jsonData: JSON.stringify(data)
            }
        });
    }
}

// DataTable initialization
$(document).ready(() => {
    const baseColumns = [
        {
            render: (data, type, row) => row.DT_RowIndex
        },
        { data: 'name', orderable: false, searchable: true },
        { data: 'email', orderable: false, searchable: true },
        { data: 'phone', orderable: false, searchable: true },
        {
            data: 'company',
            orderable: false,
            searchable: true,
            render: (data) => data ?? '-'
        }
    ];

    if (isAdmin) {
        $('.admin-only').show();
        baseColumns.push({
            render: (data, type, row) => `
                <div class="actions">
                    <a onclick="CustomerManager.edit(${row.id})" class="cursor-pointer" title="Düzenle">
                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M14.3601 4.07866L15.2869 3.15178C16.8226 1.61607 19.3125 1.61607 20.8482 3.15178C22.3839 4.68748 22.3839 7.17735 20.8482 8.71306L19.9213 9.63993M14.3601 4.07866C14.3601 4.07866 14.4759 6.04828 16.2138 7.78618C17.9517 9.52407 19.9213 9.63993 19.9213 9.63993M14.3601 4.07866L12 6.43872M19.9213 9.63993L14.6607 14.9006L11.5613 18L11.4001 18.1612C10.8229 18.7383 10.5344 19.0269 10.2162 19.2751C9.84082 19.5679 9.43469 19.8189 9.00498 20.0237C8.6407 20.1973 8.25352 20.3263 7.47918 20.5844L4.19792 21.6782M4.19792 21.6782L3.39584 21.9456C3.01478 22.0726 2.59466 21.9734 2.31063 21.6894C2.0266 21.4053 1.92743 20.9852 2.05445 20.6042L2.32181 19.8021M4.19792 21.6782L2.32181 19.8021M2.32181 19.8021L3.41556 16.5208C3.67368 15.7465 3.80273 15.3593 3.97634 14.995C4.18114 14.5653 4.43213 14.1592 4.7249 13.7838C4.97308 13.4656 5.26166 13.1771 5.83882 12.5999L8.5 9.93872" stroke="#000" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
                    </a>
                    <a onclick="CustomerManager.delete(${row.id})" class="cursor-pointer" title="Kaldır">
                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9.17065 4C9.58249 2.83481 10.6937 2 11.9999 2C13.3062 2 14.4174 2.83481 14.8292 4" stroke="#000" stroke-width="1.5" stroke-linecap="round"></path> <path d="M20.5 6H3.49988" stroke="#000" stroke-width="1.5" stroke-linecap="round"></path> <path d="M18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5M18.8334 8.5L18.6334 11.5" stroke="#000" stroke-width="1.5" stroke-linecap="round"></path> <path d="M9.5 11L10 16" stroke="#000" stroke-width="1.5" stroke-linecap="round"></path> <path d="M14.5 11L14 16" stroke="#000" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
                    </a>
                </div>
            `
        });
    }else{
        $('.admin-only').hide();
    }

    $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/customers',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            }
        },
        columns: baseColumns,
        language: {
            search: "Arama: ",
            lengthMenu: "Göster _MENU_ kayıt",
            info: "Toplam _TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
            paginate: {
                first: "İlk",
                last: "Son",
                next: "Sonraki",
                previous: "Önceki"
            },
            emptyTable: "Tabloda veri yok",
            zeroRecords: "Eşleşen kayıt bulunamadı"
        },
        dom: '<"d-flex justify-content-between align-items-center"l<"ml-auto"f>>t<"d-flex justify-content-between align-items-center"ip>'
    });
});

// Event handlers
window.cancelCustomer = () => CustomerManager.resetForm();
window.createCustomer = () => CustomerManager.create();
window.editCustomer = (id) => CustomerManager.edit(id);
window.deleteCustomer = (id) => {
    $('#deleteCustomerBtn').attr('onclick', `CustomerManager.delete(${id})`);
    $('#confirmModal').modal('show');
};
window.cancelImport = () => {
    $('#fileInput').val('');
    $('#excelModal').modal('hide');
};
window.startImport = () => ImportManager.processFile();
