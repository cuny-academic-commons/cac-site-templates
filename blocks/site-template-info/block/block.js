/**
 * BLOCK: site-template-info
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

import SiteSearch from '../../../components/site-search'

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: a Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'cac-site-templates/cac-site-template-info', {
	title: __( 'Site Template Info' ), // Block title.
	icon: 'book-alt',
	category: 'common',
	keywords: [
		__( 'Site Template' ),
		__( 'CAC' ),
		__( 'CUNY Academic Commons' ),
	],

	attributes: {
		templateSiteId: {
			type: 'integer',
			source: 'meta',
			meta: 'template-site-id',
		}
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	edit: function( props ) {
		const {
			attributes: {
				selectedTemplateSites,
				templateSiteId
			}
		} = props

		const { FormTokenField } = wp.components

		let refs = {}

		const setSelectedTemplateSites = (selectedTemplateSites) => {
			props.setAttributes( { selectedTemplateSites } )
		}

		const setSelectedTemplateSiteId = (selectedTemplateSiteId) => {
			props.setAttributes( { templateSiteId: selectedTemplateSiteId } )
		}

		const field = <SiteSearch
			ref={(templateSite) => {refs.templateSite = templateSite}}
			labelText="Template Site"
			setSelectedSites={setSelectedTemplateSites}
			setSelectedSiteId={setSelectedTemplateSiteId}
			selected={selectedTemplateSites}
			selectedSiteId={templateSiteId}
		/>

		return field
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save: function( props ) {
		return (
			<div>&nbsp;</div>
		);
	}
} );
