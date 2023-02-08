var AdminExile = new Class({
    Implements:[Events,Options],
    options: {
        invalidChars: {
            32 : 'SPACE',       // Space
            34 : 'QUOTE',       // "
            35 : 'POUND',       // #
            36 : 'DOLLAR',      // $
            37 : 'PERCENT',     // %
            38 : 'AMPERSAND',   // &
            43 : 'PLUS',        // +
            44 : 'COMMA',       // ,
            47 : 'FORWARDSLASH',// /
            58 : 'COLON',       // :
            59 : 'SEMICOLON',   // ;
            60 : 'LESSTHAN',    // <
            61 : 'EQUALS',      // =
            62 : 'GREATERTHAN', // >
            63 : 'QUESTION',    // ?
            64 : 'AT',          // @
            91 : 'LEFTBRACKET', // [
            92 : 'BACKSLASH',   // \
            93 : 'RIGHTBRACKET',// ]
            94 : 'CARAT',       // ^
            96 : 'GRAVE',       // `
            123: 'LEFTCURLY',   // {
            125: 'RIGHTCURLY',  // }
            124: 'PIPE',        // |
            126: 'TILDE'        // ~
        },
        version:2.5,
        parentelement:'li'
    },
    initialize:function(options){
        var self = this;
        self.setOptions(options);
        Joomla.JText.load();
        // identify parent element for show/hide
        switch(options.version) {
            case '2.5':
                self.options.parentelement = 'li';
                break;
            default:
                self.options.parentelement = 'div.control-group';
                break;
        }
        // set up input filter
        ['jform_params_key','jform_params_keyvalue'].each(function(item){
            document.id(item).addEvent('keyup',function(){
                self.testInput(item,document.id(item).value);
            });
        });
        // set up param hiders
        document.id('jform_params_twofactor').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.twoFactorParams();
            });
        });
        self.twoFactorParams();
        document.id('jform_params_tmpwhitelist').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.tmpWhitelistParams();
            });
        });
        self.tmpWhitelistParams();
        
        // set up redirects
        document.id('jform_params_redirect').addEvent('keyup',function(){
            self.redirect404();
        });
        self.redirect404();
        
        document.id('jform_params_frontrestrict').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.frontendRestrictParams();
            });
        });
        self.frontendRestrictParams();
        document.id('jform_params_maillink').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.mailLinkParams();
            });
        });
        self.mailLinkParams();
        document.id('jform_params_ipsecurity').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.ipSecurityParams();
            });
        });
        self.ipSecurityParams();
        document.id('jform_params_bruteforce').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.bruteForceParams();
            });
        });
        self.bruteForceParams();
        $$('.removeblock').each(function(el){
            el.addEvent('click',function(e){
                e.preventDefault();
                uri = new URI(window.location);
                uri.set('query',undefined);
                data = JSON.decode(this.getProperty('data-block'));
                data['adminexile_removeblock']='true';
                var removeRequest = new Request.JSON({
                    url:uri.toString(),
                    onSuccess:function(response){
                        if(response.success) {
                            el.getParent('tr').destroy();
                        }
                    }
                }).get(data);
            });
        });
        self.yourURL();
    },
    yourURL:function(){        
        var adminurl = new URI(window.location);  
        if(document.id('jform_params_twofactor0').checked) {       
            adminurl.setData({});           
            adminurl.set('query',document.id('jform_params_key').value);
        } else {
            var data = {};
            data[document.id('jform_params_key').value]=document.id('jform_params_keyvalue').value;
            adminurl.setData(data);
        }
        target = document.id('jform_params_url-lbl').getParent(this.options.parentelement).getElements('span.after')[0];
        target.empty();
        var anchor = new Element('a',{href:adminurl,html:adminurl}).inject(target,'top');
    },
    testInput:function(type,str){
        var self = this;
        if(type === 'jform_params_key' && (/^[0-9]+$/.test(str))) {
            document.id(type).value='';
            alert(Joomla.JText._('PLG_SYS_ADMINEXILE_MESSAGE_NOTNUMERIC'));
            return;
        }
        if(!(/^[\040-\177]*$/.test(str))) {
            while(!(/^[\040-\177]*$/.test(str))) for(i=0;i<=(str.length-1);i++) 
                if(!(/^[\040-\177]*$/.test(str.charAt(i)))) 
                    document.id(type).value = str.replace(str.charAt(i),''); 
            alert(Joomla.JText._('PLG_SYS_ADMINEXILE_MESSAGE_INVALIDASCII'));
            return;
        }
        for(i=0;i<=(str.length-1);i++) {
            if(self.options.invalidChars.hasOwnProperty(str.charCodeAt(i))) {
                document.id(type).value = str.replace(str.charAt(i),'');
                alert(Joomla.JText._('PLG_SYS_ADMINEXILE_MESSAGE_INVALIDCHAR') + "\n\n" + self.validCharsMessage());
                return;
            }
        }
        self.yourURL();
    },
    validCharsMessage:function(){
        var self = this;
        var str = [];
        Object.each(self.options.invalidChars,function(value,key){
            str.push(String.fromCharCode(key)+ '\t:\t' + Joomla.JText._('PLG_SYS_ADMINEXILE_CHAR_'+value));
        });
        str = str.join('\n');
        return str;
    },
    twoFactorParams:function(){
        var self = this;
        if(document.id('jform_params_twofactor1').checked) {
            document.id('jform_params_keyvalue').getParent(self.options.parentelement).show();
        } else {
            document.id('jform_params_keyvalue').getParent(self.options.parentelement).hide();            
        }
        self.yourURL();
    },
    tmpWhitelistParams:function(){
        var self = this;
        if(document.id('jform_params_tmpwhitelist1').checked) {
            document.id('jform_params_tmpperiod').getParent(self.options.parentelement).show();
        } else {
            document.id('jform_params_tmpperiod').getParent(self.options.parentelement).hide();            
        }
    },
    redirect404:function(){
        var self = this;
        if(document.id('jform_params_redirect').value === '{404}') {
            document.id('jform_params_fourofour').getParent(self.options.parentelement).show();
        } else {
            document.id('jform_params_fourofour').getParent(self.options.parentelement).hide();            
        }
    },
    frontendRestrictParams:function(){
        var self = this;
        if(document.id('jform_params_frontrestrict1').checked) {
            document.id('jformparamsrestrictgroup').getParent(self.options.parentelement).show();
        } else {
            document.id('jformparamsrestrictgroup').getParent(self.options.parentelement).hide();            
        }
    },
    mailLinkParams:function(){
        var self = this;
        if(document.id('jform_params_maillink1').checked) {
            document.id('jformparamsmaillinkgroup').getParent(self.options.parentelement).show();
        } else {
            document.id('jformparamsmaillinkgroup').getParent(self.options.parentelement).hide();            
        }
    },
    ipSecurityParams:function(){
        var self = this;
        if(document.id('jform_params_ipsecurity1').checked) {
            $$('.ipsecurity').each(function(el){el.getParent(self.options.parentelement).show();});
        } else {
            $$('.ipsecurity').each(function(el){el.getParent(self.options.parentelement).hide();});          
        }
    },
    bruteForceParams:function(){
        var self = this;
        if(document.id('jform_params_bruteforce1').checked) {
            $$('.bruteforce').each(function(el){el.getParent(self.options.parentelement).show();});
        } else {
            $$('.bruteforce').each(function(el){el.getParent(self.options.parentelement).hide();});          
        }
    } 
});
window.addEvent('domready',function(){
    var ae = new AdminExile(window.plg_sys_adminexile_config);
});