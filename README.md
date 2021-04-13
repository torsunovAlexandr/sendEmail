В файлe .env указать:  

1)В параметр MAIL_USERNAME добавить почтовый ящик с которого будет идти рассылка и в поле MAIL_PASSWORD пароль от ящика.<br>
2)Настройки своей бд

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3307
    DB_DATABASE=name_of_your_database
    DB_USERNAME=db_user_name
    DB_PASSWORD=db_password

3)Настройки для redis. Его я использовал для очередей

    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

Далее в файл database/seeders/UserSeeder.php в поле email прописать 4 email-адреса куда будут отправлять почта.

После этого выполнить миграции командой php artisan migrate

Далее наполнить бд запустив seeders

    php artisan db:seed --class=UserSeeder
    php artisan db:seed --class=UserActionsSeeder
    php artisan db:seed --class=LoginSourceSeeder
    php artisan db:seed --class=ActionsSeeder

После запустить менеджер очереди командой 

    php artisan queue:work

Далее перейти по адресу localhost/sendEmail

После этого сообщения поместятся в очередь и отправятся через равные промежутки времени в течении дня.
