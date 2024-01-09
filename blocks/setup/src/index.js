/**
 * File to handle the js-driven setup for this plugin.
 *
 * @source: https://wholesomecode.net/create-a-settings-page-using-wordpress-block-editor-gutenberg-components/
 */

// get individual styles.
import './setup.scss';

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
  RadioControl,
  TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react'

/**
 * Object which handles the setup.
 */
class App extends Component {
  constructor() {
    super( ...arguments );
    this.state = {
      is_api_loaded: false,
    };

    /**
     * Add our configured fields to the list with empty init value.
     */
    for(let i=0; i < this.props.config[1].length; i++){
      this.state[this.props.config[1][i]['field']] = '';
    }
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
            is_api_loaded: true,
          };

          // check if response contains one of our config field and add its value to state.
          for(let i=0; i < this.props.config[1].length; i++){
            if( response[this.props.config[1][i]['field']] ) {
              state[this.props.config[1][i]['field']] = response[this.props.config[1][i]['field']];
            }
          }

          // set resulting state.
          this.setState(state);
        } );
      }
    } );
  }

  /**
   * Render the controls with its settings.
   *
   * @param field
   * @returns {JSX.Element|string}
   */
  renderControlSetting( field ) {
    switch(field.type) {
      case 'TextControl':
        return <TextControl
          label={ field.label }
          placeholder={ field.placeholder }
          onChange={ ( value ) => onChangeField( this, value, field.field ) }
          help={ <span dangerouslySetInnerHTML={{__html: field.help}} /> }
          value={ this.state[field.field] }
        />;
      case 'RadioControl':
        return <RadioControl
          label={ field.label }
          help={ <span dangerouslySetInnerHTML={{__html: field.help}} /> }
          selected={ this.state[field.field] }
          options={ field.options }
          onChange={ ( value ) => onChangeField( this, value, field.field ) }
        />
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
    return (
      <Fragment>
        <div className="personio-integration-light__header">
          <div className="personio-integration-light__container">
            <div className="personio-integration-light__title">
              <h1>{ __( 'Personio Integration Setup', 'wholesome-plugin' ) }</h1>
            </div>
          </div>
        </div>
        <div className="personio-integration-light__main">
          <Panel>
            <PanelBody>
              {this.props.config[1].map( field => (
                <div>{this.renderControlSetting(field)}</div>
              ) )}
              <Button
                isPrimary
                onClick={() => onSaveSetup( this )}
              >
                { __( 'Continue', 'personio-integration-light' ) }
              </Button>
            </PanelBody>
          </Panel>
        </div>
      </Fragment>
    )
  }
}

/**
 * Delete field and load setup.
 */
document.addEventListener( 'DOMContentLoaded', () => {
  let html_obj = document.getElementById('wp-plugin-setup');
  if( html_obj ) {
    let confirm_dialog = ReactDOM.createRoot(html_obj);
    confirm_dialog.render(
      <App config={JSON.parse(html_obj.dataset.config)}/>
    );
  }
});

/**
 * Save the actual setup.
 */
export const onSaveSetup = ( object ) => {
  let state = object.state;
  delete state.is_api_loaded;
  console.log(state);
  const settings = new api.models.Settings( state );
  console.log(settings);
  settings.save();
}

/**
 * Change value of single field.
 *
 * @param object
 * @param newValue
 * @param field
 */
export const onChangeField = ( object, newValue, field ) => {
  object.setState( { [field]: newValue } );
}
