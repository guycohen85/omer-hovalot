/*
* This is main JavaScript file to handle registration, shipping, payment and other functions of checkout
* 
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
var Onepage =  {
clearChromeAutocomplete : function() {

	// not possible, let's try: 
	if (navigator.userAgent.toLowerCase().indexOf('chrome') >= 0) 
	{
	af = document.getElementById('adminForm');
	if (af != null)
	{
	af.setAttribute('autocomplete', 'off'); 
    setTimeout(function () {
        	document.getElementById('adminForm').setAttribute('autocomplete', 'on'); 
    }, 1500);
	}
	}
},

/*
* This function is ran by AJAX to generate shipping methods and tax information
* 
*/
op_runSS: function(elopc, onlyShipping, force, cmd, sync)
{
   
   //reset form submission
   var dtx = document.getElementById('form_submitted'); 
   if (dtx != null) dtx.value = '0'; 

	
 if (typeof sync == 'undefined') sync = false; 
 else
 if (!(sync != null)) sync = false; 
 else sync = true; 
 
 
   if (typeof callBeforeAjax != 'undefined')
	   if (callBeforeAjax != null && callBeforeAjax.length > 0)
   {
   for (var x=0; x<callBeforeAjax.length; x++)
	   {
	     eval(callBeforeAjax[x]);
	   }
   }
 
 var payment_id = Onepage.getPaymentId();
 op_saved_payment = payment_id; 
 if ((typeof elopc != 'undefined') && (elopc != null && (elopc == 'init')))
  {
	Onepage.clearChromeAutocomplete(); 
    // first run
	var elopc = document.getElementById('virtuemart_country_id'); 
//	if ((el != null) && (el.value != ''))

    var ship_id = Onepage.getInputIDShippingRate();
	   
	
					
	Onepage.getTotals(ship_id, payment_id);
	
	
  }
  
  
  var delay_ship = ''; 
 if (!(cmd != null))
 {
 
 
 
 if (typeof op_autosubmit == 'undefined') return; 
 
 if (force == null && (!op_autosubmit))
 if (typeof(elopc) != 'undefined')
 {
 if (op_delay && op_last_field)
 { 
  if (elopc != null)
  if (elopc.name != null)
  if (!(elopc.name == op_last1 || elopc.name == op_last2))
  {
    Onepage.resetShipping(); 
    Onepage.showcheckout(); 
 	delay_ship = '&delay_ship=delay_ship';   
 	
  }
  else
  {
   
  }
 }
 }
 if (typeof(elopc) == 'undefined' && (force == null) && (op_delay) && (!op_autosubmit))
 {
    Onepage.resetShipping(); 
    delay_ship = '&delay_ship=delay_ship';   
 }
 // op_last_field = false
 // force = false
 // op_delay = true
 // if delay is on, but we don't use last field, we will not load shipping
 if (op_delay && (!op_last_field) && (force != true))
 {
    Onepage.resetShipping(); 
    delay_ship = '&delay_ship=delay_ship';   
 }
 
 
 if (op_autosubmit)
 {
  if (document.adminForm != null)
  {
   document.adminForm.submit();
   return true;
  }
 }
 if (op_dontloadajax) 
  {
   Onepage.showcheckout(); 
   Onepage.op_hidePayments();
   Onepage.runPay();
   return true;
  }
 var ui = document.getElementById('user_id');
 var user_id = 0;
 if (ui!=null)
 {
  user_id = ui.value;
 }

 //if ((op_noshipping == false) || (op_noshipping == null))
 
 }
	
    
	
	// if shipping section 
    var country = '';
    var zip = '';
    var state = '';
    var address_1 = '';
    var address_2 = '';
    var onlyS = 'false';
    if (onlyShipping !=null) 
    if (onlyShipping == true)
    {
     onlyS = 'true';
    }
    else 
    {
     onlyS = 'false';
    }
	shipping_open = Onepage.shippingOpen(); 
		
	
	
    addressq = Onepage.op_getaddress();
    country = Onepage.op_getSelectedCountry();
    country = Onepage.op_escape(country);
    zip = Onepage.op_getZip();
    zip = Onepage.op_escape(zip);
    state = Onepage.op_getSelectedState();
    state = Onepage.op_escape(state);
	
	
	
	var ship_to_info_id = 0;
	
	// if we are logged in
	if (!(op_logged_in != '1'))
	{
	if (!shipping_open)
	{
	  d = document.getElementById('ship_to_info_id_bt'); 
	  if (d != null)
	  ship_to_info_id = d.value; 
	}
	//else
	{
	 var st = document.getElementsByName('ship_to_info_id');
	 
     if (st!=null)
	 for (var u=0; u<st.length; u++)
	 {
     
	    var ste = st[u]; 
		
	    if (ste.type == 'select-one')
		 {
		   if ((ste.options != null) && (ste.selectedIndex != null))
		   ship_to_info_id = ste.options[ste.selectedIndex].value; 
		 }
		else
		if (ste.type == 'radio')
        for (i=0;i<ste.length;i++)
        {
         if (ste[i].checked) 
         ship_to_info_id = Onepage.op_escape(ste[i].value);
        }
       break; 
	 }
	}
	}

	shipping_open = shipping_open.toString(); 
    var coupon_code = Onepage.getCouponCode();

    var sPayment = Onepage.getValueOfSPaymentMethod();
	sPayment = Onepage.op_escape(sPayment);
	var sShipping = "";
	if ((op_noshipping == false) || (op_noshipping == null))
    {
    sShipping = Onepage.getVShippingRate();
    sShipping = Onepage.op_escape(sShipping);
	if (sShipping != '')
	op_saved_shipping_vmid = sShipping; 
    }
	
	op_saved_shipping_local = op_saved_shipping;
    var op_saved_shipping2 = Onepage.getInputIDShippingRate();
	if (op_saved_shipping2 != 0) 
	if (op_saved_shipping2 != "")
	op_saved_shipping = op_saved_shipping2; 
	var op_saved_shipping_escaped = Onepage.op_escape(op_saved_shipping);
    
   
   
    var query = 'coupon_code='+coupon_code+delay_ship+'&shiptoopen='+shipping_open+'&stopen'+shipping_open;
	if (typeof opc_theme != 'undefined')
	if (opc_theme != null)
	query += '&opc_theme='+opc_theme; 
	
	var isB = Onepage.isBusinessCustomer(); 
	if (isB)
	query += '&opc_is_business=1'; 
	
    //var url = op_securl+"?option=com_onepage&view=ajax&format=raw&tmpl=component&op_onlyd="+op_onlydownloadable;
	if (!(typeof virtuemart_currency_id != 'undefined')) virtuemart_currency_id = ''; 
	
	var url = op_securl+"&nosef=1&task=opc&view=opc&format=opchtml&tmpl=component&op_onlyd="+op_onlydownloadable+"&lang="+op_lang;
	
	
	
	var extraquery = Onepage.buildExtra(); 
	
    
	if ((op_noshipping == false) || (op_noshipping == null))
	{
         query += "&virtuemart_country_id="+country+"&zip="+zip+"&virtuemart_state_id="+state+"&weight="+op_weight+"&ship_to_info_id="+ship_to_info_id+"&payment_method_id="+sPayment+"&os="+onlyS+"&user_id="+user_id+'&zone_qty='+op_zone_qty+addressq;
         query2 = "&selectedshipping="+op_saved_shipping_escaped+"&shipping_rate_id="+op_saved_shipping_vmid+"&order_total="+op_order_total+"&tax_total="+op_tax_total;
	}
	else 
	{
	 // no shipping section
	 query += "&no_shipping=1&virtuemart_country_id="+country+"&zip="+zip+"&virtuemart_state_id="+state+"&order_total="+op_order_total+"&tax_total="+op_tax_total+"&weight="+op_weight+"&ship_to_info_id="+ship_to_info_id+"&payment_method_id="+sPayment+"&os="+onlyS+"&user_id="+user_id+'&zone_qty='+op_zone_qty+addressq;
	 query2 = ''; 
	}
	query += "&virtuemart_currency_id="+op_currency_id;
	
	if (cmd != null)
	query += "&cmd="+cmd;
	
	if (virtuemart_currency_id != '0')
	if (virtuemart_currency_id != '')
	query += '&virtuemart_currency_id='+virtuemart_currency_id;
	
	
	// dont do duplicit requests when updated from onblur or onchange due to compatiblity
	if (cmd != null)
	{
	
	  // if we have a runpay request, check if the shipping really changed
	  if (force != true)
	  if (op_lastq == query && (op_saved_shipping_local == op_saved_shipping)) return true;
	}
	else
	if (op_lastq == query && (force != true)) 
	{
	
	return true;
	}
	
	op_lastq = query;
	query += query2+extraquery; 
	
	
	
	Onepage.showLoader(cmd);
	return Onepage.ajaxCall('POST', url, query, sync, "application/x-www-form-urlencoded; charset=utf-8", Onepage.op_get_SS_response); 

    
    
 
 
 return false; 
},

ajaxCall: function(type, url, query, sync, contentType, callBack)
{
   	if (typeof jQuery == 'undefined')
	{
	if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null))
	{
	 
	}
	else
	{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
	}
    if (xmlhttp2!=null)
    {
	
	if (sync == true)
    xmlhttp2.open(type, url, false);
	else
	xmlhttp2.open(type, url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", contentType);
    //xmlhttp2.setRequestHeader("Content-length", query.length);
    //xmlhttp2.setRequestHeader("Connection", "close");
	if (!sync)
    xmlhttp2.onreadystatechange= Onepage.op_get_SS_response ;
	

	
	 op_isrunning = true; 
     xmlhttp2.send(query); 
	 
	 if (sync)
	 {
	  if (xmlhttp2.status === 200) {
			return Onepage.op_get_SS_response(); 
		} 
	 }
	 }
	 }
	 else
	 {
	    if (!sync)
		{
	    jQuery.ajax({
				type: "POST",
				url: url,
				data: query,
				cache: false,
				complete: callBack,
				async: true
				
				});
		}
		else
		{
		var retObj = false; 
		var result = null; 
	    var ret = jQuery.ajax({
				type: "POST",
				url: url,
				data: query,
				cache: false,
				async: false,
				complete: function(datasRaw)
				  {
				    if (!(datasRaw.readyState==4 && datasRaw.status==200)) return;
					retObj = datasRaw; 
					if (datasRaw.readyState==4 && datasRaw.status==200) result = Onepage.op_get_SS_response(datasRaw); 
					return result; 
				  }
				
				
				});
		
		
		}

		
		
	
	 }
	 
	 
	 return false; 
},




/*
* This is response function of AJAX
* Response is HTML code to be used inside noshippingheremsg DIV
*/       
op_get_SS_response: function (rawData, async)
{
	if (typeof rawData != 'undefined')
	if (rawData)
	xmlhttp2 = rawData; 
   
   var returnB = true; 
   
   if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null))
   {
   if (opc_debug)
   Onepage.op_log(xmlhttp2); 
   }
    runjavascript = new Array(); 

  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	   if (opc_debug)
	  Onepage.op_log(resp); 
	 // lets clear notices, etc... 
	 //try
	 {
	var part = resp.indexOf('{"cmd":'); 
	 if (part >=0) 
	 {
	 if (part !== 0)
	 resp = resp.substr(part); 
	if ((JSON != null) && (typeof JSON.decode != 'undefined'))
	{
	try
	{
	 var reta = JSON.decode(resp); 
	}
	catch (e)
	 {
	   Onepage.op_log('Error in Json data'); 
	   Onepage.op_log(resp); 
	   Onepage.op_log(xmlhttp2); 
	 }
	}
	if ((typeof reta == 'undefined') || (!(reta != null)))
	{
	  var reta = eval("(" + resp + ")");
	}
	
	
	
    if (typeof reta.basket != 'undefined')
	{
		 Onepage.showNewBasket(reta.basket);
	}

	if (typeof reta.sthtml != 'undefined')
	 {
	    Onepage.setSTAddressHtml(reta.sthtml); 
	 }

	
	if ((reta.shipping != null) || (typeof reta == 'undefined'))
	{
	
	var shippinghtml = reta.shipping;
	}
	else
	{
	 var shippinghtml = resp;
	 return;
	}
	
	if (typeof reta.couponcode != 'undefined')
	var coupon_code = reta.couponcode; 
	else 
	var coupon_code = ''; 
	
	var coupon_percent = 0; 
	if (typeof reta.couponpercent != 'undefined')
	coupon_percent = reta.couponpercent; 
	
	
	Onepage.showCouponCode(coupon_code, coupon_percent);
	
	if (typeof reta.inform_html != 'undefined')
	if (reta.inform_html != null)
	if (reta.inform_html != "")
	{
	  var d = document.getElementById('inform_html'); 
	  if (d != null)
	    Onepage.setInnerHtml(d, reta.inform_html); 
	   //d.innerHTML = reta.inform_html; 
	}
	
	if (reta.username_exists != null)
	 {
	   Onepage.username_check_return(reta.username_exists); 
	 }
	 
	if (reta.email_exists != null)
	 {
	 
	   Onepage.email_check_return(reta.email_exists); 
	 }
	
	if ((reta.totals_html != null) && (reta.totals_html != ''))
	{
		
	  dx = document.getElementById('opc_totals_hash'); 
		if (dx != null)
		Onepage.setInnerHtml(dx, reta.totals_html); 
		//dx.innerHTML = reta.totals_html; 
	}
	
	if (typeof reta.msgs != 'undefined')
	if (reta.msgs != null)
	Onepage.setMsgs(reta.msgs); 

	if (typeof reta.debug_msgs != 'undefined')
	if (reta.debug_msgs != null)
	Onepage.printDebugInfo(reta.debug_msgs); 

	
	if (reta.payment != null)
	paymenthtml = reta.payment; 
	
	//else alert('error'); 
    
	if (reta.javascript != null)
	runjavascript = reta.javascript; 
	
    if (typeof reta.opcplugins != 'undefined')
	if (reta.opcplugins != null)
	{
	  Onepage.processPlugins(reta.opcplugins); 
	}

	var vatmsg = ''; 
	if (typeof reta.checkvat != 'undefined')
	if (reta.checkvat != null)
	vatmsg = reta.checkvat; 
	Onepage.showVatStatus(vatmsg); 
	
	
	if (reta.klarna != null)
	 {
	    //Onepage.op_log(reta.klarna); 
	    Onepage.setKlarnaAddress(reta.klarna); 
	 }
	
	 var payment_extra = ''; 
	 if (typeof reta.payment_extra != 'undefined')
	 if (reta.payment_extra != null)
	  payment_extra = reta.payment_extra; 
	
	}
	else
	var shippinghtml = resp; 
	}
	
	
	
	
	d2 = document.getElementById('op_last_field_msg'); 
    if (d2 != null)
    {
     //d2.innerHTML = ''; 
	 Onepage.setInnerHtml(d2, ''); 
    }

	

	
	 if (resp.indexOf('payment_inner_html')>0)
	 if ((typeof paymenthtml != 'undefined') && (paymenthtml != null))
	 Onepage.setPaymentHtml(paymenthtml, payment_extra);
	 
	 // != 'opc_do_not_update')
	 if (shippinghtml.indexOf('opc_do_not_update')<0)
	 Onepage.setShippingHtml(shippinghtml); 
	 Onepage.showcheckout(); 
    }
    else
    {
     
    }
   
    if ((op_saved_shipping != null) && (op_saved_shipping != ""))
     { 
      var ss = document.getElementById(op_saved_shipping);  
	  if (ss != null)
	  {
	  //Onepage.op_log(op_saved_shipping); 
	  // we use try and catch here because we don't know if what type of html element is the shipping
	  try
	  {
	   // for option
       ss.selected = true;
	   // for checkbox and radio
       ss.checked = true;
	   }
	  catch (e)
	   {
	    ;
	   }
	  }
     }
    
	if ((op_saved_payment != null) && ((op_saved_payment != "") && (op_saved_payment!=0)))
     { 
      var ss = document.getElementById(op_saved_payment);  
	  if (ss != null)
	  {
	  //Onepage.op_log(op_saved_payment); 
	  // we use try and catch here because we don't know if what type of html element is the shipping
	  try
	  {
	   // for option
       ss.selected = true;
	   // for checkbox and radio
       ss.checked = true;
	   }
	  catch (e)
	   {
	    ;
	   }
	  }
     }
	
	Onepage.op_resizeIframe();

    Onepage.op_hidePayments();
    Onepage.runPay();
    }
	else
	{
	  if (xmlhttp2.readyState==4 && ((xmlhttp2.status>=500)))
	   {
	     // here is the response from request
    var resp = xmlhttp2.responseText;
	// changed in 2.0.227
    if (resp != null) 
    { 
	 if (opc_debug)
	 Onepage.op_log(resp); 
	 resp='<input type="hidden" name="invalid_country" value="invalid_country" />'; 
	 resp += JERROR_AN_ERROR_HAS_OCCURRED+' <a href="#" onclick="return Onepage.refreshShippingRates();" >'+COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING+'</a>'; 
	 
	 
	 Onepage.setShippingHtml(resp); 

    }
	   }
	}// end response is ok
    
   
    // run shipping and payment javascript
	
   if (typeof callAfterRender != 'undefined') 
   if (callAfterRender != null && callAfterRender.length > 0)
   {
   for (var x=0; x<callAfterRender.length; x++)
	   {
	     eval(callAfterRender[x]);
	   }
   }

   if (runjavascript.length>0)
	 {
	   for (var s=0; s<runjavascript.length; s++)
	    {
		  try
		  {
		  eval(runjavascript[s]); 
		  }
		  catch (e)
		  {
		  
		  }
		}
	 }
   var dtx = document.getElementById('form_submitted'); 
   if (dtx != null) dtx.value = '0'; 
   
    return true;
},

setInnerHtml: function(el, html)
{
  if (typeof jQuery != 'undefined')
   {
     var el = jQuery(el); 
	 el.html(html); 
	 if (typeof el.trigger != 'undefined')
	 el.trigger('create'); 
   }
   else
   {
     el.innerHtml = html;
   }
  
},
doublemail_checkMail: function()
{
  msg = document.getElementById('email2_info'); 
 if (!doubleEmailCheck())
     msg.style.display = 'block';
   else 
  msg.style.display = 'none';
  
  return true;
},

setSTAddressHtml: function(html)
{
   var d = document.getElementById('edit_address_list_st_section'); 
   if (d != null)
   Onepage.setInnerHtml(d, html); 
   //d.innerHTML = html; 
   //id33
   var e1 = document.getElementById('ship_to_info_id_bt'); 
   if (e1 != null)
   {
   var bt_id = e1.value; 
   var el = document.getElementById('id'+bt_id); 
   Onepage.changeST(el); 
   }
},
changeSTajax: function(el)
{
  
  if (typeof el.options != 'undefined')
  if (typeof el.selectedIndex != 'undefined')
  if (el.selectedIndex >= 0)
  {
    var stID = el.options[el.selectedIndex].value; 
	var e1 = document.getElementById('ship_to_info_id_bt'); 
    if (e1 != null)
    {
      var bt_id = e1.value; 
	  if (bt_id == stID) 
	    {
		  return Onepage.setSTAddressHtml(''); 
		}
    }
	Onepage.op_runSS(el, false, true, 'getST', false);
  }
},

