# LaDo
>Todo + Pomodoro application based on Laravel

This is a simple application for task and pomodoro management with statistics built on
[Laravel](https://laravel.com/), [Blade](https://laravel.com/docs/5.8/blade) and
[LiveWire](https://laravel-livewire.com/). It is modular and can be easily extended with the 
addition of new components and modules.

All data is stored in a MySQL database. The application is designed to be used by multiple users
with multiple projects and tasks.

## Installation
Clone the project, modify the `.env` file and do a migration:
```bash
php artisan migrate
```
> Note that the application requires email server configuration to send emails to users.

## Usage
```bash
npm run dev # To compile the frontend
# npm run build # To compile the frontend for production
php artisan serve
```
Create a new user and login. You can now create projects, tasks and pomodoros.

All functionality is available through the frontend or as Restful API calls:
```
/api/user
/api/register
/api/login
/api/project
/api/project/{id}/task
/api/pomo
```
Please login through `/api/login` to get the token for the other API calls, and remember to add the token to the header of the request.

Logout is done through `/api/logout`.

## Testing
```bash
php artisan test
```
