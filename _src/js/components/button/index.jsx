import React from 'react';

import './style.scss';

function Button(props) {
	return (
		<button className={props.className} onClick={props.onClick}>{props.buttonText}</button>
	);
}

export default Button;