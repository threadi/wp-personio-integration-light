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
	onChangeGroupBy
} from '../../components'

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

	/**
	 * Collect return for the edit-function
	 */
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Filter', 'wp-personio-integration' ) }>
					<ToggleControl
						label={__('show filter', 'wp-personio-integration')}
						checked={ object.attributes.showFilter }
						onChange={ value => onChangeShowFilter( value, object ) }
					/>
					<div className="wp-personio-integration-selectcontrol-multiple">
						{
							<SelectControl
								label={__('choose filter', 'wp-personio-integration')}
								value={object.attributes.filter}
								options={[
									{label: __('recruiting category', 'wp-personio-integration'),value: 'recruitingCategory'},
									{label: __('schedule', 'wp-personio-integration'), value: 'schedule'},
									{label: __('office', 'wp-personio-integration'), value: 'office'},
									{label: __('department', 'wp-personio-integration'), value: 'department'},
									{label: __('seniority', 'wp-personio-integration'), value: 'seniority'},
									{label: __('experience', 'wp-personio-integration'), value: 'experience'},
									{label: __('occupation', 'wp-personio-integration'), value: 'occupation'}
								]}
								multiple={true}
								onChange={value => onChangeFilter(value, object)}
							/>
						}
					</div>
					<SelectControl
						label={__('type of filter', 'wp-personio-integration')}
						value={ object.attributes.filtertype }
						options={ [
							{ label: __('list of links', 'wp-personio-integration'), value: 'linklist' },
							{ label: __('select-box', 'wp-personio-integration'), value: 'select' }
						] }
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
						options={[
							{label: __('not grouped', 'wp-personio-integration'), value: ''},
							{label: __('recruiting category', 'wp-personio-integration'), value: 'recruitingCategory'},
							{label: __('schedule', 'wp-personio-integration'), value: 'schedule'},
							{label: __('office', 'wp-personio-integration'), value: 'office'},
							{label: __('department', 'wp-personio-integration'), value: 'department'},
							{label: __('seniority', 'wp-personio-integration'), value: 'seniority'},
							{label: __('experience', 'wp-personio-integration'), value: 'experience'},
							{label: __('occupation', 'wp-personio-integration'), value: 'occupation'}
						]}
						onChange={ value => onChangeGroupBy( value, object ) }
					/>
					<ToggleControl
						label={__('show title', 'wp-personio-integration')}
						checked={ object.attributes.showTitle }
						onChange={ value => onChangeTitleVisibility( value, object ) }
					/>
					<ToggleControl
						label={__('link title', 'wp-personio-integration')}
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
						label={__('view content', 'wp-personio-integration')}
						checked={ object.attributes.showContent }
						onChange={ value => onChangeContentVisibility( value, object )  }
					/>
					<ToggleControl
						label={__('view application link', 'wp-personio-integration')}
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
