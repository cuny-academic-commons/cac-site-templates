const { FormTokenField } = wp.components
const { Component } = wp.element

import { find, invoke, throttle } from 'lodash'

class SiteSearch extends Component {
	constructor() {
		super( ...arguments )
		this.searchSites = throttle( this.searchSites.bind( this ), 500 );
		this.state = {
			availableSites: [],
			selectedSiteId: 0
		}
	}

	componentDidMount() {
		const { selectedSiteId } = this.props

		if ( selectedSiteId > 0 ) {
			const request = wp.apiFetch( {
				path: '/cac-site-templates/v1/site/' + selectedSiteId,
			} );

			request.then( ( site ) => {
				const { setSelectedSites } = this.props

				this.setState( ( state ) => ( {
					availableSites: [ site ]
				} ) );
				this.updateSelectedSites( [ site ] );

				const selected = [ site.name + ' (' + site.url + ')' ]
				setSelectedSites( selected )
			} );
		}
	}

	searchSites( search = '' ) {
		if ( '' === search ) {
			return
		}

		invoke( this.searchRequest, [ 'abort' ] );
		this.searchRequest = this.fetchSites( { search } );
	}

	fetchSites( params = {} ) {
		const request = wp.apiFetch( {
			path: wp.url.addQueryArgs( `/cac-site-templates/v1/site`, params ),
		} );

		request.then( ( sites ) => {
			this.setState( ( state ) => ( {
				availableSites: sites
			} ) );
			this.updateSelectedSites( this.props.sites );
		} );

		return request;
	}

	updateSelectedSites( sites = [] ) {
		let selectedSiteId
		const selectedSites = sites.map( ( site ) => {
			selectedSiteId = site.id
			return site.id;
		} )

		this.setState( {
			selectedSiteId,
		} );
	}

	render() {
		const { labelText, setSelectedSites, setSelectedSiteId, selected } = this.props

		const { availableSites } = this.state
		const siteNames = availableSites.map( function( site ) {
			return site.name + ' (' + site.url + ')'
		} )

		const onSelect = ( newSelected ) => {
			setSelectedSites( newSelected )

			// Get the ID of the selected site.
			let selectedSiteLabel
			newSelected.map( (site) => {
				selectedSiteLabel = site // take the last one
			} )

			const selectedSite = find( availableSites, ( site ) => {
				return site.name + ' (' + site.url + ')' === selectedSiteLabel
			} )

			const selectedSiteId = 'undefined' === typeof selectedSite ? 0 : selectedSite.id
			setSelectedSiteId( selectedSiteId )
		}

		return (
			<div>
				<label>
					{labelText}
				</label>
				<FormTokenField
						value={ selected }
						suggestions={ siteNames }
						onChange={ onSelect }
						onInputChange={ this.searchSites }
						placeholder="Start typing..."
					/>
			</div>
		)
	}
}

export default SiteSearch
