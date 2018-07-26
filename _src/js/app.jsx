import React from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import { __ } from "@wordpress/i18n/build/index";
import fetchWP from './utils/fetchWP';

import Button from './components/button';
import Description from './components/description';
import YmlListControl from './components/yml-list-control';

/**
 * MarketExporter React component
 */
class MarketExporter extends React.Component {
	/**
	 * MarketExporter constructor
	 *
	 * @param props
	 */
	constructor( props ) {
		super( props );

		this.state = {
			loading: true,
			options: [],           // Plugin options
			headerFields: [],      // List of all available header fields
			headerItems: [],       // Currently used header fields
			unusedHeaderItems: [], // Not used header fields (available to add)
		};

		this.fetchWP = new fetchWP( {
			restURL: this.props.wpObject.api_url,
			restNonce: this.props.wpObject.api_nonce,
		} );
	}

	/**
	 * Init component states
	 */
	componentDidMount() {
		this.fetchWP.get( 'settings' ).then(
			( json ) => this.getHeaderElements( json ),
			( err )  => console.log( 'error', err )
		);
	}

	/**
	 * Get header elements
	 *
	 * @param options
	 */
	getHeaderElements( options ) {
		this.fetchWP.get( 'elements/header' ).then(
			( json ) => {
				let unusedItems = [];

				// Build the current items list.
				//const items = Object.keys( this.props.settings ).filter( item => {
				const items = Object.keys( json ).filter( item => {
					if ( options[item] ) {
						return true;
					}

					unusedItems.push( item );
					return false;
				} );

				this.setState( {
					loading: false,
					options: options,
					headerFields: json,
					headerItems: items,
					unusedHeaderItems: unusedItems
				} );
			},
			( err ) => console.log( 'error', err )
		);
	}

	handleAddField() {
		alert( 'Add first field' );
	}

	handleGenerateFile() {
		alert( 'Generate YML' );
	}

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		if ( this.state.loading ) {
			return __( 'Loading...' );
		}

		return (
			<div className="me-main-content">
				<Description />

				{ ! this.state.options &&
				<Button
					buttonText={ __( 'Add first field' ) }
					className='button button-primary me-button-callout'
					onClick={ this.handleAddField }
				/> }

				{ this.state.options &&
				<Button
					buttonText={ __( 'Generate YML' ) }
					className='button button-primary me-button-callout'
					onClick={ this.handleGenerateFile }
				/>}

				<YmlListControl
					settings={ this.state.options }
					fetchWP={ this.fetchWP } // TODO: do we need this?
					headerFields={ this.state.headerFields }
					headerItems={ this.state.headerItems }
					unusedHeaderItems={ this.state.unusedHeaderItems }
				/>
			</div>
		);
	}
}

MarketExporter.propTypes = {
	wpObject: PropTypes.object
};

document.addEventListener( 'DOMContentLoaded', function() {
	ReactDOM.render(
		/** @var {object} window.ajax_strings */
		<MarketExporter wpObject={ window.ajax_strings }/>,
		document.getElementById( 'me_components' )
	);
} );