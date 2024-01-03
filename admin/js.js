jQuery(document).ready(function($) {
    // get internationalization tools of WordPress.
    let { __ } = wp.i18n;

    // add option near to list-headline
    $('body.post-type-personioposition:not(.edit-tags-php):not(.personioposition_page_personioApplication):not(.personioposition_page_personioformtemplate) h1.wp-heading-inline').after('<a class="page-title-action personio-pro-hint" href="' + personioIntegrationLightJsVars.pro_url + '" target="_blank">' + __( 'Get Personio Integration Pro', 'personio-integration-light' ) + '</a>');
    $('body.post-type-personioposition.edit-php:not(.personio-integration-url-missing) h1.wp-heading-inline, body.post-type-personioposition.edit-personioposition-php:not(.personio-integration-url-missing) h1.wp-heading-inline').after('<a class="page-title-action personio-integration-import-hint" href="admin.php?action=personioPositionsImport">' + __( 'Run import', 'personio-integration-light' ) + '</a>');

    // save to hide transient-messages via ajax-request
    $('div[data-dismissible] button.notice-dismiss').on('click',
        function (event) {
            event.preventDefault();
            let $this = $(this);
            let attr_value, option_name, dismissible_length, data;
            attr_value = $this.closest('div[data-dismissible]').attr('data-dismissible').split('-');

            // Remove the dismissible length from the attribute value and rejoin the array.
            dismissible_length = attr_value.pop();
            option_name = attr_value.join('-');
            data = {
                'action': 'dismiss_admin_notice',
                'option_name': option_name,
                'dismissible_length': dismissible_length,
                'nonce': personioIntegrationLightJsVars.dismiss_nonce
            };

            // run ajax request to save this setting
            $.post(personioIntegrationLightJsVars.ajax_url, data);
            $this.closest('div[data-dismissible]').hide('slow');
        }
    );

    // create import-flyout with progressbar.
    $('a.personio-integration-import-hint').on('click', function (e) {
        e.preventDefault();

        // start import.
        $.ajax({
          type: "POST",
          url: personioIntegrationLightJsVars.ajax_url,
          data: {
            'action': 'personio_run_import',
            'nonce': personioIntegrationLightJsVars.run_import_nonce
          },
          beforeSend: function() {
            // show progress.
            let dialog_config = {
              detail: {
                title: __('Import in progress', 'personio-integration-light' ),
                progressbar: {
                  active: true,
                  progress: 0,
                  id: 'progress'
                },
              }
            }
            personio_integration_create_dialog( dialog_config );

            // get info about progress.
            setTimeout(function() { personio_get_import_info() }, 1000);
          }
        });
    });

    // create confirm dialog for deletion of all positions.
    $('a.personio-integration-delete-all').on('click', function (e) {
      e.preventDefault();

      let dialog_config = {
        detail: {
          title: __( 'Delete all positions', 'personio-integration-light' ),
          texts: [
            '<p>' + __( '<strong>Are you sure you want to delete all positions in WordPress?</strong><br>Hint: the positions in Personio are not influenced.', 'personio-integration-light' ) + '</p>'
          ],
          buttons: [
            {
              'action': 'location.href=" ' + $(this).attr('href') +  ' "',
              'variant': 'primary',
              'text': __( 'Yes', 'personio-integration-light' )
            },
            {
              'action': 'closeDialog();',
              'variant': 'secondary',
              'text': __( 'No', 'personio-integration-light' )
            }
          ]
        }
      }
      personio_integration_create_dialog( dialog_config );
    });

    // show pointer where user could add its Personio-URL.
    if (jQuery.fn.pointer) {
      $('body.personio-integration-pointer input#personioIntegrationUrl').pointer(
        {
          content: '<p>' + personioIntegrationLightJsVars.logo_img + sprintf( __( '<strong>Add your Personio URL.</strong></p><p>This must be the public URL of your Personio account, e.g. <i>%1$s</i>.', 'personio-integration-light' ), personioIntegrationLightJsVars.url_example ) + '</p>',
          position: {
            edge: 'bottom',
            align: 'left'
          },
          pointerClass: 'personio-integration-light-url-pointer',
          close: function () {
            // save via ajax the hiding of this hint.
            let data = {
              'action': 'personio_integration_dismiss_url_hint',
              'nonce': personioIntegrationLightJsVars.dismiss_url_nonce
            };

            // run ajax request to save this setting
            $.post(personioIntegrationLightJsVars.ajax_url, data);
          }
        }
      ).pointer('open');
    }
});

/**
 * Get import info until import is done.
 */
function personio_get_import_info() {
    // get internationalization tools of WordPress.
    let { __ } = wp.i18n;

    jQuery.ajax({
        type: "POST",
        url: personioIntegrationLightJsVars.ajax_url,
        data: {
            'action': 'personio_get_import_info',
            'nonce': personioIntegrationLightJsVars.get_import_nonce
        },
        success: function(data) {
            let stepData = data.split(";");
            let count = parseInt(stepData[0]);
            let max = parseInt(stepData[1]);
            let running = parseInt(stepData[2]);
            let errors = JSON.parse(stepData[3]);

            jQuery("#progress").attr('value', (count / max) * 100);

            /**
             * If import is still running, get next info in 500ms.
             * If import is not running and error occurred, show the error.
             * If import is not running and no error occurred, show ok-message.
             */
            if( running >= 1 ) {
                setTimeout(function() { personio_get_import_info() }, 500);
            }
            else if( errors.length > 0 ) {
              let message = '<p>' + __( '<strong>Error during import of positions.</strong> The following error occurred:', 'personio-integration-light' ) + '</p>';
              message = message + '<ul>';
              for( error of errors ) {
                message = message + '<li>' + error + '</li>';
              }
              message = message + '</ul>';
              let dialog_config = {
                detail: {
                  title: __( 'Error during import of positions', 'personio-integration-light' ),
                  texts: [
                    message
                  ],
                  buttons: [
                    {
                      'action': 'location.reload();',
                      'variant': 'primary',
                      'text': __( 'OK', 'personio-integration-light' )
                    }
                  ]
                }
              }
              personio_integration_create_dialog( dialog_config );
            }
            else {
                let message = '<p>' + sprintf(
                /* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
                __(
                  '<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
                  'personio-integration-light'
                ),
                personioIntegrationLightJsVars.url_positions_backend,
                personioIntegrationLightJsVars.url_positions_frontend
                ) + '</p>';
                let dialog_config = {
                  detail: {
                    title: __( 'Positions has been imported', 'personio-integration-light' ),
                    texts: [
                      message
                    ],
                    buttons: [
                      {
                        'action': 'location.reload();',
                        'variant': 'primary',
                        'text': __( 'OK', 'personio-integration-light' )
                      }
                    ]
                  }
                }
                personio_integration_create_dialog( dialog_config );
            }
        }
    })
}

/**
 * Helper to create a new dialog with given config.
 *
 * @param config
 */
function personio_integration_create_dialog( config ) {
  document.body.dispatchEvent(new CustomEvent("wp-easy-dialog", config));
}
