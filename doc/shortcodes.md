# Shortcodes

The following shortcodes can be used to output open positions imported into WordPress.

Except for one, all parameters at both shortcodes are optional. If a parameter is not specified, the template settings from the plugin settings will apply.

## for a list view

Structure of the shortcode:

`[personioPositions lang="en" showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" templates="title,excerpt" excerpt="recruitingCategory,schedule" sort="asc" sortby="title"]`

## for a single view

`[personioPosition lang="en" templates="title,content,form" id="42"]`

## parameters

The parameters in the shortcodes have the following tasks:

### lang

* specifies the output language
* must be a language supported by the plugin
* is specified as a 2-character value, e.g. "it" for Italian
* example:
  `[personioPositions lang="en"]`

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
  * recruitingCategory => for the category
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

### templates

* specifies the structure of the template for a job
* available values are specified comma separated:
  * title => for the title of the job
  * excerpt => for the list of categories the job is assigned to
  * content => for the description text of the job
  * form => for the link to the application form
  * meta => available only in Pro version, if SEO is enabled

### excerpt

* defines which categories will be displayed as excerpt at the position
* available values are specified comma separated:
  * office
  * recruitingCategory => for the category
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### sort

* only available for list view
* sets the sort order for the jobs in the list
* only one of the following values may be specified:
  * asc => for ascending
  * desc => for descending

### sortby

* only available for list view
* specifies by which value the list of jobs should be sorted
* only one of the following values may be specified:
  * title => by the title of the job
  * date => by the date when the job was imported into Wordpress

### ids

* only available for list view
* restricts the view to the specified PersonioIDs
* which IDs are available can be seen in the list "open jobs" in the column "PersonioId

### personioid

* only mandatory field for shortcodes
* only available for detail view
* determines which specific job is displayed
* which IDs are available can be seen in the "open jobs" list in the "PersonioId" column

## Examples

### list without filter & with title & description text per position

`[personioPositions templates="title,content"]`

### List with filter & title & excerpt per position

`[personioPositions showfilter="1" filter="recruitingCategory,schedule" templates="title,excerpt"]`

### Single view of a position with title, content & application link

`[personioPosition id="42" templates="title,content,form"]`