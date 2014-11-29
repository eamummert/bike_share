define(['./SharedRouter'], function(SharedRouter)
{
	/**
	 * Router object used to subscribe to and publish events.
	 *
	 * Create new router object using namespaces:
	 * <code>
	 *     var router = new Router('FooModule/BarComponent');
	 * </code>
	 *
	 * Subscribe to events by attaching callbacks:
	 * <code>
	 *     router.subscribe('some-event', function(param1, param2)
	 *     {
	 *     });
	 * </code>
	 *
	 * Publish an event and pass in parameters:
	 * <code>
	 *     router.publish('some-event', 'param1', 'param2');
	 * </code>
	 *
	 * All callbacks attached to the published event will be triggered and the callback will be passed
	 * the given parameters.
	 *
	 * @param {string} namespace
	 * @name Router
	 */
	return function(namespace)
	{
		var self = this;

		var listeners = {};

		/**
		 * Subscribe to an event and attach a callback.
		 *
		 * @param {string} route
		 * @param {Function} callback
		 */
		self.subscribe = function(route, callback)
		{
			if (!(route in listeners))
			{
				listeners[route] = [];
			}

			listeners[route].push(callback);
		};

		/**
		 * Publish an event and trigger all attached callbacks.
		 *
		 * <code>
		 *     router.publish('some-event', param1, param2);
		 * </code>
		 *
		 * @param {string} route
		 * @param {...} [param]
		 */
		self.publish = function(route, param)
		{
			var params = [].slice.call(arguments);
			params.shift();

			if (route in listeners)
			{
				$.each(listeners[route], function(i, callback)
				{
					callback.apply(self, params);
				});
			}

			var args = [].slice.call(arguments);
			args.unshift(namespace);
			SharedRouter.publish.apply(SharedRouter, args);
		};
	};
});

