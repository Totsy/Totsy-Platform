$(function() {
	function populate(cache) {
		$("#pending").html('<div class="loading">Loading…</div>');
		$.ajax({
			url: $('#pending').attr('target'),
			cache: cache,
			success: function(data) {
				$('#pending').html(data);
			}
		})
	}
	populate(true);

	$('#refresh-pending').click(function(e) {
		e.preventDefault();
		if (e.which != 1) {
			return;
		}
		populate(false);
	})
});

