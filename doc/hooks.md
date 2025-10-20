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

*Run additional tasks if setup is marked as completed.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 512](Plugin/Setup.php#L512-L517)

### `personio_integration_help_page`

*Add additional boxes for help page.*


Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 714](Plugin/Admin/Admin.php#L714-L717)

### `personio_integration_help_tours`

*Add additional helper tasks via hook.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 802](Plugin/Admin/Admin.php#L802-L807)

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

Source: [app/PersonioIntegration/Widgets/Details.php](PersonioIntegration/Widgets/Details.php), [line 225](PersonioIntegration/Widgets/Details.php#L225-L231)

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

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 197](PersonioIntegration/Widgets/Application_Button.php#L197-L203)

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

*Run custom actions before import of positions is running.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports_Base` | The import object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 150](PersonioIntegration/Imports/Xml.php#L150-L156)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 225](PersonioIntegration/Imports/Xml.php#L225-L230)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions but after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 236](PersonioIntegration/Imports/Xml.php#L236-L241)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 277](PersonioIntegration/Imports/Xml.php#L277-L284)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 297](PersonioIntegration/Imports/Xml.php#L297-L302)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 320](PersonioIntegration/Imports/Xml.php#L320-L327)

### `personio_integration_import_starting`

*Run custom actions before import of positions is running.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports_Base` | The import object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 138](PersonioIntegration/Imports/Api.php#L138-L144)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 404](PersonioIntegration/Imports/Api.php#L404-L409)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions but after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 415](PersonioIntegration/Imports/Api.php#L415-L420)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 467](PersonioIntegration/Imports/Api.php#L467-L472)

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 483](PersonioIntegration/Imports/Api.php#L483-L490)

### `personio_integration_import_of_url_starting`

*Run action on start of import of single URL.*

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 262](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L262-L270)

### `personio_integration_import_content_not_changed`

*Run actions for this case.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url` | The import-object.
`$md5hash` | `string` | The md5-hash from body.

**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since 3.0.4.

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 299](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L299-L307)

### `personio_integration_import_of_url_ended`

*Run action on end of import of single URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url` | The import-object.

**Changelog**

Version | Description
------- | -----------
`3.0.5` | Available since 3.0.5

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 409](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L409-L415)

### `personio_integration_import_single_position_save`

