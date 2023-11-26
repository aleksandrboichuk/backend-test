# Тестове завдання

### Завдання 
- [посилання на завдання](https://github.com/adminko/backend_test/blob/main/README.md)
- Виконано:
  - релізовано клієнт для взаємодії з API (app/Services/Client.php)
  - релізовано команду для імпорту даних з API використовуючи вищевкказаний клієнт (app/Console/Commands/StoreDataFromApi.php)
  - релізовано ендпоінт для отримання даних про компанію та її робітників (routes/api.php)
  - покриття тестами вищевказаної команди та ендпоінту

### Розгортання проєкту:
- Перейти у робочу директорію та виконати наступні команди в консолі:
    + `git clone https://github.com/aleksandrboichuk/backend_test.git` - клонування проєкту у робочу директорію
    + `cd backend-test`
    + `cp .env.example .env && cd docker && cp docker-compose.example.yml docker-compose.yml` - копіювання конфігураційних файлів для docker-compose та laravel
    + `docker-compose build && docker-compose up -d` - білд та підняття контейнерів
    + `docker-compose exec php-fpm bash` - перехід у php-fpm контейнер для встановлення композеру (та виконання в майбутньому artisan команд)
    + `composer install` - встановлення composer
    + `php artisan migrate` - виконання міграцій

Проєкт має бути доступним за посиланням http://localhost:8080
    
   

