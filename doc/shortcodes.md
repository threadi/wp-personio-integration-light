# Shortcodes

The following shortcodes can be used to output open positions imported into WordPress.

Except for one, all parameters at both shortcodes are optional. If a parameter is not specified, the template settings from the plugin settings will apply.

## for a list view

Structure of the shortcode:

`[personioPositions lang="en" showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" templates="title,excerpt" excerpt="recruitingCategory,schedule" sort="asc" sortby="title"]`

### Hint

This plugin restrict the number of entries to max. 10.
The Pro-version does not have such restriction.

## for a single view

`[personioPosition lang="en" templates="title,content,form" personioid="42"]`

## parameters

The parameters in the shortcodes have the following tasks:

### lang

* specifies the output language
* must be a language supported by the plugin
* is specified as a 2-character value, e.g. "it" for Italian
* example:
  `[personioPositions lang="en"]`

### listing_template

* set the listing-template to use

### showfilter

* only available for list view
* value 1 to activate filter view
* value 0 to disable filter view
* which contents the filter has is defined in the "filter" parameter

### filter

* only available for list view
* defines which filters are displayed
* available values are specified comma separated:
  * office
  * recruitingCategory
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * experience
  * schedule

### filtertype

* only available for list view
* defines how the filter view is displayed
* only one of the following values may be specified:
  * linklist
  * selectbox
  * linklist_without_categories (only Pro)

### templates

* specifies the structure of the template for a position
* available values are specified comma separated:
  * title => for the title of the position
  * excerpt => for the list of categories the position is assigned to
  * content => for the complete description text of the position
  * content_part => for a part of the description text of the position
  * form => for the link to the application form
  * meta => available only in Pro version, if SEO is enabled

### excerpt

* defines which categories will be displayed as excerpt at the position
* available values are specified comma separated:
  * office
  * recruitingCategory
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### jobdescription_template

* defines the template for the description of a position
* more info here: [description template](description-template.md)

### jobdescription_part

* defines which part of the description text should be used by template _content_part_
* number between 0 and max. of description parts

### sort

* only available for list view
* sets the sort order for the positions in the list
* only one of the following values may be specified:
  * asc => for ascending
  * desc => for descending

### sortby

* only available for list view
* specifies by which value the list of positions should be sorted
* only one of the following values may be specified:
  * title => by the title of the position
  * date => by the date when the position was imported into Wordpress

### ids

* only available for list view
* restricts the view to the specified PersonioIDs
* which IDs are available can be seen in the list "open positions" in the column "PersonioId

### personioid

* only mandatory field for shortcodes
* only available for detail view
* determines which specific position is displayed
* which IDs are available can be seen in the "open positions" list in the "PersonioId" column

### groupby

* only available for list view
* groups the list of positions by one of the properties
* available values are:
  * office
  * recruitingCategory
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### limit

* only available for list view
* limits the list to the specified number

### show only positions with specific attributes

* only available for list view
* Use cases:
  * Display positions from one department only
  * show only full time positions
* the property must be specified as a parameter, as a value the database ID of the desired value must be specified
* Example:
  `[personioPositions department="42"]`
* the database ID can be determined in the following way:
  1. set up a list with filter.
  2. at the filter add the property you are looking for, e.g. "department".
  3. look at the list in the browser and filter by the searched department.
  4. in the URL you will see both the parameter and the value, for example: `?personiofilter[department]=42`.
  5. copy the value in brackets and the number behind them to put them together in the shortcode as shown in the above example.

### more options

* anchor
* link_to_anchor

## Examples

### list without filter & with title & description text per position

`[personioPositions templates="title,content"]`

### List with filter & title & excerpt per position

`[personioPositions showfilter="1" filter="recruitingCategory,schedule" templates="title,excerpt"]`

### Single view of a position with title, content & application link

`[personioPosition personioid="42" templates="title,content,form"]`
