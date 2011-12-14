$(function() {
	$('.refresh').click(function(e) {
		e.preventDefault();

		var item = $(this);
		var target = $(item.attr('target'));

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
				$(this).addClass('error');
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
					$('[target="#oprhaned"]').click();
				});
			},
			error: function() {
				$(this).addClass('error');
			}
		});
	});

});

