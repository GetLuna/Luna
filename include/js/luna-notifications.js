
luna = window.luna || {};

luna.notifications = {

	run: function() {
		
	}

};

_.extend( luna.notifications, { Model: {}, View: {} } );

luna.notifications.Model.Notification = luna.Backbone.Model.extend({});
luna.notifications.Model.Notifications = luna.Backbone.Collection.extend({});

luna.notifications.View.Notification = luna.Backbone.View.extend({});
luna.notifications.View.Notifications = luna.Backbone.View.extend({});

luna.notifications.View.Menu = luna.Backbone.View.extend({});

luna.runners.push( luna.notifications );
