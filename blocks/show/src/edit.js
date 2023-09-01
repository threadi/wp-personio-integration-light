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
	onChangeTitleVisibility,
	onChangeExcerptVisibility,
	onChangeContentVisibility,
	onChangeApplicationFormVisibility,
	onChangeExcerptTemplates,
	onChangeId,
	onChangeLinkingTitle
} from '../../components';
const { useSelect } = wp.data;
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

	// useSelect to retrieve all post types
	const positions = useSelect(
		(select) => select('core').getEntityRecords('postType', 'personioposition', { per_page: -1 }), []
	);

	// Options expects [{label: ..., value: ...}]
	// noinspection JSUnresolvedVariable
	let positionOptions = !Array.isArray(positions) ? positions : positions
		.map(
			// Format the options for display in the <SelectControl/>
			(position) => ({
				label: position.title.raw,
				value: position.meta.personioId, // the value saved as postType in attributes
			})
		);

	// create an array if it is empty until now
	if( !Array.isArray(positionOptions) ) {
		positionOptions = [];
	}

	// add entry on first index of list of positions
	positionOptions.unshift({
		label: __( 'Please choose', 'wp-personio-integration' ),
		value: 0
	});

	// disable fields if no position is selected
	let disabledFields = false;
	if( object.attributes.id === 0 ) {
		disabledFields = true;
	}
	else {
		let found = false;
		positionOptions.map(function(position) { if( position.value === object.attributes.id ) { found = true; } });
		if( !found ) {
			disabledFields = true;
		}
	}

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'wp-personio-integration' ) }>
					<SelectControl
						label={__('Select position', 'wp-personio-integration')}
						options={ positionOptions }
						value={object.attributes.id}
						onChange={(value) => onChangeId( parseInt(value), object )}
					/>
					<ToggleControl
						label={__('show title', 'wp-personio-integration')}
						checked={ object.attributes.showTitle }
						onChange={ value => onChangeTitleVisibility( value, object ) }
						disabled={ disabledFields }
					/>
					<ToggleControl
						label={__('link title', 'wp-personio-integration')}
						checked={ object.attributes.linkTitle }
						onChange={ value => onChangeLinkingTitle( value, object ) }
						disabled={ disabledFields }
					/>
					<ToggleControl
						label={__('show excerpt', 'wp-personio-integration')}
						checked={ object.attributes.showExcerpt }
						onChange={ value => onChangeExcerptVisibility( value, object ) }
						disabled={ disabledFields }
					/>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose details', 'wp-personio-integration')}
								value={object.attributes.excerptTemplates}
								options={[
									{label: __('Category', 'wp-personio-integration'), value: 'recruitingCategory'},
									{label: __('Contract type', 'wp-personio-integration'), value: 'schedule'},
									{label: __('Location', 'wp-personio-integration'), value: 'office'},
									{label: __('Department', 'wp-personio-integration'), value: 'department'},
									{label: __('Experience', 'wp-personio-integration'), value: 'seniority'},
									{label: __('Years of experience', 'wp-personio-integration'), value: 'experience'},
									{label: __('Job type', 'wp-personio-integration'), value: 'occupation'}
								]}
								multiple={true}
								onChange={value => onChangeExcerptTemplates(value, object)}
								disabled={ disabledFields }
							/>
						}
					</div>
					<ToggleControl
						label={__('view content', 'wp-personio-integration')}
						checked={ object.attributes.showContent }
						onChange={ value => onChangeContentVisibility( value, object )  }
						disabled={ disabledFields }
					/>
					<ToggleControl
						label={__('view application link', 'wp-personio-integration')}
						checked={ object.attributes.showApplicationForm }
						onChange={ value => onChangeApplicationFormVisibility( value, object )  }
						disabled={ disabledFields }
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/show"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}