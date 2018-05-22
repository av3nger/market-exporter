import React from 'react';
import Tooltip from 'tooltip.js';

import { __ } from '@wordpress/i18n';

/**
 * YML list item component
 *
 * @since 1.1.0
 */
class YmlListItem extends React.Component {
	/**
	 * YmlListItem constructor
	 *
	 * @param props
	 */
	constructor(props) {
		super(props);
	}

	componentDidMount() {
		const help_tooltips = document.getElementsByClassName('dashicons-minus');

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
			<div className="me-list-group-item">
				<div className="me-item-controls">
					<span className="dashicons dashicons-minus" onClick={this.props.onClick} aria-hidden="true" />
					<div className="me-tooltip-text">
						<p>{__('Remove item')}</p>
					</div>
				</div>

				<strong>
					&lt;{this.props.name}&gt;<span contenteditable="true">{this.props.value}</span>&lt;/{this.props.name}&gt;
				</strong>
			</div>
		);
	}
}

export default YmlListItem;
