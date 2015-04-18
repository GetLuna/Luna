
luna = window.luna || {};

notifications = luna.notifications = {

	/**
	 * Runner for the notification module
	 * 
	 * Creates required Views and Models.
	 */
	run: function() {

		this.flyout = 'flyout' === luna.$( '#navnotification > a' ).attr( 'data-flyout' );

		this.pending = new notifications.Model.Notifications;
		this.view    = new notifications.View.Menu({
			flyout:        this.flyout,
			notifications: this.pending
		});
	}

};

_.extend( notifications, { Model: {}, View: {} } );

_.extend( notifications.Model, {

	/**
	 * luna.notifications.Model.Notification
	 * 
	 * Contains the notification's data
	 * 
	 * @param    string    [id]      Notification ID
	 * @param    string    [user_id] Notification User ID
	 * @param    string    [message] Notification Content
	 * @param    string    [icon]    Notification Icon
	 * @param    string    [link]    Notification Related URL
	 * @param    string    [time]    Notification Time
	 */
	Notification: Backbone.Model.extend({

		defaults: {
			id:      '',
			user_id: '',
			message: '',
			icon:    '',
			link:    '',
			time:    '',
		},

		/**
		 * Parse model's attributes.
		 * 
		 * Currently only format the notification's time into standard
		 * JS Time value.
		 * 
		 * @param    array    resp XHR Response
		 * 
		 * @return   array    Parse response
		 */
		parse: function( resp ) {

			if ( ! resp ) {
				return resp;
			}

			resp.time = new Date( resp.time * 1000 );

			return resp;
		}

	})
},
{
	/**
	 * luna.notifications.Model.Notification
	 * 
	 * Contains the notification's data
	 */
	Notifications: Backbone.Collection.extend({

		url: window.ajaxurl,

		/**
		 * Parse XHR responses into suitable notification models
		 * for the Collection.
		 * 
		 * @param    array    resp XHR Response
		 * 
		 * @return   array    Parse response
		 */
		parse: function( resp ) {

			if ( ! resp ) {
				return resp;
			}

			_.map( resp, function( attr, i ) {
				var model = new notifications.Model.Notification,
				    attr = _.pick( attr || {}, _.keys( model.defaults ) );
				    attr = model.parse( attr );
				return resp[ i ] = model.set( attr, { silent: true } );
			} );

			return resp;
		},

		/**
		 * Override Backbone.sync()
		 * 
		 * @param    string    method
		 * @param    object    Backbone.Collection
		 * @param    object    options
		 * 
		 * @return   Promise
		 */
		sync: function( method, models, options ) {

			if ( 'read' === method ) {

				var options = options || {};
				_.extend( options, {
					data: {
						action: 'fetch-notifications',
					}
				});

				return luna.ajax.send( options );
			} else {
				return Backbone.sync.apply( this, arguments );
			}
		},
	})
} );

_.extend( notifications.View, {

	/**
	 * luna.notifications.View.Notification
	 */
	Notification: luna.Backbone.View.extend({

		tagName: 'li',

		template: luna.template( 'notification-menu-item' ),

		/**
		 * Convert notification time to a simpler HH:mm format
		 */
		prepare: function() {

			if ( ! _.isUndefined( this.model ) && _.isFunction( this.model.toJSON ) ) {
				var model = this.model.toJSON();
				    model.time = model.time.getUTCHours() + ':' + model.time.getUTCMinutes()
				return model;
			} else {
				return {};
			}
		},

		/**
		 * Override luna.Backbone.View.prototype.render to create an
		 * additional divider LI after this.$el.
		 */
		render: function() {

			var options;

			if ( this.prepare )
				options = this.prepare();

			this.views.detach();

			if ( this.template ) {
				options = options || {};
				this.trigger( 'prepare', options );
				this.$el.html( this.template( options ) + '<li class="divider"></li>' );
			}

			this.views.render();
			return this;
		}
	}),

	/**
	 * luna.notifications.View.Notifications
	 */
	Notifications: luna.Backbone.View.extend({

		el: '.notification-menu',

		template: luna.template( 'notification-menu' ),

		/**
		 * Initialize the View
		 */
		initialize: function( options ) {

			_.each( this.collection.models, this.addNotification, this );
			this.listenTo( this.collection, 'add', this.addNotification );
		},

		/**
		 * Add a new notification view at the end of the pile, just
		 * before the "more" block
		 */
		addNotification: function( notification ) {

			this.views.add( new luna.notifications.View.Notification({ model: notification }), { at: -1 } );
		}
	})
},
{
	/**
	 * luna.notifications.View.Menu
	 */
	Menu: luna.Backbone.View.extend({

		el: '#navnotification',

		/**
		 * Initialize the View
		 * 
		 * @param    object    [options]
		 * @param    object    [options.notifications] Backbone.Collection
		 */
		initialize: function( options ) {

			this.notifications = options.notifications || {};
			this.flyout        = options.flyout || false;

			// Fly out mode?
			if ( this.flyout ) {
				this._createSubmenu();
			}

			_.bindAll( this, 'update' );

			this.$document = $( document );
			this.$document.on( 'heartbeat-tick', this.update );

			this.notifications.on( 'sync', this.updateCounter, this );
		},

		_createSubmenu: function() {

			this.submenu = new notifications.View.Notifications({
				collection: this.notifications,
				parent:     this
			});

			this.views.add( this.submenu );
		},

		/**
		 * Update the collection on heartbeat.
		 * 
		 * @param    object    Event
		 * @param    object    XHR Response
		 * @param    string    XHG Status
		 * @param    object    XHR
		 */
		update: function( event, response, status, xhr ) {

			if ( _.isUndefined( response.notifications ) || response.notifications === this.notifications.length ) {
				return;
			}

			this.notifications.fetch();
		},

		/**
		 * Update the menu when collection is synced.
		 */
		updateCounter: function() {

			var number = this.notifications.length || '',
			     title = document.title.replace( /^\(\d\)\ /i, '' );

			this.$( '#notifications-number' ).text( number );
			document.title = '(' + number + ') ' + title;
		}
	})
} );

luna.runners.push( notifications );
