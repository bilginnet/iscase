# _Sipariş ve İndirim Modülü_

Mail ile iletmiş olduğunuz case projesinin dosyaları ve endpointleri bu dökümanda yeralmaktadır.

## Kullanılan Teknolojiler

- Laravel
- Rest Api
- Git

## Kurulum

.env.example dosyası .env olarak kopyalandıktan sonra .env dosyası üzerinde
veritabanı ayarları yapılması gerekmektedir.

```sh
git clone https://github.com/bilginnet/iscase.git
cd iscase
cp .env.example .env
composer install
php artisan migrate
php artisan db:seed
php artisan serve
```

## REST API

Aşağıda mevcut endpointlerin listesi verilmiştir.

| Action | Method | Url |
| ------ | ------ | ------ |
| Login | POST | localhost:8000/api/login |
| Create Order | POST | localhost:8000/api/order |
| Delete Order | DELETE | localhost:8000/api/order/5 |
| All Orders | GET | localhost:8000/api/order |
| Discount | GET | localhost:8000/api/discount/5 |

