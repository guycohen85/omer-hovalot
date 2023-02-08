/* Admin v1.10.1 */

if(typeof jQuery.noConflict !== 'undefined'){
	jQuery.noConflict();
}
window.addEvent("domready",function(){
  (function(jQuery, window, document) {
	var parent = 'li:first';
	if(jQuery(".row-fluid").length){
		parent = 'div.control-group:first';
	}
	jQuery("#jform_params_asset-lbl").parents(parent).remove();
	jQuery('#module-sliders li > .btn-group').each(function(){
		if(jQuery(this).find('input').length != 2 ) return;
		if(this.id.indexOf('advancedparams') ==0) return;
		jQuery(this).hide();
		var group = this;
		var el = jQuery(group).find('input:checked');	
		var switchClass ='';

		if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='off' || el.val()=='false'){
			switchClass = 'no';
		}else{
			switchClass = 'yes';
		}
		var switcher = new Element('div',{'class' : 'switcher-'+switchClass});
		
		var name = this.id.replace('jform_params_','');
		var parent = 'li:first';
		if(jQuery(this).parent().hasClass('controls')){
			parent = 'div.control-group:first';
		}
		jQuery('.'+name + '_' + (switchClass=='yes'?'no':'yes')).parents(parent).hide();
		switcher.inject(group, 'after');
		switcher.addEvent("click", function(){
			var el = jQuery(group).find('input:checked');	
			if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='off' || el.val()=='false'){
				switcher.setProperty('class','switcher-yes');
				switcher.setProperty('title','On');
				jQuery('.'+name + '_no' ).parents(parent).hide();
				jQuery('.'+name + '_yes' ).parents(parent).show();
			}else {
				switcher.setProperty('class','switcher-no');
				switcher.setProperty('title','Off');
				jQuery('.'+name + '_yes' ).parents(parent).hide();
				jQuery('.'+name + '_no' ).parents(parent).show();
			}
			jQuery(group).find('input:not(:checked)').attr('checked',true);
		});
	});
	jQuery(".pane-sliders select").each(function(){
		if(this.id.indexOf('advancedparams') ==0) return;
		if(jQuery(this).is(":visible")) {
			if(jQuery(this).attr('multiple')){
				jQuery(this).css("width","65%");
			}else{
				jQuery(this).css("width",parseInt(jQuery(this).width())+20);
			}
			jQuery(this).chosen()
		};
	});
	jQuery(".chzn-container").click(function(){
		jQuery(".panel .pane-slider,.panel .panelform").css("overflow","visible");	
	});
	jQuery(".panel .title").click(function(){
		jQuery(".panel .pane-slider,.panel .panelform").css("overflow","hidden");		
	});
	
	/******************************************************************
							Custom Functions
	 ******************************************************************/
	
	/* style spacers and borders */
	jQuery('#module-sliders li > .spacer').parent().addClass('spacer').prev().css('border-bottom', '0');
	jQuery('#module-sliders li:last-child').css('border-bottom', '0');

	/* advanced switch controls */
	toggle_switch_controls(jQuery("#module-sliders"));
	jQuery("#module-sliders ul").click(function(){
		toggle_switch_controls(jQuery(this));
	});
	
	/* updates switch controls */
	function toggle_switch_controls(object){
		var element_parents_hidden;
		// This class toggles all fields in slider's panel
		jQuery(object).find(".main_switch_control").each(function(){
			var element = jQuery(this);
			var element_status = element.next('div').attr('class');
			if (typeof element_status !== 'undefined') {
				var element_parents = element.parents(parent);
				if (element_status == 'switcher-no') {
					element_parents_hidden = element_parents.siblings();
					element_parents_hidden.stop(true, true).hide();
					element_parents.css('border-bottom-width', '0');
				}
				if (element_status == 'switcher-yes') {
					element_parents.siblings().fadeIn(500).css('border-bottom-width', '1px');
					element_parents.css('border-bottom-width', '1px');
				}
			}
		});
		
		// This class toggles only local fields near switch
		jQuery(object).find(".local_switch_control").each(function(){
			element = jQuery(this);
			var element_status = element.next('div').attr('class');
			if (typeof element_status !== 'undefined') {
				var element_parents = element.parents(parent);
				if (element_status == 'switcher-no') {
					element_parents.nextUntil('.spacer').stop(true, true).hide();
					element_parents.css('border-bottom-width', '0');
				}
				if (element_status == 'switcher-yes') {
					element_parents.not(element_parents_hidden).nextUntil('.spacer').fadeIn(500).css('border-bottom-width', '1px');
					element_parents.css('border-bottom-width', '1px');
				}
			}
		});
	}
	
	/* toggle panel sliders */
	function toggle_panel_sliders(state){
		if (state) {
			// Expand panel sliders
			jQuery('#module-sliders').find('h3.title').removeClass('pane-toggler').addClass('pane-toggler-down').next().css('height','auto').slideDown(500);
		}
		else {
			// Collapse panel sliders
			jQuery('#module-sliders').find('h3.title').removeClass('pane-toggler-down').addClass('pane-toggler').next().stop(true, true).slideUp(500);
		}
	}
	
	/* switches radio button */
	function switch_jform_radio(id,state){
		var attrClass = 'switcher-' + (state ? 'yes':'no');
		jQuery(id).val(state).next().attr('class',attrClass);
		jQuery(id + state).attr('checked',new Boolean(state))
	}
	
	/* switches select list */
	function switch_jform_select(id,state,title){
		jQuery(id).val(state).next().find("a span").html(title);
	}
	
	/******************************************************************
								Kickstarter
	 ******************************************************************/
	
	/* append html code */
	jQuery("#module_description").
		append('<br style="clear:both">' + 
		'<h3>Kickstarter</h3>' + 
		'<ul>' +
			'<li><a class="publish" href="#publish">publish module</a></li>' +
			'<li><a class="expand" href="#expand">expand sliders</a></li>' +
			'<li><a class="accordion" href="#accordion">set accordion menu</a></li>' +
			'<li><a class="dropdown" href="#dropdown">set dropdown menu</a></li>' +
			'<li><a class="mobile" href="#mobile">set mobile menu</a></li>' +
			'<li><a class="responsive" href="#responsive">set responsive menu</a></li>' +
			'<li><a class="vertical" href="#vertical">set vertical menu</a></li>' +
		'</ul>' +
		'<br style="clear:both"><br>' + 
		'<h3>Help</h3>' +
		'<ul>' +
			'<li class="help">For break-line(s) use <kbd>Tab &#x21B9;</kbd> key or insert <code>{br}</code> in menu title (Menus&rarr; Menu Manager&rarr; Edit Menu Item&rarr; Menu Title)</li>' +
			'<li class="help">For Mega Menu functionality use menu item type "Text Separator" with syntax <code>{|position-1|}</code> or <code>{module 101}</code> in menu title.</li>' +
		'</ul>' +
		'<br style="clear:both">');
	
	/* module helpers */
	jQuery("#module_description a").click(function(e){
		e.preventDefault();
		var element = jQuery(this);
		if (element.hasClass('publish')) {
			jQuery('#jform_showtitle [value=0]').prop('checked', true);
			jQuery('#jform_published').val('1');
			jQuery('#jform_assignment').val('0');
		}
		else if (element.hasClass('expand')) {
			toggle_panel_sliders(1);
		}
		else if (element.hasClass('accordion')) {
			switch_jform_radio('#jform_params_showAllChildren', 1);
			switch_jform_select('#jform_params_cssTheme', 'accordion', 'Accordion');
			switch_jform_select('#jform_params_arrowPosition', 0, 'Before');
			switch_jform_radio('#jform_params_mobileMenuIcon', 0);
			switch_jform_radio('#jform_params_javascriptMenu', 1);
			switch_jform_select('#jform_params_animationEffect', 'slide', 'Slide');
			switch_jform_select('#jform_params_loadjQuery', 'auto', 'Auto');
			switch_jform_radio('#jform_params_responsiveMenu', 0);
			toggle_switch_controls(jQuery("#module-sliders"));
		}
		else if (element.hasClass('dropdown')) {
			switch_jform_radio('#jform_params_showAllChildren', 1);
			switch_jform_select('#jform_params_cssTheme', 'horizontal', 'Horizontal');
			switch_jform_select('#jform_params_arrowPosition', 1, 'After');
			switch_jform_radio('#jform_params_mobileMenuIcon', 0);
			switch_jform_radio('#jform_params_javascriptMenu', 1);
			switch_jform_select('#jform_params_animationEffect', 'fade', 'Fade');
			switch_jform_select('#jform_params_loadjQuery', 'auto', 'Auto');
			switch_jform_radio('#jform_params_responsiveMenu', 0);
			toggle_switch_controls(jQuery("#module-sliders"));
		}
		else if (element.hasClass('mobile')) {
			switch_jform_radio('#jform_params_showAllChildren', 1);
			switch_jform_select('#jform_params_cssTheme', 'mobile', 'Mobile');
			switch_jform_select('#jform_params_arrowPosition', 0, 'Before');
			switch_jform_radio('#jform_params_mobileMenuIcon', 1);
			switch_jform_radio('#jform_params_javascriptMenu', 1);
			switch_jform_select('#jform_params_animationEffect', 'slide', 'Slide');
			switch_jform_select('#jform_params_loadjQuery', 'auto', 'Auto');
			switch_jform_radio('#jform_params_responsiveMenu', 0);
			toggle_switch_controls(jQuery("#module-sliders"));
		}
		else if (element.hasClass('responsive')) {
			switch_jform_radio('#jform_params_showAllChildren', 1);
			switch_jform_select('#jform_params_cssTheme', 'responsive', 'Responsive');
			switch_jform_select('#jform_params_arrowPosition', 1, 'After');
			switch_jform_radio('#jform_params_mobileMenuIcon', 1);
			jQuery('#jform_params_mobileMenuTitle').val('תפריט');
			switch_jform_radio('#jform_params_javascriptMenu', 1);
			switch_jform_select('#jform_params_animationEffect', 'slide', 'Slide');
			switch_jform_select('#jform_params_loadjQuery', 'auto', 'Auto');
			switch_jform_radio('#jform_params_responsiveMenu', 1);
			toggle_switch_controls(jQuery("#module-sliders"));
		}
		else if (element.hasClass('vertical')) {
			switch_jform_radio('#jform_params_showAllChildren', 1);
			switch_jform_select('#jform_params_cssTheme', 'vertical', 'Vertical');
			switch_jform_select('#jform_params_arrowPosition', 0, 'Before');
			switch_jform_radio('#jform_params_mobileMenuIcon', 0);
			switch_jform_radio('#jform_params_javascriptMenu', 1);
			switch_jform_select('#jform_params_animationEffect', 'fade', 'Fade');
			switch_jform_select('#jform_params_loadjQuery', 'auto', 'Auto');
			switch_jform_radio('#jform_params_responsiveMenu', 0);
			toggle_switch_controls(jQuery("#module-sliders"));
		}
	});
  }(window.jQuery, window, document));
});