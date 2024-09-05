# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### `personio_integration_light_settings_export`

*Run additional task before running the export of all settings.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Settings_Export.php](Plugin/Settings_Export.php), [line 173](Plugin/Settings_Export.php#L173-L178)

### `personio_integration_uninstaller`

*Run additional task for uninstallation.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$delete_data` | `array` | Marker to delete all data or not.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Cli.php](Plugin/Cli.php), [line 74](Plugin/Cli.php#L74-L81)

### `personio_integration_installer`

*Run additional task for installation.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Cli.php](Plugin/Cli.php), [line 86](Plugin/Cli.php#L86-L91)

### `personio_integration_light_settings_import`

*Run additional task before running the import of settings.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Settings_Import.php](Plugin/Settings_Import.php), [line 225](Plugin/Settings_Import.php#L225-L230)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Text.php](Plugin/Admin/SettingFields/Text.php), [line 85](Plugin/Admin/SettingFields/Text.php#L85-L92)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Number.php](Plugin/Admin/SettingFields/Number.php), [line 52](Plugin/Admin/SettingFields/Number.php#L52-L59)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Radios.php](Plugin/Admin/SettingFields/Radios.php), [line 72](Plugin/Admin/SettingFields/Radios.php#L72-L79)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Select.php](Plugin/Admin/SettingFields/Select.php), [line 81](Plugin/Admin/SettingFields/Select.php#L81-L88)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Checkbox.php](Plugin/Admin/SettingFields/Checkbox.php), [line 63](Plugin/Admin/SettingFields/Checkbox.php#L63-L70)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Multiple_Select.php](Plugin/Admin/SettingFields/Multiple_Select.php), [line 127](Plugin/Admin/SettingFields/Multiple_Select.php#L127-L134)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/ProHint.php](Plugin/Admin/SettingFields/ProHint.php), [line 29](Plugin/Admin/SettingFields/ProHint.php#L29-L36)

### `personio_integration_admin_show_pro_hint`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$message` | `string` | The individual text.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Admin/SettingFields/Multiple_Checkboxes.php](Plugin/Admin/SettingFields/Multiple_Checkboxes.php), [line 56](Plugin/Admin/SettingFields/Multiple_Checkboxes.php#L56-L63)

### `personio_integration_help_page`

*Add additional boxes for help page.*


Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 607](Plugin/Admin/Admin.php#L607-L610)

### `personio_integration_help_tours`

*Add additional helper tasks via hook.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 696](Plugin/Admin/Admin.php#L696-L701)

### `personio_integration_import_of_url_starting`

*Run action on start of import of single URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Import` | The import-object.

**Changelog**

Version | Description
------- | -----------
`3.0.5` | Available since 3.0.5

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 169](PersonioIntegration/Import.php#L169-L175)

### `personio_integration_import_timestamp_no_changed`

*Run actions for this case.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Import` | The import-object.
`$last_modified_timestamp` | `int` | The timestamp.

**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since 3.0.4.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 244](PersonioIntegration/Import.php#L244-L252)

### `personio_integration_import_content_not_changed`

*Run actions for this case.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Import` | The import-object.
`$md5hash` | `string` | The md5-hash from body.

**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since 3.0.4.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 284](PersonioIntegration/Import.php#L284-L292)

### `personio_integration_import_of_url_ended`

*Run action on end of import of single URL.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Import` | The import-object.

**Changelog**

Version | Description
------- | -----------
`3.0.5` | Available since 3.0.5

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 389](PersonioIntegration/Import.php#L389-L395)

### `personio_integration_import_single_position_save`

*Run hook for individual settings after Position has been saved (inserted or updated).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 248](PersonioIntegration/Position.php#L248-L255)

### `personio_integration_import_single_position_save_finished`

*Run hook for individual settings after all settings for the position have been saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 302](PersonioIntegration/Position.php#L302-L309)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 124](PersonioIntegration/Availability.php#L124-L131)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 149](PersonioIntegration/Availability.php#L149-L156)

### `personio_integration_import_starting`

*Run custom actions before import of positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 134](PersonioIntegration/Imports.php#L134-L139)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 180](PersonioIntegration/Imports.php#L180-L185)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 191](PersonioIntegration/Imports.php#L191-L196)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 237](PersonioIntegration/Imports.php#L237-L242)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 260](PersonioIntegration/Imports.php#L260-L267)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 360](PersonioIntegration/Imports.php#L360-L367)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 422](PersonioIntegration/Imports.php#L422-L429)

### `personio_integration_deletion_starting`

*Run custom actions before deleting of all positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1608](PersonioIntegration/PostTypes/PersonioPosition.php#L1608-L1613)

### `personio_integration_deletion_ended`

*Run custom actions after deletion of all positions has been done.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1674](PersonioIntegration/PostTypes/PersonioPosition.php#L1674-L1679)

### `personio_integration_light_endpoint_task`

*Run the individual task.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$params` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1713](PersonioIntegration/PostTypes/PersonioPosition.php#L1713-L1716)

## Filters

### `personio_integration_admin_settings_pages`

*Allow our own capability to save settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$settings_pages` |  | 

Source: [app/Plugin/Roles.php](Plugin/Roles.php), [line 104](Plugin/Roles.php#L104-L116)

### `personio_integration_supported_languages`

*Return the supported languages.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$languages` | `string` | List of supported languages.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 93](Plugin/Languages.php#L93-L100)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 184](Plugin/Languages.php#L184-L191)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 240](Plugin/Languages.php#L240-L247)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 260](Plugin/Languages.php#L260-L267)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 288](Plugin/Languages.php#L288-L295)

### `personio_integration_get_transients_for_display`

*Filter the transients used and managed by this plugin.*

Hint: with help of this hook you could hide all transients this plugin is using. Simple return an empty array.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$transients` | `array` | List of transients.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Transients.php](Plugin/Transients.php), [line 193](Plugin/Transients.php#L193-L202)

### `personio_integration_pro_schedule_interval`

*Filter the interval for a single schedule.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$interval` | `string` | The interval.
`$this` | `\PersonioIntegrationLight\Plugin\Schedules_Base` | The schedule-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 84](Plugin/Schedules_Base.php#L84-L91)

### `personio_integration_schedule_enabling`

*Filter whether to activate this schedule.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True if this object should NOT be enabled.
`$this` | `\PersonioIntegrationLight\Plugin\Schedules_Base` | Actual object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 201](Plugin/Schedules_Base.php#L201-L211)

### `personio_integration_light_transient_title`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`Helper::get_plugin_name()` |  | 

Source: [app/Plugin/Transient.php](Plugin/Transient.php), [line 192](Plugin/Transient.php#L192-L192)

### `personio_integration_transient_hide_on`

*Filter where a single transient should be hidden.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$hide_on` | `array` | List of absolute URLs.
`$this` | `\PersonioIntegrationLight\Plugin\Transient` | The actual transient object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Transient.php](Plugin/Transient.php), [line 368](Plugin/Transient.php#L368-L376)

### `personio_integration_templates_archive`

*Filter the list of available templates for archive listings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 97](Plugin/Templates.php#L97-L104)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 138](Plugin/Templates.php#L138-L138)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 172](Plugin/Templates.php#L172-L172)

### `personio_integration_admin_template_labels`

*Filter the list of available templates for content.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 196](Plugin/Templates.php#L196-L203)

### `personio_integration_templates_jobdescription`

*Filter the list of available templates for job description.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 232](Plugin/Templates.php#L232-L239)

### `personio_integration_templates_excerpts`

*Filter the list of available templates for excerpts.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | List of templates (filename => label).

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 254](Plugin/Templates.php#L254-L261)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 361](Plugin/Templates.php#L361-L369)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 390](Plugin/Templates.php#L390-L398)

### `personio_integration_show_content`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 425](Plugin/Templates.php#L425-L425)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 480](Plugin/Templates.php#L480-L489)

### `personio_integration_show_term_list`

*Filter whether to show terms of single taxonomy as list or not.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to show the list.
`$terms` | `array` | List of terms.

**Changelog**

Version | Description
------- | -----------
`3.0.8` | Available since 3.0.8.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 543](Plugin/Templates.php#L543-L550)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 622](Plugin/Templates.php#L622-L629)

### `personio_integration_back_to_list_target_attribute`

*Set and filter the value for the target-attribute.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`'_blank'` |  | 
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The Position as object.
`$attributes` | `array` | List of attributes used for the output.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 659](Plugin/Templates.php#L659-L667)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 830](Plugin/Templates.php#L830-L838)

### `personio_integration_settings_export_filename`

*File the filename for JSON-download of all settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$filename` | `string` | The generated filename.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Settings_Export.php](Plugin/Settings_Export.php), [line 151](Plugin/Settings_Export.php#L151-L158)

### `personio_integration_setup`

*Filter the configured setup for this plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$setup` | `array` | The setup-configuration.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 177](Plugin/Setup.php#L177-L184)

### `personio_integration_light_transient_title`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`Helper::get_plugin_name()` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 194](Plugin/Setup.php#L194-L194)

### `personio_integration_setup_config`

*Filter the setup configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$config` | `array` | List of configuration for the setup.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 281](Plugin/Setup.php#L281-L287)

### `personio_integration_setup_process_completed_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$completed_text` |  | 
`$config_name` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 436](Plugin/Setup.php#L436-L436)

### `personio_integration_objects_with_db_tables`

*Install db-table of registered objects.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array('PersonioIntegrationLight\\Log')` |  | 

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 283](Plugin/Init.php#L283-L289)

### `personio_integration_objects_with_db_tables`

*Delete db-tables of registered objects.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array('PersonioIntegrationLight\\Log')` |  | 

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 297](Plugin/Init.php#L297-L303)

### `personio_integration_run_compatibility_checks`

*Filter whether the compatibility-checks should be run (false) or not (true)*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to prevent compatibility-checks.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Compatibilities.php](Plugin/Compatibilities.php), [line 56](Plugin/Compatibilities.php#L56-L63)

### `personio_integration_compatibility_checks`

*Filter the list of compatibilities.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | List of compatibility-checks.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Compatibilities.php](Plugin/Compatibilities.php), [line 104](Plugin/Compatibilities.php#L104-L111)

### `personio_integration_templates_archive`

*Hide intro via hook.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to hide the intro.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Intro.php](Plugin/Intro.php), [line 73](Plugin/Intro.php#L73-L80)

### `personio_integration_templates_archive`

*Hide intro via hook.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return true to hide the intro.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Intro.php](Plugin/Intro.php), [line 237](Plugin/Intro.php#L237-L244)

### `personio_integration_setting_field_arguments`

*Filter the arguments for this field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$arguments` | `array` | List of arguments.
`$field_settings` | `array` | Setting for this field.
`$field_name` | `string` | Internal name of the field.

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 707](Plugin/Settings.php#L707-L714)

### `personio_integration_settings_title`

*Filter for settings title.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$title` | `string` | The title.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 761](Plugin/Settings.php#L761-L768)

### `personio_integration_settings`

*Filter the plugin-settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$settings` | `array` | The settings as array.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 907](Plugin/Settings.php#L907-L914)

### `personio_integration_hide_pro_hints`

*Hide the additional buttons for reviews or pro-version.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `array` | Set true to hide the buttons.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 957](Plugin/Settings.php#L957-L964)

### `personio_integration_settings_tabs`

*Filter the list of tabs.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$tabs` |  | 

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 998](Plugin/Settings.php#L998-L1005)

### `personio_integration_dashboard_widgets`

*Filter the dashboard-widgets used by this plugin.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dashboard_widgets` | `array` | List of widgets.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Dashboard.php](Plugin/Admin/Dashboard.php), [line 87](Plugin/Admin/Dashboard.php#L87-L93)

### `personio_integration_site_health_endpoints`

*Filter the endpoints for Site Health this plugin is using.*

Hint: these are just arrays which define the endpoints.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | List of endpoints.

Source: [app/Plugin/Admin/Site_Health.php](Plugin/Admin/Site_Health.php), [line 93](Plugin/Admin/Site_Health.php#L93-L100)

### `personio_integration_settings_radio_attr`

*Change Radio-field-attributes.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attributes` | `array` | List of attributes of this Radio-field.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/SettingFields/Radios.php](Plugin/Admin/SettingFields/Radios.php), [line 27](Plugin/Admin/SettingFields/Radios.php#L27-L34)

### `personio_integration_settings_multiselect_attr`

*Change MultiSelect-field-attributes.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attributes` | `array` | List of attributes of this MultiSelect-field.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/Plugin/Admin/SettingFields/Multiple_Select.php](Plugin/Admin/SettingFields/Multiple_Select.php), [line 27](Plugin/Admin/SettingFields/Multiple_Select.php#L27-L34)

### `personio_integration_settings_multiselect_classes`

*Get additional CSS-classes for multiselect-field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$classes` | `array` | List of additional CSS-classes.
`$attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/Plugin/Admin/SettingFields/Multiple_Select.php](Plugin/Admin/SettingFields/Multiple_Select.php), [line 74](Plugin/Admin/SettingFields/Multiple_Select.php#L74-L82)

### `personio_integration_pro_hint_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$text` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 243](Plugin/Admin/Admin.php#L243-L243)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 371](Plugin/Admin/Admin.php#L371-L371)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 435](Plugin/Admin/Admin.php#L435-L435)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 558](Plugin/Admin/Admin.php#L558-L558)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 676](Plugin/Admin/Admin.php#L676-L676)

### `personio_integration_log_export_filename`

*File the filename for CSV-download.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$filename` | `string` | The generated filename.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 742](Plugin/Admin/Admin.php#L742-L749)

### `personio_integration_schedule_our_events`

*Filter the list of our own events,
e.g. to check if all which are enabled in setting are active.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$our_events` | `array` | List of our own events in WP-cron.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 123](Plugin/Schedules.php#L123-L131)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 148](Plugin/Schedules.php#L148-L156)

### `personio_integration_schedules`

*Add custom schedule-objects to use.*

This must be objects based on PersonioIntegrationLight\Plugin\Schedules_Base.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list_of_schedules` | `array` | List of additional schedules.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 261](Plugin/Schedules.php#L261-L270)

### `personio_integration_supported_themes`

*Filter the list of supported themes.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$theme_list` | `array` | The list of supported themes.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Themes.php](PersonioIntegration/Themes.php), [line 115](PersonioIntegration/Themes.php#L115-L121)

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

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 177](PersonioIntegration/Import.php#L177-L185)

### `personio_integration_import_header_status`

*Check if response has been used http-status 200, all others are errors.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$http_status` | `int` | The returned http-status.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 225](PersonioIntegration/Import.php#L225-L232)

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$position` |  | 
`$language_name` |  | 
`$personio_obj` |  | 

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 340](PersonioIntegration/Import.php#L340-L340)

### `personio_integration_import_sleep_positions_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`20` |  | 

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 358](PersonioIntegration/Import.php#L358-L358)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 265](PersonioIntegration/Taxonomies.php#L265-L272)

### `personio_integration_filter_taxonomy_label`

*Filter the taxonomy label.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$label` | `string` | The label.
`$taxonomy` | `string` | The taxonomy.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 470](PersonioIntegration/Taxonomies.php#L470-L478)

### `personio_integration_cat_labels`

*Change category list.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array` | The list of labels (internal name/slug => label).

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 499](PersonioIntegration/Taxonomies.php#L499-L506)

### `personio_integration_settings_get_list`

*Filter the taxonomy labels for template filter in listing before adding them to the settings.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array` | List of labels.
`$taxonomies` | `array` | List of taxonomies.

**Changelog**

Version | Description
------- | -----------
`2.3.0` | Available since 2.3.0.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1022](PersonioIntegration/Taxonomies.php#L1022-L1030)

### `personio_integration_check_requirement_to_import_single_position`

*Filter if position should be imported.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return false to import this position.
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 131](PersonioIntegration/Position.php#L131-L139)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 160](PersonioIntegration/Position.php#L160-L170)

### `personio_integration_prevent_import_of_single_position`

*Filter if position should be imported after we get an ID.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Return false to import this position.
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of the position.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 196](PersonioIntegration/Position.php#L196-L204)

### `personio_integration_import_single_position_filter_before_saving`

*Filter the prepared position-data just before its saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$array` | `array` | The position data as array.
`$this` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object we are in.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 224](PersonioIntegration/Position.php#L224-L232)

### `personio_integration_get_personio_url`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` |  | 
`$this` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 747](PersonioIntegration/Position.php#L747-L747)

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

Source: [app/PersonioIntegration/Themes_Base.php](PersonioIntegration/Themes_Base.php), [line 89](PersonioIntegration/Themes_Base.php#L89-L97)

### `personio_integration_theme_wrapper_classes`

*Filter the used CSS wrapper classes for this theme.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$wrapper_classes` |  | 
`$theme_name` | `string` | Internal name of the used theme (slug of the theme).

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Themes_Base.php](PersonioIntegration/Themes_Base.php), [line 109](PersonioIntegration/Themes_Base.php#L109-L117)

### `personio_integration_extensions_table_columns`

*Filter the possible columns for the extension table.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$columns` | `array` | List of columns.

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
`$extensions` | `array` | List of unsorted extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 74](PersonioIntegration/Tables/Extensions.php#L74-L81)

### `personio_integration_extension_categories`

*Filter the extension categories.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$categories` | `array` | List of categories.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 198](PersonioIntegration/Tables/Extensions.php#L198-L204)

### `personio_integration_light_position_availability_yes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 216](PersonioIntegration/Availability.php#L216-L216)

### `personio_integration_light_position_availability_no`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 253](PersonioIntegration/Availability.php#L253-L253)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 211](PersonioIntegration/Imports.php#L211-L211)

### `personio_integration_personio_urls`

*Filter the list of Personio URLs used to import positions.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$personio_urls` | `array` | List of Personio URLs.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 292](PersonioIntegration/Imports.php#L292-L299)

### `personio_integration_import_single_position_xml`

*Change the XML-object before saving the position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_object` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The object of this position.
`$position` | `object` | The XML-object with the data from Personio.
`$personio_url` | `string` | The used Personio-URL.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 467](PersonioIntegration/Imports.php#L467-L476)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 79](PersonioIntegration/Positions.php#L79-L87)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 184](PersonioIntegration/Positions.php#L184-L192)

### `personio_integration_positions_loop_id`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` |  | 

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 217](PersonioIntegration/Positions.php#L217-L217)

### `personio_integration_positions_resulting_list`

*Filter the resulting list of position objects.*

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 236](PersonioIntegration/Positions.php#L236-L245)

### `personio_integration_extend_position_object`

*Filter the possible extensions for the Personio-object.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | List of extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 135](PersonioIntegration/Extensions.php#L135-L142)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 327](PersonioIntegration/PostTypes/PersonioPosition.php#L327-L335)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 401](PersonioIntegration/PostTypes/PersonioPosition.php#L401-L410)

### `personio_integration_limit`

*Change the limit for positions in frontend.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit_by_wp` | `int` | The limit.
`$limit_by_list` | `int` | The limit for this list.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 496](PersonioIntegration/PostTypes/PersonioPosition.php#L496-L504)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 510](PersonioIntegration/PostTypes/PersonioPosition.php#L510-L518)

### `personio_integration_rest_templates_details`

*Filter the available details-templates for REST API.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | The templates.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 655](PersonioIntegration/PostTypes/PersonioPosition.php#L655-L662)

### `personio_integration_rest_templates_jobdescription`

*Filter the available jobdescription-templates for REST API.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | The templates.

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 685](PersonioIntegration/PostTypes/PersonioPosition.php#L685-L692)

### `personio_integration_rest_templates_archive`

*Filter the available archive-templates for REST API.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | The templates.

**Changelog**

Version | Description
------- | -----------
`2.6.0` | Available since 2.6.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 715](PersonioIntegration/PostTypes/PersonioPosition.php#L715-L722)

### `personio_integration_personioposition_columns`

*Filter the resulting columns.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$columns` | `array` | List of columns.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 789](PersonioIntegration/PostTypes/PersonioPosition.php#L789-L795)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1161](PersonioIntegration/PostTypes/PersonioPosition.php#L1161-L1172)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1187](PersonioIntegration/PostTypes/PersonioPosition.php#L1187-L1197)

### `personio_integration_position_attribute_defaults`

*Filter the attribute-defaults.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$default_values` | `array` | The list of default values for each attribute used to display positions in frontend.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1390](PersonioIntegration/PostTypes/PersonioPosition.php#L1390-L1397)

### `personio_integration_sitemap_entry`

*Filter the data for the sitemap-entry for single position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$entry` | `array` | List of data for the sitemap.xml of this single position.
`$position` | `\PersonioIntegrationLight\PersonioIntegration\Personio` | The Personio-object.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1488](PersonioIntegration/PostTypes/PersonioPosition.php#L1488-L1496)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1813](PersonioIntegration/PostTypes/PersonioPosition.php#L1813-L1820)

### `personio_integration_hide_pro_hints`

*Hide the extensions for pro-version.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `array` | Set true to hide the extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1865](PersonioIntegration/PostTypes/PersonioPosition.php#L1865-L1874)

### `personio_integration_hide_pro_extensions`

*Hide the extensions for pro-version if Pro is installed but license not entered.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `array` | Set true to hide the extensions.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1878](PersonioIntegration/PostTypes/PersonioPosition.php#L1878-L1887)

### `personio_integration_get_list_attributes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` |  | 
`$attributes` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks/Filter_List.php](PageBuilder/Gutenberg/Blocks/Filter_List.php), [line 121](PageBuilder/Gutenberg/Blocks/Filter_List.php#L121-L121)

### `personio_integration_get_list_attributes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` |  | 
`$attributes` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks/Archive.php](PageBuilder/Gutenberg/Blocks/Archive.php), [line 170](PageBuilder/Gutenberg/Blocks/Archive.php#L170-L170)

### `personio_integration_get_list_attributes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` |  | 
`$attributes` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks/Single.php](PageBuilder/Gutenberg/Blocks/Single.php), [line 122](PageBuilder/Gutenberg/Blocks/Single.php#L122-L122)

### `personio_integration_get_list_attributes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` |  | 
`$attributes` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks/Filter_Select.php](PageBuilder/Gutenberg/Blocks/Filter_Select.php), [line 121](PageBuilder/Gutenberg/Blocks/Filter_Select.php#L121-L121)

### `personio_integration_gutenberg_pattern`

*Filter the list of pattern we provide for Gutenberg / Block Editor.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$patterns` | `array` | List of patterns.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PageBuilder/Gutenberg/Patterns.php](PageBuilder/Gutenberg/Patterns.php), [line 79](PageBuilder/Gutenberg/Patterns.php#L79-L86)

### `personio_integration_block_templates`

*Filter the list of available block templates.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$templates` | `array` | The list of templates.

**Changelog**

Version | Description
------- | -----------
`2.2.0` | Available since 2.2.0.

Source: [app/PageBuilder/Gutenberg/Templates.php](PageBuilder/Gutenberg/Templates.php), [line 204](PageBuilder/Gutenberg/Templates.php#L204-L211)

### `personio_integration_pagebuilder`

*Filter the possible page builders.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | List of the handler.

Source: [app/PageBuilder/Page_Builders.php](PageBuilder/Page_Builders.php), [line 47](PageBuilder/Page_Builders.php#L47-L52)

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

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 108](PageBuilder/Gutenberg.php#L108-L114)

### `personio_integration_gutenberg_blocks`

*Return list of available blocks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` |  | 

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 117](PageBuilder/Gutenberg.php#L117-L134)

### `personio_integration_log_table_filter`

*Filter the list before output.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | List of filter.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/Log_Table.php](Log_Table.php), [line 270](Log_Table.php#L270-L276)

### `personio_integration_log_categories`

*Filter the list of possible log categories.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | List of categories.

**Changelog**

Version | Description
------- | -----------
`3.1.0` | Available since 3.1.0.

Source: [app/Log.php](Log.php), [line 109](Log.php#L109-L116)

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

Source: [app/Log.php](Log.php), [line 142](Log.php#L142-L148)

### `personio_integration_prevent_wpml_optimizations`

*Bail via filter.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | Whether optimizations should be prevented (true) or not (false)
`$query` | `array` | The running position query.

**Changelog**

Version | Description
------- | -----------
`3.0.3` | Available since 3.0.3.

Source: [app/Third_Party_Plugins.php](Third_Party_Plugins.php), [line 471](Third_Party_Plugins.php#L471-L481)

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

Source: [app/Helper.php](Helper.php), [line 50](Helper.php#L50-L57)

### `personio_integration_detail_slug`

*Change the detail slug.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$slug` |  | 

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 71](Helper.php#L71-L78)

### `personio_integration_filter_types`

*Change the list of possible filter-types.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$types` | `array` | The list of types.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 135](Helper.php#L135-L142)

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

Source: [app/Helper.php](Helper.php), [line 195](Helper.php#L195-L202)

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

Source: [app/Helper.php](Helper.php), [line 480](Helper.php#L480-L487)

### `personio_integration_list_of_cpts`

*Filter the list of custom post types this plugin is using.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array` | The list of post types.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Helper.php](Helper.php), [line 615](Helper.php#L615-L622)

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

Source: [app/Helper.php](Helper.php), [line 659](Helper.php#L659-L667)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

