Ліневич Денис ІПЗ-23-1
# [Документація API](https://documenter.getpostman.com/view/41673487/2sAYX3phTv)

# Запуск проекту
- Переконайтеся, що у вас встановлені всі необхідні залежності проекту:
```
composer install
```
- Згенеруйте SSL ключі
```
php bin/console lexik:jwt:generate-keypair
```
- В файлі .env пропишіть PASSPHRASE для ключів
```
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<PASSPHRASE HERE>
```
- Запустіть сервер
```
symfony server:start
