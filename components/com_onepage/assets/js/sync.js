var callSubmitFunct = new Array(); 
var callAfterPaymentSelect = new Array();  
var callAfterShippingSelect = new Array(); 
var callBeforePaymentSelect = new Array(); 
var callBeforeAjax = new Array(); 
var callAfterAjax = new Array();
var opcHashTable = new Array(); 
	
	// can override shiping div for innerhtml
var callAfterResponse = new Array(); 
	// alters loader image
var callBeforeLoader = new Array(); 
var callAfterRender = new Array(); 
// this is a timer
var opcsubmittimer = null; 


function addOpcTriggerer(name, value)
{
  // prevent duplicit inclusion of triggers
  for(var i = 0; i < opcHashTable.length; i++) {
     if (opcHashTable[i] == name+value) return; 
   }
   // add triggerer to hash table
   opcHashTable.push(name+value);

   // create the triggerer
   eval(name+'.push(value)');    
   

}

if (typeof jQuery != 'undefined')
{
(function($){
	var undefined,
	methods = {
		list: function(options) {
			
		},
		update: function() {
		},
		addToList: function() {
			
		}
	};

	$.fn.vm2frontOPC = function( method ) {
 
	};
})(jQuery)
}

function op_log(msg)
{
  return Onepage.op_log(msg); 
}

function toggleVis(obj)
{
 var elopc= document.getElementById(obj);
 if (elopc.style.display != 'none')
 {
  elopc.style.display = 'none';
 
 }
 else
 {
  elopc.style.display = '';
 }
}

function changeSemafor()
{
     
    op_semafor = true;
}
function op_unhide(el1, el2, el3)
{
  return Onepage.op_unhide(el1, el2, el3); 
}
function op_unhide2(el1, el2)
{
  return Onepage.op_unhide2(el1, el2); 
}
function op_login()
{
  return Onepage.op_login(); 
}