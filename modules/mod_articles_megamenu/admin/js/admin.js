/* Admin v1.3.3 */

jQuery.noConflict();
window.addEvent("domready",function(){
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
	
	/* spacers and borders */
	jQuery('#module-sliders li > .spacer').parent().addClass('spacer').prev().css('border-bottom', '0');
	jQuery('#module-sliders li:last-child').css('border-bottom', '0');	

	/* advanced switch controls */
	toggle_switch_controls(jQuery("#module-sliders"));
	jQuery("#module-sliders ul").click(function(){
		toggle_switch_controls(jQuery(this));
	});
	
	function toggle_switch_controls(object) {
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
})