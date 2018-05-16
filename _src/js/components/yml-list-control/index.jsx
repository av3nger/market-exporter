import React from 'react';
import { __ } from "@wordpress/i18n/build/index";
import './style.scss';

class YmlListControl extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<div className="me-list-group">
				<div className="me-list-header">
					<h2>&lt;shop&gt;</h2>
					<h4>{ __( 'header elements' ) }</h4>

					<input type="submit"
						   className="button button-primary"
						   value={ __( 'Add field' ) } />
				</div>

				<div>

				</div>
			</div>
		);
	}
}


export default YmlListControl;