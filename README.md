# Ліневич Денис ІПЗ-23-1
# [Документація API](https://documenter.getpostman.com/view/41673487/2sAYX3phTv)

# Запуск проекту
- Переконайтеся, що у вас встановлені всі необхідні залежності проекту:
```
composer install
```
- Згенеруйте SSL ключі:
```
php bin/console lexik:jwt:generate-keypair
```
- (Опціонально) Якщо спосіб зверху не спрацював, спробуйте самі в проекті створити папку config/jwt та скористайтеся командами нижче, щоб згенерувати ключі вручну. OpenSSL вже повинен бути встановлений в Git Bash:
```
openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
- В файлі .env пропишіть PASSPHRASE для ключів:
```
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<PASSPHRASE HERE>
```
- Запустіть сервер:
```
symfony server:start