doubleEmailCheck: function(useAlert)
{
 e1 = document.getElementById('email_field'); 
 e2 = document.getElementById('email2_field');
 msg = document.getElementById('email2_info'); 
 if (e1 !== null && e2 != null)
 {
   if (e1.value != e2.value)
   {
     if (useAlert != null && useAlert == true)
     {
       msg_txt = msg.innerHTML; 
	   
       alert(msg_txt);
     }
     return false;
   }
   else 
 {
  return true;
 }
 } 
  return true;
},
refreshShipping: function(elopc)
{
  Onepage.resetShipping(); 
  Onepage.op_runSS(null, null, true); 
  elopc.href = "#";
  return false;
},

resetShipping: function()
{
   d = document.getElementById('shipping_goes_here'); 
   
   if (d != null)
   {
    if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
    d.style.minHeight = d.style.height;
    //d.innerHTML = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" />'; 
	var invH = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" />'; 
	Onepage.setInnerHtml(d, invH); 
   }
   
   
   d2 = document.getElementById('op_last_field_msg'); 
   if (d2 != null)
   {
    if (op_refresh_html != '')
	Onepage.setInnerHtml(d2, ''); 
    //d2.innerHTML = ''; 	
   }
   return false;
},

setShippingHtml: function(html)
{
  //if (op_shipping_div == null)
  {
   sdiv = null;
   sdiv = document.getElementById('ajaxshipping');
   sib = document.getElementById('shipping_inside_basket');
   sib2 = document.getElementById('shipping_goes_here'); 
   if ((typeof(sib) != 'undefined') && (sib != null))
   {
     sdiv = sib;
   }
   
   if (sib2 != null)
   {
    sdiv = sib2; 
   }
   var op_shipping_div = sdiv;
  }
  
   if (callAfterResponse != null && callAfterResponse.length > 0)
   {
   for (var x=0; x<callAfterResponse.length; x++)
	   {
	     eval(callAfterResponse[x]);
	   }
   }
  
  if (op_shipping_div != null) 
  {
   if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
   op_shipping_div.style.minHeight = op_shipping_div.style.height;
   var savedHeight = op_shipping_div.clientHeight;
  
   //op_shipping_div.innerHTML = html;
   Onepage.setInnerHtml(op_shipping_div, html); 
   if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
   op_shipping_div.style.minHeight= savedHeight+'px'; 
   
  }

  
  
},


showLoader: function(cmd)
{
  if (!op_loader) return;
   if (callBeforeLoader != null && callBeforeLoader.length > 0)
   {
   for (var x=0; x<callBeforeLoader.length; x++)
	   {
	     ret = eval(callBeforeLoader[x]); 
		 if (ret == 2)
		 {
		 return; 
		 }
		 
	   }
   }
 
 if (cmd != null)
 {
   if (cmd == 'runpay')
   {
     pp = document.getElementById('payment_html'); 
	 if (pp != null)
	 {
	   var savedHeight = pp.clientHeight; 
	   if (typeof op_loader_img != 'undefined')
	   {
	   //pp.innerHTML = '<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." /><span class="payment_loader_msg">'+COM_ONEPAGE_PLEASE_WAIT_LOADING+'</span>'; 
	   var html = '<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." /><span class="payment_loader_msg">'+COM_ONEPAGE_PLEASE_WAIT_LOADING+'</span>'; 
	   Onepage.setInnerHtml(pp, html); 
	   if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
	   pp.style.minHeight = savedHeight+'px'; 
	   }
	 }
   }
   if (cmd.indexOf('shipping')>=0)
    {
	    if (op_delay) Onepage.resetShipping(); 
		if (op_loader)
		if (typeof op_loader_img != 'undefined')
		Onepage.setShippingHtml('<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." /><input type="hidden" name="please_wait_fox_ajax" id="please_wait_fox_ajax" value="please_wait_fox_ajax" />'+'<span class="shipping_loader_msg">'+COM_ONEPAGE_PLEASE_WAIT_LOADING+'</span>'); 	
	}
   
 }
 else
 {
 if (op_delay) Onepage.resetShipping(); 
 if (op_loader)
 {
   
   Onepage.setShippingHtml('<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." /><input type="hidden" name="please_wait_fox_ajax" id="please_wait_fox_ajax" value="please_wait_fox_ajax" />'); 
  
   
 }
 }
},

getCouponCode: function ()
{
 var x = document.getElementById('op_coupon_code'); 
 if (typeof x != 'undefined' && (x != null))
 {
   return Onepage.op_escape(x.value);
 }
 var x = document.getElementsByName('coupon_code'); 
 if (x != null)
 for (var j=0; j<x.length; x++)
  {
    if (typeof x[j].value != 'undefined')
	if (x[j].value != '')
	return x[j].value; 
  }
 
 return "";
},

showcheckout: function()
{
     var op_div = document.getElementById("onepage_main_div");
     if ((op_div != null) && (op_min_pov_reached == true))
     {
      if (op_div.style != null)
       if (op_div.style.display == 'none')
       {
         //will show OPC if javascript and ajax test OK
        
        
        op_div.style.display = '';
       }
       
     }
       
        
     
},

setPaymentHtml: function(html, extra)
{
  if (opc_debug)
  Onepage.op_log('setPaymentHtml'); 
  if (html.indexOf('force_show_payments')>=0)
   {
      d = document.getElementById('payment_top_wrapper'); 
	  if ((typeof d != 'undefined') && (d!=null))
	   d.style.display = 'block'; 
   }
  else
  if (html.indexOf('force_hide_payments')>=0)
   {
      d = document.getElementById('payment_top_wrapper'); 
	  if ((typeof d != 'undefined') && (d!=null))
	   d.style.display = 'none'; 
   }
  
  var insertHtml = ''; 
  var appendExtra = true;
  
  if (extra != '')   
  {
  for(var id in extra)
  {
     var myid = parseInt(id); 
	 if (myid != 'NaN')
	 if (typeof extra[id] != 'undefined')
	 if (extra[id] != '')
	   {
	    
	      var d = document.getElementById('extra_payment_'+myid);  
		  // only if it doesn't exists
		  if (!(d!=null))
		  {
		    insertHtml += extra[id];  
		  }
	   }
  }
  
  var d = document.getElementById('payment_extra_outside_basket');
  
  if (d != null)
  {
    
  
    
    //update opc 2.0.226: d.innerHTML = extra; 
	var extras = document.createElement('div');
	//extras.innerHTML = insertHtml; 
    Onepage.setInnerHtml(extras, insertHtml); 
	d.appendChild(extras);
	appendExtra = false; 
	
  }
  }
  
  //if (appendExtra) html = html+insertHtml; 
  
  var d = document.getElementById('payment_html');
  //
  if (d != null)
  if (d.innerHTML.indexOf('canvas')<0)
  {
    // d.innerHTML = html;
    Onepage.setInnerHtml(d, html); 
  }
  return true;
},

validateVat: function(el)
{
  if (typeof checkVATNumber == 'undefined') return; 
  
  var newVATNumber = checkVATNumber (el.value);
  if (newVATNumber) {
	if (el.value != newVATNumber)
    el.value = newVATNumber;
	el.className.split('invalid').join(''); 
  }  
  else 
    {
	 el.className += ' invalid'; 
	}

  return Onepage.op_runSS(this);
},

validateOpcEuVat: function(el)
{
  if (typeof op_loader_img != 'undefined')
  {
	  var loader = '<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." />';
	  Onepage.showVatStatus(loader); 
	   
  }
  Onepage.op_runSS(el, false, true, 'checkvatopc');
  return true; 
},
validateBitVat: function(el)
{
  Onepage.op_runSS(el, false, true, 'checkvat');
},
showVatStatus: function(vat)
{
  
  var d = document.getElementById('vat_info'); 
  
  
  if (d != null)
   {
     //d.innerHTML = vat; 
	 Onepage.setInnerHtml(d, vat); 
     if (vat == '')
	 {
	 d.style.display = 'none'; 
	 }
	 else
	 {
     
	 d.style.display = ''; 
	 
	 
	 
	 }
   }
   
   if (vat != '')
   {
   var d2 = document.getElementById('opc_vat_info_field'); 
   if (typeof d2 != 'undefined')
   if (d2 != null)
     d2.value = vat; 
   }
   
  //Onepage.op_log('VAT:'+vat); 
},
printDebugInfo: function (msgs)
{
 for (var i=0; i<msgs.length; i++)
  {
  
    Onepage.op_log(msgs[i]); 
  }
},

removeCoupon: function()
{
	Onepage.op_runSS(null, false, true, 'removecoupon'); 
	return false; 
},

showNewBasket: function (html)
{
	d = document.getElementById('opc_basket'); 
	if (d!=null)
	{
		//d.innerHTML = html; 
	   Onepage.setInnerHtml(d, html); 
	}
	if (typeof OPCCart != 'undefined')
	if (typeof jQuery != 'undefined')
	{
	var opccarts = jQuery("form.opccartproduct"); 
	if (opccarts != null)
	{
	 OPCCart.product(opccarts);
	 
	}
	}
	   if (typeof jQuery != 'undefined')
   {
     var b = jQuery( "#opc_basket" ); 
      
	 Onepage.jQueryLoader(b, true); 
	}

	Onepage.getTotals(); 
	
	
},

jQueryLoader: function(el, hide) {
  if (typeof jQuery == 'undefined') return; 
  if (typeof jQuery.mobile == 'undefined') return; 
  
  if (!hide)
  {
  var $this = jQuery( el ),
  theme = $this.jqmData( "theme" ) || jQuery.mobile.loader.prototype.options.theme,
  msgText = $this.jqmData( "msgtext" ) || jQuery.mobile.loader.prototype.options.text,
  textVisible = $this.jqmData( "textvisible" ) || jQuery.mobile.loader.prototype.options.textVisible,
  textonly = !!$this.jqmData( "textonly" );
  html = $this.jqmData( "html" ) || "";
  
  
  jQuery.mobile.loading( 'show', {
  text: msgText,
  textVisible: textVisible,
  theme: theme,
  textonly: textonly,
  html: html
  });
  }
  else
  {
  jQuery.mobile.loading( "hide" );
  }
},

showCouponCode: function(couponcode, couponpercent)
{
    
	// remove coupon html: 
	var r = '<a href="#" onclick="javascript: return Onepage.removeCoupon()" class="remove_coupon">X</a>';
	var dt = document.getElementById('tt_order_discount_after_txt_basket_code'); 
	if (parseInt(couponpercent) == 0) couponpercent = ''; 
	
	if (dt != null)
	{
		if (couponcode == '')
		{
			//dt.innerHTML = ''; 
			Onepage.setInnerHtml(dt, ''); 
			var d2 = document.getElementById('tt_order_discount_after_div_basket'); 
			if (d2 != null)
			d2.style.display = 'none'; 
		}
		else
		{
		
			//dt.innerHTML = '('+couponcode+' '+couponpercent+' '+r+')'; 
			html = '('+couponcode+' '+couponpercent+' '+r+')'; 
			Onepage.setInnerHtml(dt, html); 
		}
	}
	else
	{
	var d1 = document.getElementById('tt_order_discount_after_txt_basket'); 
	var newd = document.createElement('span');
	newd.id = 'tt_order_discount_after_txt_basket_code'; 
	if (couponcode == '')
	{
	//newd.innerHTML = ''; 
			Onepage.setInnerHtml(newd, ''); 
			var d2 = document.getElementById('tt_order_discount_after_div_basket'); 
			if (d2 != null)
			d2.style.display = 'none'; 

	}
		else
		{
	newd.innerHTML = '('+couponcode+' '+couponpercent+' '+r+')'; 
	
	}
	if (d1 != null)
	d1.appendChild(newd); 
	}
	
	//tt_order_discount_after_txt
	var dt = document.getElementById('tt_order_discount_after_txt_code'); 
	if (dt != null)
	{
		if (couponcode == '')
		{
					var d2 = document.getElementById('tt_order_discount_after_div_basket'); 
			if (d2 != null)
			d2.style.display = 'none'; 

			dt.innerHTML = ''; 
	    }
		else
			dt.innerHTML = '('+couponcode+' '+couponpercent+' '+r+')'; 
	}
	else
	{
	var d1 = document.getElementById('tt_order_discount_after_txt'); 
	if (d1 != null)
	if (d1.innerHTML == '')
		d1.innerHTML = op_coupon_discount_txt; 
	var newd = document.createElement('span');
	newd.id = 'tt_order_discount_after_txt_code'; 
	if (couponcode == '')
	{
				var d2 = document.getElementById('tt_order_discount_after_div_basket'); 
			if (d2 != null)
			d2.style.display = 'none'; 

	newd.innerHTML = ''; 
	}
		else
	newd.innerHTML = ' ('+couponcode+')'; 
	if (d1 != null)
	d1.appendChild(newd); 
	}
	
},

refreshShippingRates: function()
{  
  Onepage.op_runSS(null, false, true); 
  return false; 
 
},

setMsgs: function(msgs)
{
  if (msgs.length > 0)
  {
  
  
  
  for(var i=0; i<msgs.length; i++)
  {
    if (opc_debug)
   Onepage.op_log('OPC Alert: '+msgs[i]);
   //if (!no_alerts) 
   Onepage.opc_error(msgs[i]); 
  }
    if (typeof jQuery != 'undefined')
  if (jQuery != null)
  if (typeof jQuery.scrollTo != 'undefined')
  jQuery.scrollTo( '#opc_error_msgs', 800, {easing:'elasout'} );

  }
  else
  {
    
    var d=document.getElementById('opc_error_msgs');
	if (d != null)
	 {
	 
	  d.innerHTML = '';
	  d.style.display = 'none'; 
	 }
  }
},

opc_error: function(msg)
{
   var d=document.getElementById('opc_error_msgs');
   if (d!=null)
   {
   if (d.innerHTML.toString().indexOf(msg)<0)
    {
	  d.innerHTML += msg+'<br />'; 
	  
	}
  d.style.display = 'block'; 
  }
  
 },

changeTextOnePage3: function(op_textinclship, op_currency, op_ordertotal)
{

 Onepage.op_hidePayments();
 
 // disabled here 17.oct 2011
 // it should not be needed as it is fetched before ajax call
 
 Onepage.changeTextOnePage(op_textinclship, op_currency, op_ordertotal);    
},
getSPaymentElement: function(element)
{
  	if (typeof element != 'undefined')
	if (element != null)
	 {
	    if (typeof element.value != 'undefined')
		if (element.value != null) return element; 
		
		if (element.type=='select-one')
		 return element.options[element.selectedIndex]; 
		
	 }
		  // get active shipping rate
	  var e = document.getElementsByName("virtuemart_paymentmethod_id");
	  
	  //var e = document.getElementsByName("payment_method_id");
	  
	  
	  var svalue = "";
	 
	  if (typeof e.type != 'undefined')
	  if (e.type == 'select-one')
	  {
	   var ind = e.selectedIndex;
	  
	   if (ind<0) ind = 0;
	   var value = e.options[ind];
	   return value;
	  }
	  
	  
	  if (e)
	  if (typeof e.checked != 'undefined')
      if (e.checked)
	  {
	     svalue = e.value;
		 return e; 
	  }
	  
	  if (!svalue)
	  {

	  for (i=0;i<e.length;i++)
	  {
	   if (e[i].type == 'select-one')
	  {
	   if (e[i].options.length <= 0) return; 
	   var ind = e[i].selectedIndex;
	   if (ind<0) ind = 0;
	   var value = e[i].options[ind].value;
	   return e[i].options[ind];
	  }
	  
	   if (e[i].checked==true)
	    {
	     var svalue = e[i].value;
		 return e[i]; 
		}
	  }
	  }
	    //if (svalue) return svalue;
	    
	    // last resort for hidden and not empty values of payment methods:
	   for (i=0;i<e.length;i++)
	   {
	    if (e[i].value != '')
	  {
	    if (e[i].id != null && (e[i].id != 'payment_method_id_coupon'))
	    return e[i];
	  }
	    }
		
		return; 
		

},  
  // returns value of selected payment method      
getValueOfSPaymentMethod: function(element)
	{
	var e = Onepage.getSPaymentElement(element); 
	if (e != null) return e.value; 
	    return "";
	
	    
	},
	
  // returns amount of payment discout withou tax
op_getPaymentDiscount: function ()
	{
	
	 var id = Onepage.getValueOfSPaymentMethod();
	 if ((id) && (id!=""))
	 {
	  if (typeof(pdisc) !== 'undefined')
	  if (pdisc[id]) 
	  { 
            if (typeof(op_payment_discount) !== 'undefined' ) op_payment_discount = pdisc[id];
	    return pdisc[id];
	  }
	 }
	 return 0;
	},

	
	// returns value of selected shipping method
getVShippingRate: function (getfullvalue)
	{
		  // get active shipping rate
	
	  
	  var svalue = "";
	  {
	  e = document.getElementsByName("virtuemart_shipmentmethod_id");
	  if (e != null)
	  {
	  for (i=0;i<e.length;i++)
	  {
	   if (e[i].type == 'select-one')
	   {
		if (e[i].options.length<=0) return ""; 
	    index = e[i].selectedIndex;
	    if (index<0) index = 0;
	    var val = e[i].options[index].value;
		
		if (getfullvalue != null)
		if (getfullvalue == true)
		return val; 
		
		var ee = val.split('|'); 
		if (ee.length>1) return ee[0]; 
		return val; 
	   }
	   else
	   if ((e[i].checked==true) && (e[i].style.display != 'none'))
	     {
	     var val =  e[i].value;
		 
		 if (getfullvalue != null)
		 if (getfullvalue == true)
		 return val; 
		 
		 var ee = val.split('|'); 
		 if (ee.length>1) return ee[0]; 
		 return val; 
		 
		 }
	  }
	  }
	  }
	  
	    if (svalue) 
	    {
	     return svalue;
	    }
	    return "";
	    
	},
	// returns input id of selected shipping method
		//note: for checkout return attribute saved_id
		// rel_id has higher priority then id
		// if above tests fail, return just normal id
		// if nothing found returns value

	
