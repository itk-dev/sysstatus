# itstyr

Styringsværktøj til IT projekter

## Setup locally

### Preset

Make sure you have a set of JSON files for testing import Commands.

### Start Docker containers

``` shell
docker compose pull
docker compose up --detach --remove-orphans --wait
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

### Create a super admin user

``` shell
docker compose exec phpfpm bin/console app:user:create super-admin@example.com --role ROLE_SUPER_ADMIN
```

Creating users:

``` shell
docker compose exec phpfpm bin/console app:user:create --help
```

Editing user roles:

``` shell
docker compose exec phpfpm bin/console app:user:roles --help
```

### Access the site

You should now be able to browse to the application

``` shell
open "http://$(docker-compose port nginx 8080)"
```

## Import systems and reports

``` shell
docker compose exec phpfpm bin/console itstyr:import:system PATH
docker compose exec phpfpm bin/console itstyr:import:report PATH
```

### Flowchart

A helpful flowchart over the Entities, and Joinedtables.
Ilustrative figures meaning:

1. Database = Database
2. Black square = Entities
3. Grey square = relations
4. Hexagon = Joinedtables
5. arrow = relation between DB and Entity
6. bulletin = shows the mapping Entities in Jointables, and JoinCollums

``` mermaid
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
