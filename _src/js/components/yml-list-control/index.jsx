import React from 'react';

import { __ } from "@wordpress/i18n/build/index";

import Tooltips from '../tooltips';
import YmlListItem from '../yml-list-item';

import './style.scss';

/**
 * YML list control component
 *
 * @since 1.1.0
 */
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
			showAddDiv: true,
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
				/>
			)
		});

		const itemAvailable = Object.keys(unusedItems).map(item => {
			return (
				<div className="me-new-item">
					{item}
					<Tooltips tooltip={this.state.headerFields[item].description} />
				</div>
			)
		});

		return (
			<div className="me-list-group me-list-group-panel" id="me_yml_store">
				<div className="me-list-header">
					<h2>&lt;shop&gt;</h2>

					<input type="submit"
						   className="button button-primary"
						   onClick={() => this.setState({showAddDiv: ! this.state.showAddDiv})}
						   value={__('Add field')} />
				</div>

				<div className="me-list-content">
					{this.state.showAddDiv &&
					<div className="me-list-new-item">
						<h3>{__('Select item')}</h3>
						<p>{__('Select an item from the list below to add to the YML file.')}</p>

						{itemAvailable}
					</div>
					}

					<h3>{__('header elements')}</h3>

					{items}
				</div>
			</div>
		);
	}
}

export default YmlListControl;
