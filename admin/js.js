jQuery(document).ready(function($){
    // add option near to list-headline
    $('body.post-type-personioposition.personio-integration-free:not(.edit-tags-php) h1.wp-heading-inline').after('<a class="page-title-action personio-integration-import-hint personio-pro-hint" href="' + customJsVars.pro_url + '" target="_blank">' + customJsVars.label_go_pro + '</a>');
    $('body.post-type-personioposition:not(.edit-tags-php):not(.personio-integration-url-missing) h1.wp-heading-inline').after('<a class="page-title-action personio-integration-import-hint" href="admin.php?action=personioPositionsImport">' + customJsVars.label_run_import + '</a>');

    // add import hint to button
    $('a.personio-integration-import-hint').on('click', function() {
        return window.confirm(customJsVars.txt_import_hint);
    });

    // save to hide transient-messages via ajax-request
    $('div[data-dismissible] button.notice-dismiss').on('click',
        function (event) {
            event.preventDefault();
            var $this = $(this);
            var attr_value, option_name, dismissible_length, data;
            attr_value = $this.closest('div[data-dismissible]').attr('data-dismissible').split('-');

            // Remove the dismissible length from the attribute value and rejoin the array.
            dismissible_length = attr_value.pop();
            option_name = attr_value.join('-');
            data = {
                'action': 'dismiss_admin_notice',
                'option_name': option_name,
                'dismissible_length': dismissible_length,
                'nonce': customJsVars.dismiss_nonce
            };

            // run ajax request to save this setting
            $.post(customJsVars.ajax_url, data);
            $this.closest('div[data-dismissible]').hide('slow');
        }
    );
});