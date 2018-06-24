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
			options: []
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
			( json ) => this.setState( {
				loading: false,
				options: json
			} ), ( err ) => console.log( 'error', err )
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
					fetchWP={ this.fetchWP }
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