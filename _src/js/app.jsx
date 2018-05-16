import React from 'react';
import ReactDOM from 'react-dom';
import Description from './components/description';

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
			<Description />
		);
	}
}

document.addEventListener('DOMContentLoaded', function() {
	ReactDOM.render(
		<MarketExporter />,
		document.getElementById('me_components')
	);
});