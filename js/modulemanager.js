/**
 * Silverstripe Module Manager
 * Additional CMS functionality for Module Management fields
 * By James Barnsley (james@barnsley.nz, Github: jaedb)
 * Created February 2016
 **/
 
(function($) {
	$.entwine('ss', function($) {
		$('input.modulemanager-inherit-field').entwine({
			onchange: function(event) {
			
				// get our input field element
				var inputElement = $(event.target);
				
				if( inputElement.is(':checked') ){
					$(document).find('.field.modulemanager-modules-field').addClass('hide');
				}else{
					$(document).find('.field.modulemanager-modules-field').removeClass('hide');
				}
			}
		});
	})
})(jQuery);
