jQuery(document).ready(function($) {
  /**
   * Set intro after initial set up of the plugin on list view in backend.
   */
  $('body.post-type-personioposition.edit-personioposition-php .table-view-list').each(function() {

    // create the intro tour.
    let intro = window.driver.js.driver( {
      nextBtnText: personioIntegrationLightIntroJsVars.button_title_next,
      prevBtnText: personioIntegrationLightIntroJsVars.button_title_back,
      doneBtnText: personioIntegrationLightIntroJsVars.button_title_done,
      popoverClass: 'personio-integration-intro',
      disableActiveInteraction: true,
      overlayClickBehavior: function() {}, // ignore clicks on the overlay.
      steps: [
        {
          popover: {
            title: personioIntegrationLightIntroJsVars.step_1_title,
            description: personioIntegrationLightIntroJsVars.step_1_intro,
            showButtons: [ 'next', 'close' ]
          }
        },
        {
          element: this,
          popover: {
            title: personioIntegrationLightIntroJsVars.step_2_title,
            description: personioIntegrationLightIntroJsVars.step_2_intro
          }
        },
        {
          element: '#screen-meta',
          popover: {
            title: personioIntegrationLightIntroJsVars.step_3_title,
            description: personioIntegrationLightIntroJsVars.step_3_intro
          }
        },
        {
          element: '.page-title-action.personio-integration-import-hint',
          popover: {
            title: personioIntegrationLightIntroJsVars.step_4_title,
            description: personioIntegrationLightIntroJsVars.step_4_intro
          }
        },
        {
          element: '#wp-admin-bar-personio-integration-list',
          popover: {
            title: personioIntegrationLightIntroJsVars.step_5_title,
            description: personioIntegrationLightIntroJsVars.step_5_intro
          }
        },
        {
          element: '#menu-posts-personioposition li:nth-child(3)',
          popover: {
            title: personioIntegrationLightIntroJsVars.step_6_title,
            description: personioIntegrationLightIntroJsVars.step_6_intro
          }
        },
        {
          popover: {
            title: personioIntegrationLightIntroJsVars.step_7_title,
            description: personioIntegrationLightIntroJsVars.step_7_intro,
            popoverClass: 'personio-integration-intro personio-integration-intro-wide'
          }
        }
      ],

      // prepare every step before it is highlighted: toggle the screen options and the admin menu flyout.
      onHighlightStarted: function( element ) {
        let panel = $( '#screen-options-wrap' );
        let is_screen_meta = !! element && 'screen-meta' === element.id;

        // open the screen options for their step, close them for every other step.
        if ( is_screen_meta !== panel.is( ':visible' ) ) {
          $( '#screen-options-link-wrap' ).find( 'button' ).trigger( 'click' );
        }

        // open the flyout of an admin menu if the target is hidden in it (e.g. with a folded menu), close it otherwise.
        $( '#adminmenu li.opensub' ).removeClass( 'opensub' );
        if ( element && $( element ).closest( '#adminmenu' ).length && $( element ).offset().top < 0 ) {
          $( element ).closest( 'li.wp-has-submenu' ).addClass( 'opensub' );
        }
      },

      // recalculate the highlight after the slide animation of the screen options has been finished.
      onHighlighted: function( element, step, opts ) {
        $( '#screen-options-wrap' ).promise().then( function() {
          opts.driver.refresh();
        } );
      },

      // the close button ends the tour at any time, escape key and done button only on the last step.
      onCloseClick: personio_integration_tour_close,
      onDestroyStarted: personio_integration_tour_destroy_started,

      // save the exit of the tour.
      onDestroyed: function() {
        // close a flyout of the admin menu if the tour ends on its step.
        $( '#adminmenu li.opensub' ).removeClass( 'opensub' );

        personio_integration_intro_exit();
      }
    } );

    // start the tour.
    intro.drive();
  });
});

/**
 * Save the exit of the intro.
 */
function personio_integration_intro_exit() {
  jQuery.ajax( {
    type: "POST",
    url: personioIntegrationLightIntroJsVars.ajax_url,
    data: {
      'action': 'personio_integration_light_intro_closed',
      'nonce': personioIntegrationLightIntroJsVars.intro_closed_nonce
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      personio_integration_ajax_error_dialog( errorThrown )
    },
  } )
}
