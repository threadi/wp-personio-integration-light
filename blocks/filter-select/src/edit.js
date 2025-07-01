/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * Add individual dependencies.
 */
import {
	PanelBody,
	SelectControl,
	ToggleControl,
  __experimentalInputControl as InputControl
} from '@wordpress/components';
import {
	InspectorControls,
  InspectorAdvancedControls,
	useBlockProps
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
  onChangeFilter,
  onChangeHideFilterTitle,
  onChangeHideResetLink,
  onChangeHideSubmitButton,
  onChangeLinkToAnchor,
  Personio_Helper_Panel
} from '../../components'
const { dispatch, useSelect } = wp.data;
const { useEffect } = wp.element;

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param object
 * @return {WPElement} Element to render.
 */
export default function Edit( object ) {

	// secure id of this block
	useEffect(() => {
		object.setAttributes({blockId: object.clientId});
	});

	// get taxonomies
	let personioTaxonomies = [];
	if( !object.attributes.preview ) {
		useEffect(() => {
			dispatch('core').addEntities([
				{
					name: 'taxonomies', // route name
					kind: 'personio/v1', // namespace
					baseURL: '/personio/v1/taxonomies' // API path without /wp-json
				}
			]);
		}, []);
		personioTaxonomies = useSelect((select) => {
				return select('core').getEntityRecords('personio/v1', 'taxonomies', { per_page: 20 } ) || [];
			}
		);
	}

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody initialOpen={false} title={ __( 'Filter', 'personio-integration-light' ) }>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose filter', 'personio-integration-light')}
								value={object.attributes.filter}
								options={ personioTaxonomies }
								multiple={true}
								onChange={value => onChangeFilter(value, object)}
							/>
						}
					</div>
					<ToggleControl
						label={__('Hide filter title', 'personio-integration-light')}
						checked={ object.attributes.hideFilterTitle }
						onChange={ value => onChangeHideFilterTitle( value, object ) }
					/>
          <ToggleControl
            label={__('Hide submit button', 'personio-integration-light')}
            checked={ object.attributes.hideSubmitButton }
            onChange={ value => onChangeHideSubmitButton( value, object ) }
          />
          <ToggleControl
            label={__('Hide reset link', 'personio-integration-light')}
            checked={ object.attributes.hideResetLink }
            onChange={ value => onChangeHideResetLink( value, object ) }
          />
        </PanelBody>
        <Personio_Helper_Panel/>
      </InspectorControls>
      <InspectorAdvancedControls key="inspector">
        <InputControl
          label={__('Use this anchor for any links', 'personio-integration-light')}
          value={object.attributes.link_to_anchor}
          onChange={value => onChangeLinkToAnchor( value, object )}
        />
      </InspectorAdvancedControls>
      <ServerSideRender
        block="wp-personio-integration/filter-select"
        attributes={ object.attributes }
        httpMethod="POST"
      />
    </div>
  );
}
