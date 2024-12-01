# Müşteri Yönetim Uygulaması

Bu Laravel projesi, kullanıcıların müşteri bilgilerini yönetmelerine olanak sağlar. **Admin** rolüne sahip kullanıcılar tam yetkilidir (CRUD işlemleri, Excel'den müşteri aktarımı gibi), **Personel** rolündeki kullanıcılar ise sadece müşterileri görüntüleyebilir.

---

## Kurulum

### 1. Depoyu Klonlayın
```bash
git clone https://github.com/murat-ertunc/user-app.git
cd user-app
```

### 2. Çevre Değişkenlerini Yapılandırın
`.env.example` dosyasını kopyalayarak `.env` dosyasını oluşturun:

```bash
cp .env.example .env
```

Veritabanı bilgilerinizi `.env` dosyasına ekleyin:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proje_adi
DB_USERNAME=root
DB_PASSWORD=şifre
```

### 3. Uygulama Anahtarını Oluşturun
```bash
php artisan key:generate
```

### 4. Veritabanı Migrasyonlarını ve Seed'leri Çalıştırın
```bash
php artisan migrate --seed
```

### 5. Queue Sistemi Ayarları
Uygulama, Excel aktarım işlemleri için kuyruk (queue) sistemi kullanır. Kuyruk çalıştırmak için şu komutu kullanın:
```bash
php artisan queue:work
```

### 6. Uygulamayı Başlatın
Laravel yerel geliştirme sunucusunu başlatın:
```bash
php artisan serve
```

## Kullanıcı Rolleri

Bu uygulamada iki tür kullanıcı rolü bulunmaktadır:

### Admin
- **CRUD İşlemleri**: Müşteri oluşturabilir, düzenleyebilir ve silebilir.
- **Listeleme**: Müşteri listesini görüntüleyebilir.
- **Excel Aktarımı**: Müşteri verilerini Excel'den aktarabilir.

### Personel
- **Listeleme**: Sadece müşteri listesini görüntüleyebilir, düzenleme ve silme yetkisi yoktur.


## Kullanılan Teknolojiler

- **[Laravel Framework](https://laravel.com/)**: PHP tabanlı modern web uygulama geliştirme framework'ü.
- **[Yajra DataTables](https://yajrabox.com/docs/laravel-datatables)**: Laravel için DataTables entegrasyonu.
- **Queue Sistemi**: Arka planda işlemleri yönetmek için Laravel Queue mekanizması.
- **MySQL**: Veritabanı yönetimi için kullanılan ilişkisel veritabanı.
- **Composer**: PHP bağımlılık yönetim aracı.


## API Endpoint'leri

### 1. Müşteri Listesi - `GET /customers`
- **Açıklama**: Müşteri listesini döner.
- **Yetki**: Admin / Personel
- **Başarı Durumu**: `200 OK`
- **Örnek Cevap**:
  ```json
  [
    {
      "id": 1,
      "name": "Ali Veli",
      "email": "ali.veli@example.com",
      "phone": "5551234567",
      "company": "Örnek Şirket"
    },
    {
      "id": 2,
      "name": "Ahmet Can",
      "email": "ahmet.can@example.com",
      "phone": "5559876543",
      "company": "Başka Şirket"
    }
  ]
    ```


  ### 2. Müşteri Ekleme - `POST /customers`
- **Açıklama**: Yeni müşteri ekler..
- **Yetki**: Admin
- Parametreler:
```json
{
  "name": "Müşteri Adı",
  "email": "email@example.com",
  "phone": "5551234567",
  "company": "Şirket Adı"
}
```

- **Başarı Durumu**: `201 Created`
- **Örnek Cevap**:
  ```json
    {
      "message": "Müşteri başarıyla oluşturuldu"
    }

    ```


  ### 3. Belirli Bir Müşteri - `GET /customers/{id}`
- **Açıklama**: Belirli bir müşteriyi döner
- **Yetki**: Admin
- Parametreler:
`id (Müşterinin ID'si`
- **Başarı Durumu**: `200 OK`
- **Örnek Cevap**:
  ```json
    {
      "id": 1,
      "name": "Ali Veli",
      "email": "ali.veli@example.com",
      "phone": "5551234567",
      "company": "Örnek Şirket"
    }
    ```

  ### 4. Müşteri Silme - `DELETE /customers/{id}`
- **Açıklama**: Müşteriyi siler.
- **Yetki**: Admin
- Parametreler:
`id (Müşterinin ID'si`
- **Başarı Durumu**: `200 OK`
- **Örnek Cevap**:
  ```json
    {
      "message": "Müşteri başarıyla silindi"
    }
    ```

    ### Müşteri Verisi Aktarımı - `POST /import`
- **Açıklama**: JSON formatında müşteri verisi aktarır.
- **Yetki**: Admin
- Parametreler:
```json
{
  "jsonData": "[{\"name\": \"Ahmet\", \"email\": \"ahmet@example.com\", \"phone\": \"123456789\", \"company\": \"X Ltd.\"}]"
}
```
- **Başarı Durumu**: `200 OK`
- **Örnek Cevap**:
  ```json
    {
      "message": "Aktarım işlemi başladı"
    }

    ```

## Yetkilendirme

Bu projede, kullanıcıların erişim yetkileri `role` alanı ile belirlenir. Aşağıda yetkilendirme mekanizması açıklanmıştır:

### Kullanıcı Rolleri

1. **Admin**: 
   - Admin, tüm CRUD işlemleri, müşteri verisi ekleme/düzenleme/silme işlemleri ve Excel veri aktarım işlemlerini gerçekleştirebilir.
   - Admin rolüne sahip bir kullanıcı, tüm işlemler için tam yetkiye sahiptir.

2. **Personel**:
   - Personel, sadece müşteri listesini görüntüleyebilir. Müşteri ekleme, düzenleme veya silme işlemleri yapamaz.

### Yetkilendirme Middleware

Uygulamada, kullanıcıların rollerine göre yetkilendirme kontrolü yapılır. Bu, Laravel'in **middleware** sistemi ile sağlanır.




