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
				<PanelBody title={ __( 'Settings', 'wp-personio-integration' ) }>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose details', 'wp-personio-integration')}
								value={object.attributes.excerptTemplates}
								options={[
									{label: __('recruiting category', 'wp-personio-integration'), value: 'recruitingCategory'},
									{label: __('schedule', 'wp-personio-integration'), value: 'schedule'},
									{label: __('office', 'wp-personio-integration'), value: 'office'},
									{label: __('department', 'wp-personio-integration'), value: 'department'},
									{label: __('seniority', 'wp-personio-integration'), value: 'seniority'},
									{label: __('experience', 'wp-personio-integration'), value: 'experience'},
									{label: __('occupation', 'wp-personio-integration'), value: 'occupation'}
								]}
								multiple={true}
								onChange={value => onChangeExcerptTemplates(value, object)}
							/>
						}
					</div>
					<ToggleControl
						label={__('With colon', 'wp-personio-integration')}
						checked={ object.attributes.colon }
						onChange={ value => onChangeColonVisibility( value, object )  }
					/>
					<ToggleControl
						label={__('With line break', 'wp-personio-integration')}
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