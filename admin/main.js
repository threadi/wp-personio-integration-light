jQuery(document).ready(function($) {
    // add option near to list-headline.
    $('body.post-type-personioposition:not(.personio-integration-hide-buttons):not(.edit-tags-php):not(.personioposition_page_personioApplication):not(.personioposition_page_personioformtemplate) h1.wp-heading-inline').after('<a class="page-title-action personio-pro-hint" href="' + personioIntegrationLightJsVars.pro_url + '" target="_blank">' + personioIntegrationLightJsVars.title_get_pro + '</a>');
    $('body.post-type-personioposition.edit-php h1.wp-heading-inline, body.post-type-personioposition.edit-personioposition-php:not(.personio-integration-url-missing) h1.wp-heading-inline').after('<a class="page-title-action personio-integration-import-hint" href="' + personioIntegrationLightJsVars.import_url + '">' + personioIntegrationLightJsVars.title_run_import + '</a>');
    $('body.post-type-personioposition:not(.personio-integration-hide-buttons) h1').each(function() {
      let button = document.createElement('a');
      button.className = 'review-hint-button page-title-action';
      button.href = personioIntegrationLightJsVars.review_url;
      button.innerHTML = personioIntegrationLightJsVars.title_rate_us;
      button.target = '_blank';
      this.after(button);
    })

    /**
     * Get the content for the dialog via AJAX for dynamic content-changes.
     */
    $(document).on(
      'click',
      'a.personio-integration-import-hint',
      function (e) {
        e.preventDefault();
        personio_integration_light_get_import_dialog();
      }
    );

    // create confirm dialog for deletion of all positions.
    $(document).on(
      'click',
      'a.personio-integration-delete-all',
      function (e) {
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
        };

        personio_integration_create_dialog( dialog_config );
      }
    );

    /**
     * Add hint for applications in Pro-version in menu.
     */
    $("body:not(.personio-integration) #menu-posts-personioposition a[href*='personioApplication']").on( 'click', function(e) {
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
    // bail if driver.js is not loaded.
    if ( ! window.driver || ! window.driver.js ) {
      return;
    }

    window.driver.js.driver( {
      nextBtnText: personioIntegrationLightIntroJsVars.button_title_next,
      prevBtnText: personioIntegrationLightIntroJsVars.button_title_back,
      doneBtnText: personioIntegrationLightIntroJsVars.button_title_done,
      popoverClass: 'personio-integration-intro',
      disableActiveInteraction: true,
      overlayClickBehavior: function() {}, // ignore clicks on the overlay.
      onCloseClick: personio_integration_tour_close,
      onDestroyStarted: personio_integration_tour_destroy_started,
      onDestroyed: function() {
        location.href=window.location.href.replace( /import_intro=1/, '' )
      },
      steps: [
        {
          popover: {
            title: personioIntegrationLightIntroJsVars.import_intro_step_1_title,
            description: personioIntegrationLightIntroJsVars.import_intro_step_1_intro,
            showButtons: [ 'next', 'close' ]
          }
        },
        {
          element: 'tr.personio-integration-import-now',
          popover: {
            title: personioIntegrationLightIntroJsVars.import_intro_step_2_title,
            description: personioIntegrationLightIntroJsVars.import_intro_step_2_intro
          }
        },
        {
          element: 'tr.personio-integration-delete-now',
          popover: {
            title: personioIntegrationLightIntroJsVars.import_intro_step_3_title,
            description: personioIntegrationLightIntroJsVars.import_intro_step_3_intro
          }
        },
        {
          element: 'tr.personio-integration-automatic-import',
          popover: {
            title: personioIntegrationLightIntroJsVars.import_intro_step_4_title,
            description: personioIntegrationLightIntroJsVars.import_intro_step_4_intro
          }
        },
        {
          popover: {
            title: personioIntegrationLightIntroJsVars.import_intro_step_5_title,
            description: personioIntegrationLightIntroJsVars.import_intro_step_5_intro,
            popoverClass: 'personio-integration-intro personio-integration-intro-wide'
          }
        }
      ]
    } ).drive();
  });

  /**
   * Templates intro
   */
  $("body.personio-integration-template-intro").each( function() {
    // bail if driver.js is not loaded.
    if ( ! window.driver || ! window.driver.js ) {
      return;
    }

    window.driver.js.driver( {
      nextBtnText: personioIntegrationLightIntroJsVars.button_title_next,
      prevBtnText: personioIntegrationLightIntroJsVars.button_title_back,
      doneBtnText: personioIntegrationLightIntroJsVars.button_title_done,
      popoverClass: 'personio-integration-intro',
      disableActiveInteraction: true,
      overlayClickBehavior: function() {}, // ignore clicks on the overlay.
      onCloseClick: personio_integration_tour_close,
      onDestroyStarted: personio_integration_tour_destroy_started,
      onDestroyed: function() {
        location.href=window.location.href.replace( /template_intro=1/, '' ).replace( /template_intro=2/, '' )
      },
      steps: [
        {
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_1_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_1_intro,
            popoverClass: 'personio-integration-intro personio-integration-intro-wide',
            showButtons: [ 'next', 'close' ]
          }
        },
        {
          element: 'tr.personio-integration-template-filter',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_2_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_2_intro
          }
        },
        {
          element: 'tr.personio-integration-template-listing-template',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_3_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_3_intro
          }
        },
        {
          element: 'tr.personio-integration-template-content-list',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_4_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_4_intro
          }
        },
        {
          element: 'tr.personio-integration-template-excerpts-template',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_5_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_5_intro
          }
        },
        {
          element: 'tr.personio-integration-template-excerpts-defaults',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_6_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_6_intro
          }
        },
        {
          element: 'tr.personio-integration-template-content-template',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_7_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_7_intro
          }
        },
        {
          element: 'tr.personio-integration-template-content-template-2',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_8_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_8_intro
          }
        },
        {
          element: 'tr.personio-integration-template-excerpts-template-2',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_9_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_9_intro
          }
        },
        {
          element: 'tr.personio-integration-template-excerpt-detail-2',
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_10_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_10_intro
          }
        },
        {
          popover: {
            title: personioIntegrationLightIntroJsVars.template_intro_step_11_title,
            description: personioIntegrationLightIntroJsVars.template_intro_step_11_intro,
            popoverClass: 'personio-integration-intro personio-integration-intro-wide'
          }
        }
      ]
    } ).drive();
  });

    personio_integration_extension_state_button();

    /**
     * Trigger help click.
     */
    $('#personio-open-help').on('click', function(e) {
      e.preventDefault();
      $('#contextual-help-link').trigger('click');
    });
});

