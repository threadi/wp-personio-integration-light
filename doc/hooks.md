# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### `personio_integration_uninstaller`

*Run additional tasks for uninstallation via WP CLI.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$options` | `array` | Options used to call this command.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Cli.php](Plugin/Cli.php), [line 97](Plugin/Cli.php#L97-L105)

### `personio_integration_installer`

*Run additional tasks for installation via WP CLI.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Cli.php](Plugin/Cli.php), [line 110](Plugin/Cli.php#L110-L115)

### `personio_integration_light_setup_completed`

*Run additional tasks if the setup is marked as completed.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 514](Plugin/Setup.php#L514-L519)

### `personio_integration_help_page`

*Add additional boxes for the help page.*


Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 740](Plugin/Admin/Admin.php#L740-L743)

### `personio_integration_help_tours`

*Add additional helper tasks via hook.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 828](Plugin/Admin/Admin.php#L828-L833)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Details.php](PersonioIntegration/Widgets/Details.php), [line 230](PersonioIntegration/Widgets/Details.php#L230-L236)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Filter_List.php](PersonioIntegration/Widgets/Filter_List.php), [line 107](PersonioIntegration/Widgets/Filter_List.php#L107-L113)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Archive.php](PersonioIntegration/Widgets/Archive.php), [line 230](PersonioIntegration/Widgets/Archive.php#L230-L236)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Archive.php](PersonioIntegration/Widgets/Archive.php), [line 259](PersonioIntegration/Widgets/Archive.php#L259-L265)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Single.php](PersonioIntegration/Widgets/Single.php), [line 170](PersonioIntegration/Widgets/Single.php#L170-L176)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 196](PersonioIntegration/Widgets/Application_Button.php#L196-L202)

### `personio_integration_get_template_before`

*Run custom actions before the output of the archive listing.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Widgets/Filter_Select.php](PersonioIntegration/Widgets/Filter_Select.php), [line 106](PersonioIntegration/Widgets/Filter_Select.php#L106-L112)

### `personio_integration_import_starting`

*Run custom actions before the import of positions is running.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports_Base` | The import object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 157](PersonioIntegration/Imports/Xml.php#L157-L163)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 237](PersonioIntegration/Imports/Xml.php#L237-L242)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions but after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 248](PersonioIntegration/Imports/Xml.php#L248-L253)

### `personio_integration_light_import_deleted_position`

*Run tasks if a position has been deleted.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_id` | `string` | The Personio ID of the position which has been deleted.
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position which has been deleted. Hint: do not use any DB-request via this object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 289](PersonioIntegration/Imports/Xml.php#L289-L296)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 309](PersonioIntegration/Imports/Xml.php#L309-L314)

### `personio_integration_import_finished`

*Run custom actions after finished import of positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$step` | `int` | The step to add.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 329](PersonioIntegration/Imports/Xml.php#L329-L336)

### `personio_integration_import_starting`

*Run custom actions before the import of positions is running.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports_Base` | The import object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 145](PersonioIntegration/Imports/Api.php#L145-L151)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 411](PersonioIntegration/Imports/Api.php#L411-L416)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions but after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 422](PersonioIntegration/Imports/Api.php#L422-L427)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 474](PersonioIntegration/Imports/Api.php#L474-L479)

### `personio_integration_import_finished`

*Run custom actions after finished import of positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$step` | `int` | The step to add.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 490](PersonioIntegration/Imports/Api.php#L490-L497)

### `personio_integration_import_of_url_starting`

*Run action on the start of the import from a single Personio URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url` | The import-object.

**Changelog**

Version | Description
------- | -----------
`3.0.5` | Available since 3.0.5

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 180](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L180-L186)

### `personio_integration_import_timestamp_no_changed`

*Run actions for this case.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url` | The import-object.
`$last_modified_timestamp` | `int` | The timestamp.

**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since 3.0.4.

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 254](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L254-L262)

### `personio_integration_import_content_not_changed`

*Run actions if positions in Personio did not change.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url` | The import-object.
`$md5hash` | `string` | The md5-hash from the content of "body".

**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since 3.0.4.

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 291](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L291-L299)

### `personio_integration_import_of_url_ended`

*Execute action at the end of importing a single URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url` | The import-object.

**Changelog**

Version | Description
------- | -----------
`3.0.5` | Available since 3.0.5

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 401](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L401-L407)

### `personio_integration_import_single_position_save`

