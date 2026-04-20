# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### `personio_integration_pro_prevent_application_export_check`

*Run additional tasks before the transfer is run.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\Plugin\Schedules\Application_Export` | The schedule export object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules/Application_Export.php](Plugin/Schedules/Application_Export.php), [line 105](Plugin/Schedules/Application_Export.php#L105-L111)

### `personio_integration_pro_license_activated`

*Run action after activation of license.*


Source: [app/Plugin/Admin/SettingsValidation/License.php](Plugin/Admin/SettingsValidation/License.php), [line 66](Plugin/Admin/SettingsValidation/License.php#L66-L71)

### `personio_integration_pro_license_invalidated`

*Run actions if license key is invalid.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 485](Plugin/License.php#L485-L490)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Everest_Forms.php](FormHandler/Everest_Forms.php), [line 498](FormHandler/Everest_Forms.php#L498-L505)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Everest_Forms.php](FormHandler/Everest_Forms.php), [line 513](FormHandler/Everest_Forms.php#L513-L520)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application will be saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 492](FormHandler/Personio_Forms.php#L492-L499)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 515](FormHandler/Personio_Forms.php#L515-L522)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Wpforms.php](FormHandler/Wpforms.php), [line 976](FormHandler/Wpforms.php#L976-L983)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Wpforms.php](FormHandler/Wpforms.php), [line 991](FormHandler/Wpforms.php#L991-L998)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Elementor_Forms.php](FormHandler/Elementor_Forms.php), [line 726](FormHandler/Elementor_Forms.php#L726-L733)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Elementor_Forms.php](FormHandler/Elementor_Forms.php), [line 741](FormHandler/Elementor_Forms.php#L741-L748)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Forminator.php](FormHandler/Forminator.php), [line 694](FormHandler/Forminator.php#L694-L701)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Forminator.php](FormHandler/Forminator.php), [line 709](FormHandler/Forminator.php#L709-L716)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Fluent_Forms.php](FormHandler/Fluent_Forms.php), [line 1210](FormHandler/Fluent_Forms.php#L1210-L1217)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Fluent_Forms.php](FormHandler/Fluent_Forms.php), [line 1225](FormHandler/Fluent_Forms.php#L1225-L1232)

### `personio_integration_pro_application_before_save`

*Run additional tasks before application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Contact_Form_7.php](FormHandler/Contact_Form_7.php), [line 486](FormHandler/Contact_Form_7.php#L486-L493)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Contact_Form_7.php](FormHandler/Contact_Form_7.php), [line 501](FormHandler/Contact_Form_7.php#L501-L508)

### `personio_integration_pro_after_reset_honeypot`

*Run tasks after honeypot has been reset.*


**Changelog**

Version | Description
------- | -----------
`4.4.1` | Available since 4.4.1.

Source: [app/FormHandler/PersonioForms/Extensions/Honeypot.php](FormHandler/PersonioForms/Extensions/Honeypot.php), [line 433](FormHandler/PersonioForms/Extensions/Honeypot.php#L433-L438)

### `personio_integration_pro_application_before_save`

*Run additional tasks before the application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Avada_Forms.php](FormHandler/Avada_Forms.php), [line 627](FormHandler/Avada_Forms.php#L627-L634)

### `personio_integration_pro_application_after_save`

*Run additional tasks after application has been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | Object of the application.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/FormHandler/Avada_Forms.php](FormHandler/Avada_Forms.php), [line 642](FormHandler/Avada_Forms.php#L642-L649)

### `personio_integration_pro_application_template_before_form`

*Run tasks before the application form is visible for Personio Forms.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Form_Types/Template.php](FormHandler/Form_Types/Template.php), [line 134](FormHandler/Form_Types/Template.php#L134-L139)

### `personio_integration_pro_application_template_after_form`

*Run tasks after the application form is visible for Personio Forms.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Form_Types/Template.php](FormHandler/Form_Types/Template.php), [line 144](FormHandler/Form_Types/Template.php#L144-L149)

### `personio_integration_pro_add_popup_scripts`

*Run the popup scripts on any other cases.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 785](PersonioIntegration/Positions.php#L785-L790)

### `personio_integration_pro_position_table_personio_account`

*Add content to the column in the position-table in backend.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` | `int` | ID of the item.

