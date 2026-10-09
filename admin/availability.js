jQuery(document).ready(function($) {
  $('a.personio-integration-availability-check').on('click', function( e ) {
    e.preventDefault();
    personio_start_availability_check( $(this).data('post-id') );
  })
});
/**
 * Start import of positions.
 */
function personio_start_availability_check( post_id ) {
  // start the check.
  jQuery.ajax( {
    type: "POST",
    url: personioIntegrationLightAvailabilityJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_run_availability_check',
      'post': post_id,
      'nonce': personioIntegrationLightAvailabilityJsVars.availability_nonce
    },
    beforeSend: function () {
      // show progress.
      let dialog_config = {
        detail: {
          title: personioIntegrationLightAvailabilityJsVars.title_check_in_progress,
          progressbar: {
            active: true,
            progress: 50,
            id: 'progress',
            label_id: 'progress_status'
          },
        }
      }
      personio_integration_create_dialog( dialog_config );
    },
    success: function( data ) {
      // bail if the check could not be run.
      if( ! data || 'object' !== typeof data || ! data.success ) {
        personio_integration_ajax_error_dialog();
        return;
      }

      // get info about the result.
      personio_get_availability_check_info( post_id );
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_ajax_error_dialog( errorThrown )
    }
  } );
}

/**
 * Get info until import is done.
 */
function personio_get_availability_check_info( post_id ) {
    jQuery.ajax( {
      type: "POST",
      url: personioIntegrationLightAvailabilityJsVars.ajax_url,
      data: {
        'action': 'personio_integration_light_get_availability_check_info',
        'post_id': post_id,
        'nonce': personioIntegrationLightAvailabilityJsVars.get_availability_check_nonce
      },
      error: function( jqXHR, textStatus, errorThrown ) {
        personio_integration_ajax_error_dialog( errorThrown )
      },
      success: function (data) {
        // bail if response is not in the expected format.
        if( ! data || 'object' !== typeof data || undefined === data.running ) {
          personio_integration_ajax_error_dialog();
          return;
        }

        let running = data.running;
        let status = data.status;

        // show progress.
        jQuery( '#progress_status' ).text( status );

        if ( true === running || parseInt( running ) >= 1 ) {
          setTimeout( function () {
            personio_get_availability_check_info( post_id )
          }, 500 );
        } else {
          jQuery( '#progress' ).attr( 'value', 100 );
          let message = '<p>' + personioIntegrationLightAvailabilityJsVars.txt_check_success + '</p>';
          let dialog_config = {
            detail: {
              title: personioIntegrationLightAvailabilityJsVars.title_check_success,
              texts: [
                message
              ],
              buttons: [
                {
                  'action': 'location.reload();',
                  'variant': 'primary',
                  'text': personioIntegrationLightAvailabilityJsVars.lbl_ok
                }
              ]
            }
          }
          personio_integration_create_dialog( dialog_config );
        }
      }
    } )
}
