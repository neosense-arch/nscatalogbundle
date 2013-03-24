(function($){
	$(document).ready(function(){
		// boolean
		var ieBoolean = $('.ns-ie-boolean');
		ieBoolean.click(function(){
			var el = $(this).find('i');
			$.ajax({
				url:  el.data('url'),
				type: 'POST',
				data: {
					'id':    el.data('id'),
					'field': el.data('field'),
					'value': 1 - el.data('value')
				}
			})
			.done($.proxy(function(res){
				if (res.error) {
					throw res.error;
				}
				el.data('value', res.value);
				el.attr('data-value', res.value);
			}, this));
		});

		ieBoolean.mousedown(function(){
			$(this).find('.border').css({ top: 1, left: 1});
		});
		ieBoolean.mouseup(function(){
			$(this).find('.border').css({ top: 0, left: 0});
		});
	});
})(jQuery);