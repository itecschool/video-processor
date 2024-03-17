
- Requiere tener queue de laravel configurado 
.env
`QUEUE_CONNECTION=database`

php artisan queue:table
php artisan migrate

php artisan queue:work

Configurar supervisor