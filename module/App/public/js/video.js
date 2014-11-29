$(function()
{
	var video = $('video.video-player')[0];
	if (video)
	{
		video.onerror = function(e) 
		{
			console.log('error');
			$('div.video-error-msg').fadeIn();
		};
		video.onabort = function(e) 
		{
			console.log('error');
			$('div.video-error-msg').fadeIn();
		};
	}
});
