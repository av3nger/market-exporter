import React from 'react';

import { __ } from "@wordpress/i18n/build/index";

import Notice from "../notice";
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
	constructor( props ) {
		super( props );

		this.state = {
			showAddDiv: false,
			updateError: false,
			updateMessage: ''
		};
	}

	/**
	 * Handle item move (add/remnove from YML list)
	 *
	 * @param {string} item
	 * @param {string} action  Accepts: 'add', 'remove'.
	 */
	handleItemMove( item, action = 'add' ) {
		this.props.fetchWP.post( 'settings', { item: item, action: action } ).then(
			( json ) => this.moveItem( item, action ),
			( err )  => this.setState({ updateError: true, updateMessage: err.message })
		);
	}

	/**
	 * Move item in the UI.
	 *
	 * @param {string} item
	 * @param {string} action
	 */
	moveItem( item, action ) {
		let headerItems = this.props.headerItems.slice();
		let unusedHeaderItems = this.props.unusedHeaderItems.slice();

		if ( 'add' === action ) {
			const index = unusedHeaderItems.indexOf( item );
			headerItems = headerItems.concat( unusedHeaderItems.splice( index, 1 ) );
		} else {
			const index = headerItems.indexOf( item );
			unusedHeaderItems = unusedHeaderItems.concat( headerItems.splice( index, 1 ) );
		}

		this.setState({
			showAddDiv: unusedHeaderItems.length > 0,
			headerItems: headerItems, // TODO: we need to update this. move the action to app.js?
			unusedHeaderItems: unusedHeaderItems // TODO: same here
		});
	}

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		// Build the unused items list.
		const itemAvailable = this.props.unusedHeaderItems.map( item => {
			return (
				<div className="me-new-item" onClick={ () => this.handleItemMove( item, 'add' ) }>
					{item}
					<Tooltips text={ this.props.headerFields[item].description } showIcon="true" />
				</div>
			);
		} );

		// Build the current items list.
		const items = this.props.headerItems.map( item => {
			return (
				<YmlListItem
					name={ item }
					value={ this.props.settings[item] }
					onClick={ () => this.handleItemMove( item, 'remove' ) }
				/>
			);
		} );

		let buttonClasses = "button button-disabled me-tooltip-element",
			tooltipText   = __('No more items left for this type'),
		    itemDisabled  = true;
		if ( this.props.unusedHeaderItems.length > 0 ) {
			buttonClasses = "button button-primary me-tooltip-element";
			tooltipText   = __('Add new item to YML config');
			itemDisabled  = false;
		}

		return (
			<div className="me-list-group me-list-group-panel" id="me_yml_store">
				<div className="me-list-header">
					<h2>&lt;shop&gt;</h2>

					<input type="submit"
						   className={ buttonClasses }
						   onClick={ () => this.setState( { showAddDiv: ! this.state.showAddDiv } ) }
						   value={ __( 'Add field' ) }
						   disabled={ itemDisabled } />
					<Tooltips text={ tooltipText } />
				</div>

				<div className="me-list-content">
					{ this.state.showAddDiv &&
					<div className="me-list-new-item">
						<h3>{ __( 'Select item' ) }</h3>
						<p>{ __( 'Select an item from the list below to add to the YML file.' ) }</p>

						{ itemAvailable }
					</div>
					}

					{ this.state.updateError && <Notice type='error' message={ this.state.updateMessage } /> }

					<h3>{ __( 'header elements' ) }</h3>

					{ items }
				</div>
			</div>
		);
	}
}

export default YmlListControl;