/**
 * Mark if an import has been started via this page and its request is still running.
 *
 * @type {boolean}
 */
let import_running = false;

/**
 * Mark if the request to start the import failed.
 *
 * @type {boolean}
 */
let import_failed = false;

/**
 * Mark if a deletion has been started via this page and its request is still running.
 *
 * @type {boolean}
 */
let delete_running = false;

/**
 * Mark if the request to delete all positions failed.
 *
 * @type {boolean}
 */
let delete_failed = false;

/**
 * The state of the actual running progress polling (import or deletion).
 *
 * @type {{timer: (number|null), attempt: number, start: number}}
 */
let personio_integration_poll = { timer: null, attempt: 0, start: 0 };

/**
 * Max duration of progress polling in milliseconds (60 minutes).
 *
 * @type {number}
 */
const personio_integration_poll_max_duration = 60 * 60 * 1000;

/**
 * Start a new progress polling with the given callback after the given delay.
 *
 * @param callback The function to call.
 * @param delay The delay in ms.
 */
function personio_integration_start_polling( callback, delay ) {
  personio_integration_stop_polling();
  personio_integration_poll.attempt = 0;
  personio_integration_poll.start = Date.now();
  personio_integration_poll.timer = setTimeout( callback, delay );
}

/**
 * Schedule the next poll with backoff (500 ms up to 5 s).
 *
 * Returns false if the max duration of polling has been reached.
 *
 * @param callback The function to call.
 * @returns {boolean}
 */
