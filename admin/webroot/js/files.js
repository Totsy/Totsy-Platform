$(function() {
	$('.refresh').click(function(e) {
		e.preventDefault();

		var item = $(this);
		var target = $(item.attr('target'));
		target.html('<div class="loading">Loading…</div>');

		$.ajax({
			url: item.attr('href'),
			success: function(data) {
				target.html(data);
			}
		});
	}).click();

	$('.file a[href*="/delete/"]').live('click', function(e) {
		e.preventDefault();
		var item = $(this).parent().parent();
		$.ajax({
			async: false,
			type: "DELETE",
			url: $(this).attr('href'),
			success: function() {
				item.fadeOut("normal", function() {
					$(this).remove();
				});
			},
			error: function() {
				item.addClass('error');
			}
		});
	});

	$('a[href*="/associate/pending"]').live('click', function(e) {
		e.preventDefault();
		$('#pending-data').html('<div class="loading">Associating…</div>');

		$.ajax({
			async: false,
			type: "POST",
			url: $(this).attr('href'),
			success: function() {
				$('[target="#pending-data"]').click();
				$('[target="#orphaned-data"]').click();
			},
			error: function() {
				item.addClass('error');
			}
		});
	});

	$('.file a[href*="/associate/"]').live('click', function(e) {
		e.preventDefault();
		var item = $(this).parent().parent();

		$.ajax({
			async: false,
			type: "POST",
			url: $(this).attr('href'),
			success: function() {
				item.fadeOut("normal", function() {
					$(this).remove();
					$('[target="#orphaned-data"]').click();
					$('[target="#event_media_status_data"]').click();
				});
			},
			error: function() {
				item.addClass('error');
			}
		});
	});

	/* On return/enter key blur thus trigger saving. */
	$('[contenteditable]').live('keypress', function(e) {
		if (e.which == 13) {
			$(this).blur();
		}
	});

	$('.file .name').live('blur', function(e) {
		var item = $(this);
		item.addClass('saving');

		$.ajax({
			type: "POST",
			url: item.attr('target'),
			data: {
				name: item.html()
			},
			success: function(data) {
				item.removeClass('saving');

				/* Update name in case it has changed due fixing the extension. */
				item.html(data.name);
			},
			error: function() {
				item.removeClass('saving');
				item.addClass('error');
			}
		});
	});
});

