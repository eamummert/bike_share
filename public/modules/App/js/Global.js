define(['jquery', 'Libs/Collection'], function($, Collection)
{
	$('.season-holder').each(function()
	{
		new Collection({
			element: $(this),
			removeFlush: true,
			maxItems: 0,
			removeClass: 'Button',
			removeIcon: 'fa fa-minus-circle',
			addClass: 'Button'
		});
	});
});
