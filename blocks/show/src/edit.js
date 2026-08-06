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
  Personio_Helper_Panel,
  registerPersonioEntity
} from '../../components';
const { dispatch, useSelect } = wp.data;
const { useEffect, useMemo } = wp.element;

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
  useEffect( () => {
    if ( ! object.attributes.blockId ) {
      object.setAttributes( { blockId: object.clientId } );
    }
  }, [ object.attributes.blockId, object.clientId ] );

  // get taxonomies and positions.
  let personioTaxonomies = [];
  let positions = [];
  if( !object.attributes.preview ) {
    positions = useSelect(
      (select) => select( 'core' ).getEntityRecords( 'postType', 'personioposition', {per_page: -1} ), []
    );

    useEffect(() => {
      registerPersonioEntity( 'taxonomies', '/personio/v1/taxonomies' );
    }, []);
    personioTaxonomies = useSelect((select) => {
        return select('core').getEntityRecords('personio/v1', 'taxonomies', { per_page: 20 }) || [];
      }
    );
  }

  // Options expects [{label: ..., value: ...}]
  // Only rebuild this list when the fetched positions actually change, instead of
  // on every re-render of the Edit component (e.g. on every Inspector-toggle).
  const positionOptions = useMemo(() => {
    let options = !Array.isArray(positions) ? positions : positions
      .map(
        // Format the options for display in the <SelectControl/>
        (position) => ({
          label: position.title.raw,
          value: position.meta.personioId, // the value saved as postType in attributes
        })
      );

    // create an array if it is empty until now
    if( !Array.isArray(options) ) {
      options = [];
    }

    // add entry on first index of list of positions
    options.unshift({
      label: __( 'Please choose', 'personio-integration-light' ),
      value: 0
    });

    return options;
  }, [ positions ]);


  // disable fields if no position is selected.
  // Depends only on positionOptions and the selected id, so it is recalculated
  // only when either of those actually changes.
  const disabledFields = useMemo(() => {
    if( object.attributes.id === 0 ) {
      return true;
    }
    return !positionOptions.some((position) => position.value === object.attributes.id);
  }, [ positionOptions, object.attributes.id ]);

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
