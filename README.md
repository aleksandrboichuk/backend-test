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
    + `git clone https://github.com/aleksandrboichuk/backend_test.git` (клонування проєкту у робочу директорію)
    + `cd backend_test`
    + `cp .env.example .env && cd docker && cp docker-compose.example.yml docker-compose.yml`
    + `docker-compose build && docker-compose up -d`
    + `docker-compose exec php-fpm bash`
    + `composer install`

Проєкт має бути доступним за посиланням http://localhost:8080
    
   

