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
	ToggleControl,
	SelectControl,
	PanelBody
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	onChangeExcerptTemplates,
} from '../../components';
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

	/**
	 * On change of colon setting.
	 *
	 * @param newValue
	 * @param object
	 */
	const onChangeColonVisibility = ( newValue, object ) => {
		object.setAttributes( { colon: newValue } );
	}

	/**
	 * On change of line break setting.
	 *
	 * @param newValue
	 * @param object
	 */
	const onChangeWrapVisibility = ( newValue, object ) => {
		object.setAttributes( { wrap: newValue } );
	}

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'personio-integration-light' ) }>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose details', 'personio-integration-light')}
								value={object.attributes.excerptTemplates}
								options={[
									{label: __('Category', 'personio-integration-light'), value: 'recruitingCategory'},
									{label: __('Contract type', 'personio-integration-light'), value: 'schedule'},
									{label: __('Location', 'personio-integration-light'), value: 'office'},
									{label: __('Department', 'personio-integration-light'), value: 'department'},
									{label: __('Experience', 'personio-integration-light'), value: 'seniority'},
									{label: __('Years of experience', 'personio-integration-light'), value: 'experience'},
									{label: __('Job type', 'personio-integration-light'), value: 'occupation'},
									{label: __('Job type details', 'personio-integration-light'), value: 'occupation_detail'}
								]}
								multiple={true}
								onChange={value => onChangeExcerptTemplates(value, object)}
							/>
						}
					</div>
					<ToggleControl
						label={__('With colon', 'personio-integration-light')}
						checked={ object.attributes.colon }
						onChange={ value => onChangeColonVisibility( value, object )  }
					/>
					<ToggleControl
						label={__('With line break', 'personio-integration-light')}
						checked={ object.attributes.wrap }
						onChange={ value => onChangeWrapVisibility( value, object )  }
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/details"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
