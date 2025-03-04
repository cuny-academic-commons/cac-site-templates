import { registerPlugin } from '@wordpress/plugins';
import { useSelect, useDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';

import {
	PanelRow,
	TextControl
} from '@wordpress/components';

import { SiteSearch } from './components/site-search';

const CACSiteTemplateInfo = () => {
	const meta = useSelect( ( select ) =>
		select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {}
	);
	console.log(meta)

	const { editPost } = useDispatch( 'core/editor' );

	const updateMeta = ( key, value ) => {
		editPost( { meta: { ...meta, [ key ]: value } } );
	}

	return (
		<PluginDocumentSettingPanel
			name="cac-site-template-info"
			title={ __( 'Site Template Info', 'cac-site-template-info' ) }
		>
			<PanelRow>
				<SiteSearch
					labelText="Template Site"
					setSelectedSites={(selectedSites) => updateMeta('selected-template-sites', selectedSites)}
					setSelectedSiteId={(siteId) => updateMeta('template-site-id', siteId)}
					selected={meta['selected-template-sites'] || []}
					selectedSiteId={meta['template-site-id'] || ''}
				/>
			</PanelRow>

			<PanelRow>
				<TextControl
					label="Template Site Link Text"
					value={meta['template-site-link-text'] || ''}
					onChange={(newValue) => updateMeta('template-site-link-text', newValue)}
				/>
			</PanelRow>

			<PanelRow>
				<SiteSearch
					labelText="Demo Site"
					setSelectedSites={(selectedSites) => updateMeta('selected-demo-sites', selectedSites)}
					setSelectedSiteId={(siteId) => updateMeta('demo-site-id', siteId)}
					selected={meta['selected-demo-sites'] || []}
					selectedSiteId={meta['demo-site-id'] || ''}
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'cac-site-template-info', {
  render: CACSiteTemplateInfo,
  icon: 'users',
} );
