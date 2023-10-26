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
		label: __( 'Please choose', 'personio-integration-light' ),
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
				<PanelBody title={ __( 'Settings', 'personio-integration-light' ) }>
					<SelectControl
						label={__('Select position', 'personio-integration-light')}
						options={ positionOptions }
						value={object.attributes.id}
						onChange={(value) => onChangeId( parseInt(value), object )}
					/>
					<ToggleControl
						label={__('show title', 'personio-integration-light')}
						checked={ object.attributes.showTitle }
						onChange={ value => onChangeTitleVisibility( value, object ) }
						disabled={ disabledFields }
					/>
					<ToggleControl
						label={__('link title', 'personio-integration-light')}
						checked={ object.attributes.linkTitle }
						onChange={ value => onChangeLinkingTitle( value, object ) }
						disabled={ disabledFields }
					/>
					<ToggleControl
						label={__('show excerpt', 'personio-integration-light')}
						checked={ object.attributes.showExcerpt }
						onChange={ value => onChangeExcerptVisibility( value, object ) }
						disabled={ disabledFields }
					/>
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
									{label: __('Job type detail', 'personio-integration-light'), value: 'occupation_detail'}
								]}
								multiple={true}
								onChange={value => onChangeExcerptTemplates(value, object)}
								disabled={ disabledFields }
							/>
						}
					</div>
					<ToggleControl
						label={__('view content', 'personio-integration-light')}
						checked={ object.attributes.showContent }
						onChange={ value => onChangeContentVisibility( value, object )  }
						disabled={ disabledFields }
					/>
					<ToggleControl
						label={__('view application link', 'personio-integration-light')}
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