getInputIDShippingRate: function(fromSaved)
	{
	  
	  {
	    e = document.getElementsByName("virtuemart_shipmentmethod_id"); 
		
		 var id = "";
	  for (i=0;i<e.length;i++)
	  {
	  	if (e[i].type == 'select-one')
	   {
		if (e[i].options.length <= 0) return ""; 
	    index = e[i].selectedIndex;
	    if (index<0) index = 0;
		  if (fromSaved != null)
		  if (fromSaved)
		   {
		     var saved_id = Onepage.getAttr(e[i].options[index], 'saved_id'); 
			 if (saved_id != null) 
			 {
				return saved_id; 
			 }
		   }
		  var rel_id = Onepage.getAttr(e[i].options[index], 'rel_id'); 
		  if (rel_id != null) 
		  {
		  
		  return rel_id; 
		  }
		  if (typeof e[i].options[index].id != 'undefined') return e[i].options[index].id; 
	    
	   }
	   else
	   if ((e[i].checked==true) && (e[i].style.display != 'none'))
	   {
	     if (fromSaved != null)
		  if (fromSaved)
		   {
		     var saved_id = Onepage.getAttr(e[i], 'saved_id'); 
			 if (saved_id != null) 
			 {
				return saved_id; 
			 }
		   }
		  var rel_id = Onepage.getAttr(e[i], 'rel_id'); 
		  if (rel_id != null) 
		  {
		  
		  return rel_id; 
		  }
	   
	     // if you marked your shipping radio with multielement="id_of_the_select_drop_down"
		 var multi = e[i].getAttribute('multielement', false); 
		 if (multi != null)
		  {
		    var test = document.getElementById(multi); 
			if (test != null)
			 {
			    if ((test.options != null) && (test.selectedIndex != null))
				 {
				   if (test.options[test.selectedIndex] != null)
				   if (test.options[test.selectedIndex].getAttribute('multi_id') != null)
				    {
					  return test.options[test.selectedIndex].getAttribute('multi_id'); 
					}
				 }
			 }
			 var test2 = document.getElementsByName(multi); 
			 if (test2 != null)
			  {
			    for (var i = 0; i<test2.length; i++ )
				 {
				   if (test2[i].checked != null)
				   if (test2[i].checked)
				   if (test2[i].id != null)
				   {
				   Onepage.op_log('cpsol: '+test2[i].id); 
				   return test2[i].id; 
				   }
				 }
			  }
			 
		  }
	   
	     if (e[i].id != null)
	     return e[i].id;
	     else return e[i].value;
	   }
	   else
	   if (e[i].type=="hidden")
	   {
	    if ((e[i].value.indexOf('free_shipping')>=0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon')<0))) return e[i].id;
	    if ((e[i].value.indexOf('choose_shipping')>=0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon')<0))) 
	     {
	      return e[i].id;
	     }
	   }
	  }
	  
	  return "";
	   
	  }
	    
	},

	
formatCurrency: function(total)
  {
  
   if ((total == 0) || (isNaN(parseFloat(total)))) total = '0.00';
   var arr = op_vendor_style.split('|');
   
   if (arr.length > 6)
   {
     var sep = arr[3];
     var tsep = arr[4];
     var dec = arr[2];
     var stylep = arr[5];
     // 0 = '00Symb';
     // 1 = '00 Symb'
     // 2 = 'Symb00'
     // 3 = 'Symb 00';
     var stylen = arr[6];
     // 0 = (Symb00)
     // 1 = -Symb00
     // 2 = Symb-00
     // 3 = Symb00-
     // 4 = (00Symb)
     // 5 = -00Symb
     // 6 = 00-Symb
     // 7 = 00Symb-
     // 8 = -00 Symb
     // 9 = -Symb 00
     // 10 = 00 Symb-
     // 11 = Symb 00-
     // 12 = Symb -00
     // 13 = 00- Symb
     // 14 = (Symb 00)
     // 15 = (00 Symb)
     
	 // arr[8] = positive
	 // arr[9] = negative
	 
     // format the number:
     //total = parseFloat(total.toString()).toFixed(dec);
     //totalstr = '';
     //mTotal = total;
     
	 // ok, in vm2 we've got: 
	 // arr[8] = positive
	 // arr[9] = negative
	 if (arr[8] != null)
	 {
     stylepvm2 = arr[8]; 
	 stylenvm2 = arr[9]; 
	 }
	 else 
	 {
     stylepvm2 = null;
	 stylenvm2 = null; 
	 }
	 return Onepage.FormatNum2Currency(total, sep, tsep, stylep, stylen, op_currency, dec, stylepvm2, stylenvm2);
     
   }
   else
   {
    var dec = 2;
    if ((op_no_decimals != null) && (op_no_decimals == true)) dec = 0;
    if ((op_curr_after != null) && (op_curr_after == true))
    {
     total = parseFloat(total.toString()).toFixed(dec)+' '+op_currency;
    }
    else
     total = op_currency+' '+parseFloat(total.toString()).toFixed(dec);
    return total; 
   }
   
    

  },
  
  
op_validateCountryOp2: function(b1 ,b2, elopc)
{
 Onepage.changeStateList(elopc);
 if (typeof opc_bit_check_vatid != 'undefined')
  {
    var el = document.getElementById(bit_euvatid_field_name+'_field'); 
	if (el != null) 
	if (el.value != '')
	opc_bit_check_vatid(el); 
  }
 Onepage.validateCountryOp(false, b2, elopc);
 return "";
},

// aboo is whether to alert user
	 // runCh is boolean whether to change stateList
validateCountryOp: function(runCh, aboo, elopc)
	 {	  
	  Onepage.op_runSS(elopc);
	 },

changeStateList: function(elopc)
{
 
 var st = false;
 if (elopc.id != null)
 {
 if (elopc.id.toString().indexOf('shipto_')>-1)
 {
   st = true; 
 }
 }
 else return;
 
 if (elopc.selectedIndex != null)
 {
 }
 else 
 {
 //alert('err'); 
 return;
 }
 
 if (elopc.options != null)
 var value = elopc.options[elopc.selectedIndex].value; 
 else
 if (elopc.value != null)
 var value = elopc.value; 
 else
 return; 
 
 var statefor = 'state_for_'+value; 
  
 
 if (!st)
 {
   
   var st2 = document.getElementById('virtuemart_state_id'); 
   if (st2 != null)
    {
	  if (op_lastcountry != value)
	  Onepage.op_replace_select('virtuemart_state_id', statefor); 
	  op_lastcountry = value; 
	  //st2.options = html;
	 // alert(st2.innerHTML);
	}
 }
 else
 {
  
   var st3 = document.getElementById('shipto_virtuemart_state_id'); 
   if (st3 != null)
    {
	    if (op_lastcountryst != value)
		Onepage.op_replace_select('shipto_virtuemart_state_id', statefor); 
		op_lastcountryst = value; 
	}
 
 }
 
 
 

 
},


  
reverseString: function(str)
  {
    var splittext = str.toString().split("");
    var revertext = splittext.reverse();
    return revertext.join("");
  },
  
op_escape: function(str)
  {
   if ((typeof(str) != 'undefined') && (str != null))
   {
     var x = str.split("&").join("%26");
     return x;
   }
   else 
   return "";
  },
  /*
	Author: Robert Hashemian
	http://www.hashemian.com/
	Modified by stAn www.rupostel.com - Feb 2011
	You can use this code in any manner so long as the author's
	name, Web address and this disclaimer is kept intact.
	********************************************************
  */
FormatNum2Currency: function(num, decpoint, sep, stylep, stylen, curr, decnum, stylepvm2, stylenvm2) {
  // check for missing parameters and use defaults if so
  
  // vm2:
  //'1|€|2|,||3|8|8|{number} {symbol}|{sign}{number} {symbol}'
  var isPos = true;
  if (parseFloat(num)>=0) isPos = true;
  else isPos = false;
	
  num = Math.round(num*Math.pow(10,decnum))/Math.pow(10,decnum);
  if (isPos == false) num = num * (-1);
   num = num.toString();
   
   a = num.split('.');
   x = a[0];
   if (a.length > 1)
   y = a[1];
   else y = '00';
   var z = "";

  
  if ((typeof(x) != "undefined") && (x != null)) {
    // reverse the digits. regexp works from left to right.
    z = Onepage.reverseString(x);
    
    // add seperators. but undo the trailing one, if there
    z = z.replace(/(\d{3})/g, "$1" + sep);
    if (z.slice(-sep.length) == sep)
      z = z.slice(0, -sep.length);
    
    x = Onepage.reverseString(z);
    // add the fraction back in, if it was there
    if (decnum > 0)
    {
     if (typeof(y) != "undefined" && y.length > 0)
     {
       if (y.length > decnum) y = y.toString().substr(0, decnum);
       if (y.length < decnum)
       {
        var missing = decnum - y.length;
        for (var u=0; u<missing; u++)
        {
         y += '0';
        } 
       }
       x += decpoint + y;
     }
    }
  }
  
  if (isPos == true)
  {
    // 0 = '00Symb';
     // 1 = '00 Symb'
     // 2 = 'Symb00'
     // 3 = 'Symb 00';
	 if (stylepvm2 != null)
	 {
	   if (curr.length>0)
	   stylepvm2 = stylepvm2.split('{number}').join(x).split('{symbol}').join(curr);
	   else
	   stylepvm2 = stylepvm2.split('{number}').join(x); 
	   
	   if (stylepvm2.indexOf('sign') >=0)
	   stylepvm2 = stylepvm2.split('{sign}').join('+');
	   
	   x = stylepvm2; 
	 }
	 else
     switch(parseInt(stylep))
     {
      case 0: 
      	x = x+curr;
      	break;
      case 1:
      	x = x+' '+curr;
      	break;
      case 2:
      	x = curr+x;
      	break;
      default:
      	x = curr+' '+x;
     }
  }
  else
  {
   if (stylenvm2 != null)
	 {
	   if (curr.length>0)
	   stylenvm2 = stylenvm2.split('{number}').join(x).split('{symbol}').join(curr);
	   else
	   stylenvm2 = stylenvm2.split('{number}').join(x); 
	   
	   if (stylenvm2.indexOf('sign') >=0)
	   stylenvm2 = stylenvm2.split('{sign}').join('-');
	   
	   x = stylenvm2; 
	 }
   else
   switch (parseInt(stylen))
   {
     // 0 = (Symb00)
     // 1 = -Symb00
     // 2 = Symb-00
     // 3 = Symb00-
     // 4 = (00Symb)
     // 5 = -00Symb
     // 6 = 00-Symb
     // 7 = 00Symb-
     // 8 = -00 Symb
     // 9 = -Symb 00
     // 10 = 00 Symb-
     // 11 = Symb 00-
     // 12 = Symb -00
     // 13 = 00- Symb
     // 14 = (Symb 00)
     // 15 = (00 Symb)
     case 0:
     	x = '('+curr+x+')';
     	break;
     case 1:
     	x = '-'+curr+x;
     	break;
     case 2:
     	x = curr+'-'+x;
     	break;
     case 3:
     	x = curr+x+'-';
     	break;
     case 4:
     	x = '('+x+curr+')';
     	break;
     case 5:
     	x = '-'+x+curr;
     	break;
     case 6:
     	x = x+'-'+curr;
     	break;
     case 7:
     	x = x+curr+'-';
     	break;
     case 8:
     	x = '-'+x+' '+curr;
     	break;
     case 9:
     	x = '-'+curr+' '+x;
     	break;
     case 10:
      	x = x+' '+curr+'-';
      	break;
     case 11:
      	x = curr+x+' -';
      	break;
      case 12:
      	x = curr+' -'+x;
      	break;
      case 13:
      	x = x+'- '+curr;
      	break;
      case 14:
      	x = '('+curr+' '+x+')';
      	break;
      case 15:
      	x = '('+x+' '+curr+')';
      	break;
      default:
      	x = '-'+x+' '+curr;
      }
      	
  }
  
  return x;
},

  
  
  /*
  * This function disables payment methods for a selected shipping method
  * or implicitly disabled payments   
  * THIS FUNCTION MIGHT GET RENAMED TO: op_onShippingChanged
  */
op_hidePayments: function()
  {
   // check if shipping had changed:
   var op_saved_shipping2 = Onepage.getInputIDShippingRate();
   
   if ((op_saved_shipping2 == '') && (!(op_saved_shipping != null))) return; 
   
   if ((typeof(op_saved_shipping) == 'undefined' || op_saved_shipping == null) || (op_saved_shipping != op_saved_shipping2) || (op_firstrun))
   {
    op_firstrun = false;
	
   // check if the feature is enabled
   // if (op_payment_disabling_disabled) return "";
   // event handler for AfterShippingSelect
   if (callAfterShippingSelect != null && callAfterShippingSelect.length > 0)
   {
   for (var x=0; x<callAfterShippingSelect.length; x++)
	   {
	     eval(callAfterShippingSelect[x]);
	   }
   }
   }
  },
dynamicLines: function(startid)
{
  if (opc_dynamic_lines == false) return;
  /* remove last dynamic lines
  */
  if (typeof Onepage.last_dymamic != 'undefined')
  for (var i = 0; i<Onepage.last_dymamic.length; i++)
   {
     if (Onepage.last_dymamic[i]!=null)
	 {
     var d = document.getElementById(Onepage.last_dymamic[i]); 
	 if (d != null)
	 if (typeof d.parentNode != 'undefined')
	 if (d.parentNode != null)
	   {
	     d.parentNode.removeChild(d); 
		 Onepage.last_dymamic[i] = null; 
	   }
	 }
   }
  
  var d = document.getElementsByName(startid+'_dynamic'); 
  var d2 = document.getElementById('tt_genericwrapper_basket'); 
  if (d2 != null)
  if (d != null)
  for (var i=0; i<d.length; i++)
   {
      var d3 = d2.cloneNode(true); 
	  var value = d[i].value; 
	  var id = d[i].getAttribute('rel', 0); 
	  var name = d[i].getAttribute('stringname', ''); 
	 
	  if ((id == 0) || (value == '')) continue; 
	  value = Onepage.formatCurrency(parseFloat(value));
	  //d3.innerHTML = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value); 
	  var html = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value); 
	  
	  d3.style.display = ''; 
	  var new_id = d[i].id+'_basket';  
	  d3.id = new_id; 
	  // double line test: 
	  var dd = document.getElementById(new_id); 
	  if (dd != null) 
	  {
	  
	  Onepage.setInnerHtml(d3, html); 
	  
	  if (typeof d2.parentNode != 'undefined')
	  if (d2.parentNode != null)
	  {
	   d2.parentNode.insertBefore(d3, d2.nextSibling);
	   Onepage.last_dymamic.push(d3.id); 
	  }
	  }
	  
	  
   }
   //tt_genericwrapper_bottom
   
  
  var d2 = document.getElementById('tt_genericwrapper_bottom'); 
  if (d != null)
  if (d2 != null)
  for (var i=0; i<d.length; i++)
   {
      var d3 = d2.cloneNode(true); 
	  var value = d[i].value; 
	  var id = d[i].getAttribute('rel', 0); 
	  var name = d[i].getAttribute('stringname', ''); 
	 
	  if ((id == 0) || (value == '')) continue; 
	  value = Onepage.formatCurrency(parseFloat(value));
	  //d3.innerHTML = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value); 
	  var html = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value); 
	  Onepage.setInnerHtml(d3, html); 
	  d3.style.display = ''; 
	  var new_id = d[i].id+'_bottom';  
	  d3.id = new_id; 
	  
	  var dd = document.getElementById(new_id); 
	  if (dd != null) 
	  {
	  
	  if (typeof d2.parentNode != 'undefined')
	  if (d2.parentNode != null)
	  {
	   d2.parentNode.insertBefore(d3, d2.nextSibling);
	   Onepage.last_dymamic.push(d3.id); 
	  }
	  }
	  
	  
   }
   
   
},  
   /*
   * This function fetches totals array from ajax data
   */
getTotals: function(shipping_id, payment_id)
   {

	if (typeof shipping_id == 'undefined')
	var shipping_id = Onepage.getInputIDShippingRate();
	
	if (typeof payment_id == 'undefined')
	var payment_id = Onepage.getPaymentId();
   
    if (shipping_id == "") shipping_id = 'shipment_id_0';
    if (payment_id == "") return "";
	var po = document.getElementById('payment_id_override_'+payment_id); 
	if (po != null)
	  {
	    var e = Onepage.getSPaymentElement(); 
	    if (e != null)
		if (e.id != null)
		payment_id = e.id;  
		
		
	  }
    var x = document.getElementById(shipping_id+'_'+payment_id+'_subtotal');
    if (x == null) {
    return "";
    }
	
    if (typeof opc_dynamic_lines != 'undefined')
	if (opc_dynamic_lines != null)
	 {
	   Onepage.dynamicLines(shipping_id+'_'+payment_id); 
	 }
	 
    var subtotal = x.value;

	d = document.getElementById('opc_coupon_code_returned'); 
	
	if (d != null)
	var coupon_code_returned = d.value; 
	else 
	var coupon_code_returned = ''; 

	
	
    x = document.getElementById(shipping_id+'_'+payment_id+'_payment_discount');
    var payment_discount = x.value;

    x = document.getElementById(shipping_id+'_'+payment_id+'_coupon_discount');
    var coupon_discount = x.value;
	
	
	
	
    x = document.getElementById(shipping_id+'_'+payment_id+'_order_shipping');
    var order_shipping = x.value;
    
    x = document.getElementById(shipping_id+'_'+payment_id+'_order_shipping_tax');
    var order_shipping_tax = x.value;
    
    x = document.getElementById(shipping_id+'_'+payment_id+'_order_total');
    var order_total = x.value;
    
	x = document.getElementById(shipping_id+'_'+payment_id+'_coupon_discount2');
    var coupon_discount2 = x.value;
	
    x = document.getElementsByName(shipping_id+'_'+payment_id+'_tax');
	xall = document.getElementsByName(shipping_id+'_'+payment_id+'_tax_all');
	
    xname = document.getElementsByName(shipping_id+'_'+payment_id+'_taxname');
	
    // check if we have shipping inside basket
      var sib = document.getElementById('shipping_inside_basket_cost');
	  
  
      if ((sib != null))
      {
	   	  if (Onepage.isNotAShippingMethod()) 
	  {
	   
	   sib.innerHTML = op_lang_select;
	  }
	  else
	  {
	    var scost = parseFloat(order_shipping); 
        if (op_show_prices_including_tax == '1')
        var total_s = Onepage.formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
        else
        var total_s = Onepage.formatCurrency(scost);
        if ((scost == 0) && (use_free_text))
		sib.innerHTML = opc_free_text;
		else
        sib.innerHTML = total_s;
	    if (opc_debug)	
		Onepage.op_log(total_s); 
	    }
      }
    
      
      if (true)
      {
         if (op_fix_payment_vat == true)
         {
          // tax rate calculaction
          if (isNaN(parseFloat(op_detected_tax_rate)) || parseFloat(op_detected_tax_rate)==0.00) 
          taxr = parseFloat(op_custom_tax_rate);
          else
          taxr = parseFloat(op_detected_tax_rate);
          
          //else taxr = parseFloat(op_custom_tax_rate);
          
          p_disc = (-1) * (1 + taxr) * parseFloat(payment_discount);
          
         }
         else
         {
        	p_disc = (-1) * parseFloat(payment_discount);
         }
        total_s = Onepage.formatCurrency(parseFloat(p_disc));
        
        sib = document.getElementById('payment_inside_basket_cost');
        if (sib != null)
        sib.innerHTML = total_s;
      }


    
    op_tax_total = parseFloat(0.0);
    //tax_data = new Array(x.length);
	tax_data = new Array(1);
	tax_data[0] = "|0"; 
	
	tax_name = new Array(); 
	
     //opc update 2.0.108: disable for
	// opc update 2.0.127: if (x.length > 0) , reenabled for
	if (!opc_dynamic_lines)
	for (i=0; i<x.length; i++ )
    {
	  //i = 0; 
     //var y = x.value;
     //var arr = y.split("|");
     var arr = x[i].value.split("|");
     var tax = 0;
     if (arr.length == 2) tax = arr[1];
     else tax = x[i].value;
     
     tax_data[i] = x[i].value;
     if (!isNaN(parseFloat(tax)))
     op_tax_total += parseFloat(tax);
     
	 if (typeof xname[i] != 'undefined' && (xname[i] != null))
	 if (xname[i].value != null)
	 { 
	 tax_name[i] = xname[i].value; 
	 }
	 else
	 tax_name[i] = ''; 
	 
    }
	
	if (x.length == 0)
	 {
	    x2 = document.getElementById(shipping_id+'_'+payment_id+'_tax_all'); 
		if (x2 != null)
		 {
		   tax_data = new Array(1);
		   tax_data[0] = x2.value; 
		   tax_name[0] = op_shipping_tax_txt;
		   
		  
		   
		 }
		 else
		 tax_name[0] = op_shipping_tax_txt;
	 }
	
    var taxx;
	
	// init taxes first by hiding them
    for (i=x.length; i<=4; i++)
    {
     taxx = document.getElementById('tt_tax_total_'+i+'_div');
	  taxx2 = document.getElementById('tt_tax_total_'+i+'_div_basket');
     if (typeof taxx !='undefined' && (taxx != null))
     {
      taxx.style.display = 'none';
     }
	 if (typeof taxx2 !='undefined' && (taxx2 != null))
     {
      taxx2.style.display = 'none';
	 
     }
    }
	
	if (opc_dynamic_lines)
	{
	 tax_data = new Array(1);
	 tax_data[0] = "|0"; 
	}
    // formatting totals here:
	/*
    var t = document.getElementById('totalam');
	if (!(t != null)) {
	  insertHtml = '<div id="totalam"><div id="tt_order_subtotal_div"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_order_payment_discount_before_div"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div><div id="tt_order_discount_before_div"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_shipping_rate_div"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_shipping_tax_div"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_0_div"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_1_div"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_2_div"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_3_div"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_4_div"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_order_payment_discount_after_div"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_order_discount_after_div"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_total_div"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div></div>';    
	  f = document.getElementById('vmMainPageOPC'); 
	  dv = document.createElement("div");
	  dv.style.display = 'none'; 
	  dv.innerHTML = insertHtml; 
	  t = dv; 
	  f.appendChild(dv); 
	}
	*/
    var t1 = document.getElementById('tt_order_subtotal_txt'); 
	
    // for google ecommerce: op_total_total, op_tax_total, op_ship_total
    // with VAT
    op_total_total = order_total;
    // only VAT
    op_tax_total += parseFloat(order_shipping_tax);
    // without VAT
    op_ship_total = order_shipping;
    
    
    if (never_show_total == true)
    {
     t.style.display = 'none';
    } 

    
    
    
    if ((op_show_only_total != null) && (op_show_only_total == true))
    {
    	 stru = document.getElementById('tt_total_txt')
		 if (stru != null)
		 str = srtu.innerHTML;
		 else str = ''; 
    	 if (str == '')
		 {
         d1 = document.getElementById('tt_total_txt'); 
		 if (d1 != null)
		 d1.innerHTML = op_textinclship;
		 }
         if ((op_custom_tax_rate != null) && (op_add_tax != null) && (op_custom_tax_rate != '') && (op_add_tax == true))
         {
          d1 = document.getElementById('tt_total'); 
		  if (d1 != null)
		  d1.innerHTML = Onepage.formatCurrency((1+parseFloat(op_custom_tax_rate))*parseFloat(order_total));
         }
         else
		 {
    	 d1 = document.getElementById('tt_total'); 
		 if (d1 != null)
		 d1.innerHTML = Onepage.formatCurrency(order_total);
		 }
		 
	     d1 = document.getElementById('tt_order_payment_discount_before_div'); 
		 if (d1 != null)
		 d1.style.display = "none";
		 d1 = document.getElementById('tt_order_discount_before_div'); 
		 if (d1 != null) d1.style.display = "none";	
		 d1 = document.getElementById('tt_order_subtotal_div'); 
		 if (d1 != null) d1.style.display = 'none';
		 d1 = document.getElementById('tt_shipping_rate_div'); 
		 if (d1 != null) d1.style.display = 'none';
		 d1 = document.getElementById('tt_shipping_tax_div'); 
		 if (d1 != null) d1.style.display = 'none';
		 return true;
    }
    
   	  // add tax to payment discount
	  /*
	  if (false)
   	  if (op_fix_payment_vat == true)
   	  if ((op_no_taxes == true) || (op_no_taxes_show == true) || (op_show_andrea_view == true) || ((payment_discount_before == '1') && (op_show_prices_including_tax == '1')))
      {
          if (isNaN(parseFloat(op_detected_tax_rate)) || parseFloat(op_detected_tax_rate)==0.00) 
          taxr = parseFloat(op_custom_tax_rate);
          else
          taxr = parseFloat(op_detected_tax_rate);

          p_disc =  (1 + taxr) * parseFloat(payment_discount);
          payment_discount = parseFloat(p_disc);
      }
	  */
   
    
    var locp = 'after';
    if (payment_discount_before == '1')
    {
     locp = 'before';
    }
    else locp = 'after';
    
    
     if (payment_discount > 0)
     {
     
         stru = document.getElementById('tt_order_payment_discount_'+locp+'_txt'); 
		 if (stru!=null)
		 str = stru.innerHTML;
		 else str = ''; 
		 
    	 if (str == '' || str == op_payment_fee_txt)
		 {
        d1 = document.getElementById('tt_order_payment_discount_'+locp+'_txt'); 
	    if (d1 != null) d1.innerHTML = op_payment_discount_txt;
	    }
      d1 = document.getElementById('tt_order_payment_discount_'+locp); 
	  if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1)*payment_discount);
      if (op_override_basket)
      {
       e1 = document.getElementById('tt_order_payment_discount_'+locp+'_basket');
       if (e1 != null)
       e1.innerHTML = Onepage.formatCurrency((-1)*parseFloat(payment_discount));
       e2 = document.getElementById('tt_order_payment_discount_'+locp+'_txt_basket');
       if (e2 != null)
       e2.innerHTML = op_payment_discount_txt;
       if (!op_payment_inside_basket)
       {
       e3 = document.getElementById('tt_order_payment_discount_'+locp+'_div_basket');
       if (e3 != null)
       e3.style.display = "";
       }
      }
      d1 = document.getElementById('tt_order_payment_discount_'+locp+'_div'); 
	  if (d1 != null) d1.style.display = "block";
     }
     else
     if (payment_discount < 0)
     {
      stru = document.getElementById('tt_order_payment_discount_'+locp+'_txt');
	  if (stru!=null)
	  str = stru.innerHTML;
	  else str = ''; 
      if (str == '' || (str == op_payment_discount_txt))
	  {
      d1 = document.getElementById('tt_order_payment_discount_'+locp+'_txt'); 
	  if (d1 != null)
	  d1.innerHTML = op_payment_fee_txt;
	  }
      d1 = document.getElementById('tt_order_payment_discount_'+locp); 
	  if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1)*parseFloat(payment_discount));
      d1 = document.getElementById('tt_order_payment_discount_'+locp+'_div'); 
	  if (d1!=null) d1.style.display = "block";
      if (op_override_basket)
      {
       d1 = document.getElementById('tt_order_payment_discount_'+locp+'_basket'); 
	   if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1)*parseFloat(payment_discount));
       d1 = document.getElementById('tt_order_payment_discount_'+locp+'_txt_basket'); 
	   if (d1 != null) d1.innerHTML = op_payment_fee_txt;
       if (!op_payment_inside_basket)
	   {
       d1 = document.getElementById('tt_order_payment_discount_'+locp+'_div_basket'); 
	   if (d1 != null) d1.style.display = "";
	   }
      }
      
      
     }
     else 
     {
      stru = document.getElementById('tt_order_payment_discount_'+locp+'_txt'); 
	  if (stru != null) str = stru.innerHTML;
	  else str = ''; 
	  
      if (str == '')
	  {
      d1 = document.getElementById('tt_order_payment_discount_'+locp+'_txt'); 
	  if (d1 != null) d1.innerHTML = "";
	  }
      d1 = document.getElementById('tt_order_payment_discount_'+locp); 
	  if (d1 != null) d1.innerHTML = "";
      d1 = document.getElementById('tt_order_payment_discount_'+locp+'_div'); 
	  if (d1 != null) d1.style.display = "none";
      if (op_override_basket)
      {
       d1 = document.getElementById('tt_order_payment_discount_'+locp+'_basket'); 
	   if (d1 != null) d1.innerHTML = ""
       d1 = document.getElementById('tt_order_payment_discount_'+locp+'_txt_basket'); 
	   if (d1 != null) d1.innerHTML = "";
       d1 = document.getElementById('tt_order_payment_discount_'+locp+'_div_basket'); 
	   if (d1 != null) d1.style.display = "none";
      }
     
     }
    
	  
     //odl: if (Math.abs(coupon_discount) > 0)
	
	
	 locp = 'after'; 
	 //(coupon_code_returned != '') ||
	 if ( (Math.abs(coupon_discount) > 0) || (coupon_code_returned!=''))
     {
	  if (coupon_discount < 0) coupon_discount = parseFloat(coupon_discount) * (-1);
      stru = document.getElementById('tt_order_discount_'+locp+'_txt'); 
	  if (stru != null) str = stru.innerHTML;
	  else str = ''; 
      if (str == '')
	  {
      d1 = document.getElementById('tt_order_discount_'+locp+'_txt'); 
	  if (d1 != null) d1.innerHTML = op_coupon_discount_txt;
	  }
      d1 = document.getElementById('tt_order_discount_'+locp); 
	  if (d1!= null) d1.innerHTML = Onepage.formatCurrency((-1)*parseFloat(coupon_discount));
      d1 = document.getElementById('tt_order_discount_'+locp+'_div'); 
	  if (d1 != null) d1.style.display = "block";
	  if (op_override_basket)
	   {
        d1 = document.getElementById('tt_order_discount_'+locp+'_basket'); 
		if (d1 != null) 
		{
		d1.innerHTML = Onepage.formatCurrency((-1)*parseFloat(coupon_discount));
		d1.style.display = 'block'; 
		}
		//tt_order_discount_after_txt_basket
        d1 = document.getElementById('tt_order_discount_'+locp+'_div_basket'); 
		if (d1 != null) d1.style.visibility = 'visible';
        d1 = document.getElementById('tt_order_discount_'+locp+'_div_basket'); 
		if (d1 != null) 
		{
				d1.style.display = '';
				/*
		if (d1.nodeName.toLowerCase() != 'tr')
		d1.style.display = 'block';
		else d1.style.display = 'table-row'; 
		  */
		}
		//tt_order_discount_after_basket
		
	   }
     }
     else
     {
		 
      stru = document.getElementById('tt_order_discount_'+locp+'_txt'); 
	  if (stru != null) 
	  str = stru.innerHTML;
	  else str = ''; 
	  
      if (str == '')
	  {
      d1 = document.getElementById('tt_order_discount_'+locp+'_txt'); 
	  if (d1 != null) d1.innerHTML = "";
	  }
	  
      d1 = document.getElementById('tt_order_discount_'+locp+''); 
	  if (d1 != null) d1.innerHTML = "";
	  
      d1 = document.getElementById('tt_order_discount_'+locp+'_div'); 
	  if (d1 != null) d1.style.display = "none";
	   if (op_override_basket)
	   {
	    e3 = document.getElementById('tt_order_discount_'+locp+'_div_basket');
	    if (e3 != null)
		{
		
	    e3.style.display = "none";
		}
	   }
     }
	 
	 locp = 'before'; 
	 if (Math.abs(coupon_discount2) > 0)
     {
	  
	  //if (coupon_discount2 < 0) coupon_discount2 = parseFloat(coupon_discount2) * (-1);
      
      d1 = document.getElementById('tt_order_discount_'+locp+'_txt'); 
	  if (d1 != null) d1.innerHTML = op_other_discount_txt;
	  
      d1 = document.getElementById('tt_order_discount_'+locp); 
	  if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(coupon_discount2));
      d1 = document.getElementById('tt_order_discount_'+locp+'_div'); 
	  if (d1 != null) d1.style.display = "block";
	  
	  if (op_override_basket)
	   {
        d1 = document.getElementById('tt_order_discount_'+locp+'_basket'); 
		if (d1 != null) 
		{
		d1.innerHTML = Onepage.formatCurrency(parseFloat(coupon_discount));
		d1.style.display = 'block'; 
		}
		//tt_order_discount_after_txt_basket
        d1 = document.getElementById('tt_order_discount_'+locp+'_div_basket'); 
		if (d1 != null) d1.style.visibility = 'visible';
        d1 = document.getElementById('tt_order_discount_'+locp+'_div_basket'); 
		if (d1 != null) 
		{
		if (d1 != null) 
		{
		d1.style.display = '';
		/*
		if (d1.nodeName.toLowerCase() != 'tr')
		d1.style.display = 'block';
		else d1.style.display = 'table-row'; 
		*/
		}
		//d1.style.display = 'block';
		}
		//tt_order_discount_after_basket
		d1 = document.getElementById('tt_order_discount_'+locp+'_basket'); 
	  if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(coupon_discount2));
		
	   }
	  
     }
     else
     {
     
      
      d1 = document.getElementById('tt_order_discount_'+locp+'_txt'); 
	  if (d1 != null) d1.innerHTML = "";
      d1 = document.getElementById('tt_order_discount_'+locp+''); 
	  if (d1 != null) d1.innerHTML = "";
      d1 = document.getElementById('tt_order_discount_'+locp+'_div'); 
	  if (d1 != null) d1.style.display = "none";
	   if (op_override_basket)
	   {
	    e3 = document.getElementById('tt_order_discount_'+locp+'_div_basket');
	    if (e3 != null)
	    e3.style.display = "none";
	   }
     }
	 locp = 'after'; 
	
    if ((op_no_taxes != true) && (op_no_taxes_show != true))
    {
    stru = document.getElementById('tt_order_subtotal_txt'); 
	if (stru != null) str = stru.innerHTML;
	else str = ''; 
    if (str == '')
	{
     d1 = document.getElementById('tt_order_subtotal_txt'); 
	 if (d1 != null) d1.innerHTML = op_subtotal_txt;
    }
    if (op_show_andrea_view == true)
	{
    d1 = document.getElementById('tt_order_subtotal'); 
	if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(subtotal)+parseFloat(op_basket_subtotal_items_tax_only));
	}
    else
	{
    d1 = document.getElementById('tt_order_subtotal'); 
	if (d1 != null) d1.innerHTML = Onepage.formatCurrency(subtotal);
	}
    d1 = document.getElementById('tt_order_subtotal_div'); 
	if (d1 != null) d1.style.display = 'block';
    if (op_override_basket)
    {
     stru = document.getElementById('tt_order_subtotal_txt_basket'); 
	 if (stru != null) str = stru.innerHTML;
	 else str = ''; 
     if (str == '')
	 {
	 
     d1 = document.getElementById('tt_order_subtotal_txt_basket'); 
	 if (d1 != null) d1.innerHTML = op_subtotal_txt;
	 }
     if (op_show_andrea_view == true)
	 {
     d1 = document.getElementById('tt_order_subtotal_basket'); 
	 if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(subtotal)+parseFloat(op_basket_subtotal_items_tax_only));
	 }
     else
	 {
     d1 = document.getElementById('tt_order_subtotal_basket'); 
	 if (d1 != null) d1.innerHTML = Onepage.formatCurrency(subtotal);
	 }
     d1 = document.getElementById('tt_order_subtotal_div_basket'); 
	 if (d1 != null) d1.style.display = '';
     
    }
    }
    else
    {
     d1 = document.getElementById('tt_order_subtotal_div'); 
	 if (d1 != null) d1.style.display = 'none';
         if (op_override_basket)
    {
     d1 = document.getElementById('tt_order_subtotal_div_basket'); 
	 if (d1 != null) d1.style.display = 'none';
     
    }

    }
    
    if (op_noshipping == false)
    {
     stru = document.getElementById('tt_shipping_rate_txt'); 
	 
	 if (stru!=null) str = stru.innerHTML;
	 else str = ''; 
	 
     if (str == '')
	 {
	 
     d1 = document.getElementById('tt_shipping_rate_txt'); 
	 if (d1 != null) d1.innerHTML = op_shipping_txt;
     }
    
    if (Onepage.isNotAShippingMethod()) 
     { 
     if (op_override_basket)
	 {
      d1 =  document.getElementById('tt_shipping_rate_basket'); 
	  if (d1 != null) d1.innerHTML = op_lang_select;
	 }
     d1 = document.getElementById('tt_shipping_rate'); 
	 if (d1 != null) d1.innerHTML = op_lang_select;
     
     
     }
    else
    if (op_no_taxes_show != true && op_show_andrea_view != true)
    {
     if (op_show_prices_including_tax == '1')
	 {
      d1 = document.getElementById('tt_shipping_rate'); 
	  if (d1 != null) 
	  {
	  		 var opcs = parseFloat(order_shipping)+parseFloat(order_shipping_tax); 
			 if ((opcs == 0) && (use_free_text))
			 d1.innerHTML = opc_free_text;
			 else
		     d1.innerHTML = Onepage.formatCurrency(opcs);

	 // d1.innerHTML = Onepage.formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
	  }
	 }
     else
	 {
     d1 = document.getElementById('tt_shipping_rate'); 
	 if (d1 != null) 
	 {
	 	     var opcs = parseFloat(order_shipping); 
			 if ((opcs == 0) && (use_free_text))
			 d1.innerHTML = opc_free_text;
			 else
		     d1.innerHTML = Onepage.formatCurrency(opcs);

	    
	 }
	 }
     if (op_override_basket)
     {
      if (Onepage.isNotAShippingMethod()) 
	  {
	  d1 = document.getElementById('tt_shipping_rate_basket'); 
	  if (d1 != null) d1.innerHTML = op_lang_select;
	  }
      else
      {
       if (op_show_prices_including_tax == '1')
	   {
        d1 = document.getElementById('tt_shipping_rate_basket'); 
		if (d1 != null) 
		{
		     var opcs = parseFloat(order_shipping)+parseFloat(order_shipping_tax); 
			 if ((opcs == 0) && (use_free_text))
			 d1.innerHTML = opc_free_text;
			 else
		     d1.innerHTML = Onepage.formatCurrency(opcs);
		}
	   }
       else
	   {
       d1 = document.getElementById('tt_shipping_rate_basket'); 
	   if (d1 != null) 
	     {
		     var opcs = parseFloat(order_shipping);
			 if ((opcs == 0) && (use_free_text))
			 d1.innerHTML = opc_free_text;
			 else
		     d1.innerHTML = Onepage.formatCurrency(opcs);
		 
		 }
	   }
      }
     }
    }
    else
    {
     d1 = document.getElementById('tt_shipping_rate'); 
	 if (d1 != null) 
	 {
	         var opcs = parseFloat(order_shipping)+parseFloat(order_shipping_tax);
			 if ((opcs == 0) && (use_free_text))
			 d1.innerHTML = opc_free_text;
			 else
		     d1.innerHTML = Onepage.formatCurrency(opcs);
	 
	 }
     if (op_override_basket)
     {
      d1 = document.getElementById('tt_shipping_rate_basket'); 
	  if (d1 != null) 
	  {
	         var opcs = parseFloat(order_shipping)+parseFloat(order_shipping_tax);
			 if ((opcs == 0) && (use_free_text))
			 d1.innerHTML = opc_free_text;
			 else
		     d1.innerHTML = Onepage.formatCurrency(opcs);
	    
	  }
     }
    }
    d1 = document.getElementById('tt_shipping_rate_div'); 
	if (d1 != null) d1.style.display = 'block';
	
	if (op_override_basket)
	{
	 if (!op_shipping_inside_basket)
	 {
	  d1 = document.getElementById('tt_shipping_rate_div_basket'); 
	  if (d1 != null) d1.style.display = '';
	  
	  
	 }
	 else 
	 {
	 d1 = document.getElementById('tt_shipping_rate_div_basket'); 
	 if (d1 != null) d1.style.display = 'none';
	 }
	}
	
	  
	  
    
     
	
    if ((order_shipping_tax > 0) && (op_sum_tax != true) && (op_no_taxes != true) && (op_no_taxes_show != true) && (op_show_andrea_view!=true) && (op_show_prices_including_tax != '1'))
    {
    stru = document.getElementById('tt_shipping_tax_txt'); 
	if (stru!=null) str = stru.innerHTML;
	else str = ''; 
	
    if (str == '')
	{
	
    d1 = document.getElementById('tt_shipping_tax_txt'); 
	if (d1 != null) d1.innerHTML = op_shipping_tax_txt;
	}
    d1 = document.getElementById('tt_shipping_tax'); 
	if (d1 != null) d1.innerHTML = Onepage.formatCurrency(order_shipping_tax);
	
    d1 = document.getElementById('tt_shipping_tax_div'); 
	if (d1 != null) d1.style.display = "block";
    }
    else
    {
     stru = document.getElementById('tt_shipping_tax_txt'); 
	 if (stru != null) str = stru.innerHTML;
	 else str = ''; 
	 
     if (str == '')
	 {
      d1 = document.getElementById('tt_shipping_tax_txt'); 
	  if (d1 != null) d1.innerHTML = "";
	 }
     d1 = document.getElementById('tt_shipping_tax'); 
	 if (d1 != null) d1.innerHTML = "";
	 
     d1 = document.getElementById('tt_shipping_tax_div'); 
	 if (d1 != null) d1.style.display = "none";
    }
    }
    else
    {
     d1 = document.getElementById('tt_shipping_rate_div'); 
	 if (d1 != null) d1.style.display = 'none';
     d1 = document.getElementById('tt_shipping_tax_div'); 
	 if (d1 != null) d1.style.display = "none";
    }
    
    if ((op_no_taxes != true) && (op_no_taxes_show != true) && (op_show_andrea_view!=true))
    {
	
    for (i = 0; i<tax_data.length; i++)
    {
     var tx = document.getElementById('tt_tax_total_'+i);
     var tx_txt = document.getElementById('tt_tax_total_'+i+'_txt');
     var txt_div = document.getElementById('tt_tax_total_'+i+'_div');
     
	 
     {
      rate_arr = tax_data[i].split('|');
	  
	  
      {
      if (rate_arr[1]>0)
      {
	 
	   test1 = parseFloat(rate_arr[0])*100;
       test2 = Math.round(parseFloat(rate_arr[0])*100); 
	   if (test1!=test2)
	   test2 = Math.round(parseFloat(rate_arr[0])*1000)/10;
	   if (test2 != test1)
	   test2 = Math.round(parseFloat(rate_arr[0])*10000)/100;
	   
	   taxr = test2+'%';
	   
      	if (rate_arr[0] != '')
      	{
       if (tx_txt != null)
	   {
	   if ((tax_name[i] != null) && (tax_name[i] != ''))
	   tx_txt.innerHTML = tax_name[i]; 
	   else
       tx_txt.innerHTML = op_tax_txt+'('+taxr+')';
	   }
       if (op_basket_override)
       {
	   tx_txt2 = document.getElementById('tt_tax_total_'+i+'_txt_basket');
	   
	   if (tx_txt2 != null)
	   {
	   if ((tax_name[i] != null) && (tax_name[i] != ''))
	    tx_txt2.innerHTML = tax_name[i]; 
	    else
        tx_txt2.innerHTML = op_tax_txt+'('+taxr+')';
       }
	   else
	   {
	     // if the template does not have the posisions for all of the tax rates, it won't be shown !
	   }
	   
	   
	   
	   }
       }
       else
       {
	   if (tx_txt!=null)
       tx_txt.innerHTML = op_tax_txt;
	   if (op_basket_override)
       {
        d1 = document.getElementById('tt_tax_total_'+i+'_txt_basket'); 
		if (d1 != null) d1.innerHTML = op_tax_txt;
       }

       }
	   
	    if (typeof tx != 'undefined')
        if (tx != null) 
       if ((tax_data.length == 1) && (op_sum_tax == true))
       {
        tx.innerHTML = Onepage.formatCurrency(parseFloat(rate_arr[1])+parseFloat(order_shipping_tax));
       }
       else
	   {
	    if (tx != null) 
       tx.innerHTML = Onepage.formatCurrency(rate_arr[1]);
	   }
	   	if (typeof txt_div != 'undefined')
        if (txt_div != null) 
       txt_div.style.display = 'block';
	  
	   if (op_basket_override)
       {
	   
        if ((tax_data.length == 1) && (op_sum_tax == true))
        {
          document.getElementById('tt_tax_total_'+i+'_basket').innerHTML = Onepage.formatCurrency(parseFloat(rate_arr[1])+parseFloat(order_shipping_tax));
          document.getElementById('tt_tax_total_'+i+'_div_basket').style.display = '';
        }
        else
        {
        d1 = document.getElementById('tt_tax_total_'+i+'_basket'); 
		if (d1 != null) d1.innerHTML = Onepage.formatCurrency(rate_arr[1]);
        d1 = document.getElementById('tt_tax_total_'+i+'_div_basket'); 
		if (d1 != null) d1.style.display = '';
        }
       }
       
      }
      else
      {
	   if (typeof tx_txt != 'undefined')
       if (tx_txt != null) 
       tx_txt.innerHTML = "";
	   if (typeof tx != 'undefined')
       if (tx != null) 
       tx.innerHTML = "";
	   if (typeof txt_div != 'undefined')
       if (txt_div != null) 
       txt_div.style.display = 'none';
       if (op_basket_override)
       {
        d1 = document.getElementById('tt_tax_total_'+i+'_div_basket'); 
		if (d1 != null) d1.style.display = 'none';
       }
      }
      }
     }
    }
    }
    stru = document.getElementById('tt_total_txt'); 
	if (stru != null) str = stru.innerHTML;
	else str = ''; 
    if (str == '')
	{
     d1 = document.getElementById('tt_total_txt'); 
	 if (d1 != null) d1.innerHTML = op_textinclship;
	}
    d1 = document.getElementById('tt_total'); 
	if (d1 != null) d1.innerHTML = Onepage.formatCurrency(order_total);
    if (op_basket_override)
    {
     d1 = document.getElementById('tt_total_basket'); 
	 if (d1 != null) d1.innerHTML = Onepage.formatCurrency(order_total);
    }
    return "";
   },
   
