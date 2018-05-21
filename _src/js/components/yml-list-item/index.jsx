import React from 'react';

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

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		return (
			<div className="me-list-group-item">
				<div className="me-item-controls">
					<span className="dashicons dashicons-minus" aria-hidden="true" />
				</div>

				<strong>
					&lt;{this.props.name}&gt;<span contenteditable="true">{this.props.value}</span>&lt;/{this.props.name}&gt;
				</strong>
			</div>
		);
	}
}

export default YmlListItem;
