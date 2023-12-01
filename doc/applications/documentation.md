# Application forms

## Note

For a quick start please refer to [Quickstart](quickstart.md). In the document here is the detailed description on how to handle applications in this plugin.

## Requirements

* Both Personio Integration Light and Personio Integration plugins must be installed and activated.
* A valid license for the Pro version must be stored.
* A Personio URL must be specified and open position should have been imported.

## Application process

1. interested person finds a position on the website for which he wants to apply.
2. interested person fills in the application form and submits it.
3. plugin saves the application in the local WordPress database.
    * Uploaded documents (files) are stored in a non-publicly accessible directory in WordPress hosting.
4. the plugin transmits the newly received application to Personio at a configured interval.
5. as soon as the Personio API gives feedback that the application has been successfully saved there, the plugin deletes the application data the local WordPress database.
    * This behavior is configurable.
    * With the deletion of the data, uploaded documents in the WordPress hosting are also deleted.
6. everything else about the application is handled within Personio.

## Notes

In the default setting, application data is stored in WordPress only as long as absolutely necessary. Once an application is successfully submitted, its data is removed from the local database. This also affects uploaded documents.

## Configuration

### Mandatory settings

In the WordPress backend under Positions > Settings > Export Settings, the following fields must be filled in:

* _Your Company ID_
* _Access Token_

Both details are required to submit applications to Personio. The details for both can be found in your Personio account under Settings > Integrations > API Credentials.

In the WordPress backend under Positions > Settings > Applications, the value "_Display application form_" must be selected for one of the fields "_Application in list view_" or "_Application in detail view_".

### Optional

In the WordPress backend under Positions > Settings > Export Settings there are the following additional fields:

* Recruiting Channel ID (optional).
    * After specifying this ID, you can assign the incoming application in Personio to a channel of your choice. The ID to be inserted can be found in your Personio account under Settings > Channels under the name "Recruiting API Channel ID".
* Recruiting phase (optional)
    * Defines the phase to which a new application is initially assigned. You can find the possible phases in your Personio account under Settings > Phases. There you have to enter the entry from the API ID field.
* Enable automatic export
    * If enabled, new applications will be transferred at the interval specified below.
    * If it is disabled, no applications will be transferred to Personio at all.
* Set interval for export
    * Sets the time interval at which new applications are transferred to Personio.
    * Pre-selection is "Once every hour".
* Deletion of exported application data.
    * Sets how long application data is stored in your WordPress hosting.
    * Preselection is "Immediately after successful transfer".

In the WordPress backend under Positions > Settings > Applications there are the following additional fields:

* _Activate to open the link in a new window_.
    * is only relevant when using the links to Personio, not when using the application form.
* _Select a form generator_
    * Selection of the form generator to be used for the application form.
    * As of December 2023, the plugin only provides its own generator (see [here](personioformulare.md)), which is why the field is grayed out.

## Export of applications to Personio

There are 4 ways to submit applications to Personio.

### Automatic export

If configured, the automatic export will run at the stored interval.

### Manual export of all applications

Under Positions > Applications you can click on the "Export now" button at the top. This will transfer all current applications to be transferred at this moment.

### Manual export of a single application

Under Positions > Applications, you can click on "Transfer to Personio now" for each application that does not have an error status. It will be transferred at this exact moment.

### via WP-CLI

At the console there is this command for transferring all applications:

`wp personio sendApplications`

## Error messages during export to Personio

If error messages occur during export, they can be found in the log under Positions > Settings > Logs. The messages correspond 1:1 to the return of the Personio API. They are documented here:
https://developer.personio.de/reference/post_v1-recruiting-applications
