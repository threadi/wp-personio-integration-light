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
	PanelColorSettings,
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
	onChangeGroupBy
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

	// get filter types
	let filter_types = wp.hooks.applyFilters('personio_integration_filter_types', [
		{ label: __('list of links', 'wp-personio-integration'), value: 'linklist' },
		{ label: __('select-box', 'wp-personio-integration'), value: 'select' }
	]);

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
					<ToggleControl
						label={ __('Show filter', 'wp-personio-integration') }
						checked={ object.attributes.showFilter }
						onChange={ value => onChangeShowFilter( value, object ) }
					/>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={ __('Choose filter', 'wp-personio-integration') }
								value={ object.attributes.filter }
								options={ personioTaxonomies }
								multiple={ true }
								disabled={ !object.attributes.showFilter }
								onChange={ value => onChangeFilter(value, object) }
							/>
						}
					</div>
					<SelectControl
						label={ __('Type of filter', 'wp-personio-integration') }
						value={ object.attributes.filtertype }
						options={ filter_types }
						disabled={ !object.attributes.showFilter }
						onChange={ value => onChangeFilterType( value, object ) }
					/>
				</PanelBody>
			</InspectorControls>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'wp-personio-integration' ) }>
					<NumberControl
						label={__('amount', 'wp-personio-integration')}
						labelPosition='top'
						isShiftStepEnabled={ true }
						onChange={ value => onChangeLimit( value, object ) }
						shiftStep={ 1 }
						value={ object.attributes.limit }
					/>
					<SelectControl
						label={__('Sort direction', 'wp-personio-integration')}
						value={ object.attributes.sort }
						options={ [
							{ label: __('ascending', 'wp-personio-integration'), value: 'asc' },
							{ label: __('descending', 'wp-personio-integration'), value: 'desc' }
						] }
						onChange={ value => onChangeSort( value, object ) }
					/>
					<SelectControl
						label={__('Sort by', 'wp-personio-integration')}
						value={ object.attributes.sortby }
						options={ [
							{ label: __('title', 'wp-personio-integration'), value: 'title' },
							{ label: __('date', 'wp-personio-integration'), value: 'date' }
						] }
						onChange={ value => onChangeSortBy( value, object ) }
					/>
					<SelectControl
						label={__('Group by', 'wp-personio-integration')}
						value={ object.attributes.groupby }
						options={ personioTaxonomies }
						onChange={ value => onChangeGroupBy( value, object ) }
					/>
					<ToggleControl
						label={__('Show title', 'wp-personio-integration')}
						checked={ object.attributes.showTitle }
						onChange={ value => onChangeTitleVisibility( value, object ) }
					/>
					<ToggleControl
						label={__('Link title', 'wp-personio-integration')}
						checked={ object.attributes.linkTitle }
						onChange={ value => onChangeLinkingTitle( value, object ) }
					/>
					<ToggleControl
						label={__('show excerpt', 'wp-personio-integration')}
						checked={ object.attributes.showExcerpt }
						onChange={ value => onChangeExcerptVisibility( value, object ) }
					/>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('Choose excerpt components', 'wp-personio-integration')}
								value={object.attributes.excerptTemplates}
								options={ personioTaxonomies }
								multiple={true}
								disabled={ !object.attributes.showExcerpt }
								onChange={value => onChangeExcerptTemplates(value, object)}
							/>
						}
					</div>
					<ToggleControl
						label={__('View content', 'wp-personio-integration')}
						checked={ object.attributes.showContent }
						onChange={ value => onChangeContentVisibility( value, object )  }
					/>
					<ToggleControl
						label={__('View application link', 'wp-personio-integration')}
						checked={ object.attributes.showApplicationForm }
						onChange={ value => onChangeApplicationFormVisibility( value, object )  }
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
				block="wp-personio-integration/list"
				attributes={ object.attributes }
				httpMethod="POST"
			/>
		</div>
	);
}