function personio_integration_schedule_poll( callback ) {
  // bail if max duration is reached.
  if( Date.now() - personio_integration_poll.start > personio_integration_poll_max_duration ) {
    personio_integration_stop_polling();
    return false;
  }

  // calculate the delay: start with 500 ms and increase it up to 5 s.
  let delay = Math.min( 5000, Math.round( 500 * Math.pow( 1.1, personio_integration_poll.attempt ) ) );
  personio_integration_poll.attempt++;

  // schedule the next poll.
  clearTimeout( personio_integration_poll.timer );
  personio_integration_poll.timer = setTimeout( callback, delay );
  return true;
}

/**
 * Stop any running progress polling.
 */
function personio_integration_stop_polling() {
  if( personio_integration_poll.timer ) {
    clearTimeout( personio_integration_poll.timer );
  }
  personio_integration_poll.timer = null;
}

/**
 * Escape the given text for output as HTML.
 *
 * @param text The text to escape.
 * @returns {string}
 */
function personio_integration_escape_html( text ) {
  return String( text )
    .replace( /&/g, '&amp;' )
    .replace( /</g, '&lt;' )
    .replace( />/g, '&gt;' )
    .replace( /"/g, '&quot;' )
    .replace( /'/g, '&#039;' );
}

/**
 * Parse the progress info from the server response.
 *
 * Returns false if the response is not in the expected format (e.g. "0" if the user is missing the capability).
 *
 * @param data The response.
 * @returns {{count: number, max: number, running: number, status: string, errors: Array}|boolean}
 */
function personio_integration_parse_progress_info( data ) {
  // bail if response does not have the expected format.
  if( ! Array.isArray( data ) || data.length < 5 ) {
    return false;
  }

  // parse the errors.
  let errors = [];
  try {
    errors = JSON.parse( data[4] );
  }
  catch ( e ) {
    return false;
  }
  if( ! Array.isArray( errors ) ) {
    errors = errors ? Object.values( errors ) : [];
  }

  return {
    count: parseInt( data[0] ),
    max: parseInt( data[1] ),
    running: parseInt( data[2] ),
    status: data[3],
    errors: errors
  };
}

/**
 * Start import of positions.
 */
function personio_start_import() {
  // start import.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_run_import',
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

      // mark in JS as running.
      import_running = true;
      import_failed = false;

      // get info about progress.
      personio_integration_start_polling( function() { personio_get_import_info() }, 1000 );
    },
    success: function() {
      // mark import as not running.
      import_running = false;
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      // mark import as not running and stop polling.
      import_running = false;
      import_failed = true;
      personio_integration_stop_polling();
      personio_integration_ajax_error_dialog( errorThrown, personioIntegrationLightJsImportErrors )
    }
  });
}

/**
 * Get info until import is done.
 */
