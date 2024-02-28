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

      let dialog_config = {
        detail: {
          title: personioIntegrationLightJsVars.title_start_import,
          texts: [
            '<p>' + personioIntegrationLightJsVars.txt_start_import + '</p>'
          ],
          buttons: [
            {
              'action': 'personio_start_import();',
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
              'action': 'personio_delete_positions();',
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
    $('body.personioposition_page_personioPositions input[type="checkbox"], body.personioposition_page_personioPositions select').each( function() {
        let form_field = $(this);

        // check on load to hide some fields.
        $('body.personioposition_page_personioPositions [data-depends]').each( function() {
          let depending_field = $(this);
          $.each( $(this).data('depends'), function( i, v ) {
             if( i === form_field.attr('name')
               && (
                 ( form_field.attr('type') === 'checkbox' && ! form_field.is(':checked') )
                 || ( form_field.attr('type') !== 'checkbox' && v.toString() !== form_field.val() )
               ) ) {
               depending_field.closest('tr').addClass('hide');
               depending_field.closest('tr').removeClass('show_with_animation');
             }
          });
        });

        // add event-listener to changed depending fields.
        form_field.on('change', function() {
          $('body.personioposition_page_personioPositions [data-depends]').each( function() {
            let depending_field = $(this);
            $.each( $(this).data('depends'), function( i, v ) {
              if( i === form_field.attr('name') ) {
                if(
                  (form_field.attr('type') !== 'checkbox' && v.toString() === form_field.val() )
                  || (form_field.attr('type') === 'checkbox' && form_field.is(':checked') )
                ) {
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
              'action': 'window.open( "' + personioIntegrationLightJsVars.pro_url + '", "_blank" );closeDialog();',
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

    /**
     * Import intro.
     */
    $("body.personio-integration-import-intro").each( function() {
        introJs().setOptions( {
          nextLabel: personioIntegrationLightIntroJsVars.button_title_next,
          prevLabel: personioIntegrationLightIntroJsVars.button_title_back,
          doneLabel: personioIntegrationLightIntroJsVars.button_title_done,
          exitOnEsc: false,
          exitOnOverlayClick: false,
          disableInteraction: true,
          steps: [
            {
              title: personioIntegrationLightIntroJsVars.import_intro_step_1_title,
              intro: personioIntegrationLightIntroJsVars.import_intro_step_1_intro,
            },
            {
              element: document.querySelector('tr.personio-integration-import-now'),
              title: personioIntegrationLightIntroJsVars.import_intro_step_2_title,
              intro: personioIntegrationLightIntroJsVars.import_intro_step_2_intro,
            },
            {
              element: document.querySelector('tr.personio-integration-delete-now'),
              title: personioIntegrationLightIntroJsVars.import_intro_step_3_title,
              intro: personioIntegrationLightIntroJsVars.import_intro_step_3_intro,
            },
            {
              element: document.querySelector('tr.personio-integration-automatic-import'),
              title: personioIntegrationLightIntroJsVars.import_intro_step_4_title,
              intro: personioIntegrationLightIntroJsVars.import_intro_step_4_intro,
            },
            {
              title: personioIntegrationLightIntroJsVars.import_intro_step_5_title,
              intro: personioIntegrationLightIntroJsVars.import_intro_step_5_intro,
              tooltipClass: 'intro-width'
            }
          ]
        } ).onexit( function() {
          location.href=window.location.href.replace( /import_intro=1/, '' )
        } ).start();
    });

    /**
     * Templates intro
     */
    $("body.personio-integration-template-intro").each( function() {
      introJs().setOptions( {
        nextLabel: personioIntegrationLightIntroJsVars.button_title_next,
        prevLabel: personioIntegrationLightIntroJsVars.button_title_back,
        doneLabel: personioIntegrationLightIntroJsVars.button_title_done,
        exitOnEsc: false,
        exitOnOverlayClick: false,
        disableInteraction: true,
        steps: [
          {
            title: personioIntegrationLightIntroJsVars.template_intro_step_1_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_1_intro,
            tooltipClass: 'intro-width'
          },
          {
            element: document.querySelector('tr.personio-integration-template-filter'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_2_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_2_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-listing-template'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_3_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_3_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-content-list'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_4_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_4_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-excerpts-template'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_5_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_5_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-excerpts-defaults'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_6_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_6_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-content-template'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_7_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_7_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-content-template-2'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_8_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_8_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-excerpts-template-2'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_9_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_9_intro,
          },
          {
            element: document.querySelector('tr.personio-integration-template-excerpt-detail-2'),
            title: personioIntegrationLightIntroJsVars.template_intro_step_10_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_10_intro,
          },
          {
            title: personioIntegrationLightIntroJsVars.template_intro_step_11_title,
            intro: personioIntegrationLightIntroJsVars.template_intro_step_11_intro,
            tooltipClass: 'intro-width'
          }
        ]
      } ).onexit( function() {
        location.href=window.location.href.replace( /template_intro=2/, '' )
      } ).start();
    });
});

/**
 * Start import of positions.
 */
function personio_start_import() {
  // start import.
  jQuery.ajax({
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
            id: 'progress',
            label_id: 'progress_status'
          },
        }
      }
      personio_integration_create_dialog( dialog_config );

      // get info about progress.
      setTimeout(function() { personio_get_import_info() }, 1000);
    }
  });
}

/**
 * Get info until import is done.
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
            let count = parseInt(data[0]);
            let max = parseInt(data[1]);
            let running = parseInt(data[2]);
            let status = data[3];
            let errors = JSON.parse(data[4]);

            // show progress.
            jQuery('#progress').attr('value', (count / max) * 100);
            jQuery('#progress_status').html(status);

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
 * Delete all positions.
 */
function personio_delete_positions() {
  // start deletion.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.rest_personioposition_delete,
    dataType: 'json',
    method: 'DELETE',
    beforeSend: function( xhr ) {
      // set header for authentication.
      xhr.setRequestHeader( 'X-WP-Nonce', personioIntegrationLightJsVars.rest_nonce );

      // show progress.
      let dialog_config = {
        detail: {
          title: personioIntegrationLightJsVars.title_delete_progress,
          progressbar: {
            active: true,
            progress: 0,
            id: 'progress',
            label_id: 'progress_status'
          },
        }
      }
      personio_integration_create_dialog( dialog_config );

      // get info about progress.
      setTimeout(function() { personio_get_delete_info() }, 1000);
    }
  });
}

/**
 * Get info until deletion is done.
 */
function personio_get_delete_info() {
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_get_deletion_info',
      'nonce': personioIntegrationLightJsVars.get_deletion_nonce
    },
    success: function(data) {
      let count = parseInt(data[0]);
      let max = parseInt(data[1]);
      let running = parseInt(data[2]);
      let status = data[3];
      let errors = JSON.parse(data[4]);

      // show progress.
      jQuery('#progress').attr('value', (count / max) * 100);
      jQuery('#progress_status').html(status);

      /**
       * If deletion is still running, get next info in 500ms.
       * If deletion is not running and error occurred, show the error.
       * If deletion is not running and no error occurred, show ok-message.
       */
      if( running >= 1 ) {
        setTimeout(function() { personio_get_delete_info() }, 500);
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
        let message = '<p>' + personioIntegrationLightJsVars.txt_deletion_success + '</p>';
        let dialog_config = {
          detail: {
            title: personioIntegrationLightJsVars.title_deletion_success,
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
      if( data.html ) {
        let dialog_config = {
          detail: {
            className: data.success ? 'personio-integration-dialog-success' : 'personio-integration-dialog-error',
            title: personioIntegrationLightJsVars.title_settings_import_file_result,
            texts: [
              '<p>' + data.html + '</p>'
            ],
            buttons: [
              {
                'action': data.success ? 'location.reload();' : 'closeDialog();',
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
