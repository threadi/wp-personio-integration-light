/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

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
  onChangeLinkingTitle,
  Personio_Helper_Panel
} from '../../components';
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

	// useSelect to retrieve all entries on our own cpt
	const positions = useSelect(
		(select) => select('core').getEntityRecords('postType', 'personioposition', { per_page: -1 }), []
	);

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
        return select('core').getEntityRecords('personio/v1', 'taxonomies', { per_page: 20 }) || [];
      }
    );
  }

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
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
						label={__('Select position', 'personio-integration-light')}
						options={ positionOptions }
						value={object.attributes.id}
						onChange={(value) => onChangeId( parseInt(value), object )}
					/>
					<ToggleControl
                        __nextHasNoMarginBottom
						label={__('Show title', 'personio-integration-light')}
						checked={ object.attributes.showTitle }
						onChange={ value => onChangeTitleVisibility( value, object ) }
						disabled={ disabledFields }
					/>
					<ToggleControl
                        __nextHasNoMarginBottom
						label={__('Link title', 'personio-integration-light')}
						checked={ object.attributes.linkTitle }
						onChange={ value => onChangeLinkingTitle( value, object ) }
						disabled={ disabledFields }
					/>
					<ToggleControl
                        __nextHasNoMarginBottom
						label={__('Show excerpt', 'personio-integration-light')}
						checked={ object.attributes.showExcerpt }
						onChange={ value => onChangeExcerptVisibility( value, object ) }
						disabled={ disabledFields }
					/>
          <SelectControl
              __next40pxDefaultSize
              __nextHasNoMarginBottom
              label={__('Choose details', 'personio-integration-light')}
              value={object.attributes.excerptTemplates}
              options={ personioTaxonomies }
              multiple={true}
              onChange={value => onChangeExcerptTemplates(value, object)}
              disabled={ disabledFields }
          />
					<ToggleControl
                        __nextHasNoMarginBottom
						label={__('View content', 'personio-integration-light')}
						checked={ object.attributes.showContent }
						onChange={ value => onChangeContentVisibility( value, object )  }
						disabled={ disabledFields }
					/>
					<ToggleControl
                        __nextHasNoMarginBottom
						label={__('View option to apply', 'personio-integration-light')}
						checked={ object.attributes.showApplicationForm }
						onChange={ value => onChangeApplicationFormVisibility( value, object )  }
						disabled={ disabledFields }
					/>
				</PanelBody>
        <Personio_Helper_Panel/>
			</InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/show"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
