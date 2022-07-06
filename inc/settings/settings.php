<?php

use personioIntegration\Logs;

/**
 * Add settings for admin-page via custom hook.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings() {
    do_action('personio_integration_settings_add_settings');
}
add_action( 'admin_init', 'personio_integration_admin_add_settings' );

/**
 * Add settings-page for the plugin.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_menu() {
    add_submenu_page(
        'edit.php?post_type='.WP_PERSONIO_INTEGRATION_CPT,
        __( 'Personio Integration Settings', 'wp-personio-integration' ),
        __( 'Settings', 'wp-personio-integration' ),
        'manage_options',
        'personioPositions',
        'personio_integration_admin_add_settings_content',
        1
    );
}
add_action( 'admin_menu', 'personio_integration_admin_add_settings_menu' );

/**
 * Create the admin-page with tab-navigation.
 *
 * @return void
 */
function personio_integration_admin_add_settings_content() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // get the active tab from the $_GET param
    $default_tab = null;
    $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <nav class="nav-tab-wrapper">
            <a href="?post_type=personioposition&page=personioPositions" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php echo __('General Settings', 'wp-personio-integration'); ?></a>
            <?php
            // only show all options if Personio URL is available
            if( get_option('personioIntegrationUrl', false) ) {
                    do_action('personio_integration_settings_add_tab', $tab);
                }
            else {
                ?>
                    <span class="nav-tab"><?php echo __('Enter Personio URL to get more options', 'wp-personio-integration'); ?></span>
                <?php
            }
            ?>
        </nav>

        <div class="tab-content">
            <?php
                // get the content of the actual tab
                do_action('personio_integration_settings_'.($tab == null ? 'general' : $tab).'_page');
            ?>
        </div>
    </div>
    <?php
}

/**
 * Add tab in settings for logs.
 *
 * @param $tab
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_logs_tab( $tab ): void
{
    // check active tab
    $activeClass = '';
    if( $tab === 'logs' ) $activeClass = ' nav-tab-active';

    // output tab
    echo '<a href="?post_type='.WP_PERSONIO_INTEGRATION_CPT.'&page=personioPositions&tab=logs" class="nav-tab'.$activeClass.'">'._x('Logs', 'wp-personio-integration').'</a>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_logs_tab', 60, 1 );

/**
 * Show log as list.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_logs()
{
    // if WP_List_Table is not loaded automatically, we need to load it
    if( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
    $log = new Logs();
    $log->prepare_items();
    ?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2><?php echo __('Logs', 'wp-personio-integration'); ?></h2>
                <?php $log->display(); ?>
            </div>
    <?php
}
add_action('personio_integration_settings_logs_page', 'personio_integration_admin_add_menu_content_logs' );

/**
 * Define an input-number-field.
 *
 * @param $attr
 * @return void
 */
function personio_integration_admin_number_field( $attr ) {
    if( !empty($attr['fieldId']) ) {
        // get value from config
        $value = get_option($attr['fieldId'], '');

        // or get if from request
        if( isset($_POST[$attr['fieldId']]) ) {
            $value = sanitize_text_field($_POST[$attr['fieldId']]);
        }

        // readonly
        $readonly = '';
        if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
            $readonly = ' disabled="disabled"';
        }

        // get title
        $title = '';
        if( isset($attr['title']) ) {
            $title = $attr['title'];
        }

        ?>
            <input type="number" id="<?php echo $attr['fieldId']; ?>" name="<?php echo $attr['fieldId']; ?>" value="<?php echo $value; ?>" class="personio-field-width"<?php echo $readonly; ?> title="<?php echo $title; ?>">
        <?php
        if( !empty($attr['description']) ) {
            echo "<p>".$attr['description']."</p>";
        }
    }
}

/**
 * Define an input-text-field.
 *
 * @param $attr
 * @return void
 */
function personio_integration_admin_text_field( $attr ) {
    if( !empty($attr['fieldId']) ) {
        // get value from config
        $value = get_option($attr['fieldId'], '');

        // get value from request
        if( isset($_POST[$attr['fieldId']]) ) {
            $value = sanitize_text_field($_POST[$attr['fieldId']]);
        }

        // get placeholder
        $placeholder = !empty($attr['placeholder']) ? ' placeholder="'.$attr['placeholder'].'"' : '';

        // get title
        $title = '';
        if( isset($attr['title']) ) {
            $title = $attr['title'];
        }

        // readonly
        $readonly = '';
        if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
            $readonly = ' disabled="disabled"';
        }

        if( isset($attr['highlight']) && false !== $attr['highlight'] ) {
            ?><div class="highlight"><?php
        }

        // output
        ?>
        <input type="text" id="<?php echo $attr['fieldId']; ?>" name="<?php echo $attr['fieldId']; ?>" value="<?php echo $value; ?>"<?php echo $placeholder.$readonly; ?> class="widefat" title="<?php echo $title; ?>">
        <?php
        if( !empty($attr['description']) ) {
            echo "<p>".$attr['description']."</p>";
        }

        if( isset($attr['highlight']) && false !== $attr['highlight'] ) {
            ?></div><?php
        }
    }
}

