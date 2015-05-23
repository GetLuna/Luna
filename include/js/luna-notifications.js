
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
			read:    false
		},

		url: window.ajaxurl,

		/**
		 * Initialize the model.
		 */
		initialize: function() {

			this.on( 'change:read', this.read, this );
		},

		/**
		 * Mark notification as read.
		 */
		read: function() {

			this.sync( 'mark' );
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

			var options = options || {},
			       self = this;

			if ( 'mark' === method ) {

				_.extend( options, {
					data: {
						action: 'read-notification',
						_nonce: _nonces.readNotif,
						id:     this.get( 'id' )
					},
					success: function( response ) {
						// The model should self destruct, but don't actually destroy data on the server
						self.trigger( 'destroy', self, self.collection, {} );
					}
				});

				return luna.ajax.send( options );

			} else if ( 'delete' === method ) {

				_.extend( options, {
					data: {
						action: 'trash-notification',
						_nonce: _nonces.trashNotif,
						id:     this.get( 'id' )
					},
					beforeSend: function() {
						self.trigger( 'destroying' );
					},
					success: function( response ) {
						self.trigger( 'destroyed' );
					},
					error: function( response ) {
						self.trigger( 'undestroyed' );
					}
				});

				return luna.ajax.send( options );

			} else {
				return Backbone.sync.apply( this, arguments );
			}
		},

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

			var options = options || {},
			       self = this;

			if ( 'read' === method ) {

				var options = options || {};
				_.extend( options, {
					data: {
						action: 'fetch-notifications',
						_nonce: _nonces.fetchNotif
					},
					error: function( response ) {
						self.trigger( 'empty' );
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

		events: {
			'click .notification-action': 'action'
		},

		/**
		 * Initialize the View
		 */
		initialize: function() {

			this.listenTo( this.model, 'destroying',  this._destroying );
			this.listenTo( this.model, 'undestroyed', this._undestroy );
			this.listenTo( this.model, 'destroyed',   this._remove );
			this.listenTo( this.model, 'destroy',     this._remove );
		},

		/**
		 * Colorize the notification while destroying.
		 */
		_destroying: function() {

			this.$el.addClass( 'bg-danger' );
		},

		/**
		 * Destroy failed, uncolorize the notification
		 */
		_undestroy: function() {

			this.$el.removeClass( 'bg-danger' );
		},

		/**
		 * Remove the notification view and reload the collection to be
		 * sure and re-render the main view.
		 */
		_remove: function() {

			this.remove();

			this.model.collection.fetch();
		},

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
		},

		/**
		 * Find out what to do: mark as read or delete (or do nothing)?
		 * 
		 * @param    object    JS 'click' event
		 */
		action: function( event ) {

			event.preventDefault();

			var $elem = this.$( event.currentTarget ),
			   action = $elem.attr( 'data-action' ) || false;

			if ( ! action ) {
				return;
			}

			if ( 'read' === action ) {
				this.model.set({ read: true });
			} else if ( 'delete' === action ) {
				this.model.destroy();
			}

			event.stopPropagation();
		}
	}),

	/**
	 * luna.notifications.View.Notifications
	 */
	Notifications: luna.Backbone.View.extend({

		el: '.notification-menu',

		template: luna.template( 'notification-menu' ),

		_empty: true,

		/**
		 * Initialize the View
		 */
		initialize: function( options ) {

			this.listenTo( this.collection, 'add', this.addNotification );
			this.listenTo( this.collection, 'empty', this.render);
		},

		/**
		 * Add a new notification view at the end of the pile, just
		 * before the "more" block
		 */
		addNotification: function( notification ) {

			if ( this._empty ) {
				this.$( '.notification-empty' ).next( '.divider' ).remove();
				this.$( '.notification-empty' ).remove();
				this._empty = false;
			}

			var view = new luna.notifications.View.Notification({ model: notification });
			notification.view = view;

			this.views.add( view, { at: 2 } );
		},

		/**
		 * Render the view.
		 * 
		 * @since    1.1
		 * 
		 * @return   Return itself to allow chaining.
		 */
		render: function() {

			if ( ! this.collection.length ) {
				this._empty = true;
			}

			this.$el.html( this.template() );

			return this;
		}
	})
},
{
	/**
	 * luna.notifications.View.Menu
	 */
	Menu: luna.Backbone.View.extend({

		el: '#navnotification',

		template: luna.template( 'notification-nav' ),

		events: {
			'click a[data-flyout]': '_refresh'
		},

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

			this.notifications.on( 'add remove', this.render, this );
		},

		/**
		 * Create a subview for the real notifications menu (dropdown).
		 */
		_createSubmenu: function() {

			this.submenu = new notifications.View.Notifications({
				collection: this.notifications,
				parent:     this
			});

			this.views.add( this.submenu );
		},

		/**
		 * Refetch the collection when opening the flyout.
		 * 
		 * @param    object    JS 'click' Event
		 */
		_refresh: function( event ) {

			if ( this.$el.hasClass( 'open' ) ) {
				return;
			}

			this.update( event, { notifications: -1 } );
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
		 * Render part of the view.
		 * 
		 * We should override the complete view to avoid messing with
		 * Bootstrap on handling the flyout dropdown.
		 * 
		 * @return   Return itself to allow chaining.
		 */
		render: function() {

			this.$( 'a[data-flyout]' ).html( this.template({ number: this.notifications.length || '' } ) );

			return this;
		}
	})
} );

luna.runners.push( notifications );
