# Personio Forms

This form generator is provided by the plugin. It allows you to use the fields supported by Personio for application forms. This includes the mandatory fields as well as additional fields that can be displayed in a form to the prospect.

The forms are managed in the form of form templates. These define which fields should be visible in the frontend.

## Mandatory fields

Personio has the following mandatory fields that are taken into account by the generator:

* First name and last name are mandatory fields and cannot be disabled or changed.
* Email is a required field and also cannot be disabled or changed.
* CV as a file upload must be present, but is not a mandatory field.

An e-mail address is only allowed 1 time per position. I.e. an applicant can apply only 1 time for a given position. The plugin takes this requirement into account by Personio.

## Settings

* Select form template
  * The list of form templates can be found under "Application form templates" (in the menu). There you can also configure them.
* Select file types for upload field
  * The specification here applies to all upload fields.
* Activate privacy checkbox
  * If checked, a checkbox will appear below each application form asking the applicant to agree to the site's privacy policy. Please consult with your privacy officer regarding this before unchecking.
* Text for privacy checkbox
* After submitting the form
  * Select what should happen after the form is submitted:
    * Show message via form
    * Show message without form
    * Forward to specific page
* Select a page to redirect to
  * To use this field, select "Forward to specific page" in the "After submitting form" setting.

## Form templates

The management of these can be found in the menu under "Application form templates".

The plugin already provides 3 ready-made templates. You can build as many templates as you want.

Each template can be edited and duplicated. You can also delete them, except the one that is currently used for application forms.

Downloading the configuration of each template is also possible, e.g. to transfer it to another project. There you can import the configuration.

A template is only used as a template for the output in the frontend. It is not used for further processing of applications and can therefore be adapted at any time without regard to existing data.

### Editing a template

The boxes in the editing page are structured as follows:

* Configured fields
  * This view shows the construction of the form in the template.
  * You can move the fields by dragging and dropping them.
  * Where possible, you can define that the field is a required field by ticking it.
  * Some fields can be deleted by clicking on the X to the right of them.
* Add Personio fields
  * This box contains a list of all optional fields supported by Personio.
  * They can be dragged and dropped into the "Configured Fields" box to use them in the form.
  * If a field is already in "Configured Fields", you cannot add it a 2nd time.
* Add Custom Field
  * See section below.
* Assign templates to positions
  * A template can also be assigned directly to one or more positions.
  * This makes it possible to use different application forms for your own open positions.
  * In the box here you just have to put a check mark in front of the position in which this template should be displayed.

#### Field types

* Select
  * Should be used for Personio attribute fields of type "List with options".
  * After selecting this type, you must enter the options available for selection in a 2nd field.
  * To do this, use the following format per line:
    * Personio-custom-attribute:value
  * **Personio-custom-attribute** is the value for the field of Personio (must start with _custom_attribute_).
  * **value** is the name displayed to the user.
  * Our plugin passes the custom-attribute to Personio's API so that Personio can uniquely associate it with a list value.
* Text
  * Should be used for Personio attribute fields of type "text".
  * No further configuration required.
* Date
  * Should be used for Personio attribute fields of type "Date".
  * No further configuration necessary.
* Multifile
  * Can be used for Personio attribute fields of type "Text".
  * Not yet supported by Personio.
* File
  * Can be used for Personio attribute fields of type "Text".
  * Not yet supported by Personio.
* Checkbox
  * Can be used for Personio attribute fields of type "Text".
  * Not yet supported by Personio.
* Email
  * Can be used for Personio attribute fields of type "Text".
  * Not yet supported by Personio.
* Textarea
  * Can be used for Personio attribute fields of type "Text".
  * Not yet supported by Personio.