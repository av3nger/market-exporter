import React from 'react';

class YmlListItem extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<div className="me-list-group-item">
				<div className="me-item-controls">
					<span className="dashicons dashicons-minus" aria-hidden="true" />
				</div>

				<strong>
					&lt;{this.props.name}&gt;<span contenteditable="true">{this.props.value}</span>&lt;/{this.props.name}&gt;
				</strong>

				<span className="dashicons dashicons-editor-help" />
				<div className="me-tooltip-text">
					{this.props.description}
				</div>

			</div>
		);
	}
}


export default YmlListItem;