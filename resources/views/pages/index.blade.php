@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/index.css') }}" rel="stylesheet">
<div class="toastr-container" id="toastr-container"></div>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand">Australia Sports Institute and Fitness Academy</a>
            <div class="dropdown ms-auto">
                <button class="btn profile-dropdown" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('assets/images/profile.png') }}" alt="Profile">
                    <span class="text-dark">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><circle cx="9" cy="6" r="4" stroke="#1C274C" stroke-width="1.5"></circle><path d="M15 9C16.6569 9 18 7.65685 18 6C18 4.34315 16.6569 3 15 3" stroke="#1C274C"stroke-width="1.5" stroke-linecap="round"></path><path d="M5.88915 20.5843C6.82627 20.8504 7.88256 21 9 21C12.866 21 16 19.2091 16 17C16 14.7909 12.866 13 9 13C5.13401 13 2 14.7909 2 17C2 17.3453 2.07657 17.6804 2.22053 18"stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path><path d="M18 14C19.7542 14.3847 21 15.3589 21 16.5C21 17.5293 19.9863 18.4229 18.5 18.8704" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path></g></svg>
                    Müşteriler
                </h2>
                <div>
                    <button class="btn btn-primary admin-only" data-bs-toggle="modal" data-bs-target="#customerModal" id="customerModalBtn">
                        <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><circle cx="10" cy="6" r="4" stroke="#ffffff" stroke-width="1.5"></circle><path d="M21 10H19M19 10H17M19 10L19 8M19 10L19 12" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"></path><path d="M17.9975 18C18 17.8358 18 17.669 18 17.5C18 15.0147 14.4183 13 10 13C5.58172 13 2 15.0147 2 17.5C2 19.9853 2 22 10 22C12.231 22 13.8398 21.8433 15 21.5634"stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"></path></g> </svg>
                        Müşteri Oluştur
                    </button>
                    <button class="btn btn-primary admin-only" data-bs-toggle="modal" data-bs-target="#excelModal" id="excelModalBtn">
                        <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 14V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H13C16.7712 2 18.6569 2 19.8284 3.17157C20.4816 3.82476 20.7706 4.69989 20.8985 6M21 10V14C21 17.7712 21 19.6569 19.8284 20.8284C18.6569 22 16.7712 22 13 22H11C7.22876 22 5.34315 22 4.17157 20.8284C3.51839 20.1752 3.22937 19.3001 3.10149 18" stroke="#fff" stroke-width="1.5" stroke-linecap="round"></path> <path d="M8 14H13" stroke="#fff" stroke-width="1.5" stroke-linecap="round"></path> <path d="M8 10H9M16 10H12" stroke="#fff" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
                        Excel'den Yükle
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="customersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>İsim, Soyisim</th>
                                    <th>E-Posta</th>
                                    <th>Telefon Numarası</th>
                                    <th>Şirket Adı</th>
                                    <th class="admin-only">İşlemler</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Müşteri Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="customerId" class="createInput">
                    <div class="mb-4">
                        <label for="nameInput" class="form-label mb-0">İsim, Soyisim*</label>
                        <input type="text" class="form-control createInput" id="nameInput" placeholder="John Doe" required>
                    </div>
                    <div class="mb-4">
                        <label for="emailInput" class="form-label mb-0">E-Posta*</label>
                        <input type="email" class="form-control createInput" id="emailInput" placeholder="example@domain.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="phoneInput" class="form-label mb-0">Telefon Numarası*</label>
                        <input type="text" class="form-control createInput" id="phoneInput" placeholder="+905123456789" required>
                    </div>
                    <div class="mb-4">
                        <label for="companyInput" class="form-label mb-0">Şirket Adı</label>
                        <input type="text" class="form-control createInput" id="companyInput" placeholder="Australia Sports Institute and Fitness Academy" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cancelCustomer()">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="createCustomer()" id="saveButton">Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excelModal" tabindex="-1" aria-labelledby="excelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelModalLabel">Excel ile Müşteri Yükle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="fileInput" class="form-label mb-0">Excel Dosyası</label>
                        <input type="file" class="form-control" id="fileInput" required accept=".xlsx">
                        <a href="{{ asset('assets/files/Example.xlsx') }}" download class="float-end" style="text-decoration:none">
                            <small>
                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M19.8978 16H7.89778C6.96781 16 6.50282 16 6.12132 16.1022C5.08604 16.3796 4.2774 17.1883 4 18.2235" stroke="#0d6efd" stroke-width="1.5"></path> <path d="M8 7H16" stroke="#0d6efd" stroke-width="1.5" stroke-linecap="round"></path> <path d="M8 10.5H13" stroke="#0d6efd" stroke-width="1.5" stroke-linecap="round"></path> <path d="M19.5 19H8" stroke="#0d6efd" stroke-width="1.5" stroke-linecap="round"></path> <path d="M10 22C7.17157 22 5.75736 22 4.87868 21.1213C4 20.2426 4 18.8284 4 16V8C4 5.17157 4 3.75736 4.87868 2.87868C5.75736 2 7.17157 2 10 2H14C16.8284 2 18.2426 2 19.1213 2.87868C20 3.75736 20 5.17157 20 8M14 22C16.8284 22 18.2426 22 19.1213 21.1213C20 20.2426 20 18.8284 20 16V12" stroke="#0d6efd" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
                                Örnek Dosya
                            </small>
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cancelImport()">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="startImport()" id="importButton">Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Eminmisiniz?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Bu müşteriyi silmek istediğinize emin misiniz?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cancelDelete()">İptal</button>
                    <button type="button" class="btn btn-primary" id="deleteCustomerBtn">Sil</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/index.js') }}"></script>
    <script>
        let isAdmin = @hasrole('admin') true @else false @endhasrole;
    </script>
@endsection
