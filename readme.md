# Personio Integration Light

## About

This repository provides the features for the Light version of the WordPress plugin _Personio Integration_. The repository is used as a basis for deploying the plugin to the WordPress repository. It is not intended to run as a plugin as it is, even if that is possible for development.

## Preparations

Add this in your wp-config.php for development:

```
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEVELOPMENT_MODE', 'plugin' );
```

You need to install:
* composer
* npm
* nvm

## Usage

After checkout go through the following steps:

1. copy _build/build.properties.dist_ to _build/build.properties_.
2. modify the build/build.properties file - note the comments in the file.
3. execute the command in _build/_: `ant init`
4. after that the plugin can be activated in WordPress

## Release

1. increase the version number in _build/build.properties_.
2. execute the following command in _build/_: `ant build`
3. after that you will have a zip file in the release directory which could be used in WordPress to install it.

## Translations

Translations are managed in the WordPress repository: https://translate.wordpress.org/projects/wp-plugins/personio-integration-light/

For local tests I recommend using [PoEdit](https://poedit.net/) to translate texts for this plugin. But first run `npm run build` to
generate the optimized block build files.

### generate pot-file

Run in the main directory:

`wp i18n make-pot . languages/personio-integration-light.pot --exclude=blocks/show/src/,blocks/list/src/,blocks/filter-list/src/,blocks/filter-select/src/,blocks/application-button/src/,blocks/details/src/,blocks/description/src/,blocks/setup/src/,svn/,deprecated/`

### update translation-file

1. Open .po-file of the language in PoEdit.
2. Go to "Translate" > "Update from POT-file".
3. After this the new entries are added to the language-file.

### export translation-file

1. Open .po-file of the language in PoEdit.
2. Go to "File" > "Save".
3. Upload the generated .mo-file and the .po-file to the plugin-folder languages/

### generate json-translation-files

Run in the main directory:

`wp i18n make-json languages`

OR use ant in build/-directory: `ant json-translations`

## Check for WordPress Coding Standards

### Initialize

`composer install`

### Run

`vendor/bin/phpcs --standard=ruleset.xml .`

### Repair

`vendor/bin/phpcbf --standard=ruleset.xml .`

## Check for WordPress VIP Coding Standards

Hint: this check runs against the VIP-GO-platform which is not our target for this plugin. Many warnings can be ignored.

### Run

`vendor/bin/phpcs --extensions=php --ignore=*/vendor/*,*/build/*,*/node_modules/*,*/blocks/*,*/svn/*,*/example/*,*/deprecated/* --standard=WordPress-VIP-Go .`

## Generate documentation

`vendor/bin/wp-documentor parse app --format=markdown --output=doc/hooks.md --prefix=personio_integration --exclude=Section.php --exclude=Tab.php --exclude=Import.php --exclude=Export.php --exclude=Field_Base.php --exclude=Settings.php --exclude=Page.php --exclude=Widget_Base.php --exclude=Transients.php`

## Analyze with PHPStan

`vendor/bin/phpstan analyse`

## Check PHP compatibility

`vendor/bin/phpcs -p app --standard=PHPCompatibilityWP`

## Check with plugin "Plugin Check"

This runs the plugin check as the plugin check in the WordPress repository does on every plugin update. It should result in no errors.

Hint: run this not in the development environment, it would also check all dependencies that is unnecessary.
Use a normal WordPress installation with an installed PCP plugin.

`wp plugin check --error-severity=7 --warning-severity=6 --include-low-severity-errors --categories=plugin_repo --format=json --slug=personio-integration-light .`

## PHP Unit tests

### Preparation

Be sure to have run `composer install` or `composer update` before.

Then: `composer test-install`

### Run

`composer test`
