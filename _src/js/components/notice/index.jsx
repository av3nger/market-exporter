import React from 'react';

import './style.scss';

function capitalizeFirstLetter( string ) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Functional error component
 *
 * @since 1.1.0
 *
 * @param {object} props
 * @returns {*}
 * @constructor
 */
function Notice( props ) {
	const classNames = 'me-notice me-' + props.type;

	return (
		<div className={ classNames }>
			<p>{ capitalizeFirstLetter( props.type ) }: { props.message }</p>
		</div>
	);
}

export default Notice;