*Run hook for individual settings after the position has been saved (inserted or updated).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 234](PersonioIntegration/Position.php#L234-L241)

### `personio_integration_import_single_position_save_finished`

*Run hook for individual settings after all settings for the position have been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 294](PersonioIntegration/Position.php#L294-L301)

### `personio_integration_light_import_error`

*Run additional tasks for processing errors during import of positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this->get_errors()` |  | 

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 183](PersonioIntegration/Imports_Base.php#L183-L189)

### `personio_integration_import_count`

*Add actual count on third party components (like Setup).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$count` | `int` | The value to add.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 216](PersonioIntegration/Imports_Base.php#L216-L223)

### `personio_integration_import_max_count`

*Add max count on third party components (like Setup).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$max_count` | `int` | The max count to set.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 249](PersonioIntegration/Imports_Base.php#L249-L256)

### `personio_integration_light_extension_table_buttons`

*Add additional buttons to the extension table.*


**Changelog**

Version | Description
------- | -----------
`5.1.0` | Available since 5.1.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 451](PersonioIntegration/Tables/Extensions.php#L451-L456)

### `personio_integration_import_max_count`

*Add max count on third party components (like Setup).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_count` | `int` | 

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 166](PersonioIntegration/Availability.php#L166-L173)

### `personio_integration_import_count`

*Add actual count on third party components (like Setup).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$count` | `int` | The value to add.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 190](PersonioIntegration/Availability.php#L190-L197)

### `personio_integration_light_extension_initialized`

*Run additional action after extension as been initialized.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$extension_obj` | `\PersonioIntegrationLight\PersonioIntegration\Extensions_Base` | The extension object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 89](PersonioIntegration/Extensions.php#L89-L95)

### `personio_integration_light_edit_position_box_personio_id`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as an object.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1023](PersonioIntegration/PostTypes/PersonioPosition.php#L1023-L1029)

### `personio_integration_light_edit_position_box_title`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as an object.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1045](PersonioIntegration/PostTypes/PersonioPosition.php#L1045-L1051)

### `personio_integration_light_edit_position_box_content`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post` | `\PersonioIntegrationLight\PersonioIntegration\PostTypes\WP_post` | The post as an object.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1095](PersonioIntegration/PostTypes/PersonioPosition.php#L1095-L1101)

### `personio_integration_light_edit_position_box_taxonomy`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as an object.
`$attr` | `array` | Attributes used for this meta-box.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1196](PersonioIntegration/PostTypes/PersonioPosition.php#L1196-L1203)

### `personio_integration_light_dashboard_widget_pre_query`

*Run additional tasks before the positions have been loaded.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1466](PersonioIntegration/PostTypes/PersonioPosition.php#L1466-L1471)

### `personio_integration_light_dashboard_widget_post_query`

*Run additional tasks after the positions have been loaded.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1482](PersonioIntegration/PostTypes/PersonioPosition.php#L1482-L1487)

### `personio_integration_deletion_starting`

*Run custom actions before deleting of all positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1563](PersonioIntegration/PostTypes/PersonioPosition.php#L1563-L1568)

### `personio_integration_deletion_ended`

*Run custom actions after deletion of all positions has been done.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1634](PersonioIntegration/PostTypes/PersonioPosition.php#L1634-L1639)

### `personio_integration_light_endpoint_task`

*Run the individual task.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$params` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1673](PersonioIntegration/PostTypes/PersonioPosition.php#L1673-L1676)

## Filters

### `personio_integration_admin_settings_pages`

*Allow our own capability to save settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$settings_pages` |  | 

Source: [app/Plugin/Roles.php](Plugin/Roles.php), [line 114](Plugin/Roles.php#L114-L126)

### `personio_integration_supported_languages`

*Return the supported languages.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$languages` | `string[]` | List of supported languages.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 94](Plugin/Languages.php#L94-L101)

### `personio_integration_fallback_language`

*Filter the fallback language.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$fallback_language` |  | 

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 185](Plugin/Languages.php#L185-L192)

### `personio_integration_current_language`

*Filter the resulting language.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wp_language` | `string` | The language-name (e.g., "en").

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 241](Plugin/Languages.php#L241-L248)

### `personio_integration_language_mappings`

*Filter the possible mapping languages.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$mapping_languages` | `array` | List of language mappings.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 261](Plugin/Languages.php#L261-L268)

### `personio_integration_language_mappings`

*Filter the possible mapping languages.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$mapping_languages` | `array` | List of language mappings.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 289](Plugin/Languages.php#L289-L296)

### `personio_integration_light_schedule_interval`

*Filter the interval to a single schedule.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$interval` | `string` | The interval.
`$instance` | `\PersonioIntegrationLight\Plugin\Schedules_Base` | The schedule-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 85](Plugin/Schedules_Base.php#L85-L92)

### `personio_integration_schedule_enabling`

*Filter whether to activate this schedule.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if this object should NOT be enabled.
`$instance` | `\PersonioIntegrationLight\Plugin\Schedules_Base` | Actual object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 230](Plugin/Schedules_Base.php#L230-L240)

### `personio_integration_templates_archive`

*Filter the list of available templates for archive listings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<string,string>` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 113](Plugin/Templates.php#L113-L120)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 154](Plugin/Templates.php#L154-L154)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 188](Plugin/Templates.php#L188-L188)

### `personio_integration_admin_template_labels`

*Filter the list of available templates for content.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<string,string>` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 212](Plugin/Templates.php#L212-L219)

### `personio_integration_templates_jobdescription`

*Filter the list of available templates for job description.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<string,string>` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 248](Plugin/Templates.php#L248-L255)

### `personio_integration_templates_excerpts`

*Filter the list of available templates for excerpts.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<string,string>` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 270](Plugin/Templates.php#L270-L277)

### `personio_integration_load_single_template`

*Decide whether to use our own template (false) or not (true).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true if our own single template should not be used.
`$single_template` | `string` | The single template, which will be used instead.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 403](Plugin/Templates.php#L403-L412)

### `personio_integration_load_archive_template`

*Decide whether to use our own archive template (false) or not (true).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true if our own archive template should not be used.
`$archive_template` | `string` | The archive template, which will be used instead.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 449](Plugin/Templates.php#L449-L458)

### `personio_integration_show_content`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 502](Plugin/Templates.php#L502-L502)

### `personio_integration_title_size`

*Filter the heading size.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$heading_size` | `string` | The heading size.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the requested position.
`$attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 573](Plugin/Templates.php#L573-L582)

### `personio_integration_light_filter_taxonomy_to_use`

*Filter whether we found a taxonomy to use for the filter.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomy_to_use` | `string` | The taxonomy to use for the filter.
`$filter` | `string` | The filter slug.
`$attributes` | `array` | List of attributes for the filter.

**Changelog**

Version | Description
------- | -----------
`5.1.0` | Available since 5.1.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 732](Plugin/Templates.php#L732-L740)

### `personio_integration_light_filter_terms`

*Filter the terms to use in filters.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$terms` | `array<int,\WP_Term>\|\WP_Error` | List of terms.
`$taxonomy_to_use` | `string` | The taxonomy of these terms to use for the filter.

**Changelog**

Version | Description
------- | -----------
`4.2.4` | Available since 4.2.4.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 755](Plugin/Templates.php#L755-L762)

### `personio_integration_add_kses_filter`

*Prevent filtering the HTML code via kses.*

We need this only for the filter-form.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | False if the filter should be run.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 858](Plugin/Templates.php#L858-L867)

### `personio_integration_light_position_classes`

*Filter the class list to a single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_classes` | `array<int,string>` | List of classes.
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | Position as an object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 977](Plugin/Templates.php#L977-L984)

### `personio_integration_light_position_filter_classes`

*Filter the class list for the filter.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_classes` | `array<int,string>` | List of classes.

**Changelog**

Version | Description
------- | -----------
`5.2.0` | Available since 5.2.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 1002](Plugin/Templates.php#L1002-L1008)

### `personio_integration_light_term_classes`

*Filter the class list of a term.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_classes` | `array` | List of classes.
`$term` | `\WP_Term` | The term object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 1036](Plugin/Templates.php#L1036-L1043)

### `personio_integration_light_import_error_support_hint`

*Filter the support part of an email.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$support_part` | `string` | The text to use.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Plugin/Email_Base.php](Plugin/Email_Base.php), [line 251](Plugin/Email_Base.php#L251-L257)

### `personio_integration_light_email_headers`

*Filter the email header.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$headers` | `array<int,string>` | List of headers.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Email_Base.php](Plugin/Email_Base.php), [line 350](Plugin/Email_Base.php#L350-L356)

### `personio_integration_light_intervals`

*Filter the list of possible intervals.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of our interval objects.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Intervals.php](Plugin/Intervals.php), [line 71](Plugin/Intervals.php#L71-L77)

### `personio_integration_light_emails`

*Filter the list of possible email objects.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$trigger` | `array<int,string>` | List of Email trigger objects.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Emails.php](Plugin/Emails.php), [line 131](Plugin/Emails.php#L131-L137)

### `personio_integration_light_setup_is_completed`

*Filter the setup complete marker.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$completed` | `bool` | True if setup has been completed.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 131](Plugin/Setup.php#L131-L137)

### `personio_integration_setup`

*Filter the configured setup for this plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$setup` | `array<int,array<string,mixed>>` | The setup-configuration.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 207](Plugin/Setup.php#L207-L214)

### `personio_integration_light_transient_title`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`Helper::get_plugin_name()` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 224](Plugin/Setup.php#L224-L224)

### `personio_integration_setup_config`

*Filter the setup configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$config` | `array<string,array<int,mixed>\|string>` | List of configuration for the setup.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 312](Plugin/Setup.php#L312-L318)

### `personio_integration_setup_process_completed_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$completed_text` |  | 
`$config_name` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 490](Plugin/Setup.php#L490-L490)

### `personio_integration_light_plugin_row_meta`

*Filter the links in row meta of our plugin in the plugin list.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$row_meta` | `array<string,string>` | List of links.

**Changelog**

Version | Description
------- | -----------
`4.2.4` | Available since 4.2.4.

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 211](Plugin/Init.php#L211-L217)

### `personio_integration_objects_with_db_tables`

*Add additional objects for this plugin, which use custom tables.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$objects` | `array<int,string>` | List of objects.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 333](Plugin/Init.php#L333-L339)

### `personio_integration_objects_with_db_tables`

*Add additional objects for this plugin, which use custom tables.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$objects` | `array<int,string>` | List of objects.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 365](Plugin/Init.php#L365-L371)

### `personio_integration_run_compatibility_checks`

*Filter whether the compatibility-checks should be run (false) or not (true)*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to prevent compatibility-checks.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Compatibilities.php](Plugin/Compatibilities.php), [line 67](Plugin/Compatibilities.php#L67-L76)

### `personio_integration_compatibility_checks`

*Filter the list of compatibilities.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `string[]` | List of compatibility-checks.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Compatibilities.php](Plugin/Compatibilities.php), [line 164](Plugin/Compatibilities.php#L164-L171)

### `personio_integration_light_hide_intro`

*Hide intro via hook.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to hide the intro.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Intro.php](Plugin/Intro.php), [line 77](Plugin/Intro.php#L77-L85)

### `personio_integration_dashboard_widgets`

*Filter the dashboard-widgets used by this plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dashboard_widgets` | `array<string,array<string,mixed>>` | List of widgets.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Dashboard.php](Plugin/Admin/Dashboard.php), [line 84](Plugin/Admin/Dashboard.php#L84-L90)

### `personio_integration_light_show_help`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$allowed` |  | 
`$screen` |  | 

Source: [app/Plugin/Admin/Help_System.php](Plugin/Admin/Help_System.php), [line 78](Plugin/Admin/Help_System.php#L78-L78)

### `personio_integration_light_help_sidebar_content`

*Filter the sidebar content.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$sidebar_content` | `string` | The content.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Admin/Help_System.php](Plugin/Admin/Help_System.php), [line 110](Plugin/Admin/Help_System.php#L110-L116)

### `personio_integration_light_help_tabs`

*Filter the list of help tabs with its contents.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,mixed>` | List of help tabs.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Admin/Help_System.php](Plugin/Admin/Help_System.php), [line 130](Plugin/Admin/Help_System.php#L130-L136)

### `personio_integration_hide_pro_hints`

*Hide hint for Pro-plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the hint.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Admin/Help_System.php](Plugin/Admin/Help_System.php), [line 149](Plugin/Admin/Help_System.php#L149-L157)

### `personio_integration_site_health_endpoints`

*Filter the endpoints for Site Health this plugin is using.*

Hint: these are just arrays that define the endpoints.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,array<string,mixed>>` | List of endpoints.

Source: [app/Plugin/Admin/Site_Health.php](Plugin/Admin/Site_Health.php), [line 83](Plugin/Admin/Site_Health.php#L83-L90)

### `personio_integration_light_do_not_encrypt`

*Do not encrypt a given value if requested.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to prevent decrypting.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Admin/SettingsSavings/SaveAsCryptValue.php](Plugin/Admin/SettingsSavings/SaveAsCryptValue.php), [line 33](Plugin/Admin/SettingsSavings/SaveAsCryptValue.php#L33-L42)

### `personio_integration_light_do_not_decrypt`

*Do not decrypt a given value if requested.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to prevent decrypting.
`$value` | `string` | The requested value.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Admin/SettingsRead/GetDecryptValue.php](Plugin/Admin/SettingsRead/GetDecryptValue.php), [line 33](Plugin/Admin/SettingsRead/GetDecryptValue.php#L33-L43)

### `personio_integration_pro_hint_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$text` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 262](Plugin/Admin/Admin.php#L262-L262)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 473](Plugin/Admin/Admin.php#L473-L473)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 537](Plugin/Admin/Admin.php#L537-L537)

### `personio_integration_light_show_admin_bar_menu`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 577](Plugin/Admin/Admin.php#L577-L577)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 689](Plugin/Admin/Admin.php#L689-L689)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 808](Plugin/Admin/Admin.php#L808-L808)

### `personio_integration_log_export_filename`

*Filter the filename for CSV-download.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$filename` | `string` | The generated filename for CSV-download.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 884](Plugin/Admin/Admin.php#L884-L891)

### `personio_integration_hide_pro_hints`

*Hide the additional buttons for reviews or pro-version.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the buttons.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 1030](Plugin/Admin/Admin.php#L1030-L1037)

### `personio_integration_hide_pro_hints`

*Hide hint for Pro-plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the hint.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/License.php](Plugin/License.php), [line 68](Plugin/License.php#L68-L76)

### `personio_integration_light_url_after_pro_installation`

*Filter the referer URL after Personio Integration Pro has been installed and activated.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL to use as the forward target.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 608](Plugin/License.php#L608-L614)

### `personio_integration_light_download_pro_url`

*Filter the download URL during the installation of Personio Integration Pro.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$download_url` | `string` | The download URL.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 673](Plugin/License.php#L673-L679)

### `personio_integration_light_url_after_pro_installation`

*Filter the referer URL after Personio Integration Pro has been installed and activated.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL to use as the forward target.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 701](Plugin/License.php#L701-L707)

### `personio_integration_schedule_our_events`

*Filter the list of our own events, e.g., to check if all, which are enabled in setting are active.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$our_events` | `array<string,array<string,mixed>>` | List of our own events in WP-cron.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 140](Plugin/Schedules.php#L140-L147)

### `personio_integration_disable_cron_check`

*Disable the additional cron check.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if the check should be disabled.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 165](Plugin/Schedules.php#L165-L173)

### `personio_integration_schedules`

*Add custom schedule-objects to use.*

They must be objects based on PersonioIntegrationLight\Plugin\Schedules_Base.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list_of_schedules` | `string[]` | List of additional schedules.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 307](Plugin/Schedules.php#L307-L316)

### `personio_integration_personio_urls`

*Filter the list of Personio URLs used to import positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_urls` | `string[]` | List of Personio URLs.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Personio_Accounts.php](PersonioIntegration/Personio_Accounts.php), [line 159](PersonioIntegration/Personio_Accounts.php#L159-L166)

### `personio_integration_show_term_list`

*Filter whether to show terms of single taxonomy as list or not.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to show the list.
`$terms` | `\WP_Term[]\|false` | List of terms.

**Changelog**

Version | Description
------- | -----------
`3.0.8` | Available since 3.0.8.

Source: [app/PersonioIntegration/Widgets/Details.php](PersonioIntegration/Widgets/Details.php), [line 175](PersonioIntegration/Widgets/Details.php#L175-L183)

### `personio_integration_pagination`

*Set pagination settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$pagination` | `bool` | The pagination setting (true to disable it).

**Changelog**

Version | Description
------- | -----------
`1.2.0` | Available since 1.2.0.

Source: [app/PersonioIntegration/Widgets/Archive.php](PersonioIntegration/Widgets/Archive.php), [line 109](PersonioIntegration/Widgets/Archive.php#L109-L118)

### `personio_integration_limit`

*Change the limit for positions in frontend.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit_by_wp` | `int` | The limit define by wp which will be used for the list.
`$limit_by_list` | `int` | The limit explicitly set for this listing.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/Widgets/Archive.php](PersonioIntegration/Widgets/Archive.php), [line 206](PersonioIntegration/Widgets/Archive.php#L206-L214)

### `personio_integration_get_template`

*Change settings for output.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | The attributes used for this output.
`$attribute_defaults` | `array` | The default attributes.

**Changelog**

Version | Description
------- | -----------
`1.2.0` | Available since 1.2.0.

Source: [app/PersonioIntegration/Widgets/Archive.php](PersonioIntegration/Widgets/Archive.php), [line 220](PersonioIntegration/Widgets/Archive.php#L220-L228)

### `personio_integration_light_default_css_classes`

*Filter the default classes for each output of positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_classes` | `array<int,string>` | List of classes.

**Changelog**

Version | Description
------- | -----------
`4.2.0` | Available since 4.2.0

Source: [app/PersonioIntegration/Widgets/Archive.php](PersonioIntegration/Widgets/Archive.php), [line 302](PersonioIntegration/Widgets/Archive.php#L302-L308)

### `personio_integration_get_template`

*Change settings for output.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_attributes` | `array` | The attributes used for this output.
`$default_attributes` | `array` | The default attributes.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/Widgets/Single.php](PersonioIntegration/Widgets/Single.php), [line 153](PersonioIntegration/Widgets/Single.php#L153-L161)

### `personio_integration_position_attribute_defaults`

*Filter the attribute-defaults.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$default_values` | `array<string,mixed>` | The list of default values for each attribute used to display positions in the frontend.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Widgets/Single.php](PersonioIntegration/Widgets/Single.php), [line 234](PersonioIntegration/Widgets/Single.php#L234-L241)

### `personio_integration_hide_button`

*Bail if no button should be visible.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to prevent button-output.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 106](PersonioIntegration/Widgets/Application_Button.php#L106-L114)

### `personio_integration_light_position_application_link`

*Filter the application URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$link` | `string` | The URL.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as object.
`$attributes` | `array<string,mixed>` | List of attributes used for the output.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 170](PersonioIntegration/Widgets/Application_Button.php#L170-L178)

### `personio_integration_back_to_list_target_attribute`

*Set and filter the value for the target-attribute.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$target` | `string` | The target value.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as an object.
`$attributes` | `array<string,mixed>` | List of attributes used for the output.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 181](PersonioIntegration/Widgets/Application_Button.php#L181-L190)

### `personio_integration_light_application_button_output`

*Filter the output of the application button.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$content` | `string` | The content to output.
`$attributes` | `array<string,mixed>` | List of used attributes.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 210](PersonioIntegration/Widgets/Application_Button.php#L210-L218)

### `personio_integration_supported_themes`

*Filter the list of supported themes.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$theme_list` | `array<int,string>` | The list of supported themes.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Themes.php](PersonioIntegration/Themes.php), [line 128](PersonioIntegration/Themes.php#L128-L134)

### `personio_integration_light_import_bail_before_cleanup`

*Cancel the import before clean up the database.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to prevent the cleanup tasks.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports_Base` | The import object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 207](PersonioIntegration/Imports/Xml.php#L207-L216)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 268](PersonioIntegration/Imports/Xml.php#L268-L268)

### `personio_integration_import_single_position_xml`

*Change the XML-object before saving the position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_object` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.
`$xml_object` | `\SimpleXMLElement` | The XML-object with the data from Personio.
`$personio_url` | `string` | The used Personio-URL.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 419](PersonioIntegration/Imports/Xml.php#L419-L428)

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$object` |  | 
`$language_name` |  | 
`$personio_obj` |  | 
`$imports_obj` |  | 

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 322](PersonioIntegration/Imports/Api.php#L322-L322)

### `personio_integration_import_single_position_api`

*Change the position-object before saving it.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.
`$data` | `array` | The data from Personio.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 352](PersonioIntegration/Imports/Api.php#L352-L360)

### `personio_integration_light_import_bail_before_cleanup`

*Cancel the import before cleanup the database.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to prevent the cleanup tasks.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 385](PersonioIntegration/Imports/Api.php#L385-L393)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 442](PersonioIntegration/Imports/Api.php#L442-L442)

### `personio_integration_import_url`

*Change the URL via hook.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.
`$language_name` | `string` | Name of the language.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 188](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L188-L196)

### `personio_integration_light_import_of_url_starting`

*Set marker to check for timestamp and md5-hash-compare.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$check_for_changes` | `bool` | False to prevent this check.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 199](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L199-L207)

### `personio_integration_import_header_status`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$http_status` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 243](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L243-L243)

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$xml_object` |  | 
`$language_name` |  | 
`$personio_obj` |  | 
`$imports_obj` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 356](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L356-L356)

### `personio_integration_import_sleep_positions_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`20` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 375](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L375-L375)

### `personio_integration_light_get_{$taxonomy_name}_translate_taxonomy`

*Filter the taxonomy array just before it is registered.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomy_array` | `array<string,mixed>` | List of settings for the taxonomy.
`$taxonomy_name` | `string` | Name of the taxonomy.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 148](PersonioIntegration/Taxonomies.php#L148-L156)

### `personio_integration_taxonomies`

*Filter all taxonomies and return the resulting list as an array.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomies` | `array` | The list of taxonomies.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 276](PersonioIntegration/Taxonomies.php#L276-L283)

### `personio_integration_filter_taxonomy_label`

*Filter the taxonomy label.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$label` | `array<string,string>` | The label.
`$taxonomy` | `string` | The taxonomy.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 486](PersonioIntegration/Taxonomies.php#L486-L494)

### `personio_integration_cat_labels`

*Change category list.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array<string,string>` | The list of labels (internal name/slug => label).

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 515](PersonioIntegration/Taxonomies.php#L515-L522)

### `personio_integration_settings_get_list`

*Filter the taxonomy labels for template filter in the listing before adding them to the settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array<string,string>` | List of labels.
`$taxonomies` | `array<string,string>` | List of taxonomies.

**Changelog**

Version | Description
------- | -----------
`2.3.0` | Available since 2.3.0.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1056](PersonioIntegration/Taxonomies.php#L1056-L1064)

### `personio_integration_light_rest_taxonomies`

*Filter the resulting list of taxonomies for REST API response.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomies` | `array<int,array<string,mixed>>` | List of taxonomies.
`$data` | `\WP_REST_Request` | The REST API request.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1159](PersonioIntegration/Taxonomies.php#L1159-L1166)

### `personio_integration_check_requirement_to_import_single_position`

*Filter if the position should be imported.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return false to import this position.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 113](PersonioIntegration/Position.php#L113-L122)

### `personio_integration_light_import_single_query_for_existing_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$params` |  | 
`$instance` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 145](PersonioIntegration/Position.php#L145-L145)

### `personio_integration_import_single_position_filter_existing`

*Filter the post_id.*

Could return false to force a non-existing position.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` | `int` | The post_id to check.
`$lang` | `string` | The used language.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 148](PersonioIntegration/Position.php#L148-L158)

### `personio_integration_prevent_import_of_single_position`

*Filter if position should be imported after we get an ID.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return false to import this position.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 180](PersonioIntegration/Position.php#L180-L190)

### `personio_integration_import_single_position_filter_before_saving`

*Filter the prepared position-data just before it's saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$array` | `array<string,mixed>` | The position data as an array.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object we are in.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 210](PersonioIntegration/Position.php#L210-L218)

### `personio_integration_light_position_title`

*Filter the title of the position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$title` | `string` | The title.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 437](PersonioIntegration/Position.php#L437-L445)

### `personio_integration_single_url`

*Filter the public URL from a single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 524](PersonioIntegration/Position.php#L524-L532)

### `personio_integration_get_personio_url`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` |  | 
`$instance` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 779](PersonioIntegration/Position.php#L779-L779)

### `personio_integration_theme_css`

*Filter the used CSS file for this theme.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_file` | `string` | Name of the CSS file located in /css in this plugin.
`$theme_name` | `string` | Internal name of the used theme (slug of the theme).

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Themes_Base.php](PersonioIntegration/Themes_Base.php), [line 93](PersonioIntegration/Themes_Base.php#L93-L101)

### `personio_integration_theme_wrapper_classes`

*Filter the used CSS wrapper classes for this theme.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wrapper_classes` | `string` | Name of the wrapper-classes.
`$theme_name` | `string` | Internal name of the used theme (slug of the theme).

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Themes_Base.php](PersonioIntegration/Themes_Base.php), [line 113](PersonioIntegration/Themes_Base.php#L113-L121)

### `personio_integration_extensions_table_columns`

*Filter the possible columns for the extension table.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$columns` | `array<string,string>` | List of columns.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 39](PersonioIntegration/Tables/Extensions.php#L39-L46)

### `personio_integration_extensions_table_extensions`

*Filter the list of extensions in this table.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$extensions` | `\PersonioIntegrationLight\PersonioIntegration\Extensions_Base[]` | List of unsorted extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 73](PersonioIntegration/Tables/Extensions.php#L73-L80)

### `personio_integration_extension_categories`

*Filter the extension categories.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$categories` | `array<string,string>` | List of categories.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 193](PersonioIntegration/Tables/Extensions.php#L193-L199)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 232](PersonioIntegration/Tables/Extensions.php#L232-L232)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 270](PersonioIntegration/Tables/Extensions.php#L270-L270)

### `personio_integration_light_extension_all_url`

*Filter the main url for "all".*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 330](PersonioIntegration/Tables/Extensions.php#L330-L336)

### `personio_integration_light_extension_table_views`

*Filter the list of possible views in extension table.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,string>` | List of views.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 358](PersonioIntegration/Tables/Extensions.php#L358-L364)

### `personio_integration_light_position_availability_yes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 256](PersonioIntegration/Availability.php#L256-L256)

### `personio_integration_light_position_availability_no`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 293](PersonioIntegration/Availability.php#L293-L293)

### `personio_integration_admin_show_pro_hint`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$pro_hint` |  | 
`$true` |  | 

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 176](PersonioIntegration/Imports.php#L176-L176)

### `personio_integration_admin_show_pro_hint`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$pro_hint` |  | 
`$true` |  | 

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 179](PersonioIntegration/Imports.php#L179-L179)

### `personio_integration_light_import_extensions`

*Filter the import extensions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$import_extensions` | `array<int,string>` | List of import extensions.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 216](PersonioIntegration/Imports.php#L216-L222)

### `personio_integration_get_position_obj`

*Filter the requested position object.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$postion_obj` |  | 
`$language_code` | `string` | The requested language.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 87](PersonioIntegration/Positions.php#L87-L95)

### `personio_integration_positions_query`

*Filter the custom query for positions just before it is used.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$query` | `array` | The configured query.
`$parameter_to_add` | `array` | The parameter to filter for.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 193](PersonioIntegration/Positions.php#L193-L201)

### `personio_integration_positions_loop_id`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` |  | 

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 235](PersonioIntegration/Positions.php#L235-L235)

### `personio_integration_positions_resulting_list`

*Filter the resulting and sorted list of position objects.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$resulting_position_list` | `array` | List of resulting position objects.
`$limit` | `int` | The limitation of the list.
`$parameter_to_add` | `array` | The list of parameters used to get this list.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 258](PersonioIntegration/Positions.php#L258-L267)

### `personio_integration_light_request_time_limit`

*Filter the request time limit for Personio API. We use default 90s (60s from Personio API + 30s puffer).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$time_limit` | `int` | The limit in seconds

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Api_Request.php](PersonioIntegration/Api_Request.php), [line 129](PersonioIntegration/Api_Request.php#L129-L135)

### `personio_integration_light_request_header`

*Filter the headers for the request.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$headers` | `array<string,string>` | List of headers.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Api_Request` | The Api_Request-object.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Api_Request.php](PersonioIntegration/Api_Request.php), [line 163](PersonioIntegration/Api_Request.php#L163-L171)

### `personio_integration_extend_position_object`

*Filter the possible extensions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `(string\|\PersonioIntegrationLight\PersonioIntegration\Extensions_Base)[]` | List of extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 197](PersonioIntegration/Extensions.php#L197-L204)

### `personio_integration_light_extension_state_changed_dialog`

*Filter the success dialog if state of extension has been changed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` |  | 
`$obj` |  | 

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 307](PersonioIntegration/Extensions.php#L307-L310)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 609](PersonioIntegration/Extensions.php#L609-L609)

### `personio_integration_rest_templates_details`

*Filter the available details-templates for REST API.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<int,array<string,mixed>>` | The templates.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 385](PersonioIntegration/PostTypes/PersonioPosition.php#L385-L392)

### `personio_integration_rest_templates_jobdescription`

*Filter the available jobdescription-templates for REST API.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<int,array<string,mixed>>` | The templates.

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 415](PersonioIntegration/PostTypes/PersonioPosition.php#L415-L422)

### `personio_integration_rest_templates_archive`

*Filter the available archive-templates for REST API.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<int,array<string,mixed>>` | The templates.

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 445](PersonioIntegration/PostTypes/PersonioPosition.php#L445-L452)

### `personio_integration_light_term_translate_hint`

*Adjust the dialog for a hint for the possibility to translate terms.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` | `array<string,mixed>` | The dialog to change.
`$taxonomy_name` | `string` | The taxonomy name.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 876](PersonioIntegration/PostTypes/PersonioPosition.php#L876-L883)

### `personio_integration_position_prevent_meta_box_remove`

*Prevent removing of all meta-boxes in cpt edit view.*

Caution: the boxes will not be able to be saved.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to prevent removing of each meta-box.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 928](PersonioIntegration/PostTypes/PersonioPosition.php#L928-L939)

### `personio_integration_do_not_hide_meta_box`

*Decide if we should not remove the support for this meta-box.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to ignore this box.
`$box` | `array` | Settings of the meta-box.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 954](PersonioIntegration/PostTypes/PersonioPosition.php#L954-L964)

### `personio_integration_position_attribute_defaults`

*Filter the attribute-defaults.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$default_values` | `array<string,mixed>` | The list of default values for each attribute used to display positions in frontend.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1297](PersonioIntegration/PostTypes/PersonioPosition.php#L1297-L1304)

### `personio_integration_sitemap_entry`

*Filter the data for the sitemap-entry for single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$entry` | `array<string,mixed>` | List of data for the sitemap.xml of this single position.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as an object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1405](PersonioIntegration/PostTypes/PersonioPosition.php#L1405-L1413)

### `personio_integration_import_dialog`

*Filter the initial import dialog.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` | `array` | The dialog to send.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1811](PersonioIntegration/PostTypes/PersonioPosition.php#L1811-L1818)

### `personio_integration_hide_pro_hints`

*Hide hint for Pro-plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the hint.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1863](PersonioIntegration/PostTypes/PersonioPosition.php#L1863-L1871)

### `personio_integration_hide_pro_extensions`

*Hide the extensions for a pro-version if Pro is installed but license not entered.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1875](PersonioIntegration/PostTypes/PersonioPosition.php#L1875-L1884)

### `personio_integration_light_limit`

*Filter the max allowed limit.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit` | `int` | The max. limit to use.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2038](PersonioIntegration/PostTypes/PersonioPosition.php#L2038-L2044)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2090](PersonioIntegration/PostTypes/PersonioPosition.php#L2090-L2090)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2128](PersonioIntegration/PostTypes/PersonioPosition.php#L2128-L2128)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2155](PersonioIntegration/PostTypes/PersonioPosition.php#L2155-L2155)

### `personio_integration_hide_pro_hints`

*Hide hint for Pro-plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the hint.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2278](PersonioIntegration/PostTypes/PersonioPosition.php#L2278-L2286)

### `personio_integration_light_statistics`

*Filter the statistics.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$statistics` | `array<string,mixed>` | The statistic array.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Statistics.php](PersonioIntegration/Statistics.php), [line 120](PersonioIntegration/Statistics.php#L120-L126)

### `personio_integration_get_list_attributes`

*Filter the attributes for this template.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` | `array` | List of attributes to use.
`$attributes` | `array` | List of attributes vom PageBuilder.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0

Source: [app/PageBuilder/Gutenberg/Blocks/Filter_List.php](PageBuilder/Gutenberg/Blocks/Filter_List.php), [line 138](PageBuilder/Gutenberg/Blocks/Filter_List.php#L138-L146)

### `personio_integration_get_list_attributes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` |  | 
`$attributes` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks/Archive.php](PageBuilder/Gutenberg/Blocks/Archive.php), [line 188](PageBuilder/Gutenberg/Blocks/Archive.php#L188-L188)

### `personio_integration_get_list_attributes`

*Filter the attributes for this template.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` | `array` | List of attributes to use.
`$attributes` | `array` | List of attributes vom PageBuilder.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0

Source: [app/PageBuilder/Gutenberg/Blocks/Single.php](PageBuilder/Gutenberg/Blocks/Single.php), [line 131](PageBuilder/Gutenberg/Blocks/Single.php#L131-L139)

### `personio_integration_get_list_attributes`

*Filter the attributes for this template.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` | `array` | List of attributes to use.
`$attributes` | `array` | List of attributes vom PageBuilder.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0

Source: [app/PageBuilder/Gutenberg/Blocks/Filter_Select.php](PageBuilder/Gutenberg/Blocks/Filter_Select.php), [line 138](PageBuilder/Gutenberg/Blocks/Filter_Select.php#L138-L146)

### `personio_integration_gutenberg_pattern`

*Filter the list of pattern we provide for Gutenberg / Block Editor.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$patterns` | `array<string,mixed>` | List of patterns.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/Gutenberg/Patterns.php](PageBuilder/Gutenberg/Patterns.php), [line 82](PageBuilder/Gutenberg/Patterns.php#L82-L89)

### `personio_integration_block_templates`

*Filter the list of available block templates.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array<string,array<string,string>>` | The list of templates.

**Changelog**

Version | Description
------- | -----------
`2.2.0` | Available since 2.2.0.

Source: [app/PageBuilder/Gutenberg/Templates.php](PageBuilder/Gutenberg/Templates.php), [line 215](PageBuilder/Gutenberg/Templates.php#L215-L222)

### `personio_integration_block_help_url`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`Helper::get_plugin_support_url()` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 124](PageBuilder/Gutenberg/Blocks_Basis.php#L124-L124)

### `personio_integration_gutenberg_block_{$name}_attributes`

*Filter the attributes for a Block.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$single_attributes` | `array<string,mixed>` | The settings as an array.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 198](PageBuilder/Gutenberg/Blocks_Basis.php#L198-L205)

### `personio_integration_gutenberg_block_{$name}_path`

*Filter the path of a Block.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$path` | `string` | The absolute path to the block.json.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 216](PageBuilder/Gutenberg/Blocks_Basis.php#L216-L223)

### `personio_integration_light_block_language_path`

*Return the language path this plugin should use.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$language_path` | `string` | The path to the languages.
`$instance` | `\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis` | The Block object.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 253](PageBuilder/Gutenberg/Blocks_Basis.php#L253-L263)

### `personio_integration_pagebuilder`

*Filter the possible page builders.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `string[]` | List of the handler.

Source: [app/PageBuilder/Page_Builders.php](PageBuilder/Page_Builders.php), [line 70](PageBuilder/Page_Builders.php#L70-L75)

### `personio_integration_is_block_theme`

*Filter whether this theme is a block theme (true) or not (false).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$resulting_value` | `bool` | The resulting value.

**Changelog**

Version | Description
------- | -----------
`3.0.2` | Available since 3.0.2

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 133](PageBuilder/Gutenberg.php#L133-L139)

### `personio_integration_gutenberg_blocks`

*Filter the list of available Gutenberg blocks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | List of blocks.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 150](PageBuilder/Gutenberg.php#L150-L156)

### `personio_integration_log_table_filter`

*Filter the list before output.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,string>` | List of filter.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/Log_Table.php](Log_Table.php), [line 271](Log_Table.php#L271-L277)

### `personio_integration_light_status_list`

*Filter the list of possible states in the log table.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` |  | 

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Log_Table.php](Log_Table.php), [line 330](Log_Table.php#L330-L335)

### `personio_integration_log_categories`

*Filter the list of possible log categories.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,string>` | List of categories.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/Log.php](Log.php), [line 159](Log.php#L159-L166)

### `personio_integration_light_log_limit`

*Filter limit to prevent possible errors on big tables.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit` | `int` | The actual limit.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/Log.php](Log.php), [line 195](Log.php#L195-L201)

### `personio_integration_light_log_category`

*Filter the used category.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$category` | `string` | The category to use.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Log.php](Log.php), [line 206](Log.php#L206-L212)

### `personio_integration_light_log_md5`

*Filter the used md5.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$md5` | `string` | The md5 to use.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Log.php](Log.php), [line 222](Log.php#L222-L228)

### `personio_integration_light_log_errors`

*Filter for errors.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$errors` | `int` | Should be 1 to filter only for errors.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Log.php](Log.php), [line 233](Log.php#L233-L239)

### `personio_integration_prevent_wpml_optimizations`

*Bail via filter.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Whether optimizations should be prevented (true) or not (false)
`$query` | `array<string,mixed>` | The running position query.

**Changelog**

Version | Description
------- | -----------
`3.0.3` | Available since 3.0.3.

Source: [app/Third_Party_Plugins.php](Third_Party_Plugins.php), [line 461](Third_Party_Plugins.php#L461-L471)

### `personio_integration_archive_slug`

*Change the archive slug.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$slug` | `string` | The archive slug.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 59](Helper.php#L59-L66)

### `personio_integration_detail_slug`

*Change the single slug.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$slug` |  | 

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 80](Helper.php#L80-L87)

### `personio_integration_filter_types`

*Change the list of possible filter-types.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$types` | `array<string,string>` | The list of types.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 145](Helper.php#L145-L152)

### `personio_integration_get_shortcode_attributes`

*Pre-filter the given attributes.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$filtered` | `array` | The list of attributes.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 218](Helper.php#L218-L225)

### `personio_integration_light_current_url`

*Filter the resulting current URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$page_url` | `string` | The resulting current URL.

**Changelog**

Version | Description
------- | -----------
`5.1.2` | Available since 5.1.2.

Source: [app/Helper.php](Helper.php), [line 420](Helper.php#L420-L426)

### `personio_integration_url`

*Filter the Personio URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The configured Personio URL.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 510](Helper.php#L510-L517)

### `personio_integration_list_of_cpts`

*Filter the list of custom post-types this plugin is using.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | The list of the post-types.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 644](Helper.php#L644-L651)

### `personio_integration_file_version`

*Filter the used file version (for JS- and CSS-files which get enqueued).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$plugin_version` | `string` | The plugin-version.
`$filepath` | `string` | The absolute path to the requested file.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 697](Helper.php#L697-L705)

### `personio_integration_light_do_not_load_on_cpt`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array(PersonioPosition::get_instance()->get_name())` |  | 

Source: [app/Helper.php](Helper.php), [line 779](Helper.php#L779-L779)

### `personio_integration_light_wp_config_name`

*Filter to change the filename of the used wp-config.php without its extension .php.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wp_config_php` | `string` | The filename.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Helper.php](Helper.php), [line 843](Helper.php#L843-L849)

### `personio_integration_light_wp_config_path`

*Filter the path for the wp-config.php before we return it.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wp_config_php_path` | `string` | The path.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Helper.php](Helper.php), [line 854](Helper.php#L854-L860)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

