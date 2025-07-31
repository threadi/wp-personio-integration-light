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
	__experimentalNumberControl as NumberControl,
	PanelBody,
	SelectControl,
	ToggleControl
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	onChangeLimit,
	onChangeSort,
	onChangeTitleVisibility,
	onChangeExcerptVisibility,
	onChangeContentVisibility,
	onChangeApplicationFormVisibility,
	onChangeExcerptTemplates,
	onChangeLinkingTitle,
	onChangeSortBy,
	onChangeGroupBy,
	onChangeTemplate,
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

	// secure id of this block.
	useEffect(() => {
		object.setAttributes({blockId: object.clientId});
	});

	// get possible templates.
	let archive_templates = [];
	if( !object.attributes.preview ) {
		useEffect(() => {
			dispatch('core').addEntities([
				{
					name: 'archive-templates',
					kind: 'personio/v1',
					baseURL: '/personio/v1/archive-templates'
				}
			]);
		}, []);
		archive_templates = useSelect((select) => {
				return select('core').getEntityRecords('personio/v1', 'archive-templates', { per_page: 20 }) || [];
			}
		);
	}

	// get filter types.
	let filter_types = wp.hooks.applyFilters('personio_integration_filter_types', [
		{ label: __('list of links', 'personio-integration-light'), value: 'linklist' },
		{ label: __('select-box', 'personio-integration-light'), value: 'select' }
	], object.attributes.preview);

	// get taxonomies.
	let personioTaxonomies = [];
  let personioTaxonomiesGrouped = [];
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
    personioTaxonomiesGrouped = personioTaxonomies.map((x) => x);
    if( personioTaxonomiesGrouped[0] && personioTaxonomiesGrouped[0].id !== 0 ) {
      personioTaxonomiesGrouped.unshift( {id: 0, label: __( 'Ungrouped', 'personio-integration-light' ), value: ''} );
    }
	}

	// set max amount for listings.
	let max_amount = wp.hooks.applyFilters('personio.list.amount', 10);

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody initialOpen={false} title={ __( 'Settings', 'personio-integration-light' ) }>
					<div className="wp-personio-integration-selectcontrol">
						{
							<SelectControl
								label={ __('Choose template', 'personio-integration-light') }
								value={ object.attributes.template }
								options={ archive_templates }
								multiple={ false }
								onChange={ value => onChangeTemplate(value, object) }
							/>
						}
					</div>
					<NumberControl
						label={__('Amount', 'personio-integration-light')}
						labelPosition='top'
						isShiftStepEnabled={ true }
						onChange={ value => onChangeLimit( value, object ) }
						shiftStep={ 1 }
						max={ max_amount }
						min={ 0 }
						value={ object.attributes.limit }
					/>
					<SelectControl
						label={__('Sort direction', 'personio-integration-light')}
						value={ object.attributes.sort }
						options={ [
							{ label: __('Ascending', 'personio-integration-light'), value: 'asc' },
							{ label: __('Descending', 'personio-integration-light'), value: 'desc' }
						] }
						onChange={ value => onChangeSort( value, object ) }
					/>
					<SelectControl
						label={__('Sort by', 'personio-integration-light')}
						value={ object.attributes.sortby }
						options={ [
							{ label: __('Title', 'personio-integration-light'), value: 'title' },
							{ label: __('Date', 'personio-integration-light'), value: 'date' }
						] }
						onChange={ value => onChangeSortBy( value, object ) }
					/>
					<SelectControl
						label={__('Group by', 'personio-integration-light')}
						value={ object.attributes.groupby }
						options={ personioTaxonomiesGrouped }
						onChange={ value => onChangeGroupBy( value, object ) }
					/>
					<ToggleControl
						label={__('Show title', 'personio-integration-light')}
						checked={ object.attributes.showTitle }
						onChange={ value => onChangeTitleVisibility( value, object ) }
					/>
					<ToggleControl
						label={__('Link title', 'personio-integration-light')}
						checked={ object.attributes.linkTitle }
						onChange={ value => onChangeLinkingTitle( value, object ) }
					/>
					<ToggleControl
						label={__('Show excerpt', 'personio-integration-light')}
						checked={ object.attributes.showExcerpt }
						onChange={ value => onChangeExcerptVisibility( value, object ) }
					/>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose details', 'personio-integration-light')}
								value={object.attributes.excerptTemplates}
								options={ personioTaxonomies }
								multiple={true}
								disabled={ !object.attributes.showExcerpt }
								onChange={value => onChangeExcerptTemplates(value, object)}
							/>
						}
					</div>
					<ToggleControl
						label={__('View content', 'personio-integration-light')}
						checked={ object.attributes.showContent }
						onChange={ value => onChangeContentVisibility( value, object )  }
					/>
					<ToggleControl
						label={__('View option to apply', 'personio-integration-light')}
						checked={ object.attributes.showApplicationForm }
						onChange={ value => onChangeApplicationFormVisibility( value, object )  }
					/>
				</PanelBody>
        <Personio_Helper_Panel/>
      </InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/list"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
