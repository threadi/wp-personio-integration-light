import {onChangeField} from "./setup";

/**
 * Import dependencies.
 */
import { Component } from '@wordpress/element';
import { TextControl } from '@wordpress/components';

/**
 * Declare our custom TextControl-object
 */
export default class TextControlObject extends Component {
  constructor() {
    super( ...arguments );
  }

  render() {
    /**
     * Create helper text.
     *
     * @type {JSX.Element}
     */
    let helper_text = <span dangerouslySetInnerHTML={{__html: this.props.field.help}}/>

    /**
     * Get classes for TextControl depending on errors in actual setup.
     *
     * @type {string}
     */
    let classes = "";
    if( this.props.object.result && this.props.object.result.field_name === this.props.field_name ) {
      if ( this.props.object.result.result.error) {
        classes = 'wp-setup-error';
        if (this.props.object.result.result.text) {
          helper_text = <><span className="hint">{this.props.object.result.result.text}</span><span
            dangerouslySetInnerHTML={{__html: this.props.field.help}}/></>;
        }
        let successfully_filled_field_name_index =  this.props.object.state.successfully_filled.indexOf( this.props.field_name );
        if( successfully_filled_field_name_index !== -1 ) {
          this.props.object.state.successfully_filled.splice( successfully_filled_field_name_index, 1 );
        }
      }
      else if( this.props.object.state[this.props.field_name].length > 0 ) {
        classes = 'wp-setup-ok';
        let successfully_filled_field_name_index =  this.props.object.state.successfully_filled.indexOf( this.props.field_name );
        if( successfully_filled_field_name_index === -1 ) {
          this.props.object.state.successfully_filled.push( this.props.field_name );
        }
      }
      else if( this.props.object.state[this.props.field_name].length === 0 ) {
        let successfully_filled_field_name_index =  this.props.object.state.successfully_filled.indexOf( this.props.field_name );
        if( successfully_filled_field_name_index !== -1 ) {
          this.props.object.state.successfully_filled.splice( successfully_filled_field_name_index, 1 );
        }
      }
    }

    /**
     * Output resulting TextControl.
     */
    return <TextControl
        label={this.props.field.label}
        className={classes}
        help={helper_text}
        onChange={(value) => onChangeField( this.props.object, this.props.field_name, this.props.field, value )}
        placeholder={this.props.field.placeholder}
        value={this.props.object.state[this.props.field_name]}
      />
  }
}
