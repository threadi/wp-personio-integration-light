// noinspection NpmUsedModulesInstalled,JSUnresolvedReference

/**
 * File to handle the js-driven setup for this plugin.
 *
 * @source: https://wholesomecode.net/create-a-settings-page-using-wordpress-block-editor-gutenberg-components/
 */

// get individual styles.
import './setup.scss';
import RadioControlObject from './RadioControlObject';
import TextControlObject from './TextControlObject';

// import dependencies.
import {
  Fragment,
  Component,
} from '@wordpress/element';
import api from '@wordpress/api';
import {
  Button,
  Panel,
  PanelBody,
} from '@wordpress/components';
import React from 'react'

/**
 * Object which handles the setup.
 */
class App extends Component {
  constructor() {
    super( ...arguments );
    this.state = {
      result: {},
      successfully_filled: [],
      is_api_loaded: false,
    };

    /**
     * Add our fields to the list with empty init value.
     */
    Object.keys(this.props.fields[1]).map( field_name => {
      this.state[field_name] = '';
    })
  }

  /**
   * Get actual values for each setting.
   */
  componentDidMount() {
    api.loadPromise.then( () => {
      const { is_api_loaded } = this.state;
      if ( is_api_loaded === false ) {
        this.settings = new api.models.Settings();
        this.settings.fetch().then( ( response ) => {
          // collect settings for state, first mark the api as loaded.
          let state = {
            result: {},
            is_api_loaded: true,
          };

          // check if response contains one of our fields and add its value to state.
          Object.keys(this.props.fields[1]).map( field_name => {
            if( response[field_name] ) {
              state[field_name] = response[field_name];
            }
          });

          // set resulting state.
          this.setState(state);
        } );
      }
    } );
  }

  /**
   * Render the controls with its settings.
   *
   * @param field_name
   * @param field
   * @returns {JSX.Element|string}
   */
  renderControlSetting( field_name, field ) {
    switch(field.type) {
      /**
       * Show TextControl component for setting.
       */
      case 'TextControl':
        return <TextControlObject field_name={ field_name } field={ field } object={ this } />;

      /**
       * Show RadioControl component for setting.
       */
      case 'RadioControl':
        return <RadioControlObject field_name={ field_name } field={ field } object={ this } />;

      /**
       * Return empty string for all other types.
       */
      default:
        return ''
    }
  }

  /**
   * Generate output.
   *
   * @returns {JSX.Element}
   */
  render() {
    /**
     * Check button usability.
     * Return true if button should be disabled.
     * Return false if he should be enabled.
     *
     * @returns {boolean}
     */
    let checkButtonUsability = () => {
      console.log("aaaa");
      return true;
    }
    /**
     * TODO Erfassen, ob alle Felder im aktuellen Schritt erfolgreich ausgefüllt sind über successfully_filled.
     * Und wenn ja den Button aktivieren.
     */

    return (
      <Fragment>
        <div className="wp-easy-setup__header">
          <div className="wp-easy-setup__container">
            <div className="wp-easy-setup__title">
              <h1>{ this.props.config.title }</h1>
            </div>
          </div>
        </div>
        <div className="wp-easy-setup__main">
          <Panel>
            <PanelBody>
              {Object.keys(this.props.fields[1]).map( field_name => (
                <div key={ field_name }>{this.renderControlSetting( field_name, this.props.fields[1][field_name] )}</div>
              ) )}
              <Button
                isPrimary
                disabled={() => checkButtonUsability()}
                onClick={() => onSaveSetup( this )}
              >
                { this.props.config.continue_button_label }
              </Button>
            </PanelBody>
          </Panel>
        </div>
      </Fragment>
    )
  }
}

/**
 * Load setup.
 */
document.addEventListener( 'DOMContentLoaded', () => {
  let html_obj = document.getElementById('wp-plugin-setup');
  if( html_obj ) {
    let confirm_dialog = ReactDOM.createRoot(html_obj);
    confirm_dialog.render(
      <App fields={JSON.parse(html_obj.dataset.fields)} config={JSON.parse(html_obj.dataset.config)} />
    );
  }
});

/**
 * Save the actual setup.
 */
export const onSaveSetup = ( object ) => {
  let state = object.state;
  delete state.is_api_loaded;
  new api.models.Settings( state ).save();
}

/**
 * Check value of single field. Mark field with hints if some error occurred.
 *
 * Change value of single field no matter what the result is.
 *
 * @param object
 * @param field
 * @param newValue
 * @param field_name
 */
export const onChangeField = ( object, field_name, field, newValue,  ) => {
  if( field.validation_callback ) {
    fetch( wp_easy_setup.validation_url, {
      method: 'POST',
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Content-Type': 'application/json',
        'X-WP-Nonce': wp_easy_setup.rest_nonce
      },
      body: JSON.stringify({
        'step': 1,
        'field_name': field_name,
        'value': newValue
      })
    } )
      .then( response => response.json() )
      .then( function(result) {
          object.result = result;
          object.setState( {[field_name]: newValue} );
        }
      )
      .catch( error => console.log( error ) ); // TODO better error handling
  }
  else {
    object.setState( {[field_name]: newValue} )
  }
}
