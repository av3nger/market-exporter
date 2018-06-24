import React from 'react';

import { __ } from '@wordpress/i18n';

import Tooltips from "../tooltips";

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
	constructor( props ) {
		super( props );
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
					<span className="dashicons dashicons-minus me-tooltip-element" onClick={ this.props.onClick } aria-hidden="true" />
					<Tooltips text={ __( 'Remove item' ) } />
				</div>

				<strong>
					&lt;{ this.props.name }&gt;<span contenteditable="true">{ this.props.value }</span>&lt;/{ this.props.name }&gt;
				</strong>
			</div>
		);
	}
}

export default YmlListItem;
