## Task Management

This web application allow the user to manage tasks, comments and their users.


## To run this app, follow the following steps

Copy .env.example and paste it in the .env file, adjust the variables according to your environment

Run these commands:

```
php artisan key:generate
php artisan migrate
php artisan serve
```


In another terminal, to allow the system to send emails through jobs, run the following
```
php artisan queue:work
```

To run the command that gets the number of tasks for each user, run in another terminal:
```
statistics:tasks-per-user
```

## Application Routes

In this app, there are multiple routes as follows:
```
POST auth/login
POST auth/register
POST auth/logout
GET auth/current

GET /api/tasks/
POST /api/tasks/
PUT /api/tasks/{task}
DELETE /api/tasks
GET /api/tasks/{task}/comments
POST /api/tasks/{task}/comments
```

## Postman

To test the apis, you can find the postman collection stored in the root project directory
`Task Management.postman_collection.json`
