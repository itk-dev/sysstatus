# itstyr
Styringsværktøj til IT projekter

## Setup locally
Set up a local database (mysql).

Set the database string in .env file. Copy .env.dist to .env and change to match database.

```sh
$ composer install
$ bin/console doctrine:migrations:migrate
```

### Create a super admin user
```sh
bin/console fos:user:create --super-admin
```

### Running server
https://symfony.com/doc/current/setup/built_in_web_server.html

Start the PHP development server with
```sh
bin/console server:run
```

Access the site at http://127.0.0.1:8000

## Setup on server
https://symfony.com/doc/current/setup/web_server_configuration.html

## Import from system portal
```sh
bin/console itstyr:import PATH
```
