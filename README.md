### Clone the project.

### Install dependencies

You will need php 7.3 or superior

Example of installation on Ubuntu 18.04

```
composer install
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load
```

Lancer le server en local :

```
php -S localhost:3000 -t public
```

### Get JWT Token

```
curl -X POST -H "Content-Type: application/json" http://localhost:3000/api/login_check -d '{"username":"laruche@test.fr","password":"laruche"}'
-> { "token": "[TOKEN]" }  
```

### Swagger Documentation

Available here : http://localhost:3000/api/doc/commercial

### MySQL Connection

Don't forget to configure your MySQL conenction in .env.dev or .env.dev.local
DATABASE_URL=mysql://user:password@127.0.0.1:3306/laruche