function personio_get_import_info() {
  // bail if the import request failed.
  if( import_failed ) {
    return;
  }

  jQuery.ajax( {
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_get_import_info',
      'nonce': personioIntegrationLightJsVars.get_import_nonce
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_stop_polling();
      personio_integration_ajax_error_dialog( errorThrown )
    },
    success: function (data) {
      // bail if the import request failed in the meantime.
      if( import_failed ) {
        return;
      }

      // parse the response.
      let info = personio_integration_parse_progress_info( data );
      if( false === info ) {
        personio_integration_stop_polling();
        personio_integration_ajax_error_dialog();
        return;
      }

      // show progress.
      jQuery( '#progress' ).attr( 'value', info.max > 0 ? (info.count / info.max) * 100 : 0 );
      jQuery( '#progress_status' ).html( info.status );

      /**
       * If import is still running, get next info (with backoff from 500ms up to 5s).
       * If import is not running and error occurred, show the error.
       * If import is not running and no error occurred, show ok-message.
       */
      if (info.running >= 1 || import_running) {
        if( ! personio_integration_schedule_poll( function () { personio_get_import_info() } ) ) {
          personio_integration_ajax_error_dialog();
        }
      } else if (info.errors.length > 0) {
        let message = '<p><strong>' + personioIntegrationLightJsVars.import_txt_error + '</strong></p>';
        message = message + '<ul>';
        for (const error of info.errors) {
          message = message + '<li>' + error + '</li>'; // the texts are filtered with wp_kses_post() on the server.
        }
        message = message + '</ul>';
        let dialog_config = {
          detail: {
            className: 'personio-integration-import-error',
            title: personioIntegrationLightJsVars.import_title_error,
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
      } else {
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
  } )
}

/**
 * Delete all positions.
 */
function personio_delete_positions( reimport ) {
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

      // mark in JS as running.
      delete_running = true;
      delete_failed = false;

      // get info about progress.
      personio_integration_start_polling( function() { personio_get_delete_info( reimport ) }, 1000 );
    },
    success: function() {
      // mark deletion as not running.
      delete_running = false;
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      // mark deletion as not running and stop polling.
      delete_running = false;
      delete_failed = true;
      personio_integration_stop_polling();

      // get error message from REST API response, if available.
      let errortext = errorThrown;
      if( jqXHR.responseJSON && jqXHR.responseJSON.message ) {
        errortext = personio_integration_escape_html( jqXHR.responseJSON.message );
      }
      personio_integration_ajax_error_dialog( errortext );
    }
  });
}

/**
 * Get info until deletion is done.
 */
function personio_get_delete_info( reimport ) {
  // bail if the deletion request failed.
  if( delete_failed ) {
    return;
  }

  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_get_deletion_info',
      'nonce': personioIntegrationLightJsVars.get_deletion_nonce
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_stop_polling();
      personio_integration_ajax_error_dialog( errorThrown )
    },
    success: function(data) {
      // bail if the deletion request failed in the meantime.
      if( delete_failed ) {
        return;
      }

      // parse the response.
      let info = personio_integration_parse_progress_info( data );
      if( false === info ) {
        personio_integration_stop_polling();
        personio_integration_ajax_error_dialog();
        return;
      }

      // show progress.
      jQuery('#progress').attr('value', info.max > 0 ? (info.count / info.max) * 100 : 0);
      jQuery('#progress_status').html(info.status);

      /**
       * If deletion is still running, get next info (with backoff from 500ms up to 5s).
       * If deletion is not running and error occurred, show the error.
       * If deletion is not running and no error occurred, show ok-message.
       */
      if( info.running >= 1 || delete_running ) {
        if( ! personio_integration_schedule_poll( function() { personio_get_delete_info( reimport ) } ) ) {
          personio_integration_ajax_error_dialog();
        }
      }
      else if( info.errors.length > 0 ) {
        let message = '<p>' + personioIntegrationLightJsVars.txt_error + '</p>';
        message = message + '<ul>';
        for( const error of info.errors ) {
          message = message + '<li>' + error + '</li>'; // the texts are filtered with wp_kses_post() on the server.
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
        if( reimport ) {
            personio_start_import();
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
    }
  })
}

/**
 * Helper to create a new dialog from an AJAX response.
 *
 * Shows an error if the response is not a dialog config (e.g. "0" if the user is missing the capability).
 *
 * @param result The response.
 */
function personio_integration_create_dialog_from_response( result ) {
  if( ! result || 'object' !== typeof result || ! result.detail ) {
    personio_integration_ajax_error_dialog();
    return;
  }
  personio_integration_create_dialog( result );
}

/**
 * Helper to create a new dialog with given config.
 *
 * @param config
 */
function personio_integration_create_dialog( config ) {
  document.body.dispatchEvent(new CustomEvent("easy-dialog-for-wordpress", config));
}

/**
 * Define dialog for AJAX-errors.
 */
