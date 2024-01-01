# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

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

Source: [app/Plugin/Admin/SettingFields/Text.php](Plugin/Admin/SettingFields/Text.php), [line 76](Plugin/Admin/SettingFields/Text.php#L76-L83)

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

Source: [app/Plugin/Admin/SettingFields/Number.php](Plugin/Admin/SettingFields/Number.php), [line 48](Plugin/Admin/SettingFields/Number.php#L48-L55)

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

Source: [app/Plugin/Admin/SettingFields/MultiSelect.php](Plugin/Admin/SettingFields/MultiSelect.php), [line 105](Plugin/Admin/SettingFields/MultiSelect.php#L105-L112)

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

Source: [app/Plugin/Admin/SettingFields/Select.php](Plugin/Admin/SettingFields/Select.php), [line 67](Plugin/Admin/SettingFields/Select.php#L67-L74)

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

Source: [app/Plugin/Admin/SettingFields/Checkbox.php](Plugin/Admin/SettingFields/Checkbox.php), [line 60](Plugin/Admin/SettingFields/Checkbox.php#L60-L67)

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

Source: [app/Plugin/Admin/SettingFields/Multiple_Radios.php](Plugin/Admin/SettingFields/Multiple_Radios.php), [line 63](Plugin/Admin/SettingFields/Multiple_Radios.php#L63-L70)

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

Source: [app/Plugin/Admin/SettingFields/ProHint.php](Plugin/Admin/SettingFields/ProHint.php), [line 26](Plugin/Admin/SettingFields/ProHint.php#L26-L33)

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

Source: [app/Plugin/Admin/SettingFields/Multiple_Checkboxes.php](Plugin/Admin/SettingFields/Multiple_Checkboxes.php), [line 63](Plugin/Admin/SettingFields/Multiple_Checkboxes.php#L63-L70)

### `personio_integration_import_ended`

*Run custom actions after import of single Personio-URL has been done.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 358](PersonioIntegration/Import.php#L358-L363)

### `personio_integration_import_single_position_save`

*Run hook for individual settings after Position has been saved (inserted or updated).*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$this` | `\App\PersonioIntegration\Position` | The object of this position.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 221](PersonioIntegration/Position.php#L221-L228)

## Filters

### `personio_integration_supported_languages`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`WP_PERSONIO_INTEGRATION_LANGUAGES_COMPLETE` |  | 

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 50](Plugin/Languages.php#L50-L50)

### `personio_integration_languages_names`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array('de' => __('German', 'personio-integration-light'), 'en' => __('English', 'personio-integration-light'))` |  | 

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 60](Plugin/Languages.php#L60-L66)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 93](Plugin/Templates.php#L93-L100)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 134](Plugin/Templates.php#L134-L134)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 168](Plugin/Templates.php#L168-L168)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 216](Plugin/Templates.php#L216-L223)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 257](Plugin/Templates.php#L257-L264)

### `personio_integration_show_content`

*Show the content.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$show_content_filter_value` | `bool` | Whether to show content or not.

**Changelog**

Version | Description
------- | -----------
`2.2.0` | Available since 2.2.0

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 388](Plugin/Templates.php#L388-L397)

### `personio_integration_taxonomies`

*Get all taxonomies as array.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomies` | `array` | The list of taxonomies.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 604](Plugin/Templates.php#L604-L611)

### `personio_integration_taxonomies`

*Get all taxonomies as array.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomies` | `array` | The list of taxonomies.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/Plugin/Cli/Helper.php](Plugin/Cli/Helper.php), [line 65](Plugin/Cli/Helper.php#L65-L72)

### `personio_integration_settings_get_list`

*Show hint for Pro-plugin with individual text.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array` | List of labels.
`$default_filter` | `array` | List of default filters.

**Changelog**

Version | Description
------- | -----------
`2.3.0` | Available since 2.3.0.

Source: [app/Plugin/Settings.php](Plugin/Settings.php), [line 162](Plugin/Settings.php#L162-L170)

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

Source: [app/Plugin/Admin/SettingFields/MultiSelect.php](Plugin/Admin/SettingFields/MultiSelect.php), [line 24](Plugin/Admin/SettingFields/MultiSelect.php#L24-L31)

### `personio_integration_settings_multiselect_classes`

*Get additional CSS-classes for multiselect-field.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$css_classes` | `array` | List of additional CSS-classes.
`$attributes` | `array` | List of attributes.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since 2.0.0.

Source: [app/Plugin/Admin/SettingFields/MultiSelect.php](Plugin/Admin/SettingFields/MultiSelect.php), [line 70](Plugin/Admin/SettingFields/MultiSelect.php#L70-L78)

### `personio_integration_schedules`

*Add custom schedules to use. This must be objects based on App\Plugin\Schedules_Base.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list_of_schedules` | `array` | List of additional schedules.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 34](Plugin/Schedules.php#L34-L41)

### `personio_integration_import_url`

*Change the URL via hook.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` | `string` | The individual text.
`$key` | `string` | Language-marker.

**Changelog**

Version | Description
------- | -----------
`2.5.0` | Available since 2.5.0.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 117](PersonioIntegration/Import.php#L117-L125)

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

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 158](PersonioIntegration/Import.php#L158-L165)

### `personio_integration_import_single_position`

*Check the position before import.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` | `bool` | The individual text.
`$position` |  | 
`$key` | `string` | The language-marker.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 255](PersonioIntegration/Import.php#L255-L266)

### `personio_integration_delete_single_position`

*Check if this position should be deleted.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` | `bool` | Marker to delete the position.
`$position` | `\App\PersonioIntegration\Position` | The position as object.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 324](PersonioIntegration/Import.php#L324-L332)

### `personio_integration_import_single_position_xml`

*Change the XML-object before saving the position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$position_object` | `\App\PersonioIntegration\Position` | The object of this position.
`$position` | `object` | The XML-object with the data from Personio.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Import.php](PersonioIntegration/Import.php), [line 449](PersonioIntegration/Import.php#L449-L457)

### `personio_integration_taxonomies`

*Get all taxonomies as array.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$taxonomies` | `array` | The list of taxonomies.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 209](PersonioIntegration/Taxonomies.php#L209-L216)

### `personio_integration_cat_labels`

*Change category list.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$labels` | `array` | The list of labels (internal name => label).

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 283](PersonioIntegration/Taxonomies.php#L283-L290)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 140](PersonioIntegration/Position.php#L140-L150)

### `personio_integration_import_single_position_filter_before_saving`

*Filter the prepared position-data just before its saved.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$array` | `array` | The position data as array.
`$this` | `\App\PersonioIntegration\Position` | The object we are in.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 197](PersonioIntegration/Position.php#L197-L205)

### `personio_integration_archive_slug`

*Change the archive slug.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$archive_slug` | `string` | The archive slug.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 129](PersonioIntegration/PostTypes/PersonioPosition.php#L129-L136)

### `personio_integration_detail_slug`

*Change the detail slug.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$single_slug` | `string` | The archive slug.

**Changelog**

Version | Description
------- | -----------
`1.0.0` | Available since first release.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 138](PersonioIntegration/PostTypes/PersonioPosition.php#L138-L145)

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
`2.0.0` | Available since first release.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 302](PersonioIntegration/PostTypes/PersonioPosition.php#L302-L310)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 362](PersonioIntegration/PostTypes/PersonioPosition.php#L362-L369)

### `personio_integration_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$limit_by_wp` |  | 
`$personio_attributes['limit']` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 452](PersonioIntegration/PostTypes/PersonioPosition.php#L452-L452)

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
`2.0.0` | Available since first release.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 458](PersonioIntegration/PostTypes/PersonioPosition.php#L458-L466)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 592](PersonioIntegration/PostTypes/PersonioPosition.php#L592-L599)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 622](PersonioIntegration/PostTypes/PersonioPosition.php#L622-L629)

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

Source: [app/PageBuilder/Gutenberg/Templates.php](PageBuilder/Gutenberg/Templates.php), [line 233](PageBuilder/Gutenberg/Templates.php#L233-L240)

### `personio_integration_get_taxonomy_from_position`

*Filter the taxonomy for given position.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$name` | `string` | The public name for the requested taxonomy.
`$taxonomy` | `string` | The name of the requested taxonomy.
`$position` | `\App\PersonioIntegration\Position` | The position as object.

**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since first release.

Source: [app/Helper.php](Helper.php), [line 356](Helper.php#L356-L365)

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

Source: [app/Helper.php](Helper.php), [line 425](Helper.php#L425-L432)

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

Source: [app/Helper.php](Helper.php), [line 496](Helper.php#L496-L503)

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

Source: [app/Helper.php](Helper.php), [line 843](Helper.php#L843-L851)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

