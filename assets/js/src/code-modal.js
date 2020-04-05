jQuery.fn.serializeObject = function() {
    const o = {};
    const a = this.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || "");
        } else {
            o[this.name] = this.value || "";
        }
    });
    return o;
};

(function() {
    function htmlspecialchars(string, quoteStyle, charset, doubleEncode) {
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

        let optTemp = 0,
            i = 0,
            noquotes = false;
        if (typeof quoteStyle === "undefined" || quoteStyle === null) {
            quoteStyle = 2;
        }
        string = string.toString();
        if (doubleEncode !== false) {
            // Put this first to avoid double-encoding
            string = string.replace(/&/g, "&amp;");
        }
        string = string.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        const OPTS = {
            ENT_NOQUOTES: 0,
            ENT_HTML_QUOTE_SINGLE: 1,
            ENT_HTML_QUOTE_DOUBLE: 2,
            ENT_COMPAT: 2,
            ENT_QUOTES: 3,
            ENT_IGNORE: 4
        };
        if (quoteStyle === 0) {
            noquotes = true;
        }
        if (typeof quoteStyle !== "number") {
            // Allow for a single string or an array of string flags
            quoteStyle = [].concat(quoteStyle);
            for (i = 0; i < quoteStyle.length; i++) {
                // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
                if (OPTS[quoteStyle[i]] === 0) {
                    noquotes = true;
                } else if (OPTS[quoteStyle[i]]) {
                    optTemp = optTemp | OPTS[quoteStyle[i]];
                }
            }
            quoteStyle = optTemp;
        }
        if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
            string = string.replace(/'/g, "&#039;");
        }
        if (!noquotes) {
            string = string.replace(/"/g, "&quot;");
        }

        return string;
    }

    tinymce.create("tinymce.plugins.flynsyntaxcodemodal", {
        init(editor, url) {
            // Register commands, mceMyown is name of command to be executed.
            editor.addCommand("flynsyntaxshowmodal", function() {
                // Calls the pop-up modal
                editor.windowManager.open({
                    // Modal settings
                    title: "Insert Code Block",
                    width: jQuery(window).width() * 0.7,
                    // minus head and foot of dialog box
                    height: (jQuery(window).height() - 36 - 50) * 0.7,
                    inline: 1,
                    id: "plugin-slug-insert-dialog",
                    file: ajaxurl + "?action=flyn-syntax-code-modal",
                    buttons: [
                        {
                            text: "Insert",
                            id: "plugin-slug-button-insert",
                            class: "insert",
                            onclick(e) {
                                const frame = e.currentTarget.getElementsByTagName(
                                    "iframe"
                                )[0].contentWindow;
                                const options = jQuery(frame.document.body)
                                    .find(":input")
                                    .serializeObject();
                                let html = frame.editor.getValue();

                                html = htmlspecialchars(html);

                                const elem = document.createElement("pre");
                                jQuery.each(options, function(key, value) {
                                    if (value) elem.setAttribute(key, value);
                                });
                                elem.setAttribute("escaped", "true");
                                elem.innerHTML = html;

                                editor.insertContent(
                                    elem.outerHTML + "\n\n&nbsp;"
                                );
                                editor.windowManager.close();
                            }
                        },
                        {
                            text: "Cancel",
                            id: "plugin-slug-button-cancel",
                            onclick: "close"
                        }
                    ]
                });
            });

            // Register buttons,this is the button will be displayed on wordpress rich editor
            editor.addButton("flynsyntaxcodemodal", {
                title: "Insert code block",
                cmd: "flynsyntaxshowmodal",
                image: url + "/../images/code-icon.png"
            });
        },

        getInfo() {
            return {
                longname: "Flyn-Syntax Code Modal",
                author: "Flyn San",
                authorurl: "http://www.flynsarmy.com",
                infourl: "http://www.flynsarmy.com",
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add(
        "flynsyntaxcodemodal",
        tinymce.plugins.flynsyntaxcodemodal
    );
})();
