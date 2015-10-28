
window.luna = window.luna || {};

luna.$ = Backbone.$ || {};

/**
 * luna.template( id )
 *
 * Fetch a JavaScript template for an id, and return a templating function for it.
 *
 * @param  {string} id   A string that corresponds to a DOM element with an id prefixed with "tmpl-".
 *                       For example, "attachment" maps to "tmpl-attachment".
 * @return {function}    A function that lazily-compiles the template requested.
 */
luna.template = _.memoize(function ( id ) {

	var compiled,
	     options = {
			evaluate:    /<#([\s\S]+?)#>/g,
			interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
			escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
			variable:    'data'
		};
		return function ( data ) {
		compiled = compiled || _.template( $( '#tmpl-' + id ).html(), null, options );
		return compiled( data );
	};
}); 

luna.ajax = {

	settings: {
		url: window.ajaxurl || 'ajax.php'
	},

	/**
	 * luna.ajax.comment( [action], [data] )
	 *
	 * Sends a POST request to Luna.
	 *
	 * @param  {string} action The slug of the action to call.
	 * @param  {object} data   The data to populate $_POST with.
	 * @return {$.promise}     A jQuery promise that represents the request.
	 */
	comment: function( action, data ) {
		return luna.ajax.send({
			data: _.isObject( action ) ? action : _.extend( data || {}, { action: action })
		});
	},

	/**
	 * luna.ajax.send( [action], [options] )
	 *
	 * Sends a POST request to Luna.
	 *
	 * @param  {string} action  The slug of the action to call.
	 * @param  {object} options The options passed to jQuery.ajax.
	 * @return {$.promise}      A jQuery promise that represents the request.
	 */
	send: function( action, options ) {

		if ( _.isObject( action ) ) {
			options = action;
		} else {
			options = options || {};
			options.data = _.extend( options.data || {}, { action: action });
		}

		options = _.defaults( options || {}, {
			type:    'POST',
			url:     luna.ajax.settings.url,
			context: this
		});

		return luna.$.Deferred( function( deferred ) {

			// Transfer success/error callbacks.
			if ( options.success )
				deferred.done( options.success );
			if ( options.error )
				deferred.fail( options.error );

			delete options.success;
			delete options.error;

			// Use with PHP's wp_send_json_success() and wp_send_json_error()
			luna.$.ajax( options ).done( function( response ) {
				// Treat a response of `1` as successful for backwards
				// compatibility with existing handlers.
				if ( response === '1' || response === 1 )
					response = { success: true };

				if ( _.isObject( response ) && ! _.isUndefined( response.success ) )
					deferred[ response.success ? 'resolveWith' : 'rejectWith' ]( this, [response.data] );
				else
					deferred.rejectWith( this, [response] );
			}).fail( function() {
				deferred.rejectWith( this, arguments );
			});
		}).promise();
	},
};

luna.runners = [];

luna.run = function() {

	luna.runners.map(function( runner ) {
		if ( _.isFunction( runner.run ) ) {
			return runner.run();
		}
	});
};

jQuery( document ).ready(function() {
	luna.run();
});