/**
 * Define an input-checkbox-field.
 *
 * @param $attr
 * @return void
 */
function personio_integration_admin_checkbox_field( $attr ) {
    if( !empty($attr['fieldId']) ) {
        // get check state
        $checked = (get_option($attr['fieldId'], 0) == 1 || ( isset($_POST[$attr['fieldId']]) && absint($_POST[$attr['fieldId']]) == 1 ) ) ? ' checked="checked"' : '';

        // get title
        $title = '';
        if( isset($attr['title']) ) {
            $title = $attr['title'];
        }

        // readonly
        $readonly = '';
        if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
            $readonly = ' disabled="disabled"';
        }

        ?>
        <input type="checkbox" id="<?php echo $attr['fieldId']; ?>" name="<?php echo $attr['fieldId']; ?>" value="1"<?php echo $checked.$readonly; ?> class="personio-field-width" title="<?php echo $title; ?>">
        <?php

        // show optional description for this checkbox
        if( !empty($attr['description']) ) {
            echo "<p>".$attr['description']."</p>";
        }

        // show optional hint for our Pro-version
        if( !empty($attr['pro_hint']) ) {
            do_action('personio_integration_admin_show_pro_hint', $attr['pro_hint']);
        }
    }
}

/**
 * Validate any checkbox-values
 *
 * @param $value
 * @return int
 * @noinspection PhpUnused
 */
function personio_integration_admin_validateCheckBox( $value ): int
{
    return absint($value);
}

/**
 * Show select-field with given values.
 *
 * @return void
 */
function personio_integration_admin_select_field( $attr ) {
    if( !empty($attr['fieldId']) && !empty($attr['values']) ) {
        // get value from config
        $value = get_option($attr['fieldId'], '');

        // or get it from request
        if( isset($_POST[$attr['fieldId']]) ) {
            $value = sanitize_text_field($_POST[$attr['fieldId']]);
        }

        // get title
        $title = '';
        if( isset($attr['title']) ) {
            $title = $attr['title'];
        }

        // readonly
        $readonly = '';
        if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
            $readonly = ' disabled="disabled"';
        }

        ?>
        <select id="<?php echo $attr['fieldId']; ?>" name="<?php echo $attr['fieldId']; ?>" class="personio-field-width"<?php echo $readonly; ?> title="<?php echo $title; ?>">
            <option value=""></option>
            <?php
            foreach( $attr['values'] as $key => $schedule ) {
                $selected = $value == $key ? ' selected="selected"' : '';
                ?><option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $schedule; ?></option><?php
            }
            ?></select>
        <?php
        if( !empty($attr['description']) ) {
            echo "<p>".$attr['description']."</p>";
        }
    }
    elseif( empty($attr['values']) && !empty($attr['noValues']) ) {
        echo "<p>".$attr['noValues']."</p>";
    }
}

/**
 * Show multiselect-field with given values.
 *
 * @return void
 */
function personio_integration_admin_multiselect_field( $attr ) {
    if( !empty($attr['fieldId']) && !empty($attr['values']) ) {
        // get value from config
        $actualValues = get_option($attr['fieldId'], []);

        // or get them from request
        if( isset($_POST[$attr['fieldId']]) && is_array($_POST[$attr['fieldId']]) ) {
            $actualValues = [];
            foreach( $_POST[$attr['fieldId']] as $key => $item ) {
                $actualValues[absint($key)] = sanitize_text_field($item);
            }
        }

        // readonly
        $readonly = '';
        if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
            $readonly = ' disabled="disabled"';
        }

        // get title
        $title = '';
        if( isset($attr['title']) ) {
            $title = $attr['title'];
        }

        ?>
            <select id="<?php echo $attr['fieldId']; ?>" name="<?php echo $attr['fieldId']; ?>[]" multiple class="personio-field-width"<?php echo $readonly; ?> title="<?php echo $title; ?>">
                <?php
                foreach( $attr['values'] as $key => $schedule ) {
                    $selected = in_array($key, $actualValues) ? ' selected="selected"' : '';
                    ?><option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $schedule; ?></option><?php
                }
            ?></select>
        <?php
        if( !empty($attr['description']) ) {
            echo "<p>".$attr['description']."</p>";
        }
    }
}