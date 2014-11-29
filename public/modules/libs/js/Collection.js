define(['jquery', './Router'], function($, Router)
{
	var View = function(
		element
		, addLabel
		, addClass
		, addIcon
		, removeLabel
		, removeClass
		, removeIcon
		, removeFlush
		, createIfEmpty
		, maxItems
		, minItems
		, onHydrateRow
	)
	{
		var self = this;

		var inputs = {
			add: '.FormCollection-add',
			remove: '.FormCollection-remove'
		};

		var template = element.find('span[data-template]');
		if (!template.length)
		{
			throw new Error("Form collection does not have a template. Did you forget to set the 'should_create_template' option?");
		}

		var container = template.parent();
		template = template.data('template');

		addLabel = addLabel || element.data('add-label') || 'Add item';
		addClass = addClass || element.data('add-class') || 'FaLink';
		addIcon = addIcon || element.data('add-icon') || 'fa fa-plus-circle';

		removeLabel = removeLabel || element.data('remove-label') || 'Remove';
		removeClass = removeClass || element.data('remove-class') || 'Button Button--danger';
		removeIcon = removeIcon || element.data('remove-icon') || 'fa fa-times-circle';
		if (typeof removeFlush == 'undefined')
		{
			removeFlush = element.data('remove-flush') || false;
		}

		if (typeof createIfEmpty == 'undefined')
		{
			createIfEmpty = true;
		}

		maxItems = maxItems || element.data('max-items') || -1;
		minItems = minItems || element.data('min-items') || -1;

		var index = container.children('fieldset').length;

		self.getElement = function()
		{
			return element;
		};

		self.getContainer = function()
		{
			return container;
		};

		var hydrateRow = function(row)
		{
			$('<div class="Form-row">' +
				'<div class="Form-label">'+ (removeFlush ? '' : '&nbsp;') +'</div>' +
				'<div class="Form-input">' +
				'<a class="'+ removeClass +' FormCollection-remove" href="javascript:">' +
				'<i class="'+ removeIcon +'"></i> '+ removeLabel +
				'</a>' +
				'</div>' +
				'</div>')
				.appendTo(row.children('div'));

			onHydrateRow && onHydrateRow(row);
		};

		self.addRow = function()
		{
			if (maxItems > -1 && self.getRows().length >= maxItems)
			{
				return;
			}

			var row = $(template.replace(/__index__/g, index++)).appendTo(container);
			hydrateRow(row);

			if (maxItems > -1 && self.getRows().length >= maxItems)
			{
				self.addButtonHide();
			}
			if (minItems > -1)
			{
				self.getRows().length > minItems ? self.removeButtonShow() : self.removeButtonHide();
			}
		};

		self.getRows = function()
		{
			return container.children('fieldset');
		};

		self.removeRow = function(rowOrButton)
		{
			if (minItems > -1 && self.getRows().length <= minItems)
			{
				return;
			}

			if (!rowOrButton.is('fieldset'))
			{
				rowOrButton = rowOrButton.parents('fieldset').eq(0);
			}
			rowOrButton.remove();

			if (maxItems > -1 && self.getRows().length < maxItems)
			{
				self.addButtonShow();
			}
			if (minItems > -1 && self.getRows().length <= minItems)
			{
				self.removeButtonHide();
			}
		};

		self.addButtonHide = function()
		{
			element.find('.FormCollection-add').addClass('is-disabled');
		};

		self.addButtonShow = function()
		{
			element.find('.FormCollection-add').removeClass('is-disabled');
		};

		self.removeButtonHide = function()
		{
			element.find('.FormCollection-remove').addClass('disabled');
		};

		self.removeButtonShow = function()
		{
			element.find('.FormCollection-remove').removeClass('disabled');
		};

		self.isEmpty = function()
		{
			return self.getRows().length == 0;
		};

		self.init = function(router)
		{
			$('<div class="Form-row u-PullRight"></div>')
				.insertAfter(container)
				.append($('<a class="'+ addClass +' FormCollection-add" href="javascript:">' +
					'<i class="'+ addIcon +'"></i> <span>'+ addLabel +'</span>' +
					'</a>'));

			container.children('fieldset').each(function()
			{
				hydrateRow($(this));
			});

			if (createIfEmpty && index == 0)
			{
				var itemsToCreate = minItems == -1 ? 1 : minItems;
				for (var i = 0; i < itemsToCreate; i++)
				{
					router.publish('add');
				}
			}

			if (maxItems > -1 && self.getRows().length >= maxItems)
			{
				self.addButtonHide();
			}
			if (minItems > -1 && self.getRows().length <= minItems)
			{
				self.removeButtonHide();
			}
		};

		self.attachListeners = function(router)
		{
			$.each(inputs, function(name, selector)
			{
				element.on('click', selector, function()
				{
					router.publish(name, $(this));

					return false;
				});
			});
		};
	};

	var Presenter = function(view)
	{
		var self = this;

		var router = new Router('Libs/Collection');

		router.subscribe('add', function()
		{
			view.addRow();
		});

		router.subscribe('remove', function(button)
		{
			view.removeRow(button);
		});

		self.getElement = function()
		{
			return view.getElement();
		};

		self.getContainer = function()
		{
			return view.getContainer();
		};

		self.add = function()
		{
			view.addRow();
		};

		self.clearAll = function()
		{
			view.getRows().each(function()
			{
				router.publish('remove', $(this));
			});
		};

		self.isEmpty = function()
		{
			return view.isEmpty();
		};

		self.init = function()
		{
			view.init(router);
			view.attachListeners(router);
		};
	};

	return function(params)
	{
		var view = new View(
			params.element
			, params.addLabel
			, params.addClass
			, params.addIcon
			, params.removeLabel
			, params.removeClass
			, params.removeIcon
			, params.removeFlush
			, params.createIfEmpty
			, params.maxItems
			, params.minItems
			, params.onHydrateRow
		);
		var inst = new Presenter(view);

		var container = inst.getContainer();

		if (!container.data('collection'))
		{
			container.data('collection', inst);
			inst.init();
		}

		return container.data('collection');
	};
});

