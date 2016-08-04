window.jQuery = window.$ = jQuery;

jQuery(document).ready(function() {
	$('#checkall').change(function() {
		var state = $(this).prop('checked');
		$('.check-available').each(function (i, el) {
			$(el).prop('checked', state);
		});
	});
});
