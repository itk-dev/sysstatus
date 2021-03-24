# itstyr
Styringsværktøj til IT projekter

## Setup locally

### Start Docker containers

```sh
docker-compose up -d
docker-compose exec phpfpm composer install
docker-compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

### Create a super admin user

```sh
docker-compose exec phpfpm bin/console fos:user:create --super-admin
```

### Access the site

You can get the site url by running:

```sh
echo http://itstyr.docker.localhost:$(docker-compose port reverse-proxy 80 | cut -d: -f2)
```

Open it in your default browser by running:

```sh
open http://itstyr.docker.localhost:$(docker-compose port reverse-proxy 80 | cut -d: -f2)
```

Note: You may have to add the line

```
0.0.0.0	itstyr.docker.localhost
```

to your `hosts` file to access the site.

## Debugging

To enable debugging in the `phpfpm` container, you have to restart it and enable `xdebug`:

```sh
PHP_XDEBUG=1 \
PHP_XDEBUG_REMOTE_AUTOSTART=1 \
PHP_XDEBUG_REMOTE_HOST=$(ipconfig getifaddr en0) \
PHP_XDEBUG_REMOTE_PORT=9000 \
PHP_XDEBUG_REMOTE_CONNECT_BACK=0 \
docker-compose up -d
```

To disable `xdebug`, restart the container:

```sh
docker-compose up -d
```

## Setup on server
https://symfony.com/doc/current/setup/web_server_configuration.html

## Import systems and reports
```sh
bin/console itstyr:import:system PATH
bin/console itstyr:import:report PATH
```