Source: [app/PersonioIntegration/Personio_Accounts.php](PersonioIntegration/Personio_Accounts.php), [line 363](PersonioIntegration/Personio_Accounts.php#L363-L383)

### `personio_integration_pro_blacklist_table_buttons_top`

*Add custom actions in head of blacklist table.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Tables/Blacklist.php](PersonioIntegration/Tables/Blacklist.php), [line 175](PersonioIntegration/Tables/Blacklist.php#L175-L180)

### `personio_integration_pro_application_table_buttons_top`

*Add custom actions in head of applications table.*


**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Tables/Applications.php](PersonioIntegration/Tables/Applications.php), [line 351](PersonioIntegration/Tables/Applications.php#L351-L356)

### `personio_integration_pro_application_table_buttons_bottom`

*Add custom actions in bottom of applications table.*


**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Tables/Applications.php](PersonioIntegration/Tables/Applications.php), [line 368](PersonioIntegration/Tables/Applications.php#L368-L373)

### `personio_integration_pro_application_remove_credentials`

*Run additional actions after deleting the API credentials.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 2626](Applications/Applications.php#L2626-L2631)

### `personio_integration_pro_application_saved`

*Run additional tasks after the application has been successfully saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 359](Applications/Application.php#L359-L365)

### `personio_integration_pro_application_updated`

*Run additional tasks after the application has been successfully updated.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 372](Applications/Application.php#L372-L378)

### `personio_integration_pro_application_metas_updated`

*Run additional tasks after the meta-data of an application has been successfully updated.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application as object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 403](Applications/Application.php#L403-L409)

### `personio_integration_pro_application_delete`

*Run additional tasks before an application is deleted.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.
`$identifier` | `string` | The used identifier.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 445](Applications/Application.php#L445-L452)

### `personio_integration_pro_application_deleted`

*Run additional tasks after an application has been deleted.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application as object.
`$identifier` | `string` | The used identifier.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 501](Applications/Application.php#L501-L508)

### `personio_integration_pro_application_transfer_failed`

*Run action if transfer of application failed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.
`$message` | `string` | The message.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 261](Applications/Transfer.php#L261-L268)

### `personio_integration_pro_application_transfer_failed`

*Run action if transfer of application failed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.
`$message` | `string` | The message.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 301](Applications/Transfer.php#L301-L308)

### `personio_integration_pro_application_transfer_failed`

*Run action if transfer of application failed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.
`$message` | `string` | The message.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 602](Applications/Transfer.php#L602-L609)

### `personio_integration_pro_application_transfer_failed`

*Run action if transfer of application failed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application as an object.
`$message` | `string` | The message.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 624](Applications/Transfer.php#L624-L631)

### `personio_integration_pro_form_template_init`

*Run additional tasks for initialization of this form.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationPro\FormTemplates\Template` | The template as object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/FormTemplates/Template.php](FormTemplates/Template.php), [line 128](FormTemplates/Template.php#L128-L134)

## Filters

### `personio_integration_pro_update_check`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Update.php](Plugin/Update.php), [line 151](Plugin/Update.php#L151-L151)

### `personio_integration_pro_update_transient`

*Filter the plugin updates transient.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$transient` | `\stdClass` | The list of plugins with its settings.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0

Source: [app/Plugin/Update.php](Plugin/Update.php), [line 247](Plugin/Update.php#L247-L253)

### `personio_integration_pro_physics_units`

*Filter the available units for circle search.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$units` | `string[]` | List of unit-object-names.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Physics.php](Plugin/Physics.php), [line 103](Plugin/Physics.php#L103-L109)

### `personio_integration_pro_application_form_link_targets`

*Filter the possible link targets for application forms.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list_options` | `array<string,string>` | List of possible options.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 377](Plugin/Templates.php#L377-L384)

### `personio_integration_pro_geolocation_google_url`

*Filter the used Google Geocoding API-URL for geolocation.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL to use.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0.

Source: [app/Plugin/GeoServices/GoogleGeocodingApi.php](Plugin/GeoServices/GoogleGeocodingApi.php), [line 138](Plugin/GeoServices/GoogleGeocodingApi.php#L138-L144)

### `personio_integration_pro_geolocation_openstreetmap_url`

*Filter the used OpenStreetMap-URL for geolocation.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL to use.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0.

Source: [app/Plugin/GeoServices/OpenStreetMap.php](Plugin/GeoServices/OpenStreetMap.php), [line 215](Plugin/GeoServices/OpenStreetMap.php#L215-L221)

### `personio_integration_pro_geolocation_apis`

*Filter the available geolocation apis.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$apis` | `string[]` | List of APIs.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0.

Source: [app/Plugin/GeoServices.php](Plugin/GeoServices.php), [line 76](Plugin/GeoServices.php#L76-L82)

### `personio_integration_pro_menu`

*Filter our menu in backend.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$new_sort` | `array<int,array<int,string>>` | List of menu entries of our menu in backend.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 541](Plugin/Admin/Admin.php#L541-L547)

### `personio_integration_pro_cleanup_string_for_meta_value`

*Filter the cleaned string for meta values.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$cleaned_string` | `string` | The cleaned string.
`$original_string` | `string` | The original string.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 67](Helper.php#L67-L75)

### `personio_integration_pro_file_version`

*Change the used file version.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$plugin_version` | `string` | The plugin-version.
`$filepath` | `string` | The absolute path to the requested file.

Source: [app/Helper.php](Helper.php), [line 154](Helper.php#L154-L160)

### `personio_integration_pro_application_options`

*Filter the possible application options.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$options` | `array<string,string>` | List of possible options.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 229](Helper.php#L229-L235)

### `personio_integration_pro_wp_config_name`

*Filter to change the filename of the used wp-config.php without its extension .php.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wp_config_php` | `string` | The filename.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Helper.php](Helper.php), [line 368](Helper.php#L368-L374)

### `personio_integration_pro_wp_config_path`

*Filter the path for the wp-config.php before we return it.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wp_config_php_path` | `string` | The path.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Helper.php](Helper.php), [line 379](Helper.php#L379-L385)

### `personio_integration_pro_before_validate_application_submission`

*Validate the submission of an application form before further checks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if the validation should break.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 278](FormHandler/Personio_Forms.php#L278-L287)

### `personio_integration_pro_url_on_submission`

*Filter the URL used for submission.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 293](FormHandler/Personio_Forms.php#L293-L300)

### `personio_integration_pro_before_validate_application_submission_with_position`

*Validate the submission of an application form before further checks with the requested position object.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if the validation should break.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | Object of called position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 340](FormHandler/Personio_Forms.php#L340-L350)

### `personio_integration_pro_display_form_template`

*Filter the template configuration for output in frontend and during save.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$formular_template_obj` | `\PersonioIntegrationPro\FormTemplates\Template\|false` | The template object.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | The object of the displayed position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 373](FormHandler/Personio_Forms.php#L373-L381)

### `personio_integration_pro_application_fields`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$formular_template_obj->get_fields()` |  | 

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 395](FormHandler/Personio_Forms.php#L395-L395)

### `personio_integration_pro_application_field`

*Filter the field object during processing its data.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$field_obj` | `\PersonioIntegrationPro\FormTemplates\Field_Base` | The field object.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 407](FormHandler/Personio_Forms.php#L407-L414)

### `personio_integration_pro_application_field_results`

*Filter the validation result of a single Personio forms field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$result` | `array` | List of results.
`$field_obj` | `\PersonioIntegrationPro\FormTemplates\Field_Base` | The checked field object with its value.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 419](FormHandler/Personio_Forms.php#L419-L426)

### `personio_integration_pro_personio_forms_templates`

*Change the list of possible filter-types.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<string,string>` | The list of types.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since first release.

Source: [app/FormHandler/Personio_Forms.php](FormHandler/Personio_Forms.php), [line 873](FormHandler/Personio_Forms.php#L873-L880)

### `personio_integration_pro_templates_prefix`

*Filter the prefix for templates from Personio Integration in third party form plugins*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$prefix` | `string` | The prefix.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/FormHandlers_Base.php](FormHandler/FormHandlers_Base.php), [line 248](FormHandler/FormHandlers_Base.php#L248-L254)

### `personio_integration_pro_fields_prefix`

*Filter the prefix for fields from Personio Integration in third party form plugins.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$prefix` | `string` | The prefix.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/FormHandlers_Base.php](FormHandler/FormHandlers_Base.php), [line 265](FormHandler/FormHandlers_Base.php#L265-L271)

### `personio_integration_pro_form_handler_logo_url`

*Filter the logo URL of the form handler.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$logo` | `string` | The logo.
`$instance` | `\PersonioIntegrationPro\FormHandler\FormHandlers_Base` | The form handler object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/FormHandlers_Base.php](FormHandler/FormHandlers_Base.php), [line 356](FormHandler/FormHandlers_Base.php#L356-L363)

### `personio_integration_pro_cf7_field_attributes`

*Filter the attributes for a Contact Form 7 field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$additional_attributes` | `string` | List of attributes as string.
`$field_obj` | `\PersonioIntegrationPro\FormTemplates\Field_Base` | The field as object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Contact_Form_7.php](FormHandler/Contact_Form_7.php), [line 775](FormHandler/Contact_Form_7.php#L775-L782)

### `personio_integration_pro_cf7_form`

*Filter the configured CF7 form.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html_form` | `string` | The generated HTML-Code.
`$template_obj` | `\PersonioIntegrationPro\FormTemplates\Template` | The used template object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Contact_Form_7.php](FormHandler/Contact_Form_7.php), [line 1078](FormHandler/Contact_Form_7.php#L1078-L1085)

### `personio_integration_pro_personio_forms_rest_type`

*Get the result type (name for the resulting object) for the response after REST-submission of an application.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$type` | `string` | The result handler name.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/PersonioForms/Routes.php](FormHandler/PersonioForms/Routes.php), [line 113](FormHandler/PersonioForms/Routes.php#L113-L119)

### `personio_integration_pro_personio_forms_rest_results`

*Get the resulting text for show in frontend.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The result message.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/PersonioForms/Routes.php](FormHandler/PersonioForms/Routes.php), [line 122](FormHandler/PersonioForms/Routes.php#L122-L128)

### `personio_integration_pro_personio_forms_rest_fields`

*Get the error fields.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$fields` | `array<string,mixed>` | The resulting error fields.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/PersonioForms/Routes.php](FormHandler/PersonioForms/Routes.php), [line 131](FormHandler/PersonioForms/Routes.php#L131-L137)

### `personio_integration_pro_honeypot_value`

*Filter the honeypot value.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$honeypot_value` | `string` | The honeypot value.

**Changelog**

Version | Description
------- | -----------
`5.0.2` | Available since 5.0.2.

Source: [app/FormHandler/PersonioForms/Extensions/Honeypot.php](FormHandler/PersonioForms/Extensions/Honeypot.php), [line 523](FormHandler/PersonioForms/Extensions/Honeypot.php#L523-L529)

### `personio_integration_pro_application_form_page_slug`

*Filter the slug for the application form page.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$slug` | `string` | The slug to use.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/PersonioForms/Extensions/SingleApplicationFormPage.php](FormHandler/PersonioForms/Extensions/SingleApplicationFormPage.php), [line 460](FormHandler/PersonioForms/Extensions/SingleApplicationFormPage.php#L460-L466)

### `personio_integration_pro_application_form_page_query_param`

*Filter the query param for the application form page. Change is only useful for usage of simple URLs.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$query_param` | `string` | The query param to use.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/PersonioForms/Extensions/SingleApplicationFormPage.php](FormHandler/PersonioForms/Extensions/SingleApplicationFormPage.php), [line 478](FormHandler/PersonioForms/Extensions/SingleApplicationFormPage.php#L478-L484)

### `personio_integration_pro_personio_forms_result_handlers`

*Filter the list of possible Personio Forms result handlers.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of handlers.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/PersonioForms/ResultHandling.php](FormHandler/PersonioForms/ResultHandling.php), [line 153](FormHandler/PersonioForms/ResultHandling.php#L153-L159)

### `personio_integration_pro_avada_field_attributes`

*Filter the attributes for an Avada Forms field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$additional_attributes` | `array<int,string>` | List of attributes as string.
`$field_obj` | `\PersonioIntegrationPro\FormTemplates\Field_Base` | The field as object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Avada_Forms.php](FormHandler/Avada_Forms.php), [line 965](FormHandler/Avada_Forms.php#L965-L972)

### `personio_integration_pro_form_type_template_obj`

*Filter the form template object before the output as a template form type.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$formular_template_obj` | `\PersonioIntegrationPro\FormTemplates\Template` | The template.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position\|null` | The position object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Form_Types/Template.php](FormHandler/Form_Types/Template.php), [line 116](FormHandler/Form_Types/Template.php#L116-L123)

### `personio_integration_pro_shortcode_form`

*Filter the generated output of a shortcode-based form.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$form` | `string` | The form HTML-code.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormHandler/Form_Types/Shortcode.php](FormHandler/Form_Types/Shortcode.php), [line 158](FormHandler/Form_Types/Shortcode.php#L158-L164)

### `personio_integration_pro_hide_application_form`

*Filter whether to hide the application form.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Should be true to prevent the visibility of the form.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | The object of the used position.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0.

Source: [app/FormHandler/Form_Handlers.php](FormHandler/Form_Handlers.php), [line 152](FormHandler/Form_Handlers.php#L152-L159)

### `personio_integration_pro_form_file_categories`

*Filter the list of possible categories for application documents on Personio.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$categories` | `array<string,string>` | List of categories.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/FormHandler/Form_Handlers.php](FormHandler/Form_Handlers.php), [line 1089](FormHandler/Form_Handlers.php#L1089-L1096)

### `personio_integration_pro_form_template_fields_before_saving`

*Filter the fields of a form template before they are saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$fields` | `array<string,\PersonioIntegrationPro\FormTemplates\Field_Base>` | The fields of the form template.
`$field_key` | `string` | The key of the field that is being saved.

**Changelog**

Version | Description
------- | -----------
`5.2.0` | Available since 5.2.0.

Source: [app/PersonioIntegration/PostTypes/Personio_Form_Template.php](PersonioIntegration/PostTypes/Personio_Form_Template.php), [line 1413](PersonioIntegration/PostTypes/Personio_Form_Template.php#L1413-L1420)

### `personio_integration_pro_position_table_personio_id`

*Filter the Personio ID for output in the table of positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_id` | `string` | The Personio ID.
`$post_id` | `int` | The requested post-ID.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 304](PersonioIntegration/Positions.php#L304-L312)

### `personio_integration_pro_popup_dialog`

*Change the dialog.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` | `array` | The dialog.
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position object.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 746](PersonioIntegration/Positions.php#L746-L753)

### `personio_integration_pro_ga_tracking_options`

*Filter the tracking options.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$options` | `\stdClass` | List of options.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/Tracking/Google_Analytics.php](PersonioIntegration/Tracking/Google_Analytics.php), [line 116](PersonioIntegration/Tracking/Google_Analytics.php#L116-L122)

### `personio_integration_pro_videos_list`

*Filters the list of videos for the actual position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$videos` | `array<int,array<string,mixed>>` | List of videos.
`$personio_attributes` | `array<string,mixed>` | List of attributes to use.
`$video_attributes` | `array<string,mixed>` | List of attributes to use for the video element.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | Object of the requested position.

**Changelog**

Version | Description
------- | -----------
`5.3.0` | Available since 5.3.0.

Source: [app/PersonioIntegration/Videos.php](PersonioIntegration/Videos.php), [line 157](PersonioIntegration/Videos.php#L157-L167)

### `personio_integration_pro_hide_form`

*Filter to hide the application form.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set to true if you want to hide the form.
`$instance` | `\PersonioIntegrationPro\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 237](PersonioIntegration/Position.php#L237-L245)

### `personio_integration_pro_position_api_token`

*Return the token used for the Personio account of this position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`get_option('personioIntegrationAccessToken')` |  | 
`$this` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 315](PersonioIntegration/Position.php#L315-L321)

### `personio_integration_pro_position_api_company_id`

*Return the token used for the Personio account of this position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`get_option('personioIntegrationCompanyId')` |  | 
`$this` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 324](PersonioIntegration/Position.php#L324-L330)

### `personio_integration_pro_position_api_channel`

*Return the token used for the Personio account of this position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`get_option('personioIntegrationRecruitingChannelId')` |  | 
`$this` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 333](PersonioIntegration/Position.php#L333-L339)

### `personio_integration_pro_position_api_phase`

*Return the token used for the Personio account of this position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`get_option('personioIntegrationRecruitingPhase')` |  | 
`$this` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 342](PersonioIntegration/Position.php#L342-L348)

### `personio_integration_pro_polylang_strings`

*Filter the possible list of strings (option-field-names) to translate via Polylang.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$strings` | `array<int,string>` | List of strings.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Multilingual/Polylang.php](PersonioIntegration/Multilingual/Polylang.php), [line 482](PersonioIntegration/Multilingual/Polylang.php#L482-L489)

### `personio_integration_pro_wpml_strings`

*Filter the possible list of strings (option-field-names) to translate via WPML.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$strings` | `array<int,string>` | List of strings.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Multilingual/Wpml.php](PersonioIntegration/Multilingual/Wpml.php), [line 389](PersonioIntegration/Multilingual/Wpml.php#L389-L396)

### `personio_integration_pro_taxonomy_thumbnail`

*Choose which image to use via filter.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$image_id` | `int` | The image to use.
`$taxonomy_images` | `array` | List of images.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/PersonioIntegration/Feature_Image.php](PersonioIntegration/Feature_Image.php), [line 193](PersonioIntegration/Feature_Image.php#L193-L200)

### `personio_integration_pro_taxonomy_thumbnail`

*Choose which image to use via filter.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$image_id` | `int` | The image to use.
`$taxonomy_images` | `array` | List of images.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/PersonioIntegration/Feature_Image.php](PersonioIntegration/Feature_Image.php), [line 267](PersonioIntegration/Feature_Image.php#L267-L274)

### `personio_integration_pro_application_table_column_name`

*Filter for custom content.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$return_value` | `string` | The return value.
`$item` | `\PersonioIntegrationPro\Applications\Application` | The application object.
`$column_name` | `string` | The column name.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Tables/Applications.php](PersonioIntegration/Tables/Applications.php), [line 107](PersonioIntegration/Tables/Applications.php#L107-L115)

### `personio_integration_pro_application_column_content`

*Filters the output for custom columns.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` | `string` | The html to output.
`$column_name` | `string` | The column name.
`$item` | `\PersonioIntegrationPro\Applications\Application` | The application as object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Applications.php](PersonioIntegration/Tables/Applications.php), [line 228](PersonioIntegration/Tables/Applications.php#L228-L236)

### `personio_integration_pro_application_table_column_name`

*Filter for custom content.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$return_value` | `string` | The return value.
`$item` | `\PersonioIntegrationPro\Applications\Application` | The application object.
`$name` | `string` | The column name.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Tables/Applications.php](PersonioIntegration/Tables/Applications.php), [line 250](PersonioIntegration/Tables/Applications.php#L250-L258)

### `personio_integration_pro_application_table_actions`

*Filter the possible actions in application table.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$actions` | `array<string,string>` | List of actions.
`$item` | `\PersonioIntegrationPro\Applications\Application` | The item.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Applications.php](PersonioIntegration/Tables/Applications.php), [line 299](PersonioIntegration/Tables/Applications.php#L299-L307)

### `personio_integration_pro_files_list`

*Filters the list of files for the actual position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$files` | `array<int,array<string,mixed>>` | List of files.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | Object of the requested position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Files.php](PersonioIntegration/Files.php), [line 156](PersonioIntegration/Files.php#L156-L164)

### `personio_integration_pro_run_taxonomy_translation`

*Filter whether taxonomy translations should be loaded.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` | `bool` | The return value.
`$taxonomy_name` | `string` | The taxonomy name.

**Changelog**

Version | Description
------- | -----------
`4.4.4` | Available since 4.4.4.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 149](PersonioIntegration/Taxonomies.php#L149-L158)

### `personio_integration_pro_office_additional_fields`

*Filter the additional fields for both office taxonomies.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$fields` | `array<string,array<string,mixed>>` | List of fields.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 618](PersonioIntegration/Taxonomies.php#L618-L624)

### `personio_integration_pro_restrict_taxonomy`

*Add additional restrictions via hook.*

Schema:
$tax_query[] = array(
 'taxonomy' => $taxonomy_name,
 'field'    => 'term_id',
 'terms'    => $term_id,
);

Example:
$tax_query[] = array(
 'taxonomy' => 'personioSubcompany',
 'field'    => 'term_id',
 'terms'    => array( 23, 42 ),
);

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$tax_query` | `array` | List of restricted terms.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1118](PersonioIntegration/Taxonomies.php#L1118-L1139)

### `personio_integration_pro_application_backup_dir`

*Filter the absolute path to the directory where application backups are stored.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$backup_dir` | `string` | The directory as an absolute path.

**Changelog**

Version | Description
------- | -----------
`5.1.0` | Available since 5.1.0.

Source: [app/Applications/Backup.php](Applications/Backup.php), [line 414](Applications/Backup.php#L414-L421)

### `personio_integration_pro_applications_list_limit`

*Filter the limit.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit` | `int` | The limit.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 190](Applications/Applications.php#L190-L196)

### `personio_integration_pro_use_application_history`

*Bail if this check is disabled.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return value: must be true if this check should not run.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 267](Applications/Applications.php#L267-L276)

### `personio_integration_pro_dashboard_application_name`

*Filter the output of the name.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$name` | `string` | The name.
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 430](Applications/Applications.php#L430-L437)

### `personio_integration_pro_application_file_type`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`WP_PERSONIO_INTEGRATION_PRO_FILETYPES` |  | 

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 735](Applications/Applications.php#L735-L735)

### `personio_integration_pro_application_upload_dir`

*Filter the absolute path to the directory where application files are stored.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$upload_dir` | `string` | The directory as absolute path.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1153](Applications/Applications.php#L1153-L1160)

### `personio_integration_pro_application_upload_url`

*Filter the URL to the directory where application files are stored.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$upload_dir` | `string` | The directory as URL.

**Changelog**

Version | Description
------- | -----------
`4.4.5` | Available since 4.4.5.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1172](Applications/Applications.php#L1172-L1179)

### `personio_integration_pro_fs_chmod`

*Filter the filesystem permissions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$mode` | `int` | The mode as int, e.g. 0644.

**Changelog**

Version | Description
------- | -----------
`4.4.6` | Available since 4.4.6

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1210](Applications/Applications.php#L1210-L1216)

### `personio_integration_pro_application_file_index_php`

*Change content of index.php to prevent access of application file directory.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$index_php` | `string` | The content of the file.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1223](Applications/Applications.php#L1223-L1229)

### `personio_integration_pro_application_file_htaccess`

*Change content of .htaccess to prevent access of application files.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$htaccess` | `string` | The content of the file.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1240](Applications/Applications.php#L1240-L1246)

### `personio_integration_pro_application_file_htaccess`

*Change content of web.config to prevent access of application files.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$web_config` |  | 

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1257](Applications/Applications.php#L1257-L1263)

### `personio_integration_pro_application_table_detail_dialog`

*Filter the dialog configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog_show_config` | `array` | The dialog configuration.
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 1835](Applications/Applications.php#L1835-L1843)

### `personio_integration_pro_application_test_post`

*Filter the POST-array for creating a test application.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$_POST` | `array<string,mixed>` | List of the post variables.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 2052](Applications/Applications.php#L2052-L2058)

### `personio_integration_pro_position_api_token`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`get_option('personioIntegrationAccessToken')` |  | 
`false` |  | 

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 2541](Applications/Applications.php#L2541-L2541)

### `personio_integration_pro_position_api_company_id`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`get_option('personioIntegrationCompanyId')` |  | 
`false` |  | 

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 2541](Applications/Applications.php#L2541-L2541)

### `personio_integration_pro_application_delete_intervals`

*Filter the possible delete option intervals for applications.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,string>` | The list.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 2817](Applications/Applications.php#L2817-L2823)

### `personio_integration_pro_application_delete_intervals_sql`

*Filter the possible delete option intervals for applications.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,string>` | The list.

**Changelog**

Version | Description
------- | -----------
`5.0.5` | Available since 5.0.5.

Source: [app/Applications/Applications.php](Applications/Applications.php), [line 2843](Applications/Applications.php#L2843-L2849)

### `personio_integration_pro_request_time_limit`

*Filter the request time limit for Personio API. We use default 90s (60s from Personio API + 30s puffer).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit` | `int` | The limit in seconds

**Changelog**

Version | Description
------- | -----------
`4.4.6` | Available since 4.4.6

Source: [app/Applications/Request.php](Applications/Request.php), [line 137](Applications/Request.php#L137-L143)

### `personio_integration_pro_request_limit_reached`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Applications/Request.php](Applications/Request.php), [line 166](Applications/Request.php#L166-L166)

### `personio_integration_pro_request_header`

*Filter the headers for the request.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$headers` | `array<string,string>` | List of headers.
`$instance` | `\PersonioIntegrationPro\Applications\Request` | The request-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Request.php](Applications/Request.php), [line 183](Applications/Request.php#L183-L191)

### `personio_integration_pro_request_token`

*Filter the company id the request is using.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$company_id` | `string` | The token.
`$instance` | `\PersonioIntegrationPro\Applications\Request` | The request-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Request.php](Applications/Request.php), [line 327](Applications/Request.php#L327-L335)

### `personio_integration_pro_request_token`

*Filter the token the request is using.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$token` | `string` | The token.
`$instance` | `\PersonioIntegrationPro\Applications\Request` | The request-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Request.php](Applications/Request.php), [line 346](Applications/Request.php#L346-L354)

### `personio_integration_pro_application_errors`

*Filter the possible errors before application is saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$errors` | `array<string,string>` | List of errors.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 234](Applications/Application.php#L234-L241)

### `personio_integration_pro_application_main`

*Filter the main data for each application during saving.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$main_data` |  | 
`$instance` |  | 

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 276](Applications/Application.php#L276-L284)

### `personio_integration_pro_application_meta`

*Filter the additional applicant data for each application during saving.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$meta_data` |  | 
`$instance` |  | 

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 286](Applications/Application.php#L286-L294)

### `personio_integration_pro_application_identifier`

*Filter the used application identifier.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$identifier` | `string` | The generated identifier.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 520](Applications/Application.php#L520-L527)

### `personio_integration_pro_application_get_field`

*Filter the application field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$value` | `string` | The value.
`$field_name` | `string` | The name of the field.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 730](Applications/Application.php#L730-L738)

### `personio_integration_pro_application_get_field`

*Filter the application field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$value` | `string` | The value.
`$field_name` | `string` | The name of the field.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 764](Applications/Application.php#L764-L772)

### `personio_integration_pro_application_get_field`

*Filter the application field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$value` | `string` | The value.
`$field_name` | `string` | The name of the field.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 798](Applications/Application.php#L798-L806)

### `personio_integration_pro_application_get_data`

*Filter the return of all data of a single application.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$data` | `array<string,mixed>` | The data of this application.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 835](Applications/Application.php#L835-L842)

### `personio_integration_pro_application_delete_dialog`

*Filter the delete dialog for an application.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog_config` | `array<string,mixed>` | The dialog configuration.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 1075](Applications/Application.php#L1075-L1082)

### `personio_integration_pro_application_transfer_dialog`

*Filter the transfer dialog for an application.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog_config` | `array<string,mixed>` | The dialog configuration.
`$instance` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Applications/Application.php](Applications/Application.php), [line 1112](Applications/Application.php#L1112-L1119)

### `personio_integration_pro_application_csv_export_state`

*Filter the state of the application during CSV-export.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$state` | `string` | The state.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Csv_Export.php](Applications/Csv_Export.php), [line 232](Applications/Csv_Export.php#L232-L238)

### `personio_integration_pro_application_csv_export_field_{$key}`

*Filter the value of this field during CSV-Export.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$value` | `string` | The value of the field.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | The position as an object.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/Applications/Csv_Export.php](Applications/Csv_Export.php), [line 311](Applications/Csv_Export.php#L311-L318)

### `personio_integration_pro_application_csv_export_field_{$field_name}`

*Filter the value of this field during CSV-Export.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$value` | `string` | The value of the field.
`$position_obj` | `\PersonioIntegrationPro\PersonioIntegration\Position` | The position.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.Filter the value of this field during CSV-Export..0.

Source: [app/Applications/Csv_Export.php](Applications/Csv_Export.php), [line 338](Applications/Csv_Export.php#L338-L345)

### `personio_integration_pro_application_export_csv_mappings`

*Filter the mappings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$mappings` | `array` | The mappings.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/Applications/Csv_Export.php](Applications/Csv_Export.php), [line 604](Applications/Csv_Export.php#L604-L610)

### `personio_integration_pro_application_encrypted_fields`

*Filter the list of fields which should be encrypted.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of fields.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Encrypt.php](Applications/Encrypt.php), [line 209](Applications/Encrypt.php#L209-L215)

### `personio_integration_pro_do_not_run_transfer`

*Filter to run transfer or not.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if the transfer should not run.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 136](Applications/Transfer.php#L136-L144)

### `personio_integration_pro_application_export_filter`

*Filter query for transferring applications to Personio.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$query` | `array<string,mixed>` | The filter-parameter.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 163](Applications/Transfer.php#L163-L169)

### `personio_integration_pro_prevent_application_export`

*Check if the transfer of this single application should not be run for individual reasons.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if import should be prevented.
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The object of the application.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 286](Applications/Transfer.php#L286-L294)

### `personio_integration_pro_delete_applications_after_transfer`

*Filter whether to delete the application at this moment.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$delete_now` | `bool` | Marker to delete now.
`$application_obj` | `\PersonioIntegrationPro\Applications\Application` | The application object.

**Changelog**

Version | Description
------- | -----------
`5.0.2` | Available since 5.0.2.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 531](Applications/Transfer.php#L531-L538)

### `personio_integration_pro_personio_application_document_url`

*Filter the Personio application document URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 643](Applications/Transfer.php#L643-L650)

### `personio_integration_pro_personio_http_states`

*Filter the possible HTTP States.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$http_states` | `array<int,string>` | The HTTP states.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 660](Applications/Transfer.php#L660-L667)

### `personio_integration_pro_personio_application_url`

*Filter the Personio application  URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 677](Applications/Transfer.php#L677-L684)

### `personio_integration_pro_personio_http_state`

*Filter the HTTP State.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$http_state` | `string` | The HTTP state.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Applications/Transfer.php](Applications/Transfer.php), [line 700](Applications/Transfer.php#L700-L707)

### `personio_integration_pro_application_table_hide_columns`

*Filter the list of columns which will used to hide personal data.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of columns.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Applications/Hide_Data.php](Applications/Hide_Data.php), [line 336](Applications/Hide_Data.php#L336-L342)

### `personio_integration_pro_wpbakery_widgets`

*Filter list of widgets for WpBakery.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$widget_list` | `array<int,string>` | List of widgets.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/WpBakery.php](PageBuilder/WpBakery.php), [line 124](PageBuilder/WpBakery.php#L124-L131)

### `personio_integration_pro_wpbakery_templates`

*Filter the available WpBakery templates.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$template_list` | `array<int,string>` | List of WP Bakery templates.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PageBuilder/WpBakery.php](PageBuilder/WpBakery.php), [line 189](PageBuilder/WpBakery.php#L189-L195)

### `personio_integration_pro_site_origin`

*Filter the list of supported widgets for SiteOrigin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$widgets` |  | 

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/SiteOrigin.php](PageBuilder/SiteOrigin.php), [line 132](PageBuilder/SiteOrigin.php#L132-L139)

### `personio_integration_pro_divi5_modules`

*Filter the available Divi 5 widgets.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$modules_list` | `array<int\|string,string>` | List of modules.

**Changelog**

Version | Description
------- | -----------
`5.2.0` | Available since 5.2.0

Source: [app/PageBuilder/Divi5.php](PageBuilder/Divi5.php), [line 365](PageBuilder/Divi5.php#L365-L372)

### `personio_integration_pro_bricks_elements`

*Filter the list of possible Bricks elements.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of elements.

**Changelog**

Version | Description
------- | -----------
`5.3.0` | Available since 5.3.0.

Source: [app/PageBuilder/Bricks.php](PageBuilder/Bricks.php), [line 232](PageBuilder/Bricks.php#L232-L238)

### `personio_integration_pro_avada_class`

*Filter the used CSS class for Avada widgets.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$class_name` | `string` | The CSS-classes.
`$this` | `\PersonioIntegrationPro\PageBuilder\Avada\Fusion_Element` | The widget-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/Avada/Fusion_Element.php](PageBuilder/Avada/Fusion_Element.php), [line 288](PageBuilder/Avada/Fusion_Element.php#L288-L296)

### `personio_integration_pro_beaver_widgets`

*Filter the Beaver widgets we support.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of widgets.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/Beaver.php](PageBuilder/Beaver.php), [line 103](PageBuilder/Beaver.php#L103-L109)

### `personio_integration_pro_avada_widgets`

*Filter the available Avada-widgets.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$widget_list` | `array<int\|string,string>` | List of widgets.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PageBuilder/Avada.php](PageBuilder/Avada.php), [line 410](PageBuilder/Avada.php#L410-L417)

### `personio_integration_pro_avia_widgets`

*Filter the list of supported widgets for Avia.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array()` |  | 

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PageBuilder/Avia.php](PageBuilder/Avia.php), [line 242](PageBuilder/Avia.php#L242-L249)

### `personio_integration_pro_elementor_widgets`

*Filter the list of supported widgets for Elementor.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of widgets.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/Elementor.php](PageBuilder/Elementor.php), [line 231](PageBuilder/Elementor.php#L231-L238)

### `personio_integration_pro_elementor_templates`

*Filter available Elementor-template before they are imported.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | List of templates.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/Elementor.php](PageBuilder/Elementor.php), [line 327](PageBuilder/Elementor.php#L327-L334)

### `personio_integration_pro_gutenberg_blocks`

*Filter the list of available Gutenberg blocks in the Pro plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of blocks.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 105](PageBuilder/Gutenberg.php#L105-L111)

### `personio_integration_pro_field_types`

*Filter the list of available field types.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of field type object names.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/FormTemplates/Fields.php](FormTemplates/Fields.php), [line 316](FormTemplates/Fields.php#L316-L322)

### `personio_integration_pro_form_template_title`

*Filter the form template title.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$title` | `string` | The title.
`$name` | `string` | The internal name.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/FormTemplates/Template.php](FormTemplates/Template.php), [line 146](FormTemplates/Template.php#L146-L153)

### `personio_integration_pro_form_template_description`

*Filter the description.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$description` | `string` | The description.
`$name` | `string` | The internal name.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/FormTemplates/Template.php](FormTemplates/Template.php), [line 182](FormTemplates/Template.php#L182-L189)

### `personio_integration_pro_forms_path`

*Filter the path of Personio form template files.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$path` | `string` | The path to use.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/FormTemplates/Templates.php](FormTemplates/Templates.php), [line 79](FormTemplates/Templates.php#L79-L85)

### `personio_integration_pro_form_template_config`

*Filter the template configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$template_config` | `array` | The template configuration.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/FormTemplates/Templates.php](FormTemplates/Templates.php), [line 203](FormTemplates/Templates.php#L203-L209)

### `personio_integration_pro_formular_templates`

*Filter the list of available form templates.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | List of templates.

Source: [app/FormTemplates/Templates.php](FormTemplates/Templates.php), [line 253](FormTemplates/Templates.php#L253-L258)

### `personio_integration_pro_form_template_label_options`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`''` |  | 
`$this` |  | 
`$hide_options` |  | 

Source: [app/FormTemplates/Fields/Select.php](FormTemplates/Fields/Select.php), [line 365](FormTemplates/Fields/Select.php#L365-L365)

### `personio_integration_pro_form_field_additional_settings`

*Filter the possible additional settings for a field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `array<int,string>` | The fields.
`$instance` | `\PersonioIntegrationPro\FormTemplates\Field_Base` | The field object.

**Changelog**

Version | Description
------- | -----------
`5.2.0` | Available since 5.2.0.

Source: [app/FormTemplates/Field_Base.php](FormTemplates/Field_Base.php), [line 485](FormTemplates/Field_Base.php#L485-L492)

### `personio_integration_pro_form_template_label_options`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`''` |  | 
`$this` |  | 
`$hide_options` |  | 

Source: [app/FormTemplates/Field_Base.php](FormTemplates/Field_Base.php), [line 533](FormTemplates/Field_Base.php#L533-L533)

### `personio_integration_pro_form_field_title`

*Filter the field title.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$title` | `string` | The title.
`$instance` | `\PersonioIntegrationPro\FormTemplates\Field_Base` | The field object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/FormTemplates/Field_Base.php](FormTemplates/Field_Base.php), [line 662](FormTemplates/Field_Base.php#L662-L669)

### `personio_integration_pro_form_field_config`

*Filter the field config.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$config` |  | 
`$instance` |  | 

**Changelog**

Version | Description
------- | -----------
`5.2.0` | Available since 5.2.0.

Source: [app/FormTemplates/Field_Base.php](FormTemplates/Field_Base.php), [line 967](FormTemplates/Field_Base.php#L967-L972)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

