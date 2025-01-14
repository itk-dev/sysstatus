# ITStyr

Styringsværktøj til IT projekter.

## Upgrade 2.x -> 3.x

To upgrade the database you have to do some manual steps to make the new doctrine 3.x migration work.

First create the new migration table.

```shell
docker compose exec phpfpm bin/console doctrine:migrations:sync-metadata-storage
```

Move old migrations and remove old table. Then run the migrations.

```shell
docker compose exec phpfpm bin/console doctrine:query:sql 'INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) SELECT concat("DoctrineMigrations\\Version", version), NULL, 1 FROM migration_versions;'
docker compose exec phpfpm bin/console doctrine:query:sql 'DROP TABLE migration_versions;'
docker compose exec phpfpm bin/console doctrine:migration:migrate
```

## Setup locally

### Preset

Make sure you have a set of JSON files for testing import Commands.

### Start Docker containers

```shell
docker compose up -d
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

### Create a super admin user

```sh
docker compose exec phpfpm bin/console SuperUser
```

### Access the site

You should now be able to browse to the application

```shell
open "http://$(docker-compose port nginx 8080)"
```

## Import systems and reports

```shell
docker compose exec phpfpm bin/console itstyr:import:system <URL>
docker compose exec phpfpm bin/console itstyr:import:report <URL>
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

<!-- markdownlint-disable MD013 -->
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
<!-- markdownlint-enable MD013 -->
