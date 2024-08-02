# Create an individual extension

## Objective

This documentation describes how you can develop your own extension for Personio Integration. This allows you to
extend the options that the plugin offers with regard to data from positions to include individual additions.

This extension is not about influencing styles or the behavior of the plugin. For the latter, you can
use the [hooks](hooks.md).

## Why an extension

Extensions have the advantage that the editor can activate and deactivate them at any time. In addition,
all functions of the Personio Integration Light plugin are available within extensions.

### Note on the Pro plugin

Of course, if the Pro plugin is available and licensed, you can also access functions from it in your own extension.

## Requirements

* Knowledge of PHP required
* Knowledge of writing WordPress plugins recommended

## Preparations

* Check whether your requirement is not already met by the plugin or extensions of others
* Check whether your requirement relates to Personio positions

## Procedure

1. First create your own WordPress plugin, see: https://developer.wordpress.org/plugins/
2. Create a file in it whose class serves as an object for your extension. You can find a template for this here:
3. Use the hook 'personio_integration_extend_position_object' to add your extension to the list of available extensions.

After these steps, your extension can be seen in the list under Positions > Extensions.

## Mapping functionality

Within your own class, you add your individual additions in the init() function. It is best to use hooks from
Personio Integration Light or WordPress' own hooks.
