/* SCRIPTS *v*********************
****************************************/

jQuery.noConflict();
(function($){$(function(){
	// login applet footer
	var loginButton = $(".moduletable.login-button .module-title");
	var loginForm = $(".moduletable.login-button #login-form");
	loginForm.hide();
	loginButton.click(function(){
		loginForm.slideToggle(150);
	});

	// disable accidental button text selection
	loginButton.attr('unselectable','on')
     .css({'-moz-user-select':'-moz-none',
           '-moz-user-select':'none',
           '-o-user-select':'none',
           '-khtml-user-select':'none', /* you could also put this in a class */
          '-webkit-user-select':'none',/* and add the CSS class here instead */
          '-ms-user-select':'none',
          'user-select':'none' 
    }).bind('selectstart', function(){ return false; });

     // back to top
		(function(){
			$('#back_to_top').on('click', function(){
				$('html, body').stop(true).animate({
					scrollTop: 0
				}, {
					duration: 800, 
					complete: window.reflow
				});
				return false;
			});
		})();

});})(jQuery);