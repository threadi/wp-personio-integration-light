<?php

/**
 * Selects and show the group-title if list is grouped by a taxonomy
 */

use personioIntegration\helper;

// only if group by is set
if( !empty($personio_attributes['groupby']) ) {
    // get the title of the given grouped taxonomy of this position
    $newGroupTitle = helper::get_taxonomy_name_of_position($personio_attributes['groupby'], $position);

    // output title if if has been changed during the loop
    if (strcmp($newGroupTitle, $groupTitle)) {
        $groupTitle = $newGroupTitle;
        echo '<h2>' . $newGroupTitle . '</h2>';
    }
}