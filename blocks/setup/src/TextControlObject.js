import {onChangeField, setButtonDisabledState} from "./setup";

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

  /**
   * Render the output.
   *
   * @returns {JSX.Element}
   */
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
    if( this.props.object.state.results[this.props.field_name] ) {
      if ( this.props.object.state.results[this.props.field_name].result.error ) {
        classes = 'wp-setup-error';
        if ( this.props.object.state.results[this.props.field_name].text ) {
          helper_text = <><span className="hint">{this.props.object.state.results[this.props.field_name].result.text}</span><span
            dangerouslySetInnerHTML={{__html: this.props.field.help}}/></>;
        }
      }
      else if( this.props.object.state[this.props.field_name].length > 0 ) {
        classes = 'wp-setup-ok';
        this.props.object.state.results[this.props.field_name].filled = true;
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
