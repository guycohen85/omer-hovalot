(function(){tinymce.create("tinymce.plugins.VitabookEmoticonsLegacyPlugin",{init:function(e,t){var n=this;var r=e.dom;e.addCommand("mceVitabookEmoticonsLegacy",function(){e.windowManager.open({file:t+"/dialog.htm",width:200,height:75,inline:1},{plugin_url:t})});e.addButton("vitabookemoticonsLegacy",{title:"Vitabook Emoticons",cmd:"mceVitabookEmoticonsLegacy",image:t+"/img/vitabookemoticons.png"});if(n.isIE()!==false&&n.isIE()<9)return true;e.onKeyDown.add(function(e,r){if(r.keyCode==13)return n.handleEnter(e,t)});e.onKeyUp.add(function(e,r){if(r.keyCode==32)return n.handleSpacebar(e,t)})},handleSpacebar:function(e,t){this.parseCurrentLine(e,0,"",t)},handleEnter:function(e,t){this.parseCurrentLine(e,-1,"",t)},isIE:function(){var e=navigator.userAgent.toLowerCase();return e.indexOf("msie")!=-1?parseInt(e.split("msie")[1]):false},parseCurrentLine:function(e,t,n,r){function L(t){a=e.selection.getBookmark();e.selection.setRng(i);tinyMCE.execCommand("mceInsertContent",false,'<img src="'+t+'" alt="" border="0" class="smiley" />');e.selection.moveToBookmark(a)}var i,s,o,u,a,f,l,c,h;r=r+"/img/smilies/";i=e.selection.getRng().cloneRange();if(i.startOffset<2){c=i.endContainer.previousSibling;if(c==null){if(i.endContainer.firstChild==null||i.endContainer.firstChild.nextSibling==null)return;c=i.endContainer.firstChild.nextSibling}h=c.length;i.setStart(c,h);i.setEnd(c,h);if(i.endOffset<2)return;s=i.endOffset;u=c}else{u=i.endContainer;if(u.nodeType!=3&&u.firstChild){while(u.nodeType!=3&&u.firstChild)u=u.firstChild;i.setStart(u,0);i.setEnd(u,u.nodeValue.length)}if(i.endOffset==1)s=2;else s=i.endOffset-1-t}o=s;do{i.setStart(u,s-2);i.setEnd(u,s-1);s-=1}while(i.toString()!=" "&&i.toString()!=""&&i.toString().charCodeAt(0)!=160&&s-2>=0&&i.toString()!=n);if(i.toString()==n||i.toString().charCodeAt(0)==160){i.setStart(u,s);i.setEnd(u,o);s+=1}else if(i.startOffset==0){i.setStart(u,0);i.setEnd(u,o)}else{i.setStart(u,s);i.setEnd(u,o)}f=i.toString();l=f.match(/^(:-?\)|:-?\(|:-?P|;-?\)|:-?D|:-?8|:zzz|:-?@|:roll|:eek|:sigh|:-?\?|;-?\(|:-?x)$/i);if(l){var p=/:-?\)/i;var d=/:-?\(/i;var v=/:-?P/i;var m=/;-?\)/i;var g=/:-?D/i;var y=/:-?8/;var b=/:zzz/;var w=/:-?@/;var E=/:roll/;var S=/:eek/;var x=/:sigh/;var T=/:-?\?/;var N=/;-?\(/;var C=/:-?x/;if(p.test(l[1])){var k=r+"sm_smile.gif";L(k)}else if(d.test(l[1])){var k=r+"sm_mad.gif";L(k)}else if(v.test(l[1])){var k=r+"sm_razz.gif";L(k)}else if(m.test(l[1])){var k=r+"sm_wink.gif";L(k)}else if(g.test(l[1])){var k=r+"sm_biggrin.gif";L(k)}else if(y.test(l[1])){var k=r+"sm_cool.gif";L(k)}else if(b.test(l[1])){var k=r+"sm_sleep.gif";L(k)}else if(w.test(l[1])){var k=r+"sm_upset.gif";L(k)}else if(E.test(l[1])){var k=r+"sm_rolleyes.gif";L(k)}else if(S.test(l[1])){var k=r+"sm_bigeek.gif";L(k)}else if(x.test(l[1])){var k=r+"sm_sigh.gif";L(k)}else if(T.test(l[1])){var k=r+"sm_confused.gif";L(k)}else if(N.test(l[1])){var k=r+"sm_cry.gif";L(k)}else if(C.test(l[1])){var k=r+"sm_dead.gif";L(k)}}},getInfo:function(){return{longname:"Vitabook Emoticons Plugin",author:"JoomVita",authorurl:"http://www.joomvita.com",infourl:"http://www.joomvita.com",version:"1.0"}}});tinymce.PluginManager.add("vitabookemoticonsLegacy",tinymce.plugins.VitabookEmoticonsLegacyPlugin)})()