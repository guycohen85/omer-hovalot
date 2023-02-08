var OPCCart = {
			setproducttype : function (form, id) {
				form.view = null;
				var $ = jQuery; 
				//orignal: datas = form.serialize();
				
				var datas = form.serializeArray(); 
				
				var query = ''; 
				var cart_id = id; 
				query += '&option=com_onepage&nosef=1&task=opc&view=opc&controller=opc&cmd=updateattributes&tmpl=component&virtuemart_product_id[0]='+id+'&format=opchtml';
				for (var i=0; i<datas.length; i++)
				{
				  if (datas[i].name != 'undefined')
				  {
				    //stAn - no other characters then & have to be encoded here, all are handled by apache and other systems
				    query += '&'+datas[i].name+'='+datas[i].value.split("&").join("%26");
					
					if (datas[i].name == 'cart_virtuemart_product_id') cart_id = datas[i].value; 
				  }
				}
				
				cart_id = cart_id.split('::').join('___').split(';').join('__').split(':').join('_'); 
				
			    prices = $("#productPrice" + cart_id+" div span");
				//console.log(cart_id); 
				
				
				//query = query.split("&view=cart").join("&view=opc");
				//query = query.split("&task=update").join("&task=updateattribute");
				//query = query.split("&option=com_virtuemart").join("&option=com_onepage");
				
				OPCCart.log(query); 
				
				Onepage.op_runSS(null, null, true, 'updateattributes'+query, true); 
				return true; 
				prices.fadeTo("fast", 0.75);
				$.ajax({
				type: "POST",
				url: op_securl,
				data: query,
				complete: 
					function (datas, textStatus) {
					if (!(datas.readyState==4 && datas.status==200)) return;
					OPCCart.log(datas); 
					datasJson = jQuery.parseJSON(datas); 
					if (!(datasJson != null)) 
					{ 
					   //the json deserialization failed 
					   part = datas.responseText.indexOf('{"'); 
					   resp = datas.responseText.substr(part); 
					   datasJson = jQuery.parseJSON(resp); 
					}
					
					
					datas = datasJson; 
					    //OPCCart.log(datas); 
						prices.fadeTo("fast", 1);
						// refresh price
						if (typeof datas.price != 'undefined')
						{
						prices.html(datas.price); 
						form.find('input[name="cart_virtuemart_product_id"]').val(datas.new_key);
						new_keyval = datas.new_key.split('::').join('___').split(';').join('__').split(':').join('_'); 
						el = document.getElementById('productPrice'+cart_id); 
						 if (el != null)
						 el.id = 'productPrice'+new_keyval; 
						Onepage.op_runSS(null, null, true, 'refreshall'); 
						}

					}
					});
				
				return false; // prevent reload
			},
product : function(carts) {
				carts.each(function(){
					var cart = jQuery(this),
					step=cart.find('input[name="quantity"]'),
				    select = cart.find('select:not(.no-vm-bind)'),
					radio = cart.find('input:radio:not(.no-vm-bind)'),
					virtuemart_product_id = cart.find('input.opc_product').val(),
					quantity = cart.find('.quantity-input');

                    var Ste = parseInt(step.val());
                    //Fallback for layouts lower than 2.0.18b
                    if(isNaN(Ste)){
                        Ste = 1;
                    }
					
					
					
					select.change(function() {
						OPCCart.setproducttype(cart,virtuemart_product_id);
					});
					radio.change(function() {
						OPCCart.setproducttype(cart,virtuemart_product_id);
					});
					
				});

			}, 
			
log: function()
			{
			   //stAn,maybe a global logging variable should be present
			   if ((typeof virtuemart_debug != 'undefined') && (virtuemart_debug == true))
			   if (typeof console != 'undefined')
			   if (typeof console.log != 'undefined')
			   if (console.log != null)
			   for (var i = 0; i < arguments.length; i++) {
				console.log(arguments[i]);
				}
			   
			}
};			
if (typeof jQuery != 'undefined')
jQuery(document).ready(function($) {

			OPCCart.product($("form.opccartproduct"));
			
			/*
			$("form.opc-recalculate").each(function(){
				if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
					var id= $(this).find('input[name="virtuemart_product_id[]"]').val();
					OPCCart.setproducttype($(this),id);

				}
			});
			*/
		});
	var virtuemart_debug = true; 