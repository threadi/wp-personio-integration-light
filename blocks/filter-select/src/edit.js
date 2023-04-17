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
	ToggleControl
} from '@wordpress/components';
import {
	InspectorControls,
	PanelColorSettings,
	useBlockProps
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	onChangeFilter,
	onChangeHideFilterTitle,
	onChangeHideResetLink,
	onChangeHideSubmitButton
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
	useEffect(() => {
		dispatch('core').addEntities([
			{
				name: 'taxonomies', // route name
				kind: 'personio/v1', // namespace
				baseURL: '/personio/v1/taxonomies' // API path without /wp-json
			}
		]);
	}, []);
	let personioTaxonomies = useSelect( ( select ) => {
			return select('core').getEntityRecords('personio/v1', 'taxonomies') || [];
		}
	);

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Filter', 'wp-personio-integration' ) }>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose filter', 'wp-personio-integration')}
								value={object.attributes.filter}
								options={ personioTaxonomies }
								multiple={true}
								onChange={value => onChangeFilter(value, object)}
							/>
						}
					</div>
					<ToggleControl
						label={__('Hide filter title', 'wp-personio-integration')}
						checked={ object.attributes.hideFilterTitle }
						onChange={ value => onChangeHideFilterTitle( value, object ) }
					/>
					<ToggleControl
						label={__('Hide submit button', 'wp-personio-integration')}
						checked={ object.attributes.hideSubmitButton }
						onChange={ value => onChangeHideSubmitButton( value, object ) }
					/>
					<ToggleControl
						label={__('Hide reset link', 'wp-personio-integration')}
						checked={ object.attributes.hideResetLink }
						onChange={ value => onChangeHideResetLink( value, object ) }
					/>
				</PanelBody>
			</InspectorControls>
			<InspectorControls>
				<PanelColorSettings
					title={__('Color settings', 'wp-personio-integration')}
					initialOpen={false}
					colorSettings={[
						{
							value: object.attributes.textColor,
							onChange: (color) => object.setAttributes({ textColor: color }),
							label: __('Text color', 'wp-personio-integration')
						},
						{
							value: object.attributes.linkColor,
							onChange: (color) => object.setAttributes({ linkColor: color }),
							label: __('Link color', 'wp-personio-integration')
						},
						{
							value: object.attributes.backgroundColor,
							onChange: (color) => object.setAttributes({ backgroundColor: color }),
							label: __('Background color', 'wp-personio-integration')
						}
					]}
				/>
			</InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/filter-select"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
