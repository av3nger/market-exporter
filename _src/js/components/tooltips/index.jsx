import React from 'react';
import Tooltip from 'tooltip.js';

import './style.scss';

/**
 * Tooltip component
 *
 * @since 1.1.0
 */
class Tooltips extends React.Component {
	/**
	 * Tooltip constructor
	 * @param props
	 */
	constructor(props) {
		super(props);
	}

	/**
	 * Init tooltip.js library here
	 */
	componentDidMount() {
		const help_tooltips = document.getElementsByClassName('dashicons-editor-help');

		for (let i = 0; i < help_tooltips.length; i++) {
			new Tooltip(help_tooltips[i], {
				placement: 'top', // or bottom, left, right, and variations
				title: () => {
					const text = help_tooltips[i].parentElement.getElementsByClassName('me-tooltip-text');
					return text[0].innerHTML;
				},
				html: true
			});
		}
	}

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		return (
			<div className="me-tooltip">
				<span className="dashicons dashicons-editor-help" />
				<div className="me-tooltip-text">
					{this.props.tooltip.split('\n').map(item => {
						return <p>{item}</p>;
					})}
				</div>
			</div>
		);
	}
}

export default Tooltips;