syncShippingAndPayment: function(paymentelement)
    {
	  if (opc_debug)
	Onepage.op_log('syncShippingAndPayment'); 
    if (op_noshipping == false) 
    {
     val = Onepage.getVShippingRate();

     if (op_shipping_inside_basket)
     {
      var d = document.getElementById('new_shipping');
      d.value = val;
     }
     var s = document.getElementById('shipping_rate_id_coupon');
     if (s != null)
	 {
	  s.value = val;
	 }
	 
    }
	 valp = Onepage.getValueOfSPaymentMethod();
	   if (opc_debug)
	   {
       Onepage.op_log('payment:'); 
	   Onepage.op_log(valp); 
	   }
     if (op_payment_inside_basket)
     {
      var df = document.getElementById('new_payment');
      df.value = valp;
     }
	 
	 if ((Onepage.last_payment_extra != '') && (Onepage.last_payment_extra != 'extra_payment_'+valp))
	 {
	  var d = document.getElementById(Onepage.last_payment_extra); 
	  if (d != null)
	  d.style.display = 'none'; 
	 }
	 //extra_payment_5
	 var d = document.getElementById('extra_payment_'+valp); 
	 var extraShown = false; 
	 if (d != null)
	 {
	 
	   Onepage.last_payment_extra = 'extra_payment_'+valp; 
	   d.style.display = 'block'; 
	   extraShown = true; 
	     if (opc_debug)
		 Onepage.op_log('showing extra'); 
	 }
	 else
	 {
	   if (opc_debug)
	   Onepage.op_log('extra not found for'+valp); 
	 }
	 
	 if ((!extraShown) && (op_payment_inside_basket))
	 {
	   d = document.getElementById('payment_top_wrapper'); 
	   if ((typeof d != 'undefined') && (d!=null))
	   d.style.display = 'none'; 
     }
	 if ((extraShown) && (op_payment_inside_basket))
	 {
	   d = document.getElementById('payment_top_wrapper'); 
	   if ((typeof d != 'undefined') && (d!=null))
	   d.style.display = 'block'; 
	 
	 }
	 
	 
	 
	
	 dd = document.getElementById('paypalExpress_ecm');
	 if (dd != null && (typeof(dd) != 'undefined'))
	 if (valp == op_paypal_id)
	 {
		// last test:
		// direct payments use payment_method_id_(PAYPALID)
		// 
		if (op_paypal_direct == true)
		{
		xx = document.getElementById('payment_method_id_'+valp); 
		if (xx.checked != true)
		dd.value = '2';
		else 
		dd.value = '';
		}
		else
		{
		 dd.value = '2';
		}
		
	    
	    
	 }
	 else
	 {
	   dd.value = '';
	 }
	 
	 var p = document.getElementById('payment_method_id_coupon');
	 
	 if (p != null)
	 {
	  p.value = valp;
	 }
	      
     
    },
	
	/* changes text of Order total
	*   msg3 is the "Order total: "
	*   curr is currency symbol html encoded
	*   order_total is VM order total (for US tax system it is generated in shippnig methods)  	
  */ 
changeTextOnePage: function(msg3, curr, order_total) {
		 
	  Onepage.syncShippingAndPayment();
	  
	
	  
	  /*
	  if (op_payment_inside_basket || op_shipping_inside_basket)
	  {
	   syncShippingAndPayment();
	  }
	  */
	  if ((never_show_total != null) && (never_show_total == true) && (!op_override_basket)) return true;
	  var op_ship_base = 0;
	  // new in version 2
	 
	  var ship_id = Onepage.getInputIDShippingRate(true);
	  
	  sd = document.getElementById('saved_shipping_id'); 
	  if (sd != null)
	   sd.value = ship_id; 
	   
	  var ship_id = Onepage.getInputIDShippingRate(false);
	  
	  var payment_id = Onepage.getPaymentId();
					
	  return Onepage.getTotals(ship_id, payment_id);
	  
	},
	
op_show_all_including: function(msg3, strtotal, curr, tax_base, tax, tax_rate, op_ship_base, payment_discount)
	{
	  var ship_info = '';
	  var payment_info = '';
	  var product_grand = '';
	  
	  if (op_always_show_all)
	  {
	   ship_info = '<span style="font-size: 100%">'+op_shipping_txt+': '+Onepage.formatCurrency(op_ship_base)+"</span><br />";  
	   if ((payment_discount != null) && (payment_discount != '') && (payment_discount != 0))
	   payment_info = '<span style="font-size: 100%">Payment discount & fees: '+Onepage.formatCurrency((-1)*parseFloat(payment_discount.toString()))+"</span><br />";  
	   //if (op_show_prices_including_tax=='1')
	   //var op_grand_subtotal2 = parseFloat(op_grand_subtotal - parseFloat(parseFloat(op_grand_subtotal) / (1+parseFloat(tax_rate))));
	   if (op_show_prices_including_tax=='1')
	   product_grand = '<span style="font-size: 100%">Product grand subtotal: '+Onepage.formatCurrency(op_grand_subtotal/(1+tax_rate))+"</span><br />";  
	   else
	   product_grand = '<span style="font-size: 100%">Product grand subtotal: '+Onepage.formatCurrency(op_grand_subtotal)+"</span><br />";  
	  }
	  
	  op_total_total = strtotal;
	  
	  
	  var tax_rate_perc = parseFloat(tax_rate)*100;
	  
	  if (Math.round(tax_rate_perc)==tax_rate_perc)
	  tax_rate_perc = tax_rate_perc.toFixed(0).toString();
	  else
	  tax_rate_perc = tax_rate_perc.toFixed(1);
	  
	  var cup_txt = '';
	  if (op_coupon_amount != null) 
	  if (op_coupon_amount != 0)
	  {
	    cup_txt = '<span style="font-size: 100%">'+op_coupon_discount_txt+': -'+Onepage.formatCurrency(op_coupon_amount.toString())+"</span><br />";
	  }
	  var show_text = msg3+"<span style='font-size:200%;'>"+Onepage.formatCurrency(op_total_total)+" </span>";
		
	  if (((tax > 0) && (tax_rate > 0)) && (op_dont_show_taxes != '1'))
	  {
	   
	  // tax_base = curr+Onepage.formatCurrency(tax_base);
	 //  tax = tax);
	   //tax_rate = (parseFloat(tax_rate.toString()) * 100).toFixed(2);
	   show_text = product_grand+ship_info+payment_info+"<span style='font-size: 100%'>"+op_subtotal_txt+": "+Onepage.formatCurrency(tax_base)+"</span><br /><span style='font-size: 100%;'>"+op_tax_txt+" ("+tax_rate_perc+"%): "+Onepage.formatCurrency(tax)+"</span><br />"+cup_txt+show_text;
	  }
	  
	  d1 = document.getElementById("totalam"); 
	  if (d1 != null) d1.innerHTML = show_text;
	  
	},
	

	
	 /* This function alters visibility of shipping address
	 *
	 */
showSA: function(chk, divid)
	 {
	   if (document.getElementById(divid))
	   document.getElementById(divid).style.display = chk.checked ? '' : 'none';
	  
	   if (chk.checked)
	   {
	   elopc = document.getElementById('shipto_virtuemart_country_id');
	   /*
	   jQuery('#opcform').('input,textarea,select,button').each(function(el){
			if (el.hasClass('opcrequired')) {
				el.attr('class', 'required');
			}
	   
	   });
	   */
	   }
	   else 
	   {
	   elopc = document.getElementById('virtuemart_country_id');
	   /*
	     jQuery('#opcform').('input,textarea,select,button').each(function(el){
			if (el.hasClass('required')) {
				el.attr('class', 'opcrequired');
			}
	   
	   });
	   */
	   }
	   
	   
	   
	   // if we have a new country in shipping fields, let's update it
	   if (elopc != null)
	   {
	   
	   Onepage.op_runSS(elopc);
	   }
	   
	 },
	 
	 
	 // this function is used when using select box for payment methods
runPaySelect: function(element)
	 {
	    ind = element.selectedIndex;
	    value = element.options[ind].value;
		
		hasExtra = Onepage.getAttr(element.options[ind], 'rel'); 
		/*
		if (hasExtra != null)
		if (hasExtra != 0)
		{
			d = document.getElementById('extra_payment_'+hasExtra); 
			if (d != null)
			{
				d.style.display = 'block'; 
				op_last_payment_extra = d; 
			}
		}
		else
		{
			if (op_last_payment_extra != null)
				op_last_payment_extra.style.display = 'none'; 
		}
		*/
	    Onepage.runPay(value, value, op_textinclship, op_currency, op_ordertotal, element);
	 },
	 /*
	 * This function is triggered when clicked on payment methods when CC payments are NOT there
	 */
runPay: function(msg_info, msg_text, msg3, curr, order_total, element)
	 {
	  Onepage.setOpcId(); 
	  if (typeof(msg_info) == 'undefined' || msg_info == null || msg_info == '')
	  {
	    var p = Onepage.getValueOfSPaymentMethod(element);
	    msg_info = p;
	    msg_text = p;
	    msg3 = op_textinclship;
	    curr = op_currency;
	    order_total = op_ordertotal;
	  }
	  
	  if (typeof(pay_btn[msg_info])!='undefined' && pay_msg[msg_info]!=null) msg_info = pay_msg[msg_info];
	  else msg_info = pay_msg['default'];
	  
	  if (typeof(pay_btn[msg_text])!='undefined' && pay_btn[msg_text]!=null) msg_text = pay_btn[msg_text];
	  else msg_text = pay_btn['default'];
	 
	  dp = document.getElementById("payment_info"); 
	  if (dp != null)
	  dp.innerHTML = msg_info;
	  cbt = document.getElementById("confirmbtn");
	  if (cbt != null)
	  {
	    
	    if (cbt.tagName.toLowerCase() == 'input')
	    cbt.value = msg_text;
	    else cbt.innerHTML = msg_text;
	  }
	  
	  Onepage.changeTextOnePage(msg3, curr, order_total);
	  for (var x=0; x<callAfterPaymentSelect.length; x++)
	   {
	     eval(callAfterPaymentSelect[x]);
	   }
	   
	  return true;
	 },
	 
	 // gets value of selected payment method
getPaymentId: function()
	 {
	  return Onepage.getValueOfSPaymentMethod();
	 },

// return address query as &address_1=xyz&address_2=yyy 
op_getaddress: function()
{
 var ret = '';
 if (Onepage.shippingOpen())
 {
  // different shipping address is activated
  
  {
   a1 = document.getElementById('shipto_address_1_field');
   if (a1 != null)
   {
     ret += '&address_1='+Onepage.op_escape(a1.value);
   }
   a2 = document.getElementById('shipto_address_2_field'); 
   if (a2 != null)
   {
     ret += '&address_2='+Onepage.op_escape(a2.value);
   }
  }
 }
 if (ret == '')
 {
   a1 = document.getElementById('address_1_field');
   if (a1 != null)
   {
     ret += '&address_1='+Onepage.op_escape(a1.value);
   }
   a2 = document.getElementById('address_2_field'); 
   if (a2 != null)
   {
     ret += '&address_2='+Onepage.op_escape(a2.value);
   }
  
 }
 
 return ret;
 
},
checkAdminForm: function(el)
{

   // check if we have form 
   if (typeof el.form != 'undefined')
   if (el.form != null)
   if (typeof el.form.id != 'undefined')
   if (el.form.id == 'adminForm') return true; 
   else return false; 
   // for the most intelligent browsers:
   return true; 
},
buildExtra: function()
{
   var eq = ''; 
   isSh = Onepage.shippingOpen(); 
    
	   for (var i=0; i<op_userfields.length; i++)
	    {
		  // filter only needed
		  //if (((op_userfields[i].indexOf('shipto_')!=0) && (!isSh))  || (op_userfields[i].indexOf('shipto_')==0) && (isSh))
		  
		  if ((((op_userfields[i].indexOf('shipto_')==0) && (isSh)) || (op_userfields[i].indexOf('shipto_')!=0)))
		   {
		     /*
		     if (typeof eval('document.adminForm.'+op_userfields[i]) != 'undefined')
			 {
			 elopc = eval('document.adminForm.'+op_userfields[i]);
			 }
			 */
			 //else
		     elopc = document.getElementsByName(op_userfields[i]); 
			  
			 if (elopc != null)
			 for (var j=0; j<elopc.length; j++)
			  {
			   // ie9 bug:
			   if (elopc[j].name != op_userfields[i]) continue; 
			   if (!Onepage.checkAdminForm(elopc[j])) continue; 
			   switch (elopc[j].type)
			    {
				  case 'password':  break;
				  case 'text':
					eq += '&'+op_userfields[i]+'='+Onepage.op_escape(elopc[j].value);
					break; 
				  case 'select-one': 
				   if ((typeof elopc[j].value != 'undefined') && ((elopc[j].value != null)))
				   eq += '&'+op_userfields[i]+'='+Onepage.op_escape(elopc[j].value);
				   else
				    {
					   if ((typeof elopc[j].options != 'undefined') && (elopc[j].options != null) && (typeof elopc[j].selectedIndex != 'undefined') && (elopc[j].selectedIndex != null))
					    eq += '&'+op_userfields[i]+'='+Onepage.op_escape(elopc[j].options[elopc[j].selectedIndex].value);
					     
					}
					break;
				  case 'radio': 
				   if ((elopc[j].checked == true) && (elopc[j].value != null))
				      eq += '&'+op_userfields[i]+'='+Onepage.op_escape(elopc[j].value);
				   else
				   if (elopc[j].checked == true)
				    eq += '&'+op_userfields[i]+'=1';
				   break; 
				  case 'hidden': 
				    if ((typeof elopc[j].value != 'undefined') && (elopc[j].value != null))
				    eq += '&'+op_userfields[i]+'='+Onepage.op_escape(elopc[j].value);
					break; 
				 
				  default: 
				    if ((typeof elopc[j].value != 'undefined') && (elopc[j].value != null))
				    eq += '&'+op_userfields[i]+'='+Onepage.op_escape(elopc[j].value);
				    break; 
				}
			  }
			  elopc = document.getElementsByName(op_userfields[i]+'[]'); 
			  if (elopc != null)
			   for (var j=0; j<elopc.length; j++)
			    {
				    if (!Onepage.checkAdminForm(elopc[j])) continue; 
				    if ((typeof elopc[j].value != 'undefined') && (elopc[j].value != null))
					if (((typeof elopc[j].checked != 'undefined') && (elopc[j].checked)) || 
					 (typeof elopc[j].selected != 'undefined') && (elopc[j].selected))
				    eq += '&'+op_userfields[i]+'[]='+Onepage.op_escape(elopc[j].value);
				   
				}
		   }
		}
	
	return eq; 
},

