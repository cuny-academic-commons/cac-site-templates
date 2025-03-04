const { useState, useEffect, useRef } = wp.element;
import { ComboboxControl } from '@wordpress/components';

const SiteSearch = ({ labelText, selectedSiteId, setSelectedSiteId }) => {
	const [selectedSiteLabel, setSelectedSiteLabel] = useState('');
	const [options, setOptions] = useState([]); // ✅ Stores available dropdown options
	const siteLookup = useRef({}); // ✅ Stores ID → Label mapping

	// Fetch site by ID when mounting
	useEffect(() => {
		if (selectedSiteId > 0) {
			wp.apiFetch({ path: `/cac-site-templates/v1/site/${selectedSiteId}` }).then((site) => {
				const label = `${site.name} (${site.url})`;
				siteLookup.current[label] = site.id;

				// ✅ Update options first, THEN selected label
				setOptions([{ label, value: label }]);
				setTimeout(() => setSelectedSiteLabel(label), 0); // ✅ Delay update slightly
			});
		}
	}, [selectedSiteId]);

	// Handle search input
	const searchSites = (search) => {
		if (!search) return;
		wp.apiFetch({ path: wp.url.addQueryArgs(`/cac-site-templates/v1/site`, { search }) }).then((sites) => {
			siteLookup.current = {}; // ✅ Reset mapping to avoid stale entries
			const newOptions = sites.map((site) => {
				const label = `${site.name} (${site.url})`;
				siteLookup.current[label] = site.id;
				return { label, value: label }; // ✅ Uses label as value for Combobox
			});
			setOptions(newOptions);
		});
	};

	// Handle selection change
	const onSelect = (selectedLabel) => {
		const selectedId = siteLookup.current[selectedLabel] || 0;
		setSelectedSiteId(selectedId);
		setSelectedSiteLabel(selectedLabel);
	};

	return (
		<ComboboxControl
			label={labelText}
			value={selectedSiteLabel} // ✅ Uses label for display
			onChange={onSelect} // ✅ Maps label back to ID
			onFilterValueChange={searchSites} // ✅ Dynamically filters results
			options={options}
		/>
	);
};

export { SiteSearch };
