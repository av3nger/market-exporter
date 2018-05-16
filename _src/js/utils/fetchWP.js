import fetch from 'isomorphic-fetch';

const methods = [
	'get',
	'post',
	'put',
	'delete',
];

/**
 * Class to fetch data from WP REST API
 *
 * @since 1.1.0
 */
class fetchWP {
	/**
	 * Class constructor
	 *
	 * @param {object} options
	 */
	constructor( options = {} ) {
		this.options = options;

		/** @var {string} options.restURL */
		if ( ! options.restURL )
			throw new Error('restURL option is required');

		/** @var {string} options.restNonce */
		if ( ! options.restNonce )
			throw new Error('restNonce option is required');

		methods.forEach(method => {
			this[method] = this._setup(method);
		});
	}

	/**
	 * Setup
	 *
	 * @param method
	 * @returns {function(*=, *=): *}
	 * @private
	 */
	_setup( method ) {
		return (endpoint = '/', data = false) => {
			let fetchObject = {
				credentials: 'same-origin',
				method: method,
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-WP-Nonce': this.options.restNonce,
				}
			};

			if ( data ) {
				fetchObject.body = JSON.stringify(data);
			}

			return fetch(this.options.restURL + endpoint, fetchObject)
				.then(response => {
					return response.json().then(json => {
						return response.ok ? json : Promise.reject(json);
					});
				});
		}
	}
}

export default fetchWP;