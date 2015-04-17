
luna = {};
luna.Backbone = Backbone;
luna.$ = luna.Backbone.$;

luna.run = function() {
	console.log( 'Hey!' );
};

jQuery( document ).ready(function() {
	luna.run();
});