op_getSelectedCountry: function()
{
	
	  var sel_country = "";
	  if (Onepage.shippingOpen())
	  {
	   // different shipping address is activated
	    
	     var sa = document.getElementById("sa_yrtnuoc_field");
	     if (sa != null)
	     sel_country = sa.value;
	     else
		 {
		
		  sa = document.getElementById('shipto_virtuemart_country_id');
		  if (sa != null)
		  if ((typeof sa.options != 'undefined') && (sa.options != null))
		     sel_country = sa.options[sa.selectedIndex].value;
		  else
		  if ((sa != null) && (sa.value != null)) sel_country = sa.value;
		  
		  //sel_country = sa.value;
		 }
	  }

	  // we will get country from bill to
	  if (sel_country == "")
	  {
	    var ba = document.getElementById("country_field");
	    if (ba!=null)
	    sel_country = ba.value;
		else
		{
	     ba = document.getElementById('virtuemart_country_id');
		 if (ba != null)
		 {
		 if ((typeof ba.options != 'undefined') && (ba.options != null))
		  sel_country = ba.options[ba.selectedIndex].value;
		 else
		 if ((ba != null) && (ba.value != null)) sel_country = ba.value;
		 }
		}
		
	  }

	 return sel_country; 
	 
},
	 
op_getSelectedState: function()
    {
     sel_state = '';
   	if (Onepage.shippingOpen())
	{
	  
	     var sa = document.getElementById("shipto_virtuemart_state_id");
	     if (sa != null)
		 {
		 if (((typeof sa.options != 'undefined') && (sa.options != null)) && (sa.selectedIndex != null))
		 {
		 if (typeof sa.options[sa.selectedIndex] != 'undefined')
	     sel_state = sa.options[sa.selectedIndex].value;
		 }
		 else 
		  {
		    // maybe it's hidden
			if ((typeof sa.value != 'undefined') && (sa.value != null))
			 sel_state = sa.value; 
		  }
		 }
	    
    }
    if (sel_state == '')
    {
    var c2 = document.getElementById("virtuemart_state_id");
    if (c2!=null)
    {
	if ((typeof c2.options != 'undefined') && (c2.options != null))
	{
	if (typeof c2.options[c2.selectedIndex] != 'undefined')
	sel_state = c2.options[c2.selectedIndex].value;
	}
	else 
	  {
		 
		  
		    // maybe it's hidden
			if ((typeof c2.value != 'undefined') && (c2.value != null))
			 sel_state = c2.value; 
		     
	  }
    }
    }
 return sel_state;
},

// return true if the shipping fields are open
shippingOpen: function()
{
    var sc = document.getElementById("sachone");
    if (sc != null)
	{
	  if ((typeof(sc.checked) != 'undefined' && sc.checked) || (typeof(sc.selected) != 'undefined' && sc.selected))
	  {
	    
		if (!shippingOpenStatus)
	    for (var i=0; i<shipping_obligatory_fields.length; i++)
		 {
		   d = document.getElementById('shipto_'+shipping_obligatory_fields[i]+'_field'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.setAttribute('required', 'required'); 
			  d.setAttribute('aria-required', 'true'); 
			  if (d.className.indexOf('opcrequired')>=0) d.className = d.className.split('opcrequired').join(''); 
			  if (d.className.indexOf('required')<0) d.className += ' required'; 
			  
			  
			}
			
		   
		 }
		 
		shippingOpenStatus = true; 
	    return true;
	  }
	}
	
	if (shippingOpenStatus)
	 {
	  for (var i=0; i<shipping_obligatory_fields.length; i++)
		 {
		   d = document.getElementById('shipto_'+shipping_obligatory_fields[i]+'_field'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.removeAttribute('required'); 
			  d.removeAttribute('aria-required'); 
			  d.removeAttribute('aria-invalid'); 
			  d.className = d.className.split('required').join(''); 
			  d.className = d.className.split('invalid').join(''); 
			  
			}
		  // 
		 }
	  //... 
	 }
	shippingOpenStatus = false; 
	return false;
	
},

doublemail_checkMailTwo: function(el)
{
	Onepage.email_check(el); 
	// loaded from double.js
	Onepage.doublemail_checkMail(); 
},

op_log: function(msg)
{
   
  if (typeof msg != 'undefined')
  if (msg != null)
  if ((typeof console != 'undefined') && (console != null))
  if (typeof console.log != 'undefined')
  {
   if (msg != '')
   console.log(msg); 
  }
},

op_getZip: function()
{
    var sel_zip = '';
    
	if (Onepage.shippingOpen())
     {
	    {
	     var sa = document.getElementById("shipto_zip_field");
	     if (sa)
	     sel_zip = sa.value;
	    }
	  }
    if (sel_zip == '')
    {
    var c2 = document.getElementById("zip_field");
    if (c2!=null)
    {
	sel_zip = c2.value;
    }
    }
 return sel_zip;
},
	 
// return true if problem
isNotAShippingMethod: function(invalidation)
{
 
 if (op_noshipping == true) return false;
 
 
 if ((typeof invalidation != 'undefined') && (invalidation != null) && (invalidation == true))
 invalidation = true; 
 else invalidation = false; 
 
 
 var sh = Onepage.getVShippingRate(invalidation);
 
 
 
 
 if (sh.toString().indexOf('choose_shipping')>=0)
 {
  return true;
 }
 
 
 var ship_id = Onepage.getInputIDShippingRate(true);
	  
 var sd = document.getElementById('saved_shipping_id'); 
	  if (sd != null)
	   sd.value = ship_id; 
 
 return false;  
},


// failsafe function to unblock the button in case of any problems
unblockButton: function()
{
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = false; 
   //alert('ok');
  }
  else
  {
   so = document.getElementById('confirmbtn');
   if (so != null)
   so.disabled = false;
  }
},
// will disable the submit button so it cannot be pressed twice
startValidation: function()
{
   
  
  // to prevend double clicking, we are using both button and input
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = true; 
   //alert('ok');
  }
  else
  {
   so = document.getElementById('confirmbtn');
   if (so != null)
   so.disabled = true;
  }
  
  if (so != null)
   {
      var inserting = document.getElementById('checkout_loader'); 
	  if (inserting != null)
	  {
	   inserting.style.display = 'block'; 
	  }
	  else
	  {
       var inserting = document.createElement("div");
	   inserting.id = 'checkout_loader'; 
	   inserting.innerHTML = '<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." />'; 
	   if (typeof so.parentNode.insertBefore != 'undefined')
       so.parentNode.insertBefore(inserting,so);
	  }
   }
  // IE8 and IE7 check: 
  if (window.attachEvent && !window.addEventListener) {
   //return true; 
  }
  else
  opcsubmittimer = setTimeout('Onepage.unblockButton()', 10000);
  // if any of javascript processes take more than 10 seconds, the button will get unblocked
  // the delay can occur on google ecommerce tracking, OPC tracking or huge DOM, or maybe a long insert query
  
  
},

// submit the form or unblock the button
endValidation: function(retVal)
{
   var typeB = 'submit'; 
   {
    // unblock the submit button
  // to prevend double clicking, we are using both button and input
  // confirmbtn_button
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = false; 
   typeB = so.type; 
   //alert('ok');
  }
  else
  {
   so = document.getElementById('confirmbtn');
   if (so != null)
   {
    so.disabled = false;
    typeB = so.type; 
   }
  }
  // the form will not be sumbmitted
   if (!retVal)
   {
   
    
    
      var inserting = document.getElementById('checkout_loader'); 
	  if (inserting != null)
	  {
	   inserting.style.display = 'none'; 
	  }
     
   
   
   return false; 
   }
   }
   


  if (!(window.attachEvent && !window.addEventListener)) 
  if (typeof opcsubmittimer != 'undefined')
  if (opcsubmittimer != null)
  clearTimeout(opcsubmittimer); 
  
  
  var fs = document.getElementById('form_submitted'); 
  if (fs != null)
  fs.value = '1';
  // updated code: 
  if (typeB == 'submit')
  return true; 
  
 
  
  try
  {
  // submit the form by javascript
  document.adminForm.submit();
  return false; 
  }
  catch(e)
  {
   // submit the form by returning true
   return true; 
  }
  
},
// opc1 double click prevention end
opc_checkUsername: function(wasValid)
{

	if (!opc_no_duplicit_username) return wasValid; 
	if (!Onepage.getRegisterAccount()) return wasValid; 
	// there has not been the ajax check yet
	
	
   if (!last_username_check)
   {
	   if (Onepage.getRegisterAccount())
	   {
		   
		   return false; 
	   }
	   else 
	   {
	   
		   return wasValid; 
	   }
	 
   }
   return wasValid; 

},
opc_valid_username: function(wasValid)
{
  if (!Onepage.getRegisterAccount()) return wasValid; 
  var pattern = "^[^#%&*:<>?/{|}\"';()]+$"; 
  var regExp = new RegExp(pattern,"");
  var usd = document.getElementById('username_field'); 
  if (usd != null)
   {
     if (usd.value == '')
	  {
	  
	   	   var msg = op_general_error; 
	   
		   if (typeof op_userfields_named['username'] != 'undefined')
		    {
			  msg += ' '+op_userfields_named['username']; 
			}
		 alert(msg); 
	    usd.className += ' invalid'; 
	    return false; 

	    
	  }
     if (!regExp.test(usd.value)) 
	  {
	  usd.className += ' invalid';
	  alert(JERROR_AN_ERROR_HAS_OCCURRED); 	  
	  return false; 
	  }
	  else
	  usd.className = usd.className.split('invalid').join(''); 
   }
   return true; 
},
opc_checkEmail: function(wasValid)
{
   
	if (op_logged_in_joomla) return wasValid; 
	if (!Onepage.emailCheckReg(true)) return false; 
	if (!opc_no_duplicit_email) return wasValid; 
	
	// there has not been the ajax check yet
	
	
   if (!last_email_check)
   {
	   alert(email_error); 
	   return false; 
	   if (Onepage.getRegisterAccount())
		   return false; 
	   else 
		   return wasValid; 
	   
	 
   }
   return wasValid; 

},



getRegisterAccount: function()
{
	// double check in case google has autofill
		var el2 = document.getElementById('register_account'); 
		if (el2 != null)
		{
		
		if (el2.type == 'hidden')
		{
		   if (el2.value == '1') return true; 
           else return false; 		   
		}

		
			 if (el2.checked == true) return true; 
			 else return false; 
		}
		else
		{
			// if we have not register account check if password exists
			d = document.getElementById('opc_password_field'); 
			if (d!=null)
			return true; 
			else
			return false; 
		}
	   el = document.getElementsByName('register_account'); 
	   if (el != null)
	   if (el.length > 0)
	   {
		   if (typeof el.checked != 'undefined')
		   {
			    if (el.checked) return true; 
				if (el.checked == false) return false; 
		
		   }
		    if (typeof el.type != 'undefined')
			{
		    if (el.type != null)
		   	if (el.type == 'hidden')
				{
					if (el.value != '1') return false; 
				}
			}
			else
			{
			if (el[0].type != null)
			{
		   	if (el[0].type == 'hidden')
				{
					if (el[0].value != '1') return false; 
					
				}
			
		   	if (el[0].type == 'checkbox')
				{
					if (el[0].value != '1') return false; 
					
				}
			
				
			}	
			if (el[0].checked != null)
			{
			if (el[0].checked) return true; 
			else return false; 
			}
				
			}
			
				
				
	   }
	   else
	   {
	   		// if we have not register account check if password exists
			d = document.getElementById('opc_password_field'); 
			if (d!=null)
			return true; 
			else
			return false; 

	   }
	   // by default register account
	   return true; 
	   
},
isBusinessCustomer: function()
{
  var is_b = false; 
	d = document.getElementById('opc_is_business'); 
	if (d != null)
	{
		if (d.value == 1)
				return true; 
	}
	return false; 
},
getBusinessState: function()
{
	var is_b = false; 
	d = document.getElementById('opc_is_business'); 
	if (d != null)
	{
		if (d.value == 1)
				var is_b = true; 
	}
	
	if (!is_b)
	{
		if ((typeof business_fields != 'undefined') && (business_fields != null))
		for (var i=0; i<business_fields.length; i++)
		{
			d2 = document.getElementById(business_fields[i]+'_field'); 
			if (d2 != null)
			d2.className = d2.className.split('required').join('notequired'); 
		    
		
			d2 = document.getElementById('shipto_'+business_fields[i]+'_field'); 
			if (d2 != null)
			d2.className = d2.className.split('required').join('notequired'); 
		    
		}
	}
    else
	{
		if ((typeof business_fields != 'undefined') && (business_fields != null))
		for (var i=0; i<business_fields.length; i++)
		{
			d2 = document.getElementById(business_fields[i]+'_field'); 
			if (d2 != null)
			d2.className = d2.className.split('notequired').join('required'); 

			d2 = document.getElementById('shipto_'+business_fields[i]+'_field'); 
			if (d2 != null)
			d2.className = d2.className.split('notequired').join('required'); 

		}
	}

	
},

processPlaceholders: function()
{
   // support of placeholders and browsers that do not support them
	   var invalid = false; 
	for (var i = 0; i < op_userfields.length; i++) {
       var d = document.getElementsByName(op_userfields[i]); 
	   if (d != null)
	   if (d.length > 0)
	   {
		   
		   var title = Onepage.getAttr(d[0], 'placeholder'); 
		   
		   if (title != null)
		   if (title != 0)
		   if (d[0].className.indexOf('required')>=0)
		   if (typeof d[0].value != 'undefined')
		   if (d[0].value == title)
		   {
			   invalid = true; 
			   d[0].className = d[0].className.split('invalid').join('').concat(' invalid ');
		   }
	   }
    
	}
	if (invalid) return true; 
	return false; 
},
emailCheckReg: function(showalert, id)
{
var found = false; 
if (id != null)
{
var em = document.getElementById(id); 
found = true; 
}
else
{
  var dd = document.getElementById('guest_email'); 
  if (dd != null)
  if (dd.value != '')
  {
  var em = dd; 
  found = true; 
  }
}
if (!found)
var em = document.getElementById('email_field'); 
			if (em != null)
			 {
			  var emv = em.value; 
			  //var pattern = "[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])";
			    var pattern = "[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])"; 
			  
			  
				//var pattern =/^[a-zA-Z0-9._-]+(\+[a-zA-Z0-9._-]+)*@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
			  var regExp = new RegExp(pattern,"");
			  if (!regExp.test(emv))
			  {
				 em.className += ' invalid'; 
				 if (showalert)
				 alert(op_email_error);
			     return false; 
			   }
			   else 
			    {
				  em.className = em.className.split('invalid').join(''); 
				  return true; 
				}
			 }
			 return true; 
},

removePlaceholders: function()
{

	for (var i = 0; i < op_userfields.length; i++) {
       d = document.getElementsByName(op_userfields[i]); 
	   if (d != null)
	   if (d.length > 0)
	   {
		   
		   var title = Onepage.getAttr(d[0], 'placeholder'); 
		   if (title != null)
		   if (title != 0)
		   if (typeof d[0].value != 'undefined')
		   if (d[0].value == title)
		   {
			   d[0].value=''; 
		   }
	   }
    
	}
},
formSubmit: function(event, el)
{
  Onepage.op_log(event); 
  Onepage.op_log(el); 
  return false; 
},
validateFormOnePage: function(event, el, wasValid)
{
  
  
  if (!opc_debug)
  {
  try 
  {
    return Onepage.validateFormOnePagePrivate(wasValid); 
  }
  catch(e)
  {
    return true; 
  }
  }
  else
  {
   return Onepage.validateFormOnePagePrivate(wasValid); 
  }
  return true; 
},

