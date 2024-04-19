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

Source: [app/Plugin/Settings_Export.php](Plugin/Settings_Export.php), [line 167](Plugin/Settings_Export.php#L167-L172)

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

Source: [app/Plugin/Settings_Import.php](Plugin/Settings_Import.php), [line 219](Plugin/Settings_Import.php#L219-L224)

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


Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 590](Plugin/Admin/Admin.php#L590-L593)

### `personio_integration_help_tours`

*Add additional helper tasks via hook.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 679](Plugin/Admin/Admin.php#L679-L684)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 301](PersonioIntegration/Position.php#L301-L308)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 126](PersonioIntegration/Availability.php#L126-L133)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 151](PersonioIntegration/Availability.php#L151-L158)

### `personio_integration_import_starting`

*Run custom actions before import of positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 131](PersonioIntegration/Imports.php#L131-L136)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 181](PersonioIntegration/Imports.php#L181-L186)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 224](PersonioIntegration/Imports.php#L224-L229)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 244](PersonioIntegration/Imports.php#L244-L251)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 343](PersonioIntegration/Imports.php#L343-L350)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 405](PersonioIntegration/Imports.php#L405-L412)

### `personio_integration_deletion_starting`

*Run custom actions before deleting of all positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1544](PersonioIntegration/PostTypes/PersonioPosition.php#L1544-L1549)

### `personio_integration_deletion_ended`

*Run custom actions after deletion of all positions has been done.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1610](PersonioIntegration/PostTypes/PersonioPosition.php#L1610-L1615)

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

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 75](Plugin/Schedules_Base.php#L75-L82)

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

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 187](Plugin/Schedules_Base.php#L187-L197)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 599](Plugin/Templates.php#L599-L606)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 636](Plugin/Templates.php#L636-L644)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 807](Plugin/Templates.php#L807-L815)

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

Source: [app/Plugin/Settings_Export.php](Plugin/Settings_Export.php), [line 145](Plugin/Settings_Export.php#L145-L152)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 272](Plugin/Setup.php#L272-L278)

### `personio_integration_setup_process_completed_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$completed_text` |  | 
`$config_name` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 427](Plugin/Setup.php#L427-L427)

### `personio_integration_objects_with_db_tables`

*Install db-table of registered objects.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array('PersonioIntegrationLight\\Log')` |  | 

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 282](Plugin/Init.php#L282-L288)

### `personio_integration_objects_with_db_tables`

*Delete db-tables of registered objects.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array('PersonioIntegrationLight\\Log')` |  | 

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 296](Plugin/Init.php#L296-L302)

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

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 681](Plugin/Settings.php#L681-L688)

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

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 735](Plugin/Settings.php#L735-L742)

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

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 871](Plugin/Settings.php#L871-L878)

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

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 920](Plugin/Settings.php#L920-L927)

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

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 961](Plugin/Settings.php#L961-L968)

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

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 242](Plugin/Admin/Admin.php#L242-L242)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 370](Plugin/Admin/Admin.php#L370-L370)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 429](Plugin/Admin/Admin.php#L429-L429)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 541](Plugin/Admin/Admin.php#L541-L541)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 659](Plugin/Admin/Admin.php#L659-L659)

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

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 735](Plugin/Admin/Admin.php#L735-L742)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 117](Plugin/Schedules.php#L117-L125)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 142](Plugin/Schedules.php#L142-L150)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 251](Plugin/Schedules.php#L251-L260)

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

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 185](PersonioIntegration/Import.php#L185-L193)

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

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 317](PersonioIntegration/Import.php#L317-L317)

### `personio_integration_import_sleep_positions_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`20` |  | 

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 335](PersonioIntegration/Import.php#L335-L335)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 257](PersonioIntegration/Taxonomies.php#L257-L264)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 462](PersonioIntegration/Taxonomies.php#L462-L470)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 491](PersonioIntegration/Taxonomies.php#L491-L498)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1014](PersonioIntegration/Taxonomies.php#L1014-L1022)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 742](PersonioIntegration/Position.php#L742-L742)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 218](PersonioIntegration/Availability.php#L218-L218)

### `personio_integration_light_position_availability_no`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 255](PersonioIntegration/Availability.php#L255-L255)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 201](PersonioIntegration/Imports.php#L201-L201)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 272](PersonioIntegration/Imports.php#L272-L279)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 450](PersonioIntegration/Imports.php#L450-L459)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 77](PersonioIntegration/Positions.php#L77-L85)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 182](PersonioIntegration/Positions.php#L182-L190)

### `personio_integration_positions_loop_id`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` |  | 

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 209](PersonioIntegration/Positions.php#L209-L209)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 228](PersonioIntegration/Positions.php#L228-L237)

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

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 134](PersonioIntegration/Extensions.php#L134-L141)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 323](PersonioIntegration/PostTypes/PersonioPosition.php#L323-L331)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 397](PersonioIntegration/PostTypes/PersonioPosition.php#L397-L406)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 492](PersonioIntegration/PostTypes/PersonioPosition.php#L492-L500)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 506](PersonioIntegration/PostTypes/PersonioPosition.php#L506-L514)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 652](PersonioIntegration/PostTypes/PersonioPosition.php#L652-L659)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 682](PersonioIntegration/PostTypes/PersonioPosition.php#L682-L689)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 712](PersonioIntegration/PostTypes/PersonioPosition.php#L712-L719)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 786](PersonioIntegration/PostTypes/PersonioPosition.php#L786-L792)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1108](PersonioIntegration/PostTypes/PersonioPosition.php#L1108-L1119)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1134](PersonioIntegration/PostTypes/PersonioPosition.php#L1134-L1144)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1326](PersonioIntegration/PostTypes/PersonioPosition.php#L1326-L1333)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1424](PersonioIntegration/PostTypes/PersonioPosition.php#L1424-L1432)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1756](PersonioIntegration/PostTypes/PersonioPosition.php#L1756-L1763)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1808](PersonioIntegration/PostTypes/PersonioPosition.php#L1808-L1817)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1821](PersonioIntegration/PostTypes/PersonioPosition.php#L1821-L1830)

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

Source: [app/PageBuilder/Page_Builders.php](PageBuilder/Page_Builders.php), [line 46](PageBuilder/Page_Builders.php#L46-L51)

### `personio_integration_gutenberg_blocks`

*Return list of available blocks.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` |  | 

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 109](PageBuilder/Gutenberg.php#L109-L126)

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

Source: [app/Helper.php](Helper.php), [line 192](Helper.php#L192-L199)

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

Source: [app/Helper.php](Helper.php), [line 469](Helper.php#L469-L476)

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

Source: [app/Helper.php](Helper.php), [line 595](Helper.php#L595-L602)

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

Source: [app/Helper.php](Helper.php), [line 639](Helper.php#L639-L647)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

