import React from 'react';

import './style.scss';

/**
 * Functional button component
 *
 * @since 1.1.0
 *
 * @param {object} props
 * @returns {*}
 * @constructor
 */
function Button( props ) {
	return (
		<button className={ props.className } onClick={ props.onClick }>
			{ props.buttonText }
		</button>
	);
}

export default Button;
