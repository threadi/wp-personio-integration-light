# Shortcodes

The following shortcodes can be used to output the open positions imported into WordPress and their data.

Shortcodes should only be used if your theme or page builder is not supported by the plugin. With the Block Editor or a
page builder supported by _Personio Integration Pro_ you can use the widgets of the plugin instead.

## Overview

| Shortcode                                                                         | Purpose                                                | Available in |
|-----------------------------------------------------------------------------------|--------------------------------------------------------|--------------|
| [`[personioPositions]`](#list-of-positions-personiopositions)                     | list of positions incl. filter                         | Light & Pro  |
| [`[personioPosition]`](#single-position-personioposition)                         | single view of a position                              | Light & Pro  |
| [`[personio_integration_widget_application_button]`](#application-button)        | button to apply for a position                         | Light & Pro  |
| [`[personio_integration_widget_description]`](#description)                       | description of a position                              | Light & Pro  |
| [`[personio_integration_widget_details]`](#details)                               | details (e.g. office, department) of a position        | Light & Pro  |
| [`[personio_integration_widget_description_part]`](#description-part)             | a single part of the description of a position         | Pro          |
| [`[personio_integration_widget_field]`](#field)                                   | a single field of a position                           | Pro          |
| [`[personio_integration_widget_salary]`](#salary)                                 | salary of a position                                   | Pro          |
| [`[personio_integration_widget_files]`](#files)                                   | files of a position                                    | Pro          |
| [`[personio_integration_widget_videos]`](#videos)                                 | videos of a position                                   | Pro          |
| [`[personio_integration_widget_contact_person]`](#contact-person)                 | contact person of a position                           | Pro          |
| [`[personio_integration_widget_map]`](#map)                                       | map with the offices of the positions                  | Pro          |
| [`[personio_integration_widget_position_count]`](#position-count)                 | number of positions                                    | Pro          |
| [`[personio_integration_widget_term_count]`](#term-count)                         | number of entries in a taxonomy                        | Pro          |
| [`[personio_integration_widget_general_application_form]`](#general-application-form) | general application form, independent of a position | Pro          |
| [`[personio_integration_gravityform]`](#gravity-forms-form)                       | Gravity Forms form for an application                  | Pro          |

`[personio_integration_widget_archive]` is an alias of `[personioPositions]`, `[personio_integration_widget_single]` is an alias of `[personioPosition]`.
Both use exactly the same attributes.

## General hints

* All attributes are optional, unless marked as _required_.
* If an attribute is not set, the corresponding setting of the plugin under _Positions > Settings > Templates_ applies.
* Lists of values are separated by comma, e.g. `templates="title,excerpt"`.
* Attribute names are not case-sensitive. WordPress converts them to lowercase.
* Attributes marked with _(Pro)_ only work with an active license of _Personio Integration Pro_.
* Unknown attributes are ignored.
* The shortcodes of the Pro widgets are only available if the respective widget is enabled under _Positions > Extensions_.
* _Personio Integration Pro_ contains a shortcode generator, which helps to create the shortcodes with all options.
  Its tab _Widget Shortcodes_ lists all widget shortcodes with the values possible in your project.

## List of positions: personioPositions

Outputs a list of positions. Optionally with a filter above the list.

Structure of the shortcode:

`[personioPositions lang="en" showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" templates="title,excerpt" excerpt="recruitingCategory,schedule" sort="asc" sortby="title"]`

### Hint

_Personio Integration Light_ restricts the number of entries to max. 10.
_Personio Integration Pro_ does not have such restriction and supports pagination.

### Attributes for the content

#### lang

* specifies the output language
* must be a language supported by the plugin, otherwise the fallback language is used
* is specified as a 2-character value, e.g. `en` for English
* example: `[personioPositions lang="en"]`

#### templates

* specifies which parts are shown for each position and in which order
* available values, comma separated:
  * `title` => title of the position
  * `excerpt` => details of the position (e.g. office, department), see attribute `excerpt`
  * `content` => complete description of the position
  * `formular` => link or button to apply
  * `aboutus` => "about us" text of the company _(Pro)_
  * `content_part` => a single part of the description, see attribute `jobdescription_part` _(Pro)_
  * `files` => files of the position _(Pro, if the extension for files is enabled)_
  * `videos` => videos of the position _(Pro, if the extension for videos is enabled)_
  * `comments` => comments of the position _(Pro, if the extension for comments is enabled)_
* example: `[personioPositions templates="title,excerpt,formular"]`

#### listing_template

* sets the template for the list
* available values:
  * `default`
  * `listing`
  * `accordion` _(Pro)_
  * `tile` _(Pro)_
  * `table` _(Pro)_
  * `popup` _(Pro)_
* example: `[personioPositions listing_template="tile"]`

#### excerpt

* defines which details are shown at each position, if `excerpt` is used in `templates`
* available values, comma separated: the slugs of the taxonomies, see [taxonomies](#taxonomies)
* example: `[personioPositions templates="title,excerpt" excerpt="office,employmenttype"]`

#### excerpt_template

* sets the template for the details
* available values:
  * `default`
  * `list`
  * `extended_list` _(Pro)_

#### jobdescription_template

* sets the template for the description, if `content` is used in `templates`
* available values:
  * `default`
  * `list`
* more info here: [description template](description-template.md)

#### jobdescription_part

* only available with _Personio Integration Pro_
* defines which part of the description is shown, if `content_part` is used in `templates`
* the value is the identifier of the part (a hash value)
* the possible values of your project are listed in the shortcode generator in the tab _Widget Shortcodes_ at
  `[personio_integration_widget_description_part]`

#### jobdescription_part_template

* only available with _Personio Integration Pro_
* sets the template for the part of the description
* available values:
  * `default`
  * `h2`
  * `h4`
  * `without_heading`

#### donotlink

* value `1` => the titles of the positions are not linked to their detail page
* value `0` => the titles are linked
* example: `[personioPositions donotlink="1"]`

### Attributes for the selection and order

#### ids

* restricts the list to the given Personio IDs, comma separated
* which IDs are available can be seen in the list "Positions" in the column "PersonioID"
* example: `[personioPositions ids="42,43"]`

#### limit

* limits the list to the given number of positions
* `0` for no limit (in _Personio Integration Light_ max. 10)
* example: `[personioPositions limit="5"]`

#### sort

* sort order of the list
* available values:
  * `asc` => ascending
  * `desc` => descending

#### sortby

* the value the list is sorted by
* available values:
  * `title` => by the title of the position
  * `date` => by the date the position was imported into WordPress

#### groupby

* groups the list by a taxonomy
* available values: one of the slugs of the taxonomies, see [taxonomies](#taxonomies)
* example: `[personioPositions groupby="office"]`

#### Show only positions with specific properties

* use cases:
  * show only positions of one department
  * show only full-time positions
* the slug of the taxonomy is used as attribute name, the ID of the desired entry as value
* example: `[personioPositions department="42"]`
* _(Pro)_ multiple IDs can be specified, comma separated, e.g. `[personioPositions office="12,34"]`
* several taxonomies can be combined, e.g. `[personioPositions department="42" employmenttype="7"]`
* if a visitor uses the filter for the same taxonomy, the filter replaces this restriction
* the ID can be determined in the following way:
  1. set up a list with filter.
  2. add the taxonomy you are looking for to the filter, e.g. "department".
  3. look at the list in the browser and filter by the desired entry.
  4. in the URL you will see the taxonomy and the ID, for example: `?personiofilter[department]=42`.
  5. use both in the shortcode as shown in the example above.

### Attributes for the filter

#### showfilter

* value `1` => show the filter above the list
* value `0` => hide the filter
* which filters are shown is defined in the attribute `filter`

#### filter

* defines which filters are shown
* available values, comma separated: the slugs of the taxonomies, see [taxonomies](#taxonomies)
* _(Pro)_ additionally `pi_places`, which combines main office and additional offices in one filter
* _(Pro)_ the filters are shown in the order defined in the settings
* example: `[personioPositions showfilter="1" filter="recruitingCategory,office"]`

#### filtertype

* defines how the filter is displayed
* only one of the following values may be specified:
  * `linklist` => list of links, grouped by taxonomy
  * `select` => select boxes with a button to search
  * `linklist_without_categories` => list of links without headings _(Pro)_
  * `search` => search field, the attribute `filter` is ignored _(Pro)_
  * `circle_search` => search in a radius around a location, the attribute `filter` is ignored _(Pro, requires a
    configured geo service)_
* if an invalid value is specified, `linklist` is used

#### filtercombine

* only available with _Personio Integration Pro_
* value `AND` => the values chosen in different filters are combined, e.g. "full-time" and "Berlin" shows only
  full-time positions in Berlin
* default: empty
* see [combination of filters](#combination-of-filters)
* example: `[personioPositions showfilter="1" filter="employmenttype,office" filtertype="select" without_reload="1" filtercombine="AND"]`

#### without_reload

* only available with _Personio Integration Pro_
* value `1` => the filter shows or hides the positions directly in the browser without reloading the page
* only the positions already loaded on the page are filtered
* see [combination of filters](#combination-of-filters)
* example: `[personioPositions showfilter="1" filter="office" without_reload="1"]`

#### Combination of filters

How the values of multiple filters work together depends on the filter type and the attribute `without_reload`:

| Filter                                          | Behavior without `filtercombine`                      | Behavior with `filtercombine="AND"`                          |
|-------------------------------------------------|-------------------------------------------------------|--------------------------------------------------------------|
| `select` with reload                            | all chosen values are combined                        | all chosen values are combined                               |
| `linklist` with reload                          | the values of the links clicked one after another are combined | the values of the links clicked one after another are combined |
| `linklist_without_categories` with reload _(Pro)_ | only the last clicked value is used                 | the values of the links clicked one after another are combined |
| `select` without reload _(Pro)_                 | only the last chosen value is used, the other select boxes are reset | all chosen values are combined                |
| `linklist` / `linklist_without_categories` without reload _(Pro)_ | only the last clicked value is used | one value per taxonomy, all taxonomies are combined, a second click on a value deselects it |

Hints:

* with reload, the combination only works if all filters are part of the same shortcode
* combined means: only positions which match all chosen values are shown

### Attributes for the layout

#### anchor

* ID of the HTML element of the filter
* is generated automatically, if not specified

#### link_to_anchor

* ID of an HTML element on the page the browser jumps to after using the filter (without `#`)
* is generated automatically, if not specified
* example: `[personioPositions showfilter="1" filter="office" link_to_anchor="positions"]`

#### classes

* additional CSS classes for the output, separated by space

## Single position: personioPosition

Outputs a single position.

Structure of the shortcode:

`[personioPosition lang="en" templates="title,content,formular" personioid="42"]`

### Attributes

#### personioid

* _required_
* the Personio ID of the position to show
* which IDs are available can be seen in the list "Positions" in the column "PersonioID"

#### lang

* specifies the output language, see [lang](#lang)

#### templates

* specifies which parts of the position are shown and in which order
* available values: see [templates](#templates)

#### excerpt

* defines which details are shown, if `excerpt` is used in `templates`
* available values: see [excerpt](#excerpt)

#### excerpt_template

* sets the template for the details, see [excerpt_template](#excerpt_template)

#### jobdescription_template

* sets the template for the description, see [jobdescription_template](#jobdescription_template)

#### jobdescription_part _(Pro)_ and jobdescription_part_template _(Pro)_

* see [jobdescription_part](#jobdescription_part) and [jobdescription_part_template](#jobdescription_part_template)

#### donotlink

* value `0` => the title is linked to the detail page of the position
* default: `1` (not linked)

#### classes

* additional CSS classes for the output, separated by space

## Shortcodes for parts of a position

These shortcodes output a single part of a position. They are intended for the detail page of a position, e.g. in a
template of your theme or page builder. There they automatically use the position of the page.

### Application button

`[personio_integration_widget_application_button]`

* outputs the button to apply for the position
* attributes:
  * `personioid` => Personio ID of the position, if not used on the detail page of a position
  * `show_back_to_list` => value `0` hides the link "back to list" on the detail page
  * `classes` => additional CSS classes

### Description

`[personio_integration_widget_description]`

* outputs the description of the position
* attributes:
  * `personioid` => Personio ID of the position, if not used on the detail page of a position
  * `template` => template for the description, see [jobdescription_template](#jobdescription_template)

### Details

`[personio_integration_widget_details excerpt="office,department"]`

* outputs the details of the position, e.g. office or department
* attributes:
  * `excerpt` => the details to show, comma separated, see [taxonomies](#taxonomies); without this attribute nothing is shown
  * `excerpt_template` => template for the details, see [excerpt_template](#excerpt_template)
  * `separator` => separator between multiple values of one detail
  * `colon` => empty value (`colon=""`) hides the colon after the name of the detail
  * `line_break` => empty value (`line_break=""`) shows the details without line break
  * `personioid` => Personio ID of the position, if not used on the detail page of a position
  * `classes` => additional CSS classes

### Description part

`[personio_integration_widget_description_part jobdescription_part="..."]` _(Pro)_

* outputs a single part of the description of the position of the current page
* attributes:
  * `jobdescription_part` => identifier of the part, see [jobdescription_part](#jobdescription_part)
  * `jobdescription_part_template` => template, see [jobdescription_part_template](#jobdescription_part_template)

### Field

`[personio_integration_widget_field field="title"]` _(Pro)_

* outputs a single field of the position of the current page
* attributes:
  * `field` => _required_, one of these values:
    * `personioId` => the Personio ID
    * `title` => the title
    * `description` => the description

### Salary

`[personio_integration_widget_salary personioid="42"]` _(Pro)_

* outputs the salary of a position, if it is set in Personio
* attributes:
  * `personioid` => _required_, Personio ID of the position
  * `template` => template for the output, value: `default`

### Files

`[personio_integration_widget_files]` _(Pro)_

* outputs the files of the position of the current page
* requires the enabled extension for files
* does not have any attributes

### Videos

`[personio_integration_widget_videos]` _(Pro)_

* outputs the videos of the position of the current page
* requires the enabled extension for videos
* does not have any attributes

### Contact person

`[personio_integration_widget_contact_person]` _(Pro)_

* outputs the contact person of the position of the current page
* requires the enabled extension for contact persons
* attributes:
  * `template` => template for the output, value: `default`
  * `classes` => additional CSS classes

## Further shortcodes

### Map

`[personio_integration_widget_map]` _(Pro)_

* outputs a map with the offices of the positions
* requires a configured geo service
* attributes:
  * `zoom` => zoom level as number between 1 and 20, default: 6
  * restriction to entries of a taxonomy: the slug of the taxonomy as attribute name, the IDs as value, comma
    separated, e.g. `[personio_integration_widget_map department="42"]` (only with OpenStreetMap)

### Position count

`[personio_integration_widget_position_count]` _(Pro)_

* outputs the number of positions
* does not have any attributes

### Term count

`[personio_integration_widget_term_count taxonomy="office"]` _(Pro)_

* outputs the number of entries of a taxonomy, e.g. the number of offices
* attributes:
  * `taxonomy` => _required_, slug of the taxonomy, see [taxonomies](#taxonomies)

### General application form

`[personio_integration_widget_general_application_form]` _(Pro)_

* outputs the application form for applications, which are not related to a specific position
* requires the enabled extension for applications
* does not have any attributes

### Gravity Forms form

`[personio_integration_gravityform id="12"]` _(Pro)_

* outputs a form of Gravity Forms and assigns the applications to the position of the current page
* requires the plugin Gravity Forms
* attributes:
  * `id` => _required_, ID of the form in Gravity Forms

## Taxonomies

The following slugs can be used in the attributes `filter`, `excerpt` and `groupby`, as attribute name to restrict lists
and in the attribute `taxonomy` of `[personio_integration_widget_term_count]`:

| Slug                 | Name in the plugin    | Hint                                           |
|----------------------|-----------------------|------------------------------------------------|
| `recruitingCategory` | Categories            |                                                |
| `occupation`         | Job types             |                                                |
| `occupation_detail`  | Job type details      |                                                |
| `office`             | Main workplaces       |                                                |
| `department`         | Departments           |                                                |
| `employmenttype`     | Employment types      |                                                |
| `seniority`          | Experiences           |                                                |
| `schedule`           | Contract types        |                                                |
| `experience`         | Years of experiences  |                                                |
| `keyword`            | Keywords              |                                                |
| `language`           | Languages             | not usable as filter                           |
| `subcompany`         | Legal entities        | _(Pro)_                                        |
| `additionaloffices`  | Other workplaces      | _(Pro)_                                        |
| `pi_places`          | Places                | _(Pro)_, main and other workplaces, only usable as filter |

## Examples

### List without filter, with title & description per position

`[personioPositions templates="title,content"]`

### List with filter, title & details per position

`[personioPositions showfilter="1" filter="recruitingCategory,schedule" templates="title,excerpt"]`

### List of the positions of one department, grouped by office

`[personioPositions department="42" groupby="office" templates="title,excerpt" excerpt="employmenttype"]`

### List with combined filter without reload _(Pro)_

`[personioPositions listing_template="tile" showfilter="1" filter="recruitingCategory,pi_places" filtertype="select" without_reload="1" filtercombine="AND" templates="title,excerpt"]`

### Single view of a position with title, description & application link

`[personioPosition personioid="42" templates="title,content,formular"]`
