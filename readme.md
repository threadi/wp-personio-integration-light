# Personio Integration Light

## About

This repository provides the features of the Light version of the WordPress plugin _Personio Integration_. The repository is used as a basis for deploying the plugin to the WordPress repository. It is not intended to run as a plugin as it is, even if that is possible for development.

## Preparations

Add this in your wp-config.php for development:

```
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEVELOPMENT_MODE', 'plugin' );
```

You need to install:
* npm
* node

## Usage

After checkout go through the following steps:

1. copy _build/build.properties.dist_ to _build/build.properties_.
2. modify the build/build.properties file - note the comments in the file.
3. execute the command in _build/_: `ant init`
4. after that the plugin can be activated in WordPress

## Release

1. increase the version number in _build/build.properties_.
2. execute the following command in _build/_: `ant build`
3. after that you will finde in the release directory a zip file which could be used in WordPress to install it.

## Translations

I recommend to use [PoEdit](https://poedit.net/) to translate texts for this plugin.

### generate pot-file

Run in main directory:

`wp i18n make-pot . languages/personio-integration-light.pot --exclude=blocks/show/src/,blocks/list/src/,blocks/filter-list/src/,blocks/filter-select/src/,blocks/application-button/src/,blocks/details/src/,blocks/description/src/,blocks/setup/src/,svn/,deprecated/`

### update translation-file

1. Open .po-file of the language in PoEdit.
2. Go to "Translate" > "Update from POT-file".
3. After this the new entries are added to the language-file.

### export translation-file

1. Open .po-file of the language in PoEdit.
2. Go to File > Save.
3. Upload the generated .mo-file and the .po-file to the plugin-folder languages/

### generate json-translation-files

Run in main directory:

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

`vendor/bin/wp-documentor parse app --format=markdown --output=doc/hooks.md --prefix=personio_integration`

## Analyse with PHPStan

`vendor/bin/phpstan analyse`