validateFormOnePagePrivate: function(wasValid)
{
  
   // prevent double submission:
   var fs = document.getElementById('form_submitted'); 
   if (fs != null)
   if (fs.value == '1') return false; 
   


   if (wasValid != null) 
   {;}
   else wasValid = true; 
   
   if (wasValid) Onepage.startValidation();
   
   if (op_logged_in != '1')
   {
   if (!Onepage.opc_checkUsername(wasValid))
   {
	   alert(username_error); 
	   return Onepage.endValidation(false);
   }
   
   if (!Onepage.opc_valid_username(wasValid))
    {
	   
	   return Onepage.endValidation(false);
	}
   }
   
   var isGuest = false; 
   var dg = document.getElementById('guest_email'); 
   if (dg != null)
   if (dg.value != '')
   isGuest = true; 
   
   if (!isGuest)
   if (op_logged_in != '1')
   if (!Onepage.opc_checkEmail(wasValid))
   {
	   
	   return Onepage.endValidation(false);
   }
	
   Onepage.getBusinessState(); 
	// registration validation
	// var elem = jQuery('#name_field');
	//	elem.attr('class', "required");
    d = document.getElementById('name_field'); 
	if (d != null)
	{
			if (Onepage.getRegisterAccount())
			{
				d.className += ' required'; 
			}				
			else
			{
				d.className = d.className.split('required').join(''); 				
			}				
	}		
		
	d = document.getElementById('register_account');
	if (!isGuest)
	if (d != null && (typeof d != 'undefined'))
	 {
	    //if ((d.checked) || ((!(d.checked != null)) && d.value=='1'))
		if (Onepage.getRegisterAccount())
		{
			
		 if (!op_usernameisemail)
		 {
		 // if register account checked, make sure username, pwd1 and pwd2 are required
		 var d2 = document.getElementById('username_field'); 
		 if (d2 != null)
		 {
         d2.className += " required";
		 }
		 }
		 
		 var d2 = document.getElementById('opc_password_field');
		if (d2 != null)
		 {
         d2.classnName += " required";
		 }
		 
		 d2 = document.getElementById('opc_password2_field'); 
		 if (d2 != null)
		  {
            d2.className += ' required'; 
            
		  }
		}
		else
		{
		  if (!op_usernameisemail)
		  {
		// unset required for username, pwd1 and pwd2
		 var d2 = document.getElementById('username_field'); 
		 if (d2 != null)
		 {
		  
		  
          d2.className = d2.className.split('required').join(''); 
		 }
		 }
		   d2 = document.getElementById('opc_password_field');
		if (d2 != null)
		 {
        
         
		 d2.className = d2.className.split('required').join(''); 
		 }
		 var d2 = document.getElementById('opc_password2_field'); 
		 if (d2 != null)
		  {
		    d2.className = d2.className.split('required').join(''); 
		  }
		}
	 }
	 else
	 {
	  {
	   var d2 = document.getElementById('username_field'); 
		 if (d2 != null)
		 {
		 d2.className += ' required'; 
         
		 }
		  var d2 = document.getElementById('password_field');
		if (d2 != null)
		 {
         d2.className += ' required'; 
		}
		 var d2 = document.getElementById('opc_password_field');
		if (d2 != null)
		 {
		   d2.className += ' required'; 
         
		 }
		 var d2 = document.getElementById('opc_password2_field'); 
		 if (d2 != null)
		  {
           
		   d2.className += ' required'; 
		  }
		}
	 }
	 
	  var test = document.createElement('input');
	 
   // before we proceed with validation we have to check placeholders: 
   if (!('placeholder' in test))
   {
	   var isInvalid = Onepage.processPlaceholders(); 
	 
	if (isInvalid)
	 {
		 alert(op_general_error); 
		 return Onepage.endValidation(false);
	 }
	 else
	 {
	   Onepage.removePlaceholders(); 
	 }
   }
	 
	 // passwords dont' match error
	 if (!isGuest)
	 if (Onepage.getRegisterAccount())
	 {
	 var p = document.getElementById('opc_password_field'); 
	 if ((typeof p != 'undefined') && (p!=null))
	  {
	    var p2 = document.getElementById('opc_password2_field'); 
		if (p2 != null)
		{
		if (p.value != p2.value)
		{
		 alert(op_pwderror); 
		 return Onepage.endValidation(false);
		}
		}
	  }
	}
	 // op_pwderror
if (Onepage.isNotAShippingMethod(true)) 
 {
  alert(shipChangeCountry);
  return Onepage.endValidation(false);
 }
 var invalid_c = document.getElementById('invalid_country');
 if (invalid_c != null)
 {
  alert (noshiptocmsg);
  return Onepage.endValidation(false);
 }
 
 //
 var invalid_w = document.getElementById('please_wait_fox_ajax');
 if (invalid_w != null)
 {
  alert (COM_ONEPAGE_PLEASE_WAIT);
  return Onepage.endValidation(false);
 }

 var agreed=document.getElementById('agreed_field');
 if (agreed != null)
 if (agreed.checked != null)
 if (agreed.checked != true) 
 {
  alert(agreedmsg);
  return Onepage.endValidation(false);
 }
 
 
var payment_id = Onepage.getPaymentId();
if (payment_id == 0)
 {
    var dd = document.getElementById('opc_missing_payment'); 
	if (dd != null)
	 {
	   alert(NO_PAYMENT_ERROR); 
	   return Onepage.endValidation(false);
	 }
 }
	
	var invalidf = new Array(); 
	if (op_logged_in == '1')
	{
	if (shipping_obligatory_fields.length > 0)
	{
	  var dd = document.getElementsByName('ship_to_info_id'); 
	  if (dd != null)
	  if (typeof dd.value != 'undefined')
	  if (dd.value != null)
	  if (dd.value == 'new')
	  {
	  invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf); 

	  }
	  
	  if (dd.length > 0)
	  if (dd[0] != null)
	  if (typeof dd[0].options != 'undefined')
	  {
	    
		var myval = dd[0].options[dd[0].selectedIndex].value; 
		if (myval == 'new')
		invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf); 
		else
		{
		    var dx = document.getElementById('ship_to_info_id_bt'); 
			var bt = ''; 
			if (dx != null)
			{
			  var bt = dx.value; 
			}
			if (myval != bt)
			{
			var d = document.getElementById('opc_st_changed_'+myval);
			if (d!=null)
			if (d.value != null)
			if (d.value == '1')
			 {
			   invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf); 
			 }
			}
		}
		
		
	  
	
	  
	  	  
 
	  
	}
	}
	}
	else
	if (Onepage.shippingOpen())
	{
	  invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf); 
	}
	
	if ((op_logged_in != '1'))
	{
	invalidf = Onepage.fastValidation('', op_userfields, wasValid, invalidf); 
	if (opc_debug)
	{
	 Onepage.op_log('fields valid: '); 
	 Onepage.op_log(wasValid); 
	}
	}
	else
	{
			var dx = document.getElementById('ship_to_info_id_bt'); 
			var bt = ''; 
			if (dx != null)
			{
			  var bt = dx.value; 
			}
			var d = document.getElementById('opc_st_changed_'+bt);
			if (d!=null)
			if (d.value != null)
			if (d.value == '1')
			 {
			   invalidf = Onepage.fastValidation('', op_userfields, wasValid, invalidf); 
			 }
		
	}
	if (invalidf.length > 0)
	 {
	   var msg = op_general_error; 
	   for (var i=0; i<invalidf.length; i++)
	     {
		  
		   if (typeof op_userfields_named[invalidf[i]] != 'undefined')
		    {
			  if (msg != op_general_error)
			  msg += ', '+op_userfields_named[invalidf[i]]; 
			  else msg += ' '+op_userfields_named[invalidf[i]]; 
			}
		 }
		 //we don't break validation once the name is not found as it may mean, it's not our field
		 if (msg != op_general_error)
		   {
		      alert(msg); 
			  return Onepage.endValidation(false);
		   }
		   
	 }
	
	if (!wasValid)
	{
	  alert(op_general_error); 
	  return Onepage.endValidation(false);
	}
 // we need to check email particularly
 if (!Onepage.emailCheckReg(true))
 {
 			     return Onepage.endValidation(false); 
 }
			
			 // need to check state also
			 var em = document.getElementById('virtuemart_state_id'); 
			 if (em != null)
			  {
			  if (em.className.indexOf('required')>=0)
			  {
			    if (em.options != null)
				var val = em.options[em.selectedIndex].value; 
				else
				if (em.value != null)
				var val = em.value
				else 
				var val = ''; 
				
				if ((val == '') || (val == 'none'))
				{
				// we need to check if an empty state value is valid
				
				// country:
				var elopc = document.getElementById('virtuemart_country_id'); 
				 if (elopc.options != null)
				var value = elopc.options[elopc.selectedIndex].value; 
				else
				if (elopc.value != null)
				var value = elopc.value; 

				var dtest = document.getElementById('state_for_'+value);
				 if (!(dtest != null)) 
						{
						  // validation is okay
						   em.className = em.className.split('invalid').join(''); 
						}
						else
						{
						   em.className += ' invalid'; 
						   alert(op_general_error);
						   return Onepage.endValidation(false); 
						
						}
				}
				}
			  }
			  if (Onepage.shippingOpen())
			   {
			       em = document.getElementById('shipto_virtuemart_state_id'); 
			 if (em != null)
			  {
			   if (em.className.indexOf('required')>=0)
			   {
			    if (em.options != null)
				var val = em.options[em.selectedIndex].value; 
				else
				if (em.value != null)
				var val = em.value
				else 
				var val = ''; 
				
				if ((val == '') || (val == 'none'))
				{
				// we need to check if an empty state value is valid
				
				// country:
				var elopc = document.getElementById('shipto_virtuemart_country_id'); 
				 if (elopc.options != null)
				var value = elopc.options[elopc.selectedIndex].value; 
				else
				if (elopc.value != null)
				var value = elopc.value; 

				var dtest = document.getElementById('state_for_'+value);
				 if (!(dtest != null)) 
						{
						  // validation is okay
						   em.className = em.className.split('invalid').join(''); 
						}
						else
						{
						   em.className += ' invalid'; 
						   alert(op_general_error);
						   return Onepage.endValidation(false); 
						
						}
				}
			}
			  }
			   }
			 
 var valid2 = true;
 // checks extensions functions
 if (callSubmitFunct != null)
 if (callSubmitFunct.length > 0)
 {
   for (var i = 0; i < callSubmitFunct.length; i++)
   {
     if (callSubmitFunct[i] != null)
     {
     
       if (typeof callSubmitFunct[i] == 'string' && 
        eval('typeof ' + callSubmitFunct[i]) == 'function') 
        {
          var valid3 = eval(callSubmitFunct[i]+'(true)'); 
          
          if (valid3 != null)
          if (!valid3) valid2 = false;
        }
     }
   }
 }
 
  //return false;
  
  
  if (valid2 != true) return Onepage.endValidation(false);
  if (wasValid != true) return Onepage.endValidation(false);
  
  
  // new in 208, tracking is not here: 
   
   return Onepage.endValidation(true); 
   
  //end
   Onepage.trackGoogleOrder();

  // lets differ submitting here to let google adwords to load
   if (typeof(acode) != 'undefined')
   if (acode != null)
   if (acode == '1')
     {
         op_timeout = new Date();
		 if (window.attachEvent && !window.addEventListener) {
		  return Onepage.endValidation(true); 
			//return true; 
		}
		else
         window.setTimeout('Onepage.checkIframeLoading()', 0);
		 // we don't triggere endValidatation as it would unblock the button
		 // the Onepage.endValidation is triggered by checkIframeLoading()
         return false;
     }


   
  

  
  
  
},

op_replace_select: function(dest, src)
{
  destel = document.getElementById(dest);
  if (destel != null)
  {
  destel.options.length = 0;
  srcel = document.getElementById(src); 
  if (srcel != null)
  {
  for (var i=0; i<srcel.options.length; i++)
   {
     var oOption = document.createElement("OPTION");
     //o = new Option(srcel.options[i].value, srcel.options[i].text); 
	 oOption.value = srcel.options[i].value; 
	 oOption.text = srcel.options[i].text;
     destel.options.add(oOption);
   }
   }
   else
   {
     var oOption = document.createElement("OPTION");
     //o = new Option(srcel.options[i].value, srcel.options[i].text); 
	 oOption.value = ''; 
	 oOption.text = ' - ';
     destel.options.add(oOption);
    
   }
   }
},


trackGoogleOrder: function()
{
  
 if (op_run_google == true)
 {
 var c1 = document.getElementById("city_field");
 var city = '';
 if (c1!=null) 
 {
  city = c1.value;
 }
 var c2 = document.getElementById("virtuemart_state_id");
 var state = '';
 if (c2!=null)
 {
  if (c2.selectedIndex != null)
  {
  var w = c2.selectedIndex;
  if (w != null) 
  if (w > -1)
  state = c2.options[w].text; 
  }
  else
  state = c2.value;
 }
 var c3 = document.getElementById("virtuemart_country_id");
 var country = '';
 if (c3 != null)
 {
  if (c3.selectedIndex != null)
  {
  var w = c3.selectedIndex;
  if (w != null) 
  if (w > -1)
  country = c3.options[w].text; 
  }
  else 
  {
  country = c3.value;
  }
 }
 if (state == ' - ') state = '';
 if (state == ' -= Select =- ') state = '';
 if (state == 'none') state = '';
 if (state == '-') state = '';
 // this function is not implemented
 if (!isNaN(parseFloat(op_tax_total)))
 op_tax_total = parseFloat(op_tax_total).toFixed(2);
 try
 {
 
 if (!((typeof pageTracker != 'undefined') && (pageTracker != null)))
 {
   if (typeof _gat != 'undefined')
   {
     pageTracker = _gat._getTrackerByName();
	 window.pageTracker = pageTracker; 
   }
 }
 
 if (typeof(window.pageTracker)=='object')
 {
 
 //alert(g_order_id+" "+op_vendor_name+" "+op_total_total+" "+op_tax_total+" "+op_ship_total+" "+city+" "+state+" "+country);
 if (opc_debug)
 {
   Onepage.op_log(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country); 
 }
 pageTracker._addTrans(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country );
 var ps = document.getElementsByName("prod_id");
 if (ps!=null)
 {
   for (i = 0; i<ps.length; i++)
   {
        var pid = ps[i].value;
        var sku = document.getElementById("prodsku_"+pid);
	var name = document.getElementById("prodname_"+pid);
	var cat = document.getElementById("prodcat_"+pid);
	var qu = document.getElementById("prodq_"+pid);
	var pp = document.getElementById("produprice_"+pid);
	if ((sku!=null) && (name!=null) && (cat!=null) && (qu!=null) && (pp!=null))
	{
	if (opc_debug)
	{
	Onepage.op_log(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value); 
	}
//	alert (g_order_id+" "+sku.value+" "+name.value+" "+cat.value+" "+pp.value+" "+qu.value);
 	pageTracker._addItem(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value);
 	}
   }
   pageTracker._trackTrans();
 }
 }
 
 else
 {
 
   if (window._gat && window._gat._getTracker)
   {
   if (opc_debug)
 {
   Onepage.op_log(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country); 
 }
   
	    _gaq.push(['_addTrans',
     g_order_id,           // order ID - required
     op_vendor_name,  // affiliation or store name
     op_total_total,          // total - required
     op_tax_total,           // tax
     op_ship_total,              // shipping
     city,       // city
     state,     // state or province
     country             // country
     ]);
     var ps = document.getElementsByName("prod_id");
 	if (ps!=null)
 	{
   		for (i = 0; i<ps.length; i++)
   		{
        var pid = ps[i].value;
        var sku = document.getElementById("prodsku_"+pid);
		var name = document.getElementById("prodname_"+pid);
		var cat = document.getElementById("prodcat_"+pid);
		var qu = document.getElementById("prodq_"+pid);
		var pp = document.getElementById("produprice_"+pid);

		if ((sku!=null) && (name!=null) && (cat!=null) && (qu!=null) && (pp!=null))
		{
if (opc_debug)
	{
	Onepage.op_log(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value); 
	}
 		
 		_gaq.push(['_addItem',
    	g_order_id,           // order ID - required
    	sku.value,           // SKU/code - required
    	name.value,        // product name
    	cat.value,   // category or variation
    	pp.value,          // unit price - required
    	qu.value               // quantity - required
  		]);

 		}
   		}
   
 	}
      _gaq.push(['_trackTrans']);

   }
 
 }
 }
 catch (e)
 {
   if (opc_debug) op_log(e); 
   
 }
 }
 // ok, lets track tracking code here
 //var td = document.getElementById('tracking_div');
 if (typeof(acode) != 'undefined')
 if (acode != null)
 if (acode == '1')
     {
 var tr_id = document.getElementById('tracking_div');
 if (typeof(tr_id) !== 'undefined' && tr_id != null)
 {
 	var html = '<iframe id="trackingIFrame" name="trackingFrame" src="'+op_securl+'?option=com_onepage&nosef=1&task=tracker&view=opc&format=opchtml&tmpl=component&controller=opc&amount='+op_total_total+'" height="50" width="400" frameborder="0"></iframe>';
        tr_id.innerHTML = html;
       
 }
  }
 return true;
},


checkIframeLoading: function() {
var date = new Date();
if (date - op_timeout > op_maxtimeout) op_semafor = true;
if (op_semafor == true) 
    {
        return Onepage.endValidation(true);
    }
	 if (window.attachEvent && !window.addEventListener) {
		  return Onepage.endValidation(true); 
			//return true; 
		}
		else
    window.setTimeout('Onepage.checkIframeLoading()', 300);
    return true;
},

// sets style.display to block for id
// and hide id2, id3, id4... etc... 
op_unhide: function(id)
{
 var x = document.getElementById(id);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'block';
 }
 
 for( var i = 1; i < arguments.length; i++ ) {
		
 id2 = arguments[i];
 if (id2 != null)
 {
 x = document.getElementById(id2);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'none';
 }
 }


	}
 
  // if we use it in a href we don't want to click on it, just unhide stuff
  
 return false;
},
// will unhide the first two
op_unhide2: function(id, id2)
{
 var x = document.getElementById(id);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'block';
 }
 var x = document.getElementById(id2);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'block';
 }   
 
 for( var i = 2; i < arguments.length; i++ ) {
		
 var id2 = arguments[i];
 
 if (id2 != null)
 {
 x = document.getElementById(id2);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'none';
 }
 }


	}
 
  // if we use it in a href we don't want to click on it, just unhide stuff
  
 return false;
},



inValidate: function(el)
{
  el.className = el.className+= ' invalid'; 
  if (opc_debug)
   {
     if (typeof el.name != 'undefined')
	 if (el.name != null)
     Onepage.op_log(el.name); 
   }
},
makedValidated: function(el)
{
  el.className = el.className.split('invalid').join(''); 
},

checkEmpty: function(el)
{
  if (el.disabled) return true; 
  if (el.type == 'radio')
  {
    var col = document.getElementsByName(el.name); 
	for (var i = 0; i<col.length; i++)
	 {
	   if (typeof col[i].checked != 'undefined')
	   if (col[i].checked) return false; 
	   if (typeof col[i].checked != 'undefined')
	   if (col[i].selected) return false; 
	 }
	 return true; 
  }
  
  if (el.name.indexOf('virtuemart_state')>=0) return false; 
  
  if (el.type == 'checkbox')
  {
	 if (el.checked) return false; 
	 return true; 
  }

  
  if (typeof el.value != 'undefined')
  if (el.value != null)
  {
   if (el.value == '') return true; 
   placeholder = Onepage.getAttr(el, 'placeholder');
   if (placeholder != null)
   if (el.value == placeholder) return true;
  }
  if (typeof el.options != 'undefined')
  if (typeof el.selectedIndex != 'undefined')
  {
  if (el.options.length <= 1) return false; 
  if (el.selectedIndex < 0) return true; 
  if (el.options[el.selectedIndex] == '') return true; 
  }
  return false; 
},

