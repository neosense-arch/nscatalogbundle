(function($){
	$(document).ready(function(){
		var ieBoolean = $('.ns-ie-boolean');
		ieBoolean.click(function(){
			var el = $(this).find('i');
			var val = 1 - el.data('value');
			$.ajax({
				url:  el.data('url'),
				type: 'POST',
				data: {
					'id':    el.data('id'),
					'field': el.data('field'),
					'value': val
				}
			})
			.done($.proxy(function(res){
				if (res.error) {
					throw res.error;
				}
				el.data('value', val);
				el.attr('data-value', val);
			}, this));
		});

		ieBoolean.mousedown(function(){
			$(this).find('.border').css({ top: 1, left: 1});
		});
		ieBoolean.mouseup(function(){
			$(this).find('.border').css({ top: 0, left: 0});
		});

		var ieTextLink = $('.ns-ie-text a');
		ieTextLink.click(function(){
			$(this).hide();
			$(this).parent().find('input').show().focus().select();
		});
		var fnIeTextInputChange = function(){
			var el = $(this);
			el.hide();
			$.ajax({
				url:  el.data('url'),
				type: 'POST',
				data: {
					'id':    el.data('id'),
					'field': el.data('field'),
					'value': el.val().replace(',', '.').replace(' ', '')
				}
			})
			.done($.proxy(function(res){
				if (res.error) {
					throw res.error;
				}
				el.parent().find('a').show().text(el.val());
			}, this));
		};
		$('.ns-ie-text input')
			.blur(fnIeTextInputChange)
			.keypress(function(e) {
				var code = (e.keyCode ? e.keyCode : e.which);
				if (code == 13) {
					$(this).blur();
				}
			});
	});
})(jQuery);