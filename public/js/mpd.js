function mpdAction(element)
{
	var dataJson = {};
	var message = undefined;
	if (element != undefined)
	{
		dataJson = {'action': element.attr('func'), 'params': element.attr('params'), 'resultOnly': element.attr('resultOnly')};
		message = element.attr('msg');
	}

	$.ajax({
		url: '/zf2/music/mpd-action',
		beforeSend: function()
		{
			if (message != undefined)
			{
				$('div#loading').html(message);
				$('div#loading').fadeIn();
			}
		},
		type: 'post',
		data: dataJson,
		success: function(result)
		{
			var result = jQuery.parseJSON(result);
			if (result["connected"] != undefined)
			{
				updateCurrentTrack(result);
				updateButtons(result);
			}
			else if (result["artist"] != undefined)
			{
				updateSearchResults(result);
			}
		},
		error: function(result)
		{
			console.log('error');
		}
	}).done(function()
	{
		$('div#loading').delay(500).fadeOut();
	});
}

function updateCurrentTrack(mpd)
{
	var currTrackId = mpd["current_track_id"];
	var currTrack = mpd["playlist"][currTrackId];

	if (currTrack)
	{
		$('span#track-name').html(currTrack["Title"]);
		$('span#artist-name').html(currTrack["Artist"]);
		$('span#album-name').html(currTrack["Album"]);
	}
}

function updateButtons(mpd)
{
	$('span.curr-track').each(function()
	{
		$(this).html('');
	});
	switch (mpd["state"])
	{
		case 'pause':
			$('span#play').hide();
			$('span#pause').show();
			$('span#pause').html('<i class="fa fa-play fa-2x"></i>');
			$('span#curr-track-' + mpd["current_track_id"]).html('<i class="fa fa-pause"></i>');
			break;
		case 'play':
			$('span#play').hide();
			$('span#pause').show();
			$('span#pause').html('<i class="fa fa-pause fa-2x"></i>');
			$('span#curr-track-' + mpd["current_track_id"]).html('<i class="fa fa-play"></i>');
			break;
		case 'stop':
			$('span#play').show();
			$('span#pause').hide();
		default:
			break;
	}
	
	var repeat = $('span#repeat');
	if (mpd["repeat"] == 1)
	{
		repeat.addClass('selected');
		repeat.attr('params', 0)
	}
	else 
	{
		repeat.removeClass('selected');
		repeat.attr('params', 1)
	}
	
	var shuffle = $('span#shuffle');
	if (mpd["random"] == 1)
	{
		shuffle.addClass('selected');
		shuffle.attr('params', 0)
	}
	else 
	{
		shuffle.removeClass('selected');
		shuffle.attr('params', 1)
	}

	$('input[name="volume"]').val(mpd["volume"]);
}

function updateSearchResults(results)
{
	var artists = results["artist"];
	var album = results["album"];
	var title = results["title"];

	$('tbody.search-results').html('');

	$.each(results, function(type, tracks)
	{
		var typeBody = $('tbody#' + type);
		var typeCount = $('span#' + type + '-result-count').html(tracks.length);
		$.each(tracks, function(index, track)
		{
			typeBody.append("" +
				"<tr class=\"search-result\" func=\"pLAdd\" params=\"" + track["file"] + "\">" +
					"<td>" +
						"<div>" + track["Title"] + "</div>" +	
						"<div>" + track["Artist"] + "&nbsp;-&nbsp;" + track["Album"] + "</div>" +
					"</td>" +
				"</tr>" +
			"");
		});
		if (tracks.length == 0)
		{
			typeBody.append("" +
				"<tr>" +
					"<td>" +
						"There were no " + type + " results for this search" +	
					"</td>" +
				"</tr>" +
			"");
		}
	});
}
	
$(function()
{
	$('div.controls span:not(span#position-slider span#volume-slider)').click(function()
	{
		mpdAction($(this));
	});
	$('span#position-slider input, span#volume-slider input').on('mouseup', function()
	{
		$(this).attr('params', $(this).val());
		mpdAction($(this));
	});

	var constantUpdate = function()
	{
		mpdAction(undefined);
	};
	setInterval(constantUpdate, 1000);
});
