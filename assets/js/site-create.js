(function($){
	var shouldconfirm = false;

	window.onbeforeunload = function(e) {
		if ( shouldconfirm ) {
			e.message = CACSiteTemplatesSiteCreate.confirm;
			return CACSiteTemplatesSiteCreate.confirm;
		}
	}

	$(document).ready(function() {
		var $form = $('#setupform');
		$form.change(function(){
			shouldconfirm = true;
		}).submit(function() {
			shouldconfirm = false;
		});
	});
}(jQuery));
