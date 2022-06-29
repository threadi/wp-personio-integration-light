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

import { iconEl } from '../../components'

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( 'wp-personio-integration/show', {
	title: __( 'Personio Position', 'wp-personio-integration' ),
	description: __('Provides a Gutenberg Block to show a position provided by personio.', 'wp-personio-integration'),
	icon: iconEl,
	example: {
		attributes: {
			mode: 'preview'
		}
	},

	/**
	 * Attributes for this block.
	 */
	attributes: {
		id: {
			type: 'integer',
			default: 0
		},
		showTitle: {
			type: 'boolean',
			default: true
		},
		linkTitle: {
			type: 'boolean',
			default: false
		},
		showExcerpt: {
			type: 'boolean',
			default: false
		},
		excerptTemplates: {
			type: 'array',
			default: ['recruitingCategory','schedule','office']
		},
		showContent: {
			type: 'boolean',
			default: true
		},
		showApplicationForm: {
			type: 'boolean',
			default: true
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
