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

### Flowchart

A helpful flowchart over the Entities, and Joinedtables.

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
 
 fos_user_user_group{{JoinTable: fos_user_user_group }}
 group_system_themes{{JoinTable: group_system_themes }}
 group_report_themes{{JoinTable: group_report_themes }}

 Database[(Database)] --> Answers & Category & Group & ImportRun & Question  & Report & SelServiceAFI & System & Theme & ThemeCategory & User

 User---|ManyToMany| Group
  User -- JoinTable User and Group --o fos_user_user_group

 Group---|ManyToMany| Report
 Group---|ManyToMany| System
 Group---|ManyToMany| Theme
  Group -- JoinTable Theme and Group --o group_system_themes
 Group---|ManyToMany| Theme
  Group -- JoinTable Theme and Group --o group_report_themes

 Report --- |ManyToOne| Answers

 System --- |ManyToOne| Answers
 System --- |ManyToMany| SelServiceAFI

 Theme --- |ManyToOne| ThemeCategory
 
 Answers --- |ManyToOne| Question
  Question -- JoinCollum Answers and Question --o Answers

 ThemeCategory --- |ManyToOne| Category
  Category -- JoinCollum ThemeCategory and Category --o ThemeCategory

 Question --- |ManyToOne| Category
```

