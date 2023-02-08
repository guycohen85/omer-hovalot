/*
* This file sends feedback to your OPC if a tracking event was sucessfully shown
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

function opc_pingDone(url, data)
{
if ((typeof t_xmlhttp2 != 'undefined') && (t_xmlhttp2 != null))
	{
	 
	}
	else
	{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     var t_xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	 var t_xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
	}
    if (t_xmlhttp2!=null)
    {
	 
	 t_xmlhttp2.open("POST", url, false);
     //Send the proper header information along with the request
     t_xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     t_xmlhttp2.onreadystatechange= function() { 
		if ((typeof t_xmlhttp2 != 'undefined') && (t_xmlhttp2 != null))
		{
		
		console.log(t_xmlhttp2); 
		}
	 } ;
     t_xmlhttp2.send(data); 
	}
}	