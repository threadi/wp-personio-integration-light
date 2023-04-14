/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import Save from "./save";
import {iconEl} from "../../components";

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( 'wp-personio-integration/list', {
	title: __( 'Personio Positions', 'wp-personio-integration' ),
	description: __('Provides a Gutenberg Block to show a list of positions provided by Personio.', 'wp-personio-integration'),
	icon: iconEl,

	attributes: {
		showFilter: {
			type: 'boolean',
			default: true
		},
		filter: {
			type: 'array',
			default: ['recruitingCategory','schedule','office']
		},
		filtertype: {
			type: 'string',
			default: 'linklist'
		},
		limit: {
			type: 'integer',
			default: 0
		},
		sort: {
			type: 'string',
			default: 'asc'
		},
		sortby: {
			type: 'string',
			default: 'title'
		},
		groupby: {
			type: 'string',
			default: ''
		},
		showTitle: {
			type: 'boolean',
			default: true
		},
		linkTitle: {
			type: 'boolean',
			default: true
		},
		showExcerpt: {
			type: 'boolean',
			default: true
		},
		excerptTemplates: {
			type: 'array',
			default: ['recruitingCategory','schedule','office']
		},
		showContent: {
			type: 'boolean',
			default: false
		},
		showApplicationForm: {
			type: 'boolean',
			default: false
		},
		color: {
			type: 'string'
		}
	},

	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save: Save,
} );
