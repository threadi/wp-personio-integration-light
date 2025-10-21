/**
 * Show the dialog to start the manual import.
 */
function personio_integration_show_manual_import_dialog() {
  // get the dialog via AJAX.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightManualImportJsVars.ajax_url,
    data: {
      'action': 'personio_integration_get_manual_import_dialog',
      'nonce': personioIntegrationLightManualImportJsVars.manual_import_dialog_nonce
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_ajax_error_dialog( errorThrown )
    },
    success: function( result ) {
      personio_integration_create_dialog( result );
    }
  });
}

/**
 * Start the manual import.
 */
function personio_integration_run_manual_import() {
  // start import.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightManualImportJsVars.ajax_url,
    data: {
      'action': 'personio_integration_run_manual_import',
      'nonce': personioIntegrationLightManualImportJsVars.run_manual_import_nonce
    },
    beforeSend: function() {
      // show progress.
      let dialog_config = {
        detail: {
          title: personioIntegrationLightManualImportJsVars.title_manual_import_progress,
          progressbar: {
            active: true,
            progress: 0,
            id: 'progress',
            label_id: 'progress_status'
          },
        }
      }
      personio_integration_create_dialog( dialog_config );
    },
    success: function( response ) {
      personio_integration_create_dialog( response );
    },
  });
}

/**
 * Add events in choose dialog.
 */
function personio_integration_run_manual_import_callback() {
  // add event.
  jQuery( '.personio-integration-manual-import-selection #check_all').on( 'change', function() {
    let new_state = jQuery( this ).is(':checked');
    jQuery( '.personio-integration-manual-import-selection input[type="checkbox"]' ).each(function() {
      jQuery( this ).attr( 'checked', new_state );
    });
  });
}

/**
 * Save the selected positions in WordPress.
 */
function personio_integration_save_manual_import() {
  // get the selected positions.
  let selected_positions = []
  jQuery( '.personio-integration-manual-import-selection input[type="checkbox"]:checked' ).each(function() {
    selected_positions.push( jQuery( this ).data('personio-id') );
  });

  // get the list of all positions.
  let all_positions = jQuery( '#all_positions' ).val();

  // start import.
  jQuery.ajax({
    type: "POST",
    url: personioIntegrationLightManualImportJsVars.ajax_url,
    data: {
      'action': 'personio_integration_save_manual_import',
      'nonce': personioIntegrationLightManualImportJsVars.save_manual_import_nonce,
      'selected_positions': selected_positions,
      'all_positions': all_positions
    },
    beforeSend: function() {
      // show progress.
      let dialog_config = {
        detail: {
          title: personioIntegrationLightManualImportJsVars.title_manual_import_saving_progress,
          progressbar: {
            active: true,
            progress: 0,
            id: 'progress',
            label_id: 'progress_status'
          },
        }
      }
      personio_integration_create_dialog( dialog_config );
    },
    success: function( response ) {
      personio_integration_create_dialog( response )
    },
  });
}
