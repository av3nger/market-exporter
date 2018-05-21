import React from 'react';

import { __ } from "@wordpress/i18n/build/index";

/**
 * Functional description component
 *
 * @since 1.1.0
 *
 * @returns {*}
 * @constructor
 */
function Description() {
	return (
		<div className="me-description">
			<p>{__('This plugin is used to generate a valid YML file for exporting your products in WooCommerce' +
				'to Yandex Market.')}</p>

			<p>{__('Please be patient while the YML file is generated. This can take a while if your server is' +
				'slow or if you have many products in WooCommerce. Do not navigate away from this page until this' +
				'script is done or the YML file will not be created. You will be notified via this page when the' +
				'process is completed.')}</p>
		</div>
	);
}

export default Description;