fastValidation: function(type, fields, valid, invalidf)
{
  
  if (!(fields != null)) fields = op_userfields; 
  if (type != null)
  {
        Onepage.op_log('entering validation...'); 
       for (var i=0; i<fields.length; i++)
	    {
		  // filter only needed
		  //if (((op_userfields[i].indexOf('shipto_')!=0) && (!isSh))  || (op_userfields[i].indexOf('shipto_')==0) && (isSh))
		  
		  // special case, shiping fields are not validated by opc: 
		  if ((type == '') && (fields[i].indexOf('shipto_')>=0)) continue; 
		  
		  if ((fields[i].indexOf(type)==0) || (type == '')) var cF = fields[i]; 
		  else var cF = type+fields[i]; 
		  
		  
		 
		   
		   {
		      
		     var elopc = document.getElementsByName(cF); 
			  
			 if (elopc != null)
			 
			 for (var j=0; j<elopc.length; j++)
			  if (elopc[j].className.indexOf('required')>=0)
			  {
			   // ie9 bug: 
			   if (elopc[j].name != cF) continue; 
			   if (elopc[j].name == 'name') continue; 
			   if (elopc[j].name == 'username') continue; 
			   if (elopc[j].name == 'password1') continue; 
			   if (elopc[j].name == 'password2') continue; 
			   if (elopc[j].name == 'email') continue; 
			   
			   switch (elopc[j].type)
			    {
				  case 'password':  break;
				  case 'text':
					if (Onepage.checkEmpty(elopc[j])) 
					{
					  Onepage.inValidate(elopc[j]);
					  invalidf.push(elopc[j].name); 
					  valid = false;
					}
					else Onepage.makedValidated(elopc[j]); 
					break; 
				  case 'select-one': 
				   
				   if (Onepage.checkEmpty(elopc[j])) 
					{
					  Onepage.inValidate(elopc[j]); 
					  invalidf.push(elopc[j].name); 
					  valid = false;
					}
					else Onepage.makedValidated(elopc[j]); 
					break; 
				   
				  case 'radio': 
				  if (Onepage.checkEmpty(elopc[j])) 
					{
					  Onepage.inValidate(elopc[j]); 
					  invalidf.push(elopc[j].name); 
					  valid = false;
					}
					else Onepage.makedValidated(elopc[j]); 
					break; 

				  case 'hidden': 
				    
					break; 
				 
				  default: 
				   if (Onepage.checkEmpty(elopc[j])) 
					{
					  Onepage.inValidate(elopc[j]); 
					  invalidf.push(elopc[j].name); 
					  valid = false;
					}
					else Onepage.makedValidated(elopc[j]); 
					break; 
				}
				Onepage.op_log('Validating: '+elopc[j].name+': '+valid.toString() ); 
				
			  }
			  
			  var elopc = document.getElementsByName(op_userfields[i]+'[]'); 
			  
			  var localtest = false; 
			  var sum = 0; 
			  if (elopc != null)
			  if (elopc.length > 0)
			  {
			   for (var j=0; j<elopc.length; j++)
			    if (elopc[j].className.indexOf('required')>=0)
			    {
				  // at least one from array must be selected
				   
				   if (!Onepage.checkEmpty(elopc[j])) sum++;
				   
				}
				
				if (elopc != null)
			   for (var j=0; j<elopc.length; j++)
			   if (elopc[j].className.indexOf('required')>=0)
			    if (sum == 0)
				{
				  var divd = document.getElementById(cF+'_div'); 
				  if (divd != null)
				  Onepage.inValidate(divd); 
			      Onepage.inValidate(elopc[j]); 
				  invalidf.push(elopc[j].name); 
				  valid = false;
				}
				else 
				{
				  var divd = document.getElementById(cF+'_div'); 
				  if (divd != null)
				  Onepage.makedValidated(divd); 

				  Onepage.makedValidated(elopc[j]); 
				}
			  }
			  
		   }
		}
  }
  if (valid != true) return invalidf; 
  return valid; 
},


processPlugins: function(data)
{
   
   if (data.length == 0) return; 
   for (var i=0; i<data.length; i++)
    {
	   var id = data[i].id; 
	   
	   if (typeof data[i].data == 'undefined') data[i].data = ''; 
	   if (!(data[i].data != null)) data[i].data = ''; 

	   if (typeof data[i].where == 'undefined') data[i].where = id; 
	   if (!(data[i].where != null)) data[i].where = id; 
	   

	   var html = data[i].data; 
	   if (html == '') 
	    {

		   var d = document.getElementById(id); 
		   if (d != null)
		    {
			  d.style.display = 'none'; 
			}
	   
		}
		else
		{
		   var d = document.getElementById(id); 
		   
		   if (d != null)
		    {
			
			  d.style.display = 'block'; 
			  
			  	d2 = document.getElementById(data[i].where); 
				d2.innerHTML = html; 
			}
		   
		}
	   
	}

   
},


op_openlink: function(el)
{
  if (el.className.indexOf('modal')>=0) return false; 
  window.open(el.href,'','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
  return false;
},

op_resizeIframe: function ()
{


if ((typeof parent != 'undefined') && (parent != null))
{
if (typeof parent.resizeIframe != 'undefined')
{
 parent.resizeIframe(document.body.scrollHeight);
}
}
},



updateProduct: function(el, quantity)
{
  
  if (typeof jQuery != 'undefined')
   {
   
     var b = jQuery( "#opc_basket" ); 
	 Onepage.jQueryLoader(b, false); 
   
   }
  var rel = Onepage.getAttr(el, 'rel'); 
  
  
  
  if (typeof quantity == 'undefined')
  var quantity = 0; 
  
  var hash = ''; 
  var cart_id = ''; 
  
  if (rel != null)
  {
  if (rel.toString().indexOf('|')>=0)
   {
		var arr = rel.split('|'); 
		cart_id = arr[0]; 
		hash = arr[1]; 
	}
	else
	{
	  cart_id = rel; 
	}

  if (hash != '')
  {
    // element change: 
	var d = document.getElementById('quantity_for_'+hash); 
	if (d != null) 
	el = d; 
	
  }
	
  if (typeof el.options != 'undefined')
  if (typeof el.selectedIndex != 'undefined')
   quantity = el.options[el.selectedIndex].value;  
   
  if (quantity == 0)
  if (typeof el.value != 'undefined')
  {
     quantity = el.value; 
  }
  
  }
  
  /* example of an ajax update input for
  \components\com_onepage\themes\icetheme_thestore_custom\overrides\update_form_ajax.tpl.php
  
  <input id="quantity_for_<?php echo md5($product->cart_item_id); ?>" value="<?php echo $product->quantity; ?>" type="text" onchange="Onepage.qChange(this);" name="quantity" rel="<?php echo $product->cart_item_id; ?>" id="stepper1" class="quantity" min="0" max="999999" size="2" data-role="none" />
  
  */
  
  if (cart_id == '') return; 
  
   
  cart_id = Onepage.op_escape(cart_id); 
  var cmd = 'update_product&cart_virtuemart_product_id='+cart_id+'&quantity='+quantity; 
  Onepage.op_log(cmd); 
  Onepage.op_runSS(this, false, true, cmd);
  
  return false; 
},

deleteProduct: function(el)
{
  
  return Onepage.updateProduct(el, 0); 
  var rel = Onepage.getAttr(el, 'rel'); 
  if (rel != null)
  if (rel != 0)
  {
  arr = rel.split('|'); 
  cart_id = arr[0]; 
  hash = arr[1]; 
  }
  cart_id = Onepage.op_escape(cart_id); 
  cmd = 'delete_product&cart_virtuemart_product_id='+cart_id; 
  Onepage.op_runSS(this, false, true, cmd);
  
  return false; 
},
op_login: function ()
{
 
 if (document.adminForm.username != null)
 {
 
 if (typeof document.adminForm.username_login != 'undefined')
 {
  document.adminForm.username.value = document.adminForm.username_login.value;
  uname = document.adminForm.username_login.value;
 }
 else
  {
    var d = document.getElementById('username_login'); 
	if (d != null)
	 {
	   document.adminForm.username.value = d.value;
	   uname = d.value; 
	 }
  }
 
 }
 else
 {
    var usern = document.createElement('input');
    usern.setAttribute('type', 'hidden');
    usern.setAttribute('name', 'username');
    usern.setAttribute('value', document.getElementById('username_login').value);
	uname = document.getElementById('username_login').value; 
    document.adminForm.appendChild(usern);
 }
 
 pwde = document.getElementById('passwd_login'); 
 if (pwde != null)
 pwd = pwde.value; 
 else
 {
   if (typeof document.adminForm.password != 'undefined')
     {
	   pwd = document.adminForm.password.value; 
	 }
	else
	if (typeof document.adminForm.passwd != 'undefined')
	 {
	   pwd = document.adminForm.passwd.value; 
	 }
	 else 
	 pwd = ''; 
 }
 if ((pwd.split(' ').join() == '') || (uname.split(' ').join()==''))
  {
    alert(op_general_error); 
	return false; 
  }  
 document.getElementById('opc_option').value = op_com_user;
 //document.adminForm.task.value = op_com_user_task;
 document.getElementById('opc_task').value = op_com_user_task;
 
 document.adminForm.action = op_com_user_action;
 document.adminForm.controller.value = 'user'; 
 document.adminForm.view.value = ''; 
 
 document.adminForm.submit();
 return false;
},
submitenter: function(el, e)
{
 var charCode;
    
    if(e && e.which){
        charCode = e.which;
    }else if(window.event){
        e = window.event;
        charCode = e.keyCode;
    }


if (charCode == 13)
   {
   Onepage.op_login();
   return false;
   }
else
   return true;
},



op_showEditST2: function()
{
  // edit_address_list_st_section
  // edit_address_st_section
  d1 = document.getElementById('edit_address_list_st_section'); 
  if (d1 != null)
  d1.style.display = 'none'; 
  
  d2 = document.getElementById('edit_address_st_section'); 
  if (d2 != null)
  d2.style.display = 'block'; 
  
  return false; 
},

op_showEditST: function(id)
{
   var d = document.getElementById('opc_st_'+id); 
   if (d != null)
   d.style.display = 'none'; 
   
   var els = document.getElementsByName('st_complete_list'); 
   for (var i=0; i<els.length; i++)
     {
	   var lid = els[i].value; 
	   if (lid == id) continue; 
	   d = document.getElementById('opc_stedit_'+lid); 
	   if (d != null)
	   d.style.display = 'none'; 
	 }
   d = document.getElementById('opc_stedit_'+id); 
   if (d != null)
   d.style.display = 'block'; 
   
   d = document.getElementById('opc_st_changed_'+id);
   if (d!=null)
   if (d.value != null)
   d.value = '1'; 
   
   return false; 
   
},

changeST: function(el)
{
  if (el.options != null)
  if (el.selectedIndex != null)
   {
      var user_info_id = el.options[el.selectedIndex].value; 
	  var d = document.getElementById('hidden_st_'+user_info_id); 
	  var changed = document.getElementById('opc_st_changed_'+user_info_id); 
	  var bt = document.getElementById('ship_to_info_id_bt'); 
	  var sa = document.getElementById('sachone');
	  if (bt != null)
	  if (bt.value == user_info_id)
	   {
	     // the selected ship to is bt address
		 
		 sa.value = ''; 
		 sa.setAttribute('checked', false); 
		 eval('sa.checked=false'); 
		
		 
	   }
	   else
	    {
		  sa.value = 'adresaina'; 
		  sa.setAttribute('checked', true); 
		  eval('sa.checked=true'); 
		}
	   
	  
	 
	  
	  if (d != null)
	   {
	     var d2 = document.getElementById('edit_address_list_st_section'); 
		 html = d.innerHTML; 
		 html = html.split('REPLACE'+user_info_id+'REPLACE').join(''); 
		 d2.innerHTML = html;
		 
		  if (changed.value == 1)
	  {
	   // Onepage.op_showEditST(user_info_id); 
	  }
		 
	   }
   }
   Onepage.op_runSS(el); 
},

send_special_cmd: function(el, cmd)
{
  
  Onepage.op_runSS(el, false, true, cmd); 
  return false; 
},

refreshPayment: function()
{
  Onepage.op_runSS(null, false, false, 'runpay');
},

setKlarnaAddress: function(address)
{
  

  if (address != null)
    { ;; } else return;
	
   
  d = document.getElementById('email_field'); 
  if (d != null)
  if (address.email != null)
  if (address.email != '')
  //if (d.value == '')
  d.value = address.email; 

  d = document.getElementById('phone_1_field'); 
  if (d != null)
  if (address.telno != null)
  if (address.telno != '')
  //if (d.value == '')
  d.value = address.telno; 

  d = document.getElementById('first_name_field'); 
  if (d != null)
  if (address.fname != null)
  if (address.fname != '')
  //if (d.value == '')
  d.value = address.fname; 

  d = document.getElementById('company_name_field'); 
  if (d != null)
  if (address.company != null)
  if (address.company != '')
  //if (d.value == '')
  d.value = address.company; 

  d = document.getElementById('last_name_field'); 
  if (d != null)
  if (address.lname != null)
  if (address.lname != '')
  //if (d.value == '')
  d.value = address.lname; 
  
  d = document.getElementById('zip_field'); 
  if (d != null)
  if (address.zip != null)
  if (address.zip != '')
  //if (d.value == '')
  d.value = address.zip; 
  
   d = document.getElementById('city_field'); 
  if (d != null)
  if (address.city != null)
  if (address.city != '')
  //if (d.value == '')
  d.value = address.city; 
  
  d = document.getElementById('address_1_field'); 
  if (d != null)
  if (address.street != null)
  if (address.street != '')
  //if (d.value == '')
  {
   d.value = address.street; 
   if (address.house_number != null)
   if (address.house_number != '')
   d.value += ' '+address.house_number;
   
   if (address.house_extension != null)
   if (address.house_extension != '')
   d.value += ' '+address.house_extension;
  
  }
  
  
  
 
},
// this function is used when you need to get rid of a javascript within opc's themes and you are using $html = str_replace('op_runSS', 'op_doNothing', $html);
// returns false so there is no form submission or action 
// use op_doNothing2 to allow return action such as link redirect or similar
op_doNothing: function()
{
  return false; 
},
op_doNothing2: function()
{
  return true; 
},
showFields: function( show, fields ) {

		   	if( fields ) {
			var d = null; 
			var found = false; 
		   		for (i=0; i<fields.length;i++) {
		   			if( show ) {
		   				d = document.getElementById( fields[i] + '_div' );
						if (d != null)
						{
						found = true; 
						d.style.display = '';
						}
		   				d = document.getElementById( fields[i] + '_input' );
						if (d != null)
						d.style.display = '';
						
						if (!found)
						 {
						   // registration page, not opc: 
						   var d = document.getElementById( fields[i]+'_field'); 
						   if (d != null)
						   if (typeof d.parentNode != 'undefined')
						   {
						    var p1 = d.parentNode; 
						    if (p1 != null)
							 {
							   if (typeof p1.parentNode != 'undefined')
							   if (p1.parentNode != null)
							   {
							   var p2 = p1.parentNode; 
							   if (p2 != null)
							     {
								   p2.style.display = ''; 
								 }
							   }
							 }
						   }
						 }
						
		   			} else {
		   				d = document.getElementById( fields[i] + '_div' );
						
						if (d != null)
						{
						found = true; 
						d.style.display = 'none';
						}
		   				d = document.getElementById( fields[i] + '_input' );
						if (d!=null)
						d.style.display = 'none';
						
						if (!found)
						 {
						   // registration page, not opc: 
						   var d = document.getElementById( fields[i]+'_field'); 
						   if (d != null)
						   if (typeof d.parentNode != 'undefined')
						   {
						    var p1 = d.parentNode; 
						    if (p1 != null)
							 {
							   if (typeof p1.parentNode != 'undefined')
							   if (p1.parentNode != null)
							   {
							   var p2 = p1.parentNode; 
							   if (p2 != null)
							     {
								   p2.style.display = 'none'; 
								 }
							   }
							 }
						   }
						 }
		   			}
		   		}
		   	}
			return true; 
		   },
		   
getPaymentElement: function()
{
		  // get active shipping rate
	  var e = document.getElementsByName("virtuemart_paymentmethod_id");
	  if (!(e != null)) return; 
	  
	  //var e = document.getElementsByName("payment_method_id");
	  
	  
	  var svalue = "";
	 
	  
	  if (e.type == 'select-one')
	  {
	   ind = e.selectedIndex;
	   if (ind<0) ind = 0;
	   value = e.options[ind].value;
	   return e.options[ind];
	  }
	  
	  
	  if (e)
      if (e.checked)
	  {
	    return e; 
	    svalue = e.value;

	  }
	  else
	  {

	  for (i=0;i<e.length;i++)
	  {
	   if (e[i].type == 'select-one')
	  {
	  if (e[i].options.length <= 0) return ""; 
	   ind = e[i].selectedIndex;
	   if (ind<0) ind = 0;
	   value = e[i].options[ind].value;
	   return e[i].options[ind];
	  }
	  
	   if (e[i].checked==true)
	     return e[i];
	  }
	  }
	    
	    
	    // last resort for hidden and not empty values of payment methods:
	   for (i=0;i<e.length;i++)
	   {
	    if (e[i].value != '')
	  {
	    if (e[i].id != null && (e[i].id != 'payment_method_id_coupon'))
	    return e[i];
	  }
	    }
		
	    return null

},
setOpcId: function()
{
  el = Onepage.getPaymentElement(); 
  if (!(el!=null)) return; 
  
  d = document.getElementById('opc_payment_method_id'); 
  
  atr = Onepage.getAttr(el, 'id'); 
  if ((atr != null) && (atr != "") && (atr != 0))
    {
	   
	   if (d!=null)
	   d.value = atr; 
	   return;
	}
  if (d!=null)
  d.value = ''; 
  return;

},
username_check_return: function(exists)
{
  op_user_name_checked = true; 
  d = document.getElementById('username_already_exists'); 
  if (d != null)
  if (exists)
   {
	 if (opc_no_duplicit_username) last_username_check  = false; 
     d.style.display = 'block'; 
   }
  else
   {
	if (opc_no_duplicit_username) last_username_check  = true; 
    d.style.display = 'none'; 
   }
},
email_check_return: function(exists)
{
  op_email_checked = true; 
  d = document.getElementById('email_already_exists'); 
  if (d != null)
   if (exists)
   {
	 if (opc_no_duplicit_email) last_email_check  = false; 
     d.style.display = 'block'; 
   }
  else
   {
	if (opc_no_duplicit_email) last_email_check  = true; 
    d.style.display = 'none'; 
   }

},
username_check: function(el)
{
  //username_already_exists
  Onepage.op_runSS(el, false, true, 'checkusername');
  return true; 
},
email_check: function(el)
{
  //email_already_exists
  Onepage.op_runSS(el, false, true, 'checkemail');
  return true; 
},

getAttr: function(ele, attr) {
	   
        var result = (ele.getAttribute && ele.getAttribute(attr)) || null;
        if( !result ) {
            var attrs = ele.attributes;
            var length = attrs.length;
            for(var i = 0; i < length; i++)
                if(attrs[i].nodeName === attr)
                    result = attrs[i].nodeValue;
        }
        return result;
    },
	

qChange: function(el)  {
  return Onepage.updateProduct(el);
  console.log(el.value); 
},
	

 last_payment_extra: '',
 last_dymamic: new Array()

}


/* support for async loading */
if (typeof jQuery != 'undefined' && (jQuery != null))
			{
			 jQuery(document).ready(function() {

			 if (typeof Onepage.op_runSS == 'undefined') return;
			  Onepage.op_runSS('init'); 
 		  		 
				 
			
	});
			
			}
			else
			 {
			   if ((typeof window != 'undefined') && (typeof window.addEvent != 'undefined'))
			   {
			   window.addEvent('domready', function() {
			   
			      Onepage.op_runSS('init'); 
			    });
			   }
			   else
			   {
			     if(window.addEventListener){ // Mozilla, Netscape, Firefox
			window.addEventListener("load", function(){ Onepage.op_runSS('init', false, true, null ); }, false);
			 } else { // IE
			window.attachEvent("onload", function(){ Onepage.op_runSS('init', false, true, null); });
			 }
			   }
			 }