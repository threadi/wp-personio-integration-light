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
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {onChangeTemplate, Personio_Helper_Panel} from "../../components";
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

	// get possible templates.
	let templates = [];
	if( !object.attributes.preview ) {
		useEffect(() => {
			dispatch('core').addEntities([
				{
					name: 'jobdescription-templates',
					kind: 'personio/v1',
					baseURL: '/personio/v1/jobdescription-templates'
				}
			]);
		}, []);
		templates = useSelect((select) => {
				return select('core').getEntityRecords('personio/v1', 'jobdescription-templates', { per_page: 20 }) || [];
			}
		);
	}

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'personio-integration-light' ) }>
					<div className="wp-personio-integration-selectcontrol">
						{
							<SelectControl
								label={ __('Choose template', 'personio-integration-light') }
								value={ object.attributes.template }
								options={ templates }
								multiple={ false }
								onChange={ value => onChangeTemplate(value, object) }
							/>
						}
					</div>
				</PanelBody>
        <Personio_Helper_Panel/>
			</InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/description"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