*Run hook for individual settings after Position has been saved (inserted or updated).*

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

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 190](PersonioIntegration/Imports_Base.php#L190-L196)

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

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 223](PersonioIntegration/Imports_Base.php#L223-L230)

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

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 256](PersonioIntegration/Imports_Base.php#L256-L263)

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
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as object.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 995](PersonioIntegration/PostTypes/PersonioPosition.php#L995-L1001)

### `personio_integration_light_edit_position_box_title`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as object.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1017](PersonioIntegration/PostTypes/PersonioPosition.php#L1017-L1023)

### `personio_integration_light_edit_position_box_content`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post` | `\PersonioIntegrationLight\PersonioIntegration\PostTypes\WP_post` | The post as object.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1067](PersonioIntegration/PostTypes/PersonioPosition.php#L1067-L1073)

### `personio_integration_light_edit_position_box_taxonomy`

*Run additional tasks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as object.
`$attr` | `array` | Attributes used for this meta box.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1168](PersonioIntegration/PostTypes/PersonioPosition.php#L1168-L1175)

### `personio_integration_light_dashboard_widget_pre_query`

*Run additional tasks before the positions have been loaded.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1437](PersonioIntegration/PostTypes/PersonioPosition.php#L1437-L1442)

### `personio_integration_light_dashboard_widget_post_query`

*Run additional tasks after the positions have been loaded.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1453](PersonioIntegration/PostTypes/PersonioPosition.php#L1453-L1458)

### `personio_integration_deletion_starting`

*Run custom actions before deleting of all positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1534](PersonioIntegration/PostTypes/PersonioPosition.php#L1534-L1539)

### `personio_integration_deletion_ended`

*Run custom actions after deletion of all positions has been done.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1605](PersonioIntegration/PostTypes/PersonioPosition.php#L1605-L1610)

### `personio_integration_light_endpoint_task`

*Run the individual task.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$params` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1644](PersonioIntegration/PostTypes/PersonioPosition.php#L1644-L1647)

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
`$wp_language` | `string` | The language-name (e.g. "en").

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

### `personio_integration_light_crypt_methods`

*Filter the available crypt-methods.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$methods` | `array<int,string>` | List of methods.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Crypt.php](Plugin/Crypt.php), [line 141](Plugin/Crypt.php#L141-L147)

### `personio_integration_light_schedule_interval`

*Filter the interval for a single schedule.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$interval` | `string` | The interval.
`$instance` | `\PersonioIntegrationLight\Plugin\Schedules_Base` | The schedule-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 83](Plugin/Schedules_Base.php#L83-L90)

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

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 201](Plugin/Schedules_Base.php#L201-L211)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 112](Plugin/Templates.php#L112-L119)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 153](Plugin/Templates.php#L153-L153)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 187](Plugin/Templates.php#L187-L187)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 211](Plugin/Templates.php#L211-L218)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 247](Plugin/Templates.php#L247-L254)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 269](Plugin/Templates.php#L269-L276)

### `personio_integration_load_single_template`

*Decide whether to use our own template (false) or not (true).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true if our own single template should not be used.
`$single_template` | `string` | The single template which will be used instead.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 402](Plugin/Templates.php#L402-L411)

### `personio_integration_load_archive_template`

*Decide whether to use our own archive template (false) or not (true).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true if our own archive template should not be used.
`$archive_template` | `string` | The archive template which will be used instead.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 448](Plugin/Templates.php#L448-L457)

### `personio_integration_show_content`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 501](Plugin/Templates.php#L501-L501)

### `personio_integration_title_size`

*Filter the heading size.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$heading_size` | `string` | The heading size.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object ob the requested position.
`$attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 572](Plugin/Templates.php#L572-L581)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 743](Plugin/Templates.php#L743-L750)

### `personio_integration_add_kses_filter`

*Prevent filtering the HTML-code via kses.*

We need this only for the filter-form.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | False if filter should be run.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 841](Plugin/Templates.php#L841-L850)

### `personio_integration_light_position_classes`

*Filter the class list of a single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_classes` | `array<int,string>` | List of classes.
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | Position as object.

**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 957](Plugin/Templates.php#L957-L964)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 987](Plugin/Templates.php#L987-L994)

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
`$headers` | `array<string,string>` | List of headers.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/Email_Base.php](Plugin/Email_Base.php), [line 349](Plugin/Email_Base.php#L349-L355)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 133](Plugin/Setup.php#L133-L139)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 209](Plugin/Setup.php#L209-L216)

### `personio_integration_light_transient_title`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`Helper::get_plugin_name()` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 226](Plugin/Setup.php#L226-L226)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 314](Plugin/Setup.php#L314-L320)

### `personio_integration_setup_process_completed_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$completed_text` |  | 
`$config_name` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 488](Plugin/Setup.php#L488-L488)

### `personio_integration_light_plugin_row_meta`

*Filter the links in row meta of our plugin in plugin list.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$row_meta` | `array<string,string>` | List of links.

**Changelog**

Version | Description
------- | -----------
`4.2.4` | Available since 4.2.4.

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 197](Plugin/Init.php#L197-L203)

### `personio_integration_objects_with_db_tables`

*Add additional objects for this plugin which use custom tables.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$objects` | `array<int,string>` | List of objects.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 319](Plugin/Init.php#L319-L325)

### `personio_integration_objects_with_db_tables`

*Add additional objects for this plugin which use custom tables.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$objects` | `array<int,string>` | List of objects.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 351](Plugin/Init.php#L351-L357)

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

Source: [app/Plugin/Compatibilities.php](Plugin/Compatibilities.php), [line 156](Plugin/Compatibilities.php#L156-L163)

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

Hint: these are just arrays which define the endpoints.

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

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 259](Plugin/Admin/Admin.php#L259-L259)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 447](Plugin/Admin/Admin.php#L447-L447)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 511](Plugin/Admin/Admin.php#L511-L511)

### `personio_integration_light_show_admin_bar_menu`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 551](Plugin/Admin/Admin.php#L551-L551)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 663](Plugin/Admin/Admin.php#L663-L663)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 782](Plugin/Admin/Admin.php#L782-L782)

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

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 853](Plugin/Admin/Admin.php#L853-L860)

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

Source: [app/Plugin/License.php](Plugin/License.php), [line 67](Plugin/License.php#L67-L75)

### `personio_integration_light_url_after_pro_installation`

*Filter the referer URL after Personio Integration Pro has been installed and activated.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL to use as forward target.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 590](Plugin/License.php#L590-L596)

### `personio_integration_light_download_pro_url`

*Filter the download URL during installation of Personio Integration Pro.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$download_url` | `string` | The download URL.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 655](Plugin/License.php#L655-L661)

### `personio_integration_light_url_after_pro_installation`

*Filter the referer URL after Personio Integration Pro has been installed and activated.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL to use as forward target.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 683](Plugin/License.php#L683-L689)

### `personio_integration_schedule_our_events`

*Filter the list of our own events,
e.g. to check if all which are enabled in setting are active.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$our_events` | `array<string,array<string,mixed>>` | List of our own events in WP-cron.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 136](Plugin/Schedules.php#L136-L144)

### `personio_integration_disable_cron_check`

*Disable the additional cron check.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if check should be disabled.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 162](Plugin/Schedules.php#L162-L170)

### `personio_integration_schedules`

*Add custom schedule-objects to use.*

This must be objects based on PersonioIntegrationLight\Plugin\Schedules_Base.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list_of_schedules` | `string[]` | List of additional schedules.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 285](Plugin/Schedules.php#L285-L294)

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

Source: [app/PersonioIntegration/Personio_Accounts.php](PersonioIntegration/Personio_Accounts.php), [line 158](PersonioIntegration/Personio_Accounts.php#L158-L165)

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

Source: [app/PersonioIntegration/Widgets/Details.php](PersonioIntegration/Widgets/Details.php), [line 170](PersonioIntegration/Widgets/Details.php#L170-L178)

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
`$limit_by_list` | `int` | The limit explicit set for this listing.

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
`$default_values` | `array<string,mixed>` | The list of default values for each attribute used to display positions in frontend.

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

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 107](PersonioIntegration/Widgets/Application_Button.php#L107-L115)

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

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 171](PersonioIntegration/Widgets/Application_Button.php#L171-L179)

### `personio_integration_back_to_list_target_attribute`

*Set and filter the value for the target-attribute.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$target` | `string` | The target value.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as object.
`$attributes` | `array<string,mixed>` | List of attributes used for the output.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 182](PersonioIntegration/Widgets/Application_Button.php#L182-L191)

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

Source: [app/PersonioIntegration/Widgets/Application_Button.php](PersonioIntegration/Widgets/Application_Button.php), [line 211](PersonioIntegration/Widgets/Application_Button.php#L211-L219)

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

*Cancel the import before cleanup the database.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to prevent the cleanup tasks.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 197](PersonioIntegration/Imports/Xml.php#L197-L205)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 256](PersonioIntegration/Imports/Xml.php#L256-L256)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 400](PersonioIntegration/Imports/Xml.php#L400-L409)

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$object` |  | 
`$language_name` |  | 
`$personio_obj` |  | 
`$imports_obj` |  | 

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 315](PersonioIntegration/Imports/Api.php#L315-L315)

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 345](PersonioIntegration/Imports/Api.php#L345-L353)

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 378](PersonioIntegration/Imports/Api.php#L378-L386)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 435](PersonioIntegration/Imports/Api.php#L435-L435)

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
`$check_for_changes` | `bool` | False to prevent this checks.

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 251](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L251-L251)

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$xml_object` |  | 
`$language_name` |  | 
`$personio_obj` |  | 
`$imports_obj` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 364](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L364-L364)

### `personio_integration_import_sleep_positions_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`20` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 383](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L383-L383)

### `personio_integration_taxonomies`

*Filter all taxonomies and return the resulting list as array.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomies` | `array` | The list of taxonomies.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 275](PersonioIntegration/Taxonomies.php#L275-L282)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 485](PersonioIntegration/Taxonomies.php#L485-L493)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 514](PersonioIntegration/Taxonomies.php#L514-L521)

### `personio_integration_settings_get_list`

*Filter the taxonomy labels for template filter in listing before adding them to the settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array<string,string>` | List of labels.
`$taxonomies` | `array<string,string>` | List of taxonomies.

**Changelog**

Version | Description
------- | -----------
`2.3.0` | Available since 2.3.0.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1055](PersonioIntegration/Taxonomies.php#L1055-L1063)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1158](PersonioIntegration/Taxonomies.php#L1158-L1165)

### `personio_integration_check_requirement_to_import_single_position`

*Filter if position should be imported.*

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

*Filter the prepared position-data just before its saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$array` | `array<string,mixed>` | The position data as array.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object we are in.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 210](PersonioIntegration/Position.php#L210-L218)

### `personio_integration_single_url`

*Filter the public url of a single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The URL.
`$instance` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`3.2.0` | Available since 3.2.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 521](PersonioIntegration/Position.php#L521-L529)

### `personio_integration_get_personio_url`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` |  | 
`$instance` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 776](PersonioIntegration/Position.php#L776-L776)

### `personio_integration_theme_css`

*Filter the used CSS file for this theme.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_file` | `string` | Name of the CSS-file located in /css in this plugin.
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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 180](PersonioIntegration/Imports.php#L180-L180)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 206](PersonioIntegration/Imports.php#L206-L212)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 86](PersonioIntegration/Positions.php#L86-L94)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 191](PersonioIntegration/Positions.php#L191-L199)

### `personio_integration_positions_loop_id`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` |  | 

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 232](PersonioIntegration/Positions.php#L232-L232)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 251](PersonioIntegration/Positions.php#L251-L260)

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

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 196](PersonioIntegration/Extensions.php#L196-L203)

### `personio_integration_light_extension_state_changed_dialog`

*Filter the success dialog if state of extension has been changed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` |  | 
`$obj` |  | 

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 301](PersonioIntegration/Extensions.php#L301-L304)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 588](PersonioIntegration/Extensions.php#L588-L588)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 381](PersonioIntegration/PostTypes/PersonioPosition.php#L381-L388)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 411](PersonioIntegration/PostTypes/PersonioPosition.php#L411-L418)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 441](PersonioIntegration/PostTypes/PersonioPosition.php#L441-L448)

### `personio_integration_light_term_translate_hint`

*Adjust the dialog for hint for possibility to translate terms.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` | `array<string,mixed>` | The dialog to change.
`$taxonomy_name` | `string` | The taxonomy name.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 848](PersonioIntegration/PostTypes/PersonioPosition.php#L848-L855)

### `personio_integration_position_prevent_meta_box_remove`

*Prevent removing of all meta boxes in cpt edit view.*

Caution: the boxes will not be able to be saved.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to prevent removing of each meta box.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 900](PersonioIntegration/PostTypes/PersonioPosition.php#L900-L911)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 926](PersonioIntegration/PostTypes/PersonioPosition.php#L926-L936)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1269](PersonioIntegration/PostTypes/PersonioPosition.php#L1269-L1276)

### `personio_integration_sitemap_entry`

*Filter the data for the sitemap-entry for single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$entry` | `array<string,mixed>` | List of data for the sitemap.xml of this single position.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position as object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1376](PersonioIntegration/PostTypes/PersonioPosition.php#L1376-L1384)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1759](PersonioIntegration/PostTypes/PersonioPosition.php#L1759-L1766)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1811](PersonioIntegration/PostTypes/PersonioPosition.php#L1811-L1819)

### `personio_integration_hide_pro_extensions`

*Hide the extensions for pro-version if Pro is installed but license not entered.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Set true to hide the extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1823](PersonioIntegration/PostTypes/PersonioPosition.php#L1823-L1832)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1962](PersonioIntegration/PostTypes/PersonioPosition.php#L1962-L1968)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2014](PersonioIntegration/PostTypes/PersonioPosition.php#L2014-L2014)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2052](PersonioIntegration/PostTypes/PersonioPosition.php#L2052-L2052)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2082](PersonioIntegration/PostTypes/PersonioPosition.php#L2082-L2082)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2189](PersonioIntegration/PostTypes/PersonioPosition.php#L2189-L2197)

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

Source: [app/PersonioIntegration/Statistics.php](PersonioIntegration/Statistics.php), [line 119](PersonioIntegration/Statistics.php#L119-L125)

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

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 128](PageBuilder/Gutenberg/Blocks_Basis.php#L128-L128)

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

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 249](PageBuilder/Gutenberg/Blocks_Basis.php#L249-L259)

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

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 131](PageBuilder/Gutenberg.php#L131-L137)

### `personio_integration_gutenberg_blocks`

*Return list of available blocks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` |  | 

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 140](PageBuilder/Gutenberg.php#L140-L149)

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

*Filter the list of possible states in log table.*

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

Source: [app/Log.php](Log.php), [line 154](Log.php#L154-L161)

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

Source: [app/Log.php](Log.php), [line 187](Log.php#L187-L193)

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

Source: [app/Log.php](Log.php), [line 198](Log.php#L198-L204)

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

Source: [app/Log.php](Log.php), [line 214](Log.php#L214-L220)

### `personio_integration_light_log_errors`

*Filter for errors.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$errors` | `int` | Should 1 to filter only for errors.

**Changelog**

Version | Description
------- | -----------
`4.1.0` | Available since 4.1.0.

Source: [app/Log.php](Log.php), [line 225](Log.php#L225-L231)

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

Source: [app/Third_Party_Plugins.php](Third_Party_Plugins.php), [line 466](Third_Party_Plugins.php#L466-L476)

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

Source: [app/Helper.php](Helper.php), [line 505](Helper.php#L505-L512)

### `personio_integration_list_of_cpts`

*Filter the list of custom post types this plugin is using.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,string>` | The list of post types.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 638](Helper.php#L638-L645)

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

Source: [app/Helper.php](Helper.php), [line 691](Helper.php#L691-L699)

### `personio_integration_light_do_not_load_on_cpt`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array(PersonioPosition::get_instance()->get_name())` |  | 

Source: [app/Helper.php](Helper.php), [line 790](Helper.php#L790-L790)

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

Source: [app/Helper.php](Helper.php), [line 854](Helper.php#L854-L860)

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

Source: [app/Helper.php](Helper.php), [line 865](Helper.php#L865-L871)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

