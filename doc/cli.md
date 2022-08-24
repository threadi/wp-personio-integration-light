## Using WP CLI

# General

The WP Cli executes WordPress commands at the console. This should only be executed with appropriate prior knowledge. The plugin provides a whole set of commands for quick editing of open positions in the database.

# Main command

Show list of available commands for this plugin:

`wp personio`

# Commands

`wp personio deleteAll`
=> delete all actual imported data (positions and all taxonomies)

`wp personio deletePositions`
=> delete all actual imported positions
=> additional import data like taxonomies will resist

`wp personio getPositions`
=> get actual positions from Personio
=> requires valid PersonioURL in settings
=> could be used to import positions via system cronjob

`wp personio resetPlugin`
=> resets the plugin completely
=> deletes all data
=> initiate the plugin as if it was fresh installed

# additional Commands in Pro-version

`wp personio deletePartials`
`wp personio removeAllDataPro`
`wp personio resetPluginPro`
`wp personio runPartialImport`

# Hint

Depending on your hosting-system this commands has to be run in the user-context of your website.