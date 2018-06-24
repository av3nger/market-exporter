import React from 'react';
import Tooltip from 'tooltip.js';

import './style.scss';

/**
 * Tooltip component
 *
 * The preceding element needs to have a class 'me-tooltip-element'.
 *
 * Can be shown with an icon:
 *     <Tooltip text="Some tooltip text" showIcon="true" />
 * or without one:
 *     <Tooltip text="Some tooltip text" />
 *
 * @since 1.1.0
 */
class Tooltips extends React.Component {
	/**
	 * Tooltip constructor
	 * @param props
	 */
	constructor( props ) {
		super( props );
	}

	/**
	 * Init tooltip.js library here
	 */
	componentDidMount() {
		const help_tooltips = document.getElementsByClassName( 'me-tooltip-element' );

		for ( let i = 0; i < help_tooltips.length; i++ ) {
			new Tooltip(help_tooltips[i], {
				placement: 'top', // or bottom, left, right, and variations
				title: () => {
					const text = help_tooltips[i].parentElement.getElementsByClassName( 'me-tooltip-text' );
					return text[0].innerHTML;
				},
				html: true
			});
		}
	}

	/**
	 * Show tooltip div.
	 *
	 * @param text
	 * @returns {*}
	 */
	static showTooltip( text ) {
		return (
			<div className="me-tooltip-text">
				{ text.split( '\n' ).map( item => {
					return <p>{ item }</p>;
				} ) }
			</div>
		);
	}

	/**
	 * Render component
	 *
	 * @returns {*}
	 */
	render() {
		// Show tooltip with icon.
		if ( "true" === this.props.showIcon ) {
			return (
				<div className="me-tooltip">
					<span className="dashicons dashicons-editor-help me-tooltip-element" />
					{ Tooltips.showTooltip( this.props.text ) }
				</div>
			);
		}

		return Tooltips.showTooltip( this.props.text );
	}
}

export default Tooltips;