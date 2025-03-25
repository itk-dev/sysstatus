# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

* [PR-29](https://github.com/itk-dev/sysstatus/pull/29)
  * Defined and applied coding standards
  * Updated user management commands

## [2.1.0]

* Merged https://github.com/aakb/itstyr/pull/14 - Upgraded symfony to 4.4.2.
* Merged https://github.com/aakb/itstyr/pull/13 - Added import run entity to track import run success. Changed report fields that are imported after change in Anmeldelsesportalen.

## [2.0.0]

* Changed to group control.
* Adds export of comments on answers instead of results.
* Adds color option for each answer in export.

## [1.9.0]

* Added eDoc url to Report and System.

## [1.8.0]

* Switched to JSON feeds.

## [1.7.1]

* Added ignore to archived systems and reports in exports.

## [1.6.1]

* Changed export function.

## [1.6.0]

* Added excel export of all systems and reports.

## [1.5.4]

* Changed fos user bundle templates.
* Fixed IE smiley styling.

## [1.5.3]

* Added system.sysStatus field.
* Changed smileys.
* Changed title of smileys to bootstrap tooltip.
* Removed system.sysArchiving field.

## [1.5.2]

* Fixed id and link in system.

## [1.5.1]

* Fixed links in lists.

## [1.5.0]

* Fixed various report mappings.

## [1.4.2]

* Adjusted fields for report importing.
* Fixed boolean types in system/report importing.

## [1.4.1]

* Update to symfony 4.1.3.

## [1.4.0]

* Added sub owner filter.
* Added extraction of sub owner for sysOwner field.

## [1.3.2]

* Fixed issue with template override for EasyAdminExtensionBundle.

## [1.3.1]

* Fixed primary key constraint issue with migration.
* Added scrollable to menu.

## [1.3.0]

* Changed from Hillrange/CKEditor to FOS/CKEditor.
* Filtered out inactive systems.
* Added Theme-Category.

## [1.2.2]

* Fixed issues with IE select.
* Moved icons to root of public folder.

## [1.2.1]

* Updated bundles.

## [1.2.0]

* Added groups, to users, systems, reports.
* Added group filter to lists.
* Added responsible user to systems and reports.
* Changed NULL values to display as empty instead of black square.
* Added reports/systems dashboards.
* Added script (itstyr:group:assign) to extract group from sysOwner for reports
  and systems.

## [1.1.0]

* Added themes, categories, questions, answers.

## [1.0.2]

* Added .htaccess file.

## [1.0.1]

* Added dotenv.
* Added security-checker.

## [1.0.0]

* Initial version.
* Reports and systems, with import scripts.
* Adding notes to each system and report.
* Adding general notes.
* Basic user management.

[Unreleased]: https://github.com/itk-dev/sysstatus/compare/2.1.0...HEAD
[2.1.0]: https://github.com/itk-dev/sysstatus/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/itk-dev/sysstatus/compare/1.9.0...2.0.0
[1.9.0]: https://github.com/itk-dev/sysstatus/compare/1.8.0...1.9.0
[1.8.0]: https://github.com/itk-dev/sysstatus/compare/1.7.1...1.8.0
[1.7.1]: https://github.com/itk-dev/sysstatus/compare/1.6.1...1.7.1
[1.6.1]: https://github.com/itk-dev/sysstatus/compare/1.6.0...1.6.1
[1.6.0]: https://github.com/itk-dev/sysstatus/compare/1.5.4...1.6.0
[1.5.4]: https://github.com/itk-dev/sysstatus/compare/1.5.3...1.5.4
[1.5.3]: https://github.com/itk-dev/sysstatus/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/itk-dev/sysstatus/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/itk-dev/sysstatus/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/itk-dev/sysstatus/compare/1.4.2...1.5.0
[1.4.2]: https://github.com/itk-dev/sysstatus/compare/1.4.1...1.4.2
[1.4.1]: https://github.com/itk-dev/sysstatus/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/itk-dev/sysstatus/compare/1.3.2...1.4.0
[1.3.2]: https://github.com/itk-dev/sysstatus/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/itk-dev/sysstatus/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/itk-dev/sysstatus/compare/1.2.2...1.3.0
[1.2.2]: https://github.com/itk-dev/sysstatus/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/itk-dev/sysstatus/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/itk-dev/sysstatus/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/itk-dev/sysstatus/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/itk-dev/sysstatus/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/itk-dev/sysstatus/compare/1.1.0...1.0.1
[1.0.0]: https://github.com/itk-dev/sysstatus/releases/tag/1.1.0
