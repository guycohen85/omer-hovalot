/**
 * vmsite.js: General Javascript Library for VirtueMart Frontpage
 *
 *
 * @package	VirtueMart
 * @subpackage Javascript Library
 * @author Patrick Kohl
 * @author Max Milbers
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

if (typeof Virtuemart === "undefined")
	var Virtuemart = {};
(function($){
	var undefined,
	methods = {
		list: function(options) {
			var dest = options.dest;
			var ids = options.ids;
			var prefix = options.prefiks;
            methods.update(this,dest,ids,prefix);
			$(this).change( function() { methods.update(this,dest,ids,prefix)});

		},
		update: function(org,dest,ids,prefix) {
			var opt = $(org),
				optValues = opt.val() || [],
				byAjax = [] ;
			if (!$.isArray(optValues)) optValues = jQuery.makeArray(optValues);
			if ( typeof  oldValues !== "undefined") {
				//remove if not in optValues
				$.each(oldValues, function(key, oldValue) {
					if ( ($.inArray( oldValue, optValues )) < 0 ) $("#"+prefix+"group"+oldValue).remove();
				});
			}
			//push in 'byAjax' values and do it in ajax
			$.each(optValues, function(optkey, optValue) {
				if( opt.data( 'd'+optValue) === undefined ) byAjax.push( optValue );
			});

			if (byAjax.length >0) {
				$.getJSON('index.php?option=com_virtuemart&view=state&format=json&virtuemart_country_id=' + byAjax,
						function(result){

						var virtuemart_state_id = jQuery('#'+prefix+'virtuemart_state_id');
						var status = virtuemart_state_id.attr('required');
						
						if(status == 'required') {
							if( result[byAjax].length > 0 ) {
								virtuemart_state_id.attr('required','required');
							} else {
								virtuemart_state_id.removeAttr('required');
							}
						}

                        jQuery.each(result, function(key, value) {
							if (value.length >0) {
								opt.data( 'd'+key, value );	
							} else { 
								opt.data( 'd'+key, 0 );		
							}
						});
						methods.addToList(opt,optValues,dest,prefix);
						if ( typeof  ids !== "undefined") {
							var states =  ids.length ? ids.split(',') : [] ;
							$.each(states, function(k,id) {
								$(dest).find('[value='+id+']').attr("selected","selected");
							});
						}
                        jQuery(dest).trigger("liszt:updated"); //in new chosen this is chosen:updated
					}
				);
			} else {
				methods.addToList(opt,optValues,dest,prefix)
				$(dest).trigger("liszt:updated");
			}
			oldValues = optValues ;
			
		},
		addToList: function(opt,values,dest,prefix) {
			$.each(values, function(dataKey, dataValue) { 
				var groupExist = $("#"+prefix+"group"+dataValue+"").size();
				if ( ! groupExist ) {
					var datas = opt.data( 'd'+dataValue );
					if (datas.length >0) {
					var label = opt.find("option[value='"+dataValue+"']").text();
					var group ='<optgroup id="'+prefix+'group'+dataValue+'" label="'+label+'">';
					$.each( datas  , function( key, value) {
						if (value) group +='<option value="'+ value.virtuemart_state_id +'">'+ value.state_name +'</option>';
					});
					group += '</optgroup>';
					$(dest).append(group);
					
					}
				}
			});
		}
	};

	$.fn.vm2front = function( method ) {

		if ( methods[method] ) {
		  return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
		  $.error( 'Method ' +  method + ' does not exist on Vm2 front jQuery library' );
		}    
	
	};
})(jQuery)
