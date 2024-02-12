jQuery(document).ready(function($) {
    // add option near to list-headline.
    $('body.post-type-personioposition:not(.personio-integration-hide-buttons):not(.edit-tags-php):not(.personioposition_page_personioApplication):not(.personioposition_page_personioformtemplate) h1.wp-heading-inline').after('<a class="page-title-action personio-pro-hint" href="' + personioIntegrationLightJsVars.pro_url + '" target="_blank">' + personioIntegrationLightJsVars.title_get_pro + '</a>');
    $('body.post-type-personioposition.edit-php h1.wp-heading-inline, body.post-type-personioposition.edit-personioposition-php:not(.personio-integration-url-missing) h1.wp-heading-inline').after('<a class="page-title-action personio-integration-import-hint" href="admin.php?action=personioPositionsImport">' + personioIntegrationLightJsVars.title_run_import + '</a>');
    $('body.post-type-personioposition:not(.personio-integration-hide-buttons) h1').each(function() {
      let button = document.createElement('a');
      button.className = 'review-hint-button page-title-action';
      button.href = 'https://wordpress.org/plugins/personio-integration-light/#reviews';
      button.innerHTML = personioIntegrationLightJsVars.title_rate_us;
      button.target = '_blank';
      this.after(button);
    })

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
                title: personioIntegrationLightJsVars.title_import_progress,
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
          title: personioIntegrationLightJsVars.title_delete_positions,
          texts: [
            '<p>' + personioIntegrationLightJsVars.txt_delete_positions + '</p>'
          ],
          buttons: [
            {
              'action': 'location.href="' + $(this).attr('href') +  '"',
              'variant': 'primary',
              'text': personioIntegrationLightJsVars.lbl_yes
            },
            {
              'action': 'closeDialog();',
              'variant': 'secondary',
              'text': personioIntegrationLightJsVars.lbl_no
            }
          ]
        }
      }
      personio_integration_create_dialog( dialog_config );
    });

    /**
     * Handle depending settings on settings page.
     *
     * Get all fields which depends from another.
     * Hide fields where the dependends does not match.
     * Set handler on depending fields to show or hide the dependend fields.
     *
     * Hint: hide the surrounding "tr"-element.
     */
    $('body.personioposition_page_personioPositions input[type="checkbox"]').each( function() {
        let form_field = $(this);

        // check on load to hide some fields.
        $('body.personioposition_page_personioPositions ul, body.personioposition_page_personioPositions p, body.personioposition_page_personioPositions select, body.personioposition_page_personioPositions input, body.personioposition_page_personioPositions textarea').each( function() {
          let depending_field = $(this);
          $.each( $(this).data('depends'), function( i, v ) {
               if( i === form_field.attr('name') && v === 1 && ! form_field.is(':checked') ) {
                 depending_field.closest('tr').addClass('hide');
                 depending_field.closest('tr').removeClass('show_with_animation');
               }
          });
        });

        // add event-listener to changed depending fields.
        form_field.on('change', function() {
          $('body.personioposition_page_personioPositions ul, body.personioposition_page_personioPositions p, body.personioposition_page_personioPositions select, body.personioposition_page_personioPositions input, body.personioposition_page_personioPositions textarea').each( function() {
            let depending_field = $(this);
            $.each( $(this).data('depends'), function( i, v ) {
              if( i === form_field.attr('name') && v === 1 ) {
                if( form_field.is(':checked') ) {
                  depending_field.closest('tr').removeClass('hide');
                  depending_field.closest( 'tr' ).addClass('show_with_animation')
                }
                else {
                  depending_field.closest('tr').addClass('hide');
                  depending_field.closest('tr').removeClass('show_with_animation');
                }
              }
            });
          });
        })
    });

    /**
     * Add hint for applications in Pro-version in menu.
     */
    $("#menu-posts-personioposition a[href='#']").on( 'click', function(e) {
      e.preventDefault();

      let dialog_config = {
        detail: {
          className: 'personio-integration-applications-hint',
          title: personioIntegrationLightJsVars.title_pro_hint,
          texts: [
            '<p>' + personioIntegrationLightJsVars.txt_pro_hint + '</p>'
          ],
          buttons: [
            {
              'action': 'window.open( "' + personioIntegrationLightJsVars.pro_url + '", "_blank" )',
              'variant': 'primary',
              'text': personioIntegrationLightJsVars.lbl_get_more_information
            },
            {
              'action': 'closeDialog();',
              'variant': 'secondary',
              'text': personioIntegrationLightJsVars.lbl_look_later
            }
          ]
        }
      }
      personio_integration_create_dialog( dialog_config );
    });
});

/**
 * Get import info until import is done.
 */
function personio_get_import_info() {
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
              let message = '<p>' + personioIntegrationLightJsVars.txt_error + '</p>';
              message = message + '<ul>';
              for( error of errors ) {
                message = message + '<li>' + error + '</li>';
              }
              message = message + '</ul>';
              let dialog_config = {
                detail: {
                  title: personioIntegrationLightJsVars.title_error,
                  texts: [
                    message
                  ],
                  buttons: [
                    {
                      'action': 'location.reload();',
                      'variant': 'primary',
                      'text': personioIntegrationLightJsVars.lbl_ok
                    }
                  ]
                }
              }
              personio_integration_create_dialog( dialog_config );
            }
            else {
                let message = '<p>' + personioIntegrationLightJsVars.txt_import_success + '</p>';
                let dialog_config = {
                  detail: {
                    title: personioIntegrationLightJsVars.title_import_success,
                    texts: [
                      message
                    ],
                    buttons: [
                      {
                        'action': 'location.reload();',
                        'variant': 'primary',
                        'text': personioIntegrationLightJsVars.lbl_ok
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

/**
 * Import given settings file via AJAX.
 */
function personio_integration_import_settings_file() {

  let file = jQuery('#import_settings_file')[0].files[0];
  if( undefined === file ) {
    let dialog_config = {
      detail: {
        title: personioIntegrationLightJsVars.title_settings_import_file_missing,
        texts: [
          '<p>' + personioIntegrationLightJsVars.text_settings_import_file_missing + '</p>'
        ],
        buttons: [
          {
            'action': 'closeDialog();',
            'variant': 'primary',
            'text': personioIntegrationLightJsVars.lbl_ok
          }
        ]
      }
    }
    personio_integration_create_dialog( dialog_config );
    return;
  }

  let request = new FormData();
  request.append('file', file);
  request.append( 'action', 'personio_integration_settings_import_file' );
  request.append( 'nonce', personioIntegrationLightJsVars.settings_import_file_nonce );

  jQuery.ajax({
    url: personioIntegrationLightJsVars.ajax_url,
    type: "POST",
    data: request,
    contentType: false,
    processData: false,
    success: function( data ){
      console.log(data);
      if( data.html ) {
        let dialog_config = {
          detail: {
            title: personioIntegrationLightJsVars.title_settings_import_file_result,
            texts: [
              '<p>' + data.html + '</p>'
            ],
            buttons: [
              {
                'action': 'closeDialog();',
                'variant': 'primary',
                'text': personioIntegrationLightJsVars.lbl_ok
              }
            ]
          }
        }
        personio_integration_create_dialog( dialog_config );
      }
    },
  });
}
