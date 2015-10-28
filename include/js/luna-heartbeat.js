
( function( $, window ) {

	
	var luna = window.luna = window.luna || {};

	luna.heartbeat = function() {
		var $document = $( document ),
		      $window = $( window ),
		      options = {
				interval: 60,
				ticked:   0,
				beat:     0,
	
				suspend:  false,
				beating:  false,

				queue:    {},
			};

		function startBeating() {

			options.interval = options.interval * 1000;

			$window.on( 'unload.wp-heartbeat', function() {
				options.suspend = true;
			});

			$document.ready( function() {
				ticked = time();
				schedule();
			});
		}

		function time() {
			return ( new Date() ).getTime();
		}

		function schedule() {

			var delta = time() - options.ticked,
			interval = options.interval;

			if ( options.suspend ) {
				return;
			}

			window.clearTimeout( options.beat );

			if ( delta < interval ) {
				options.beat = window.setTimeout(
					function() {
						connect();
					},
					interval - delta
				);
			} else {
				connect();
			}
		}

		function connect() {

			var data, heartbeat;

			// If the connection to the server is slower than the interval,
			// heartbeat connects as soon as the previous connection's response is received.
			if ( options.beating || options.suspend ) {
				return;
			}

			options.ticked = time();

			var heartbeat = $.extend( {}, options.queue );
			// Clear the data queue, anything added after this point will be send on the next tick
			options.queue = {};

			$document.trigger( 'heartbeat-send', [ heartbeat ] );

			var data = {
				action:   'heartbeat',
				data:     heartbeat,
				interval: options.interval / 1000,
				_nonce:   window._nonces.heartbeat || '',
			};

			options.beating = true;
			options.xhr = $.ajax({
				url:      ajaxurl,
				type:     'comment',
				timeout:  30000, // throw an error if not completed after 30 sec.
				data:     data,
				dataType: 'json'
			}).always( function() {
				options.beating = false;
				schedule();
			}).done( function( response, textStatus, jqXHR ) {
				if ( ! response ) {
					return;
				}
				$document.trigger( 'heartbeat-tick', [response, textStatus, jqXHR] );
			}).fail( function( jqXHR, textStatus, error ) {
				$document.trigger( 'heartbeat-error', [jqXHR, textStatus, error] );
			});
		}

		startBeating();
	}
	
	luna.pulse = new luna.heartbeat();

}( jQuery, window ) );

