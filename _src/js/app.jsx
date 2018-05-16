import React from 'react';
import ReactDOM from 'react-dom';
import { __ } from "@wordpress/i18n/build/index";
import Description from './components/description';
import Button from './components/button';
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
	constructor(props) {
		super(props);

		this.state = {
			loading: true,
			options: [],
		};
	}

	/**
	 * Init component states
	 */
	componentDidMount() {

	}

	handleOnClick() {
		alert('asdds');
	}

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		const isLoggedIn = true;

		return (
			<div className="me-main-content">
				<Description />
				{isLoggedIn &&
					<Button
						buttonText={ __( 'Add first field' ) }
						className='button button-primary me-button-callout'
						onClick={this.handleOnClick}
					/>}
				<YmlListControl />
			</div>
		);
	}
}

document.addEventListener('DOMContentLoaded', function() {
	ReactDOM.render(
		<MarketExporter />,
		document.getElementById('me_components')
	);
});