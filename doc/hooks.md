# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 157](PersonioIntegration/Imports/Api.php#L157-L163)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 467](PersonioIntegration/Imports/Api.php#L467-L472)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions but after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 478](PersonioIntegration/Imports/Api.php#L478-L483)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 530](PersonioIntegration/Imports/Api.php#L530-L535)

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 546](PersonioIntegration/Imports/Api.php#L546-L553)

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 195](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L195-L201)

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 275](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L275-L283)

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 333](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L333-L341)

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 444](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L444-L450)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 159](PersonioIntegration/Imports/Xml.php#L159-L165)

### `personio_integration_import_without_changes`

*Run custom actions in this case.*


**Changelog**

Version | Description
------- | -----------
`3.0.4` | Available since release 3.0.4.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 255](PersonioIntegration/Imports/Xml.php#L255-L260)

### `personio_integration_import_before_cleanup`

*Run custom actions before cleanup of positions but after import.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 266](PersonioIntegration/Imports/Xml.php#L266-L271)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 310](PersonioIntegration/Imports/Xml.php#L310-L317)

### `personio_integration_import_ended`

*Run custom actions after import of positions has been done without errors.*


**Changelog**

Version | Description
------- | -----------
`2.0.0` | Available since release 2.0.0.

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 330](PersonioIntegration/Imports/Xml.php#L330-L335)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 350](PersonioIntegration/Imports/Xml.php#L350-L357)

### `personio_integration_light_extension_table_buttons`

*Add additional buttons to the extension table.*


**Changelog**

Version | Description
------- | -----------
`5.1.0` | Available since 5.1.0.

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 432](PersonioIntegration/Tables/Extensions.php#L432-L437)

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

Source: [app/PersonioIntegration/Widgets/Details.php](PersonioIntegration/Widgets/Details.php), [line 239](PersonioIntegration/Widgets/Details.php#L239-L245)

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

Source: [app/PersonioIntegration/Widgets/Filter_List.php](PersonioIntegration/Widgets/Filter_List.php), [line 106](PersonioIntegration/Widgets/Filter_List.php#L106-L112)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 233](PersonioIntegration/Position.php#L233-L240)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 287](PersonioIntegration/Position.php#L287-L294)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 165](PersonioIntegration/Availability.php#L165-L172)

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

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 189](PersonioIntegration/Availability.php#L189-L196)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1064](PersonioIntegration/PostTypes/PersonioPosition.php#L1064-L1070)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1086](PersonioIntegration/PostTypes/PersonioPosition.php#L1086-L1092)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1136](PersonioIntegration/PostTypes/PersonioPosition.php#L1136-L1142)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1237](PersonioIntegration/PostTypes/PersonioPosition.php#L1237-L1244)

### `personio_integration_light_dashboard_widget_pre_query`

*Run additional tasks before the positions have been loaded.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1507](PersonioIntegration/PostTypes/PersonioPosition.php#L1507-L1512)

### `personio_integration_light_dashboard_widget_post_query`

*Run additional tasks after the positions have been loaded.*


**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1523](PersonioIntegration/PostTypes/PersonioPosition.php#L1523-L1528)

### `personio_integration_deletion_starting`

*Run custom actions before deleting of all positions is running.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1611](PersonioIntegration/PostTypes/PersonioPosition.php#L1611-L1616)

### `personio_integration_deletion_ended`

*Run custom actions after deletion of all positions has been done.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since release 3.0.0.

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1682](PersonioIntegration/PostTypes/PersonioPosition.php#L1682-L1687)

### `personio_integration_light_endpoint_task`

*Run the individual task.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$params` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1849](PersonioIntegration/PostTypes/PersonioPosition.php#L1849-L1852)

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

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 206](PersonioIntegration/Imports_Base.php#L206-L212)

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

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 239](PersonioIntegration/Imports_Base.php#L239-L246)

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

Source: [app/PersonioIntegration/Imports_Base.php](PersonioIntegration/Imports_Base.php), [line 272](PersonioIntegration/Imports_Base.php#L272-L279)

### `personio_integration_light_setup_completed`

*Run additional tasks if the setup is marked as completed.*


**Changelog**

Version | Description
------- | -----------
`4.0.0` | Available since 4.0.0.

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 512](Plugin/Setup.php#L512-L517)

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

Source: [app/Plugin/Cli.php](Plugin/Cli.php), [line 105](Plugin/Cli.php#L105-L113)

### `personio_integration_installer`

*Run additional tasks for installation via WP CLI.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Cli.php](Plugin/Cli.php), [line 118](Plugin/Cli.php#L118-L123)

### `personio_integration_help_page`

*Add additional boxes for the help page.*


Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 758](Plugin/Admin/Admin.php#L758-L761)

### `personio_integration_help_tours`

*Add additional helper tasks via hook.*


**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 846](Plugin/Admin/Admin.php#L846-L851)

## Filters

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

Source: [app/Third_Party_Plugins.php](Third_Party_Plugins.php), [line 467](Third_Party_Plugins.php#L467-L477)

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

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 139](PageBuilder/Gutenberg.php#L139-L145)

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

Source: [app/PageBuilder/Gutenberg.php](PageBuilder/Gutenberg.php), [line 156](PageBuilder/Gutenberg.php#L156-L162)

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

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 199](PageBuilder/Gutenberg/Blocks_Basis.php#L199-L206)

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

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 217](PageBuilder/Gutenberg/Blocks_Basis.php#L217-L224)

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

Source: [app/PageBuilder/Gutenberg/Blocks_Basis.php](PageBuilder/Gutenberg/Blocks_Basis.php), [line 254](PageBuilder/Gutenberg/Blocks_Basis.php#L254-L264)

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

Source: [app/PageBuilder/Gutenberg/Templates.php](PageBuilder/Gutenberg/Templates.php), [line 220](PageBuilder/Gutenberg/Templates.php#L220-L227)

### `personio_integration_gutenberg_ability_block_template_types`

*Filter the template types for which our blocks are meant.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<string,array<int,string>>` | List of block names with their template types.

**Changelog**

Version | Description
------- | -----------
`5.3.0` | Available since 5.3.0.

Source: [app/PageBuilder/Gutenberg/Template_Adapter.php](PageBuilder/Gutenberg/Template_Adapter.php), [line 153](PageBuilder/Gutenberg/Template_Adapter.php#L153-L159)

### `personio_integration_light_template_disallowed_blocks`

*Filter the blocks which are not allowed in templates saved via abilities.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`self::DISALLOWED_BLOCKS` |  | 

**Changelog**

Version | Description
------- | -----------
`6.0.0` | Available since 6.0.0.

Source: [app/PageBuilder/Gutenberg/Template_Adapter.php](PageBuilder/Gutenberg/Template_Adapter.php), [line 686](PageBuilder/Gutenberg/Template_Adapter.php#L686-L693)

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

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$attribute_defaults` |  | 
`$attributes` |  | 

Source: [app/PageBuilder/Gutenberg/Blocks/Archive.php](PageBuilder/Gutenberg/Blocks/Archive.php), [line 193](PageBuilder/Gutenberg/Blocks/Archive.php#L193-L193)

### `personio_integration_pagebuilder`

*Filter the possible page builders.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `string[]` | List of the handler.

Source: [app/PageBuilder/Page_Builders.php](PageBuilder/Page_Builders.php), [line 70](PageBuilder/Page_Builders.php#L70-L75)

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

### `personio_integration_light_is_cli`

*Filter whether the actual process is handled as WP CLI request.*

Used to suppress the output for WP CLI (messages, progress bars and the exit on errors) if a task is
run by an ability in a WP CLI process, e.g. by an MCP server which uses STDIO as transport.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$is_cli` | `bool` | True if this is a WP CLI request.

**Changelog**

Version | Description
------- | -----------
`5.8.0` | Available since 5.8.0.

Source: [app/Helper.php](Helper.php), [line 392](Helper.php#L392-L401)

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

Source: [app/Helper.php](Helper.php), [line 431](Helper.php#L431-L437)

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

Source: [app/Helper.php](Helper.php), [line 579](Helper.php#L579-L586)

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

Source: [app/Helper.php](Helper.php), [line 742](Helper.php#L742-L749)

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

Source: [app/Helper.php](Helper.php), [line 795](Helper.php#L795-L803)

### `personio_integration_light_do_not_load_on_cpt`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`array(PersonioPosition::get_instance()->get_name())` |  | 

Source: [app/Helper.php](Helper.php), [line 877](Helper.php#L877-L877)

### `personio_integration_light_log_without_debug`

*Filter whether a log entry should be written when debug mode is disabled.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$should_log` | `bool` | Whether the entry should be logged.
`$category` | `string` | The log entry category.
`$state` | `string` | The log entry state.
`$log` | `string` | The log message.
`$md5` | `string` | The unique marker.

**Changelog**

Version | Description
------- | -----------
`5.5.3` | Available since 5.5.3.

Source: [app/Log.php](Log.php), [line 120](Log.php#L120-L131)

### `personio_integration_light_log_with_debug`

*Filter whether a log entry should be written when debug mode is enabled.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$should_log` | `bool` | Whether the entry should be logged.
`$category` | `string` | The log entry category.
`$state` | `string` | The log entry state.
`$log` | `string` | The log message.
`$md5` | `string` | The unique marker.

**Changelog**

Version | Description
------- | -----------
`5.5.3` | Available since 5.5.3.

Source: [app/Log.php](Log.php), [line 150](Log.php#L150-L161)

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

Source: [app/Log.php](Log.php), [line 253](Log.php#L253-L260)

### `personio_integration_light_log_entries_order`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$order` |  | 

Source: [app/Log.php](Log.php), [line 285](Log.php#L285-L285)

### `personio_integration_light_log_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`10000` |  | 

Source: [app/Log.php](Log.php), [line 296](Log.php#L296-L296)

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

Source: [app/Log.php](Log.php), [line 352](Log.php#L352-L358)

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

Source: [app/Log.php](Log.php), [line 368](Log.php#L368-L374)

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

Source: [app/Log.php](Log.php), [line 379](Log.php#L379-L385)

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

Source: [app/PersonioIntegration/Statistics.php](PersonioIntegration/Statistics.php), [line 121](PersonioIntegration/Statistics.php#L121-L127)

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

Source: [app/PersonioIntegration/Themes_Base.php](PersonioIntegration/Themes_Base.php), [line 98](PersonioIntegration/Themes_Base.php#L98-L106)

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

Source: [app/PersonioIntegration/Themes_Base.php](PersonioIntegration/Themes_Base.php), [line 118](PersonioIntegration/Themes_Base.php#L118-L126)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 164](PersonioIntegration/Taxonomies.php#L164-L172)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 292](PersonioIntegration/Taxonomies.php#L292-L299)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 489](PersonioIntegration/Taxonomies.php#L489-L497)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 518](PersonioIntegration/Taxonomies.php#L518-L525)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1101](PersonioIntegration/Taxonomies.php#L1101-L1109)

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

Source: [app/PersonioIntegration/Taxonomies.php](PersonioIntegration/Taxonomies.php), [line 1204](PersonioIntegration/Taxonomies.php#L1204-L1211)

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

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$object` |  | 
`$language_name` |  | 
`$personio_obj` |  | 
`$imports_obj` |  | 

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 373](PersonioIntegration/Imports/Api.php#L373-L373)

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 403](PersonioIntegration/Imports/Api.php#L403-L411)

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

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 436](PersonioIntegration/Imports/Api.php#L436-L444)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports/Api.php](PersonioIntegration/Imports/Api.php), [line 498](PersonioIntegration/Imports/Api.php#L498-L498)

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

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 203](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L203-L211)

### `personio_integration_light_import_of_url_starting`

*Set marker to check for timestamp and md5-hash-compare.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$check_for_changes` | `bool` | True to compare timestamp and md5-hash and skip unchanged data.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 217](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L217-L225)

### `personio_integration_import_header_status`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$http_status` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 261](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L261-L261)

### `personio_integration_light_xml_max_size`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$max_size` |  | 
`$url` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 295](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L295-L295)

### `personio_integration_import_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$run_import` |  | 
`$xml_object` |  | 
`$language_name` |  | 
`$personio_obj` |  | 
`$imports_obj` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 398](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L398-L398)

### `personio_integration_import_sleep_positions_limit`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`20` |  | 

Source: [app/PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php), [line 418](PersonioIntegration/Imports/Xml/Import_Single_Personio_Url.php#L418-L418)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 224](PersonioIntegration/Imports/Xml.php#L224-L233)

### `personio_integration_delete_single_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$do_delete` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 286](PersonioIntegration/Imports/Xml.php#L286-L286)

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

Source: [app/PersonioIntegration/Imports/Xml.php](PersonioIntegration/Imports/Xml.php), [line 440](PersonioIntegration/Imports/Xml.php#L440-L449)

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

Source: [app/PersonioIntegration/Api_Request.php](PersonioIntegration/Api_Request.php), [line 136](PersonioIntegration/Api_Request.php#L136-L142)

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

Source: [app/PersonioIntegration/Api_Request.php](PersonioIntegration/Api_Request.php), [line 171](PersonioIntegration/Api_Request.php#L171-L179)

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

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 190](PersonioIntegration/Tables/Extensions.php#L190-L196)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 229](PersonioIntegration/Tables/Extensions.php#L229-L229)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 267](PersonioIntegration/Tables/Extensions.php#L267-L267)

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

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 311](PersonioIntegration/Tables/Extensions.php#L311-L317)

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

Source: [app/PersonioIntegration/Tables/Extensions.php](PersonioIntegration/Tables/Extensions.php), [line 339](PersonioIntegration/Tables/Extensions.php#L339-L345)

### `personio_integration_admin_show_pro_hint`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$pro_hint` |  | 
`$true` |  | 

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 190](PersonioIntegration/Imports.php#L190-L190)

### `personio_integration_admin_show_pro_hint`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$pro_hint` |  | 
`$true` |  | 

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 193](PersonioIntegration/Imports.php#L193-L193)

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

Source: [app/PersonioIntegration/Imports.php](PersonioIntegration/Imports.php), [line 254](PersonioIntegration/Imports.php#L254-L260)

### `personio_integration_show_term_list`

*Filter whether to show terms of a single taxonomy as a list or not.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` | `bool` | True to show the list.
`$terms` | `\WP_Term[]\|false` | List of terms.

**Changelog**

Version | Description
------- | -----------
`3.0.8` | Available since 3.0.8.

Source: [app/PersonioIntegration/Widgets/Details.php](PersonioIntegration/Widgets/Details.php), [line 184](PersonioIntegration/Widgets/Details.php#L184-L192)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 103](PersonioIntegration/Position.php#L103-L112)

### `personio_integration_light_import_single_query_for_existing_position`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$params` |  | 
`$instance` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 135](PersonioIntegration/Position.php#L135-L135)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 138](PersonioIntegration/Position.php#L138-L148)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 170](PersonioIntegration/Position.php#L170-L180)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 212](PersonioIntegration/Position.php#L212-L220)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 429](PersonioIntegration/Position.php#L429-L437)

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

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 516](PersonioIntegration/Position.php#L516-L524)

### `personio_integration_get_personio_url`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$url` |  | 
`$instance` |  | 

Source: [app/PersonioIntegration/Position.php](PersonioIntegration/Position.php), [line 771](PersonioIntegration/Position.php#L771-L771)

### `personio_integration_light_position_availability_yes`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 255](PersonioIntegration/Availability.php#L255-L255)

### `personio_integration_light_position_availability_no`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$html` |  | 
`$position_obj` |  | 

Source: [app/PersonioIntegration/Availability.php](PersonioIntegration/Availability.php), [line 292](PersonioIntegration/Availability.php#L292-L292)

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
`$query` | `array<string,mixed>` | The configured query.
`$parameter_to_add` | `array<string,mixed>` | The parameter to filter for.

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 202](PersonioIntegration/Positions.php#L202-L210)

### `personio_integration_positions_loop_id`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$post_id` |  | 

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 245](PersonioIntegration/Positions.php#L245-L245)

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

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 276](PersonioIntegration/Positions.php#L276-L285)

### `personio_integration_positions_query`

*Filter the custom query for positions just before it is used.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$query` | `array<string,mixed>` | The configured query.
`array()` |  | 

**Changelog**

Version | Description
------- | -----------
`3.0.0` | Available since 3.0.0.

Source: [app/PersonioIntegration/Positions.php](PersonioIntegration/Positions.php), [line 350](PersonioIntegration/Positions.php#L350-L358)

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

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 208](PersonioIntegration/Extensions.php#L208-L215)

### `personio_integration_light_extension_state_changed_dialog`

*Filter the success dialog if state of extension has been changed.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$dialog` |  | 
`$obj` |  | 

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 340](PersonioIntegration/Extensions.php#L340-L343)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/Extensions.php](PersonioIntegration/Extensions.php), [line 642](PersonioIntegration/Extensions.php#L642-L642)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 394](PersonioIntegration/PostTypes/PersonioPosition.php#L394-L401)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 424](PersonioIntegration/PostTypes/PersonioPosition.php#L424-L431)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 454](PersonioIntegration/PostTypes/PersonioPosition.php#L454-L461)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 917](PersonioIntegration/PostTypes/PersonioPosition.php#L917-L924)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 969](PersonioIntegration/PostTypes/PersonioPosition.php#L969-L980)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 995](PersonioIntegration/PostTypes/PersonioPosition.php#L995-L1005)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1338](PersonioIntegration/PostTypes/PersonioPosition.php#L1338-L1345)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1446](PersonioIntegration/PostTypes/PersonioPosition.php#L1446-L1454)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 1987](PersonioIntegration/PostTypes/PersonioPosition.php#L1987-L1994)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2039](PersonioIntegration/PostTypes/PersonioPosition.php#L2039-L2047)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2051](PersonioIntegration/PostTypes/PersonioPosition.php#L2051-L2060)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2226](PersonioIntegration/PostTypes/PersonioPosition.php#L2226-L2232)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2278](PersonioIntegration/PostTypes/PersonioPosition.php#L2278-L2278)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2316](PersonioIntegration/PostTypes/PersonioPosition.php#L2316-L2316)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2343](PersonioIntegration/PostTypes/PersonioPosition.php#L2343-L2343)

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

Source: [app/PersonioIntegration/PostTypes/PersonioPosition.php](PersonioIntegration/PostTypes/PersonioPosition.php), [line 2471](PersonioIntegration/PostTypes/PersonioPosition.php#L2471-L2479)

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

Source: [app/Plugin/Email_Base.php](Plugin/Email_Base.php), [line 297](Plugin/Email_Base.php#L297-L303)

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

Source: [app/Plugin/Email_Base.php](Plugin/Email_Base.php), [line 401](Plugin/Email_Base.php#L401-L407)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 189](Plugin/Languages.php#L189-L196)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 245](Plugin/Languages.php#L245-L252)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 265](Plugin/Languages.php#L265-L272)

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

Source: [app/Plugin/Languages.php](Plugin/Languages.php), [line 293](Plugin/Languages.php#L293-L300)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 110](Plugin/Templates.php#L110-L117)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 152](Plugin/Templates.php#L152-L152)

### `personio_integration_set_template_directory`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$directory` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 191](Plugin/Templates.php#L191-L191)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 215](Plugin/Templates.php#L215-L222)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 251](Plugin/Templates.php#L251-L258)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 273](Plugin/Templates.php#L273-L280)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 406](Plugin/Templates.php#L406-L415)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 452](Plugin/Templates.php#L452-L461)

### `personio_integration_show_content`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 513](Plugin/Templates.php#L513-L513)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 584](Plugin/Templates.php#L584-L593)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 748](Plugin/Templates.php#L748-L756)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 771](Plugin/Templates.php#L771-L778)

### `personio_integration_light_allowed_html`

*Filter the allowed HTML for the output of Personio templates.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$allowed_tags` | `array<string,array<string,bool>>` | List of allowed tags with their attributes.

**Changelog**

Version | Description
------- | -----------
`6.0.0` | Available since 6.0.0.

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 913](Plugin/Templates.php#L913-L920)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 985](Plugin/Templates.php#L985-L992)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 1010](Plugin/Templates.php#L1010-L1016)

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

Source: [app/Plugin/Templates.php](Plugin/Templates.php), [line 1044](Plugin/Templates.php#L1044-L1051)

### `personio_integration_admin_settings_pages`

*Filter the list of option groups which could be saved with our own capability.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$settings_pages` | `array<int,string>` | List of option groups.

**Changelog**

Version | Description
------- | -----------
`6.0.0` | Documented since 6.0.0 (the hook itself exists since earlier versions).

Source: [app/Plugin/Roles.php](Plugin/Roles.php), [line 123](Plugin/Roles.php#L123-L129)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 129](Plugin/Setup.php#L129-L135)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 205](Plugin/Setup.php#L205-L212)

### `personio_integration_light_transient_title`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`Helper::get_plugin_name()` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 222](Plugin/Setup.php#L222-L222)

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

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 310](Plugin/Setup.php#L310-L316)

### `personio_integration_setup_process_completed_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$completed_text` |  | 
`$config_name` |  | 

Source: [app/Plugin/Setup.php](Plugin/Setup.php), [line 488](Plugin/Setup.php#L488-L488)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 165](Plugin/Schedules.php#L165-L172)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 190](Plugin/Schedules.php#L190-L198)

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

Source: [app/Plugin/Schedules.php](Plugin/Schedules.php), [line 392](Plugin/Schedules.php#L392-L401)

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

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 222](Plugin/Init.php#L222-L228)

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

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 344](Plugin/Init.php#L344-L350)

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

Source: [app/Plugin/Init.php](Plugin/Init.php), [line 376](Plugin/Init.php#L376-L382)

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

Source: [app/Plugin/Emails.php](Plugin/Emails.php), [line 132](Plugin/Emails.php#L132-L138)

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

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 92](Plugin/Schedules_Base.php#L92-L99)

### `personio_integration_light_schedule_start_time`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$start` |  | 
`$interval` |  | 
`$instance` |  | 

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 172](Plugin/Schedules_Base.php#L172-L172)

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

Source: [app/Plugin/Schedules_Base.php](Plugin/Schedules_Base.php), [line 292](Plugin/Schedules_Base.php#L292-L302)

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
`$url` | `string` | The URL to use as the forward target.

**Changelog**

Version | Description
------- | -----------
`5.0.0` | Available since 5.0.0.

Source: [app/Plugin/License.php](Plugin/License.php), [line 633](Plugin/License.php#L633-L639)

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

Source: [app/Plugin/License.php](Plugin/License.php), [line 714](Plugin/License.php#L714-L720)

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

Source: [app/Plugin/License.php](Plugin/License.php), [line 745](Plugin/License.php#L745-L751)

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

Source: [app/Plugin/Compatibilities.php](Plugin/Compatibilities.php), [line 167](Plugin/Compatibilities.php#L167-L174)

### `personio_integration_site_health_endpoints`

*Filter the endpoints for Site Health this plugin is using.*

Hint: these are just arrays that define the endpoints.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$list` | `array<int,array<string,mixed>>` | List of endpoints.

Source: [app/Plugin/Admin/Site_Health.php](Plugin/Admin/Site_Health.php), [line 83](Plugin/Admin/Site_Health.php#L83-L90)

### `personio_integration_pro_hint_text`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$text` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 271](Plugin/Admin/Admin.php#L271-L271)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 490](Plugin/Admin/Admin.php#L490-L490)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 555](Plugin/Admin/Admin.php#L555-L555)

### `personio_integration_light_show_admin_bar_menu`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$true` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 595](Plugin/Admin/Admin.php#L595-L595)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 707](Plugin/Admin/Admin.php#L707-L707)

### `personio_integration_hide_pro_hints`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$false` |  | 

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 826](Plugin/Admin/Admin.php#L826-L826)

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

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 902](Plugin/Admin/Admin.php#L902-L909)

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

Source: [app/Plugin/Admin/Admin.php](Plugin/Admin/Admin.php), [line 1065](Plugin/Admin/Admin.php#L1065-L1073)

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

### `personio_integration_ability_position_data`

*Filter the data of a position, which is returned by the abilities.*

Only add data, which is visible in the frontend anyway.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$data` | `array<string,mixed>` | The data.
`$position_obj` | `\PersonioIntegrationLight\PersonioIntegration\Position` | The position.
`$with_description` | `bool` | True if the details are requested.

**Changelog**

Version | Description
------- | -----------
`5.3.0` | Available since 5.3.0.

Source: [app/Abilities/Abilities.php](Abilities/Abilities.php), [line 677](Abilities/Abilities.php#L677-L688)

### `personio_integration_template_ability_adapters`

*Filter the list of adapters, which make templates of page builders accessible for abilities.*

Each entry must be an object based on \PersonioIntegrationLight\Abilities\Template_Adapter_Base.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$adapters` | `array<int,\PersonioIntegrationLight\Abilities\Template_Adapter_Base>` | List of adapters.

**Changelog**

Version | Description
------- | -----------
`5.3.0` | Available since 5.3.0.

Source: [app/Abilities/Template_Abilities.php](Abilities/Template_Abilities.php), [line 86](Abilities/Template_Abilities.php#L86-L94)

### `personio_integration_template_ability_values`

*Filter the allowed values for the attributes of the elements in templates.*

Each entry is a list of "name" (the value to use) and "label".

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$values` | `array<string,array<int,array<string,string>>>` | The list of values.

**Changelog**

Version | Description
------- | -----------
`5.3.0` | Available since 5.3.0.

Source: [app/Abilities/Template_Abilities.php](Abilities/Template_Abilities.php), [line 1003](Abilities/Template_Abilities.php#L1003-L1011)

### `personio_integration_ability_import_blockers`

*Filter the reasons why the import of positions can not be started via abilities.*

Add a text for each reason. The import is only started if this list is empty.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$blockers` | `array<int,string>` | The list of reasons.
`$imports_obj` | `\PersonioIntegrationLight\PersonioIntegration\Imports_Base\|false` | The import object, false if no import extension is enabled.

**Changelog**

Version | Description
------- | -----------
`5.8.0` | Available since 5.8.0.

Source: [app/Abilities/Import_Abilities.php](Abilities/Import_Abilities.php), [line 390](Abilities/Import_Abilities.php#L390-L399)

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

Source: [app/Log_Table.php](Log_Table.php), [line 313](Log_Table.php#L313-L319)

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

Source: [app/Log_Table.php](Log_Table.php), [line 372](Log_Table.php#L372-L377)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

