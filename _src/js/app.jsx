import React from 'react';
import ReactDOM from 'react-dom';

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

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		return (
			<h1>tasdasd</h1>
		);
	}
}

document.addEventListener('DOMContentLoaded', function() {
	ReactDOM.render(
		<MarketExporter />,
		document.getElementById('wrap-me-component')
	);
});