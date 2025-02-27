/**
 * BLOCK: site-template-info
 */
import './style.scss';
import './editor.scss';

import { SiteSearch } from '../../../components/site-search';
import { TextControl } from '@wordpress/components';
import { withState } from '@wordpress/compose';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

registerBlockType( 'cac-site-templates/cac-site-template-info', {
	title: __( 'Site Template Info', 'cac-site-templates' ),
	icon: 'book-alt',
	category: 'common',
	keywords: [
		__( 'Site Template', 'cac-site-templates' ),
		__( 'CAC', 'cac-site-templates' ),
		__( 'CUNY Academic Commons', 'cac-site-templates' )
	],
	attributes: {
		demoSiteId: {
			type: 'integer',
			source: 'meta',
			meta: 'demo-site-id'
		},
		templateSiteId: {
			type: 'integer',
			source: 'meta',
			meta: 'template-site-id'
		},
		templateSiteLinkText: {
			type: 'string',
			source: 'meta',
			meta: 'template-site-link-text'
		}
	},

	edit: function (props) {
		const { attributes, setAttributes } = props;

		const demoSiteId = attributes.demoSiteId || '';
		const selectedDemoSites = attributes.selectedDemoSites || [];
		const selectedTemplateSites = attributes.selectedTemplateSites || [];
		const templateSiteId = attributes.templateSiteId || '';

		const setSelectedTemplateSites = (selectedTemplateSites) => setAttributes({ selectedTemplateSites });
		const setSelectedTemplateSiteId = (selectedTemplateSiteId) => setAttributes({ templateSiteId: selectedTemplateSiteId });
		const setSelectedDemoSites = (selectedDemoSites) => setAttributes({ selectedDemoSites });
		const setSelectedDemoSiteId = (selectedDemoSiteId) => setAttributes({ demoSiteId: selectedDemoSiteId });

		return (
			<div>
				<SiteSearch
					labelText="Template Site"
					setSelectedSites={setSelectedTemplateSites}
					setSelectedSiteId={setSelectedTemplateSiteId}
					selected={selectedTemplateSites}
					selectedSiteId={templateSiteId}
				/>
			</div>
		);
	},

	save: function () {
		return <div>&nbsp;</div>;
	}
});

const TemplateSiteLinkTextField = withState({ templateSiteLinkText: '' })(({ templateSiteLinkText, setState }) => (
	<TextControl
		label="Template Site Link Text"
		value={templateSiteLinkText}
		onChange={(newValue) => setState({ templateSiteLinkText: newValue })}
	/>
));
