jQuery.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

(function() {
    function htmlspecialchars(string, quote_style, charset, double_encode) {
        //       discuss at: http://phpjs.org/functions/htmlspecialchars/
        //      original by: Mirek Slugen
        //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        //      bugfixed by: Nathan
        //      bugfixed by: Arno
        //      bugfixed by: Brett Zamir (http://brett-zamir.me)
        //      bugfixed by: Brett Zamir (http://brett-zamir.me)
        //       revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        //         input by: Ratheous
        //         input by: Mailfaker (http://www.weedem.fr/)
        //         input by: felix
        // reimplemented by: Brett Zamir (http://brett-zamir.me)
        //             note: charset argument not supported
        //        example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
        //        returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
        //        example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
        //        returns 2: 'ab"c&#039;d'
        //        example 3: htmlspecialchars('my "&entity;" is still here', null, null, false);
        //        returns 3: 'my &quot;&entity;&quot; is still here'

        var optTemp = 0,
            i = 0,
            noquotes = false;
        if (typeof quote_style === 'undefined' || quote_style === null) {
            quote_style = 2;
        }
        string = string.toString();
        if (double_encode !== false) { // Put this first to avoid double-encoding
            string = string.replace(/&/g, '&amp;');
        }
        string = string.replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        var OPTS = {
            'ENT_NOQUOTES': 0,
            'ENT_HTML_QUOTE_SINGLE': 1,
            'ENT_HTML_QUOTE_DOUBLE': 2,
            'ENT_COMPAT': 2,
            'ENT_QUOTES': 3,
            'ENT_IGNORE': 4
        };
        if (quote_style === 0) {
            noquotes = true;
        }
        if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
            quote_style = [].concat(quote_style);
            for (i = 0; i < quote_style.length; i++) {
                // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
                if (OPTS[quote_style[i]] === 0) {
                    noquotes = true;
                } else if (OPTS[quote_style[i]]) {
                    optTemp = optTemp | OPTS[quote_style[i]];
                }
            }
            quote_style = optTemp;
        }
        if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
            string = string.replace(/'/g, '&#039;');
        }
        if (!noquotes) {
            string = string.replace(/"/g, '&quot;');
        }

        return string;
    }

	tinymce.create('tinymce.plugins.flynsyntaxcodemodal', {
		init: function(editor, url) {
            // Register commands, mceMyown is name of command to be executed.
			editor.addCommand('flynsyntaxshowmodal', function() {
                // Calls the pop-up modal
                editor.windowManager.open({
                    // Modal settings
                    title: 'Insert Code Block',
                    width: jQuery( window ).width() * 0.7,
                    // minus head and foot of dialog box
                    height: (jQuery( window ).height() - 36 - 50) * 0.7,
                    inline: 1,
                    id: 'plugin-slug-insert-dialog',
                    file: ajaxurl + '?action=flyn-syntax-code-modal',
                    buttons: [
                        {
                            text: 'Insert',
                            id: 'plugin-slug-button-insert',
                            class: 'insert',
                            onclick: function( e ) {
                                var frame = e.currentTarget.getElementsByTagName('iframe')[0].contentWindow;
                                var options = jQuery(frame.document.body).find(':input').serializeObject();
                                var html = frame.editor.getValue();

                                if ( !options.escaped )
                                {
                                    options.escaped = 'true';
                                    html = htmlspecialchars(html);
                                }

                                var elem = document.createElement('pre');
                                jQuery.each(options, function(key, value) {
                                    if ( value )
                                        elem.setAttribute(key, value);
                                });
                                elem.innerHTML = html;

                                editor.insertContent(elem.outerHTML);
                                editor.windowManager.close();
                            },
                        },
                        {
                            text: 'Cancel',
                            id: 'plugin-slug-button-cancel',
                            onclick: 'close'
                        }
                    ],
                });
			});

			// Register buttons,this is the button will be displayed on wordpress rich editor
			editor.addButton('flynsyntaxcodemodal', {
                title: 'Insert code block',
                cmd: 'flynsyntaxshowmodal',
                image: url + '/../images/code-icon.png'
            });
		},

		getInfo: function() {
			return {
				longname : 'Flyn-Syntax Code Modal',
				author : 'Flyn San',
				authorurl : 'http://www.flynsarmy.com',
				infourl : 'http://www.flynsarmy.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('flynsyntaxcodemodal', tinymce.plugins.flynsyntaxcodemodal);
})();