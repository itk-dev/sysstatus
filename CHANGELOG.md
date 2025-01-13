# Changelog

![keep a changelog](https://img.shields.io/badge/Keep%20a%20Changelog-v1.1.0-brightgreen.svg?logo=data%3Aimage%2Fsvg%2Bxml%3Bbase64%2CPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiNmMTVkMzAiIHZpZXdCb3g9IjAgMCAxODcgMTg1Ij48cGF0aCBkPSJNNjIgN2MtMTUgMy0yOCAxMC0zNyAyMmExMjIgMTIyIDAgMDAtMTggOTEgNzQgNzQgMCAwMDE2IDM4YzYgOSAxNCAxNSAyNCAxOGE4OSA4OSAwIDAwMjQgNCA0NSA0NSAwIDAwNiAwbDMtMSAxMy0xYTE1OCAxNTggMCAwMDU1LTE3IDYzIDYzIDAgMDAzNS01MiAzNCAzNCAwIDAwLTEtNWMtMy0xOC05LTMzLTE5LTQ3LTEyLTE3LTI0LTI4LTM4LTM3QTg1IDg1IDAgMDA2MiA3em0zMCA4YzIwIDQgMzggMTQgNTMgMzEgMTcgMTggMjYgMzcgMjkgNTh2MTJjLTMgMTctMTMgMzAtMjggMzhhMTU1IDE1NSAwIDAxLTUzIDE2bC0xMyAyaC0xYTUxIDUxIDAgMDEtMTItMWwtMTctMmMtMTMtNC0yMy0xMi0yOS0yNy01LTEyLTgtMjQtOC0zOWExMzMgMTMzIDAgMDE4LTUwYzUtMTMgMTEtMjYgMjYtMzMgMTQtNyAyOS05IDQ1LTV6TTQwIDQ1YTk0IDk0IDAgMDAtMTcgNTQgNzUgNzUgMCAwMDYgMzJjOCAxOSAyMiAzMSA0MiAzMiAyMSAyIDQxLTIgNjAtMTRhNjAgNjAgMCAwMDIxLTE5IDUzIDUzIDAgMDA5LTI5YzAtMTYtOC0zMy0yMy01MWE0NyA0NyAwIDAwLTUtNWMtMjMtMjAtNDUtMjYtNjctMTgtMTIgNC0yMCA5LTI2IDE4em0xMDggNzZhNTAgNTAgMCAwMS0yMSAyMmMtMTcgOS0zMiAxMy00OCAxMy0xMSAwLTIxLTMtMzAtOS01LTMtOS05LTEzLTE2YTgxIDgxIDAgMDEtNi0zMiA5NCA5NCAwIDAxOC0zNSA5MCA5MCAwIDAxNi0xMmwxLTJjNS05IDEzLTEzIDIzLTE2IDE2LTUgMzItMyA1MCA5IDEzIDggMjMgMjAgMzAgMzYgNyAxNSA3IDI5IDAgNDJ6bS00My03M2MtMTctOC0zMy02LTQ2IDUtMTAgOC0xNiAyMC0xOSAzN2E1NCA1NCAwIDAwNSAzNGM3IDE1IDIwIDIzIDM3IDIyIDIyLTEgMzgtOSA0OC0yNGE0MSA0MSAwIDAwOC0yNCA0MyA0MyAwIDAwLTEtMTJjLTYtMTgtMTYtMzEtMzItMzh6bS0yMyA5MWgtMWMtNyAwLTE0LTItMjEtN2EyNyAyNyAwIDAxLTEwLTEzIDU3IDU3IDAgMDEtNC0yMCA2MyA2MyAwIDAxNi0yNWM1LTEyIDEyLTE5IDI0LTIxIDktMyAxOC0yIDI3IDIgMTQgNiAyMyAxOCAyNyAzM3MtMiAzMS0xNiA0MGMtMTEgOC0yMSAxMS0zMiAxMXptMS0zNHYxNGgtOFY2OGg4djI4bDEwLTEwaDExbC0xNCAxNSAxNyAxOEg5NnoiLz48L3N2Zz4K)

All notable changes to this project will be documented in this file.

See [keep a changelog] for information about writing changes to this log.

## [Unreleased]

* Added github actions
* Added PHPStan
* Updated composer setup

## [2.1.0] - 01-22-2020

* Merged [https://github.com/aakb/itstyr/pull/14](https://github.com/aakb/itstyr/pull/14)
  Upgraded symfony to 4.4.2.
* Merged [https://github.com/aakb/itstyr/pull/13](https://github.com/aakb/itstyr/pull/13)
  Added import run entity to track import run success. Changed report fields that are imported after change in
  Anmeldelsesportalen.

## 2.0.0

* Changed to group control.
* Adds export of comments on answers instead of results.
* Adds color option for each answer in export.

## 1.9.0

* Added eDoc url to Report and System.

## 1.8.0

* Switched to JSON feeds.

## 1.7.1

* Added ignore to archived systems and reports in exports.

## 1.6.1

* Changed export function.

## 1.6.0

* Added excel export of all systems and reports.

## 1.5.4

* Changed fos user bundle templates.
* Fixed IE smiley styling.

## 1.5.3

* Added system.sysStatus field.
* Changed smileys.
* Changed title of smileys to bootstrap tooltip.
* Removed system.sysArchiving field.

## 1.5.2

* Fixed id and link in system.

## 1.5.1

* Fixed links in lists.

## 1.5.0

* Fixed various report mappings.

## 1.4.2

* Adjusted fields for report importing.
* Fixed boolean types in system/report importing.

## 1.4.1

* Update to symfony 4.1.3.

## 1.4.0

* Added sub owner filter.
* Added extraction of sub owner for sysOwner field.

## 1.3.2

* Fixed issue with template override for EasyAdminExtensionBundle.

## 1.3.1

* Fixed primary key constraint issue with migration.
* Added scrollable to menu.

## 1.3.0

* Changed from Hillrange/CKEditor to FOS/CKEditor.
* Filtered out inactive systems.
* Added Theme-Category.

## 1.2.2

* Fixed issues with IE select.
* Moved icons to root of public folder.

## 1.2.1

* Updated bundles.

## 1.2.0

* Added groups, to users, systems, reports.
* Added group filter to lists.
* Added responsible user to systems and reports.
* Changed NULL values to display as empty instead of black square.
* Added reports/systems dashboards.
* Added script (itstyr:group:assign) to extract group from sysOwner for reports and systems.

## 1.1.0

* Added themes, categories, questions, answers.

## 1.0.2

* Added .htaccess file.

## 1.0.1

* Added dotenv.
* Added security-checker.

## 1.0.0

* Initial version.
* Reports and systems, with import scripts.
* Adding notes to each system and report.
* Adding general notes.
* Basic user management.

[keep a changelog]: https://keepachangelog.com/en/1.1.0/

[unreleased]: https://github.com/itk-dev/sysstatus/compare/main...develop

[2.1.0]: https://github.com/itk-dev/sysstatus/releases/tag/2.1.0
