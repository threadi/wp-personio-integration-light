jQuery(document).ready(function($) {
  /**
   * Set intro after initial set up of the plugin on list view in backend.
   */
  $('body.post-type-personioposition.edit-personioposition-php .table-view-list').each(function() {
    introJs().setOptions( {
      nextLabel: personioIntegrationLightIntroJsVars.button_title_next,
      prevLabel: personioIntegrationLightIntroJsVars.button_title_back,
      doneLabel: personioIntegrationLightIntroJsVars.button_title_done,
      exitOnEsc: false,
      exitOnOverlayClick: false,
      disableInteraction: true,
      steps: [
        {
          title: personioIntegrationLightIntroJsVars.step_1_title,
          intro: personioIntegrationLightIntroJsVars.step_1_intro,
        },
        {
          element: this,
          title: personioIntegrationLightIntroJsVars.step_2_title,
          intro: personioIntegrationLightIntroJsVars.step_2_intro
        },
        {
          element: document.querySelector( '#screen-meta' ),
          title: personioIntegrationLightIntroJsVars.step_3_title,
          intro: personioIntegrationLightIntroJsVars.step_3_intro
        },
        {
          element: document.querySelector( '.personio-integration-import-hint' ),
          title: personioIntegrationLightIntroJsVars.step_4_title,
          intro: personioIntegrationLightIntroJsVars.step_4_intro
        },
        {
          element: document.querySelector( '#wp-admin-bar-personio-integration-list' ),
          title: personioIntegrationLightIntroJsVars.step_5_title,
          intro: personioIntegrationLightIntroJsVars.step_5_intro
        },
        {
          element: document.querySelector( '#menu-posts-personioposition li:nth-child(3)' ),
          title: personioIntegrationLightIntroJsVars.step_6_title,
          intro: personioIntegrationLightIntroJsVars.step_6_intro
        },
        {
          title: personioIntegrationLightIntroJsVars.step_7_title,
          intro: personioIntegrationLightIntroJsVars.step_7_intro,
          tooltipClass: 'intro-width'
        }
      ]
    } )
      .onbeforechange(function() {
        let obj = $("#screen-meta");
        if( obj.is(':visible') ) {
          obj.hide();
        }
      })
      .onchange(function( targetElement ) {
        if( "screen-meta" === targetElement.id ) {
          $("#screen-options-link-wrap").find('button').trigger('click');
        }
      })
      .onexit( function () {
        jQuery.ajax( {
          type: "POST",
          url: personioIntegrationLightIntroJsVars.ajax_url,
          data: {
            'action': 'personio_intro_closed',
            'nonce': personioIntegrationLightIntroJsVars.intro_closed_nonce
          }
        } )
      } )
      .start();
  });
});
