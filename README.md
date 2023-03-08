# itstyr
Styringsværktøj til IT projekter

## Setup locally

### Start Docker containers

```sh
docker compose up -d
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

### Create a super admin user

```sh
docker compose exec phpfpm bin/console fos:user:create --super-admin
```

### Access the site

You should now be able to browse to the application

```

open "http://$(docker-compose port nginx 8080)"

```

## Import systems and reports
```sh
docker compose exec phpfpm bin/console itstyr:import:system PATH
docker compose exec phpfpm bin/console itstyr:import:report PATH
```


```mermaid
flowchart TD
 Answers[Answers]
 Category[Category]
 Group[Group]
 ImportRun[ImportrRun]
 Question[Question]
 Report[Report]
 SelServiceAFI[SelServiceAFI]
 System[System]
 Theme[Theme]
 ThemeCategory[ThemeCategory]
 User[User]
 
 fos_user_user_group[JoinTable: fos_user_user_group]
 
 Database --> Answers & Category & Group & ImportRun & Question  & Report & SelServiceAFI & System & Theme & ThemeCategory & User

 User <-- ManyToMany --> Group
 User -- JoinTable User and Groups --> fos_user_user_group
```

