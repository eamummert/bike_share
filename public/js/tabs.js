$(function()
{
	$('.Tab-navs,.tab-navs').each(function()
	{
		var tabs = $(this).find('li a');
		var panes = $(this).next('.Tab-panes,.tab-panes').find('.Tab-pane,.tab-pane');
		tabs.each(function(i)
		{
			var tab = $(this);
			tab.click(function()
			{
				tabs.removeClass('active');
				tab.addClass('active');

				panes.removeClass('active');
				panes.eq(i).addClass('active');

				return false;
			});
		});
		if (tabs.filter('.active').length == 0)
		{
			tabs.first().click();
		}
		else if (panes.filter('.active').length == 0)
		{
			panes.eq(tabs.index(tabs.filter('.active'))).addClass('active');
		}
	});
});