function personio_integration_ajax_error_dialog( errortext, texts ) {
  if( errortext === undefined || errortext.length === 0 ) {
    errortext = personioIntegrationLightJsVars.generate_error_text;
  }
  let message = '<p>' + personioIntegrationLightJsVars.txt_error + '</p>';
  message = message + '<ul>';
  if( texts && texts[errortext] ) {
    message = message + '<li>' + texts[errortext] + '</li>';
  }
  else {
    message = message + '<li>' + errortext + '</li>';
  }
  message = message + '</ul>';

  // show dialog.
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

/**
 * Change extension state via button click.
 */
function personio_integration_extension_state_button() {
  // add state change event for each extension in the list.
  jQuery('.personioposition_page_personiopositionextensions .button-state').on('click', function (e) {
    e.preventDefault();

    // get the button object.
    let button = jQuery(this);

    // send ajax request and process the response.
    jQuery.ajax( {
      type: "POST",
      url: personioIntegrationLightJsVars.ajax_url,
      data: {
        'action': 'personio_integration_light_extension_state',
        'extension': button.data( 'extension' ),
        'nonce': personioIntegrationLightJsVars.extension_state_nonce
      },
      error: function( jqXHR, textStatus, errorThrown ) {
        personio_integration_ajax_error_dialog( errorThrown )
      },
      beforeSend: function() {
        // show wait window as dialog.
        let dialog_config = {
          detail: {
            title: personioIntegrationLightJsVars.title_please_wait,
            texts: [
              '<p>' + personioIntegrationLightJsVars.txt_please_wait + '</p>',
            ],
          }
        }
        personio_integration_create_dialog(dialog_config);
      },
      success: function (dialog_config) {
        // bail if response is not in the expected format.
        if( ! dialog_config || 'object' !== typeof dialog_config || ! dialog_config.data ) {
          personio_integration_ajax_error_dialog();
          return;
        }

        // update the button only if the state has been changed (response contains the new button title).
        if( dialog_config.data.button_title ) {
          if( dialog_config.success ) {
            button.removeClass( 'button-state-disabled' );
            button.addClass( 'button-state-enabled' );
            button.parents('tr').find('.row-actions-wrapper').show();
          }
          else {
            button.removeClass( 'button-state-enabled' );
            button.addClass( 'button-state-disabled' );
            button.parents('tr').find('.row-actions-wrapper').hide();
          }
          button.html( personio_integration_escape_html( dialog_config.data.button_title ) );
        }
        personio_integration_create_dialog( dialog_config.data );
      }
    } )
  });
}

/**
 * Return the settings import via AJAX.
 */
function personio_integration_settings_import_dialog_via_setup() {
  // get the dialog via AJAX.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_get_settings_import_dialog',
      'nonce': personioIntegrationLightJsVars.settings_import_dialog_nonce
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_ajax_error_dialog( errorThrown )
    },
    success: function( result ) {
      personio_integration_create_dialog_from_response( result );
    }
  });
}

/**
 * Get the import dialog via AJAX.
 */
function personio_integration_light_get_import_dialog() {
  // get the dialog via AJAX.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_get_import_dialog',
      'nonce': personioIntegrationLightJsVars.get_import_dialog_nonce
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_ajax_error_dialog( errorThrown )
    },
    success: function( result ) {
      personio_integration_create_dialog_from_response( result );
    }
  });
}

/**
 * Send testmail via AJAX.
 */
function personio_integration_send_testmail( obj_name ) {
  // get the dialog via AJAX.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_sent_test_email',
      'nonce': personioIntegrationLightJsVars.test_email_nonce,
      'object': obj_name
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_ajax_error_dialog( errorThrown )
    },
    success: function( result ) {
      personio_integration_create_dialog_from_response( result );
    }
  });
}

/**
 * Close a tour via its close button.
 *
 * @param element The actual element.
 * @param step The actual step.
 * @param opts The options with the driver object.
 */
function personio_integration_tour_close( element, step, opts ) {
  opts.driver.destroy();
}

/**
 * End a tour via escape key or done button only on its last step.
 *
 * @param element The actual element.
 * @param step The actual step.
 * @param opts The options with the driver object.
 */
function personio_integration_tour_destroy_started( element, step, opts ) {
  if ( opts.driver.isLastStep() ) {
    opts.driver.destroy();
  }
}
