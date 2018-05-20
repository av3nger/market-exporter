import React from 'react';

import { __ } from "@wordpress/i18n/build/index";

import YmlListItem from '../yml-list-item';

import './style.scss';

class YmlListControl extends React.Component {
	/**
	 * YmlListControl constructor
	 *
	 * @param props
	 */
	constructor(props) {
		super(props);

		this.state = {
			loading: true,
			headerFields: {}
		};
	}

	/**
	 * Init component states
	 */
	componentDidMount() {
		this.props.fetchWP.get('elements/header')
			.then(
				(json) => this.setState({
					loading: false,
					headerFields: json
				}),
				(err) => console.log( 'error', err )
			);
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

		let unusedItems = {...this.state.headerFields};

		const items = Object.keys(this.props.settings).filter(item => {
			if ( this.props.settings[item] ) {
				delete unusedItems[item];
				return true;
			}
			return false;
		}).map(item => {
			return (
				<YmlListItem
					name={item}
					value={this.props.settings[item]}
					description={this.state.headerFields[item].description}
				/>
			)
		});

		const itemAvailable = Object.keys(unusedItems).map(item => {
			return (
				<span>{item}</span>
			)
		});

		return (
			<div className="me-list-group me-list-group-panel" id="me_yml_store">
				<div className="me-list-header">
					<h2>&lt;shop&gt;</h2>
					<h4>{ __( 'header elements' ) }</h4>

					<input type="submit"
						   className="button button-primary"
						   value={ __( 'Add field' ) } />
				</div>

				{itemAvailable}

				{items}
			</div>
		);
	}
}


export default YmlListControl;
