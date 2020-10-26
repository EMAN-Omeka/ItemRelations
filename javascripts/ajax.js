window.jQuery = window.$ = jQuery;

jQuery(document).on('focus', '.ir-autocomplete', function(e) {
  webroot = $('#phpWebRoot').val();
	$(this).autocomplete({
		source: function(request, response) {
			$.getJSON(webroot + "/itemrelationsajax/", {
				q: request.term,
    		username : "OmEkA",
    		password : "nm493ie698vg"				
			}, function(data) {
				// data is an array of objects and must be transformed for autocomplete to use
				var array = data.error ? [] : $.map(data, function(item) {
					return {
						label: item.text,
						id: item.record_id
					};
				});
				response(array);
			});
		},
	  minLength: 3,
		focus: function(event, ui) {
			// prevent autocomplete from updating the textbox
			event.preventDefault();
		},
		select: function(event, ui) {
			// prevent autocomplete from updating the textbox
			event.preventDefault();
			// navigate to the selected item's url
			$(this).val(ui.item.label);
			$(this).parent().find('.itemId').val(ui.item.id);
		}	  
	});  
});



