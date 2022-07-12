jQuery(document).ready(function($) {
    // add option near to list-headline
    $('body.post-type-personioposition.personio-integration-free:not(.edit-tags-php) h1.wp-heading-inline').after('<a class="page-title-action personio-pro-hint" href="' + customJsVars.pro_url + '" target="_blank">' + customJsVars.label_go_pro + '</a>');
    $('body.post-type-personioposition:not(.edit-tags-php):not(.personio-integration-url-missing) h1.wp-heading-inline').after('<a class="page-title-action personio-integration-import-hint" href="admin.php?action=personioPositionsImport">' + customJsVars.label_run_import + '</a>');

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

    // create import-flyout with progressbar
    $('a.personio-integration-import-hint').on('click', function (e) {
        e.preventDefault();

        // create dialog if if does not exists atm
        if( $('#personioImportDialog').length == 0 ) {
            $('<div id="personioImportDialog" title="' + customJsVars.label_import_is_running + '"><div id="personioStepDescription"></div><div id="personioImportProgressbar"></div></div>').dialog({
                width: 500,
                closeOnEscape: false,
                dialogClass: "personio-dialog-no-close",
                resizable: false,
                modal: true,
                draggable: false,
                buttons: [
                    {
                        text: customJsVars.label_ok,
                        click: function () {
                            location.reload();
                        }
                    }
                ]
            });
        }
        else {
            $('#personioImportDialog').dialog('open');
        }

        // disable button in dialog
        $('.personio-dialog-no-close .ui-button').prop('disabled', true);

        // init description
        let stepDescription = $('#personioStepDescription');
        stepDescription.html('<p>' + customJsVars.txt_please_wait + '</p>');

        // init progressbar
        let progressbar = jQuery("#personioImportProgressbar");
        progressbar.progressbar({
            value: 0
        }).removeClass("hidden");

        // start import
        $.ajax({
            type: "POST",
            url: customJsVars.ajax_url,
            data: {
                'action': 'personio_run_import',
                'nonce': customJsVars.run_import_nonce
            },
            beforeSend: function() {
                // get import-infos
                setTimeout(function() { personio_get_import_info(progressbar, stepDescription); }, 1000);
            }
        });
    })
});

/**
 * Get import info until import is done.
 *
 * @param progressbar
 * @param stepDescription
 */
function personio_get_import_info(progressbar, stepDescription) {
    jQuery.ajax({
        type: "POST",
        url: customJsVars.ajax_url,
        data: {
            'action': 'personio_get_import_info',
            'nonce': customJsVars.get_import_nonce
        },
        success: function(data) {
            let stepData = data.split(";");
            let count = parseInt(stepData[0]);
            let max = parseInt(stepData[1]);
            let running = parseInt(stepData[2]);

            // update progressbar
            progressbar.progressbar({
                value: (count/max)*100
            });

            // get next info until running is not 1
            if( running === 1 ) {
                setTimeout(function() { personio_get_import_info(progressbar, stepDescription) }, 500);
            }
            else {
                progressbar.addClass("hidden");
                stepDescription.html(customJsVars.txt_import_has_been_run);
                jQuery('.personio-dialog-no-close .ui-button').prop('disabled', false);
            }
        }
    })
}