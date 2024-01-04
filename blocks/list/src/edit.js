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
	ToggleControl,
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
	onChangeFilter,
	onChangeFilterType,
	onChangeShowFilter,
	onChangeSortBy,
	onChangeGroupBy,
	onChangeTemplate
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
				return select('core').getEntityRecords('personio/v1', 'archive-templates') || [];
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
				return select('core').getEntityRecords('personio/v1', 'taxonomies') || [];
			}
		);
	}

	// set max amount for listings.
	let max_amount = wp.hooks.applyFilters('personio.list.amount', 10);

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Filter', 'personio-integration-light' ) }>
          <div class="alert"><p>{ __( 'Please use the Filter Block instead of this options.', 'personio-integration-light' ) }</p></div>
					<ToggleControl
						label={ __('Show filter', 'personio-integration-light') }
						checked={ object.attributes.showFilter }
						onChange={ value => onChangeShowFilter( value, object ) }
					/>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={ __('Choose filter', 'personio-integration-light') }
								value={ object.attributes.filter }
								options={ personioTaxonomies }
								multiple={ true }
								disabled={ !object.attributes.showFilter }
								onChange={ value => onChangeFilter(value, object) }
							/>
						}
					</div>
					<SelectControl
						label={ __('Type of filter', 'personio-integration-light') }
						value={ object.attributes.filtertype }
						options={ filter_types }
						disabled={ !object.attributes.showFilter }
						onChange={ value => onChangeFilterType( value, object ) }
					/>
				</PanelBody>
			</InspectorControls>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'personio-integration-light' ) }>
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
						label={__('amount', 'personio-integration-light')}
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
							{ label: __('ascending', 'personio-integration-light'), value: 'asc' },
							{ label: __('descending', 'personio-integration-light'), value: 'desc' }
						] }
						onChange={ value => onChangeSort( value, object ) }
					/>
					<SelectControl
						label={__('Sort by', 'personio-integration-light')}
						value={ object.attributes.sortby }
						options={ [
							{ label: __('title', 'personio-integration-light'), value: 'title' },
							{ label: __('date', 'personio-integration-light'), value: 'date' }
						] }
						onChange={ value => onChangeSortBy( value, object ) }
					/>
					<SelectControl
						label={__('Group by', 'personio-integration-light')}
						value={ object.attributes.groupby }
						options={ personioTaxonomies }
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
						label={__('show excerpt', 'personio-integration-light')}
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
						label={__('View application link', 'personio-integration-light')}
						checked={ object.attributes.showApplicationForm }
						onChange={ value => onChangeApplicationFormVisibility( value, object )  }
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="wp-personio-integration/list"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
