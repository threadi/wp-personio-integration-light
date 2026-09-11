jQuery(document).ready(function($) {
  /**
   * Start diagnostic.
   */
  $( document ).on( 'click', '.personio-integration-light-diagnostics', function (e) {
    e.preventDefault();

    let out = $( '.personio-integration-light-diagnostics-results' );
    let btn = $( '.personio-integration-light-diagnostics' );
    let copy_btn = $( '.personio-integration-light-copy-diagnostic' );

    jQuery.ajax( {
      type: "POST",
      url: personioIntegrationLightDiagnosticsJsVars.ajax_url,
      data: {
        'action': 'personio_integration_light_run_diagnostics',
        'nonce': personioIntegrationLightDiagnosticsJsVars.run_diagnostics_nonce
      },
      beforeSend: function () {
        btn.prop( "disabled", true );
        btn.addClass( 'disabled' )
        copy_btn.prop( "disabled", true );
        copy_btn.addClass( 'disabled' );
        btn.html( personioIntegrationLightDiagnosticsJsVars.lbl_diagnose_running );
        out.css( 'display', 'block' );
        out.html( '<div class="spinner"></div>' );
      },
      error: function (jqXHR, textStatus, errorThrown) {
        personio_integration_ajax_error_dialog( errorThrown )
      },
      success: function (result) {
        out.html( result.data );
        btn.prop( 'disabled', false );
        btn.removeClass( 'disabled' )
        copy_btn.prop( 'disabled', false )
        copy_btn.removeClass( 'disabled' )
        btn.html( personioIntegrationLightDiagnosticsJsVars.lbl_diagnose_start );
      }
    } );
  } );

  /**
   * Copy the diagnostic results.
   */
  $( document ).on( 'click', '.personio-integration-light-copy-diagnostic', function (e) {
    e.preventDefault();

    let out = $( '.personio-integration-light-diagnostics-results' );
    let copy_btn = $( '.personio-integration-light-copy-diagnostic' );

    if( personio_light_copy_to_clipboard(out.html().trim()) ) {
      copy_btn.html( personioIntegrationLightDiagnosticsJsVars.lbl_copied );
      setTimeout( function () {
        copy_btn.html( personioIntegrationLightDiagnosticsJsVars.lbl_copy );
      }, 1000 );
    }
  });
});

/**
 * Copy given text to clipboard.
 *
 * @param text The text to copy.
 */
function personio_light_copy_to_clipboard( text ) {
  let helper = document.createElement("textarea");
  document.body.appendChild(helper);
  helper.value = text.replace(/(<([^>]+)>)/gi, "");
  helper.select();
  if( document.execCommand("copy") ) {
    document.body.removeChild(helper);
    return true;
  }
  document.body.removeChild(helper);
  return false;
}
