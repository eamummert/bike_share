define(function()
{
	/**
	 * Global shared router used to subscribe to and publish events from all routers.
	 *
	 * Useful in cases where a module must always react to events from another module.
	 *
	 * <code>
	 *     SharedRouter.subscribe('Webdev/Modal', 'request', showLoadingBar);
	 * </code>
	 */
	var SharedRouter = {};

	var listeners = {};

	/**
	 * Subscribe to an event from a particular namespace.
	 *
	 * @param {string} namespace
	 * @param {string} route
	 * @param {Function} callback
	 */
	SharedRouter.subscribe = function(namespace, route, callback)
	{
		if (!(namespace in listeners))
		{
			listeners[namespace] = {};
		}

		if (!(route in listeners[namespace]))
		{
			listeners[namespace][route] = [];
		}

		listeners[namespace][route].push(callback);
	};

	/**
	 * Publish an event from a particular namespace.
	 *
	 * @param {string} namespace
	 * @param {string} route
	 * @param {...} [param]
	 */
	SharedRouter.publish = function(namespace, route, param)
	{
		var params = [].slice.call(arguments);
		params.shift();
		params.shift();

		if (!(namespace in listeners))
		{
			return;
		}

		if (!(route in listeners[namespace]))
		{
			return;
		}

		$.each(listeners[namespace][route], function(i, callback)
		{
			callback.apply(SharedRouter, params);
		});
	};

	return SharedRouter;
});

