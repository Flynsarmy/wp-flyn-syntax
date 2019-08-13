( function( blocks, element, editor, components, codeMirror ) {
    var el = element.createElement;

    blocks.registerBlockType('flynsarmy/syntax-editor', {
        title: 'Code', // Block name visible to user
        icon: 'editor-code', // Toolbar icon can be either using WP Dashicons or custom SVG
        category: 'common', // Under which category the block would appear
        attributes: { // The data this block will be storing
            // content: { type: 'string', source: 'children', selector: 'pre' } // Code content in pre tag
            content: {
                type: 'string',
                default: ''
            },
            // CodeMirror mode
            mode: {
                type: 'string',
                default: 'php'
            },
            geshi: {
                type: 'string',
                default: 'php'
            },
            startingLine: {
                type: 'integer',
                default: 1
            },
            highlightLines: {
                type: 'string'
            }
        },
        edit: function(props) {
            const { 
                attributes: { content, mode, geshi, startingLine, highlightLines }, 
                isSelected, 
                setAttributes 
            } = props;

            // A list of languages supported in both CodeMirror and GeSHi
            let languages = [
                {mode: "apl", geshi: "applescript"},
                // {mode: "asciiarmor", geshi: ""},
                // {mode: "asn.1", geshi: ""},
                // {mode: "asterisk", geshi: ""},
                // {mode: "brainfuck", geshi: ""},
                {mode: "clike", geshi: "c"},
                {mode: "clojure", geshi: "clojure"},
                {mode: "cmake", geshi: "cmake"},
                {mode: "cobol", geshi: "cobol"},
                {mode: "coffeescript", geshi: "coffeescript"},
                {mode: "commonlisp", geshi: "lisp"},
                // {mode: "crystal", geshi: ""},
                {mode: "css", geshi: "css"},
                // {mode: "cypher", geshi: ""},
                {mode: "d", geshi: "d"},
                {mode: "dart", geshi: "dart"},
                {mode: "diff", geshi: "diff"},
                // {mode: "django", geshi: ""},
                // {mode: "dockerfile", geshi: ""},
                // {mode: "dtd", geshi: ""},
                // {mode: "dylan", geshi: ""},
                // {mode: "ebnf", geshi: ""},
                // {mode: "ecl", geshi: ""},
                {mode: "eiffel", geshi: "eiffel"},
                // {mode: "elm", geshi: ""},
                {mode: "erlang", geshi: "erlang"},
                // {mode: "factor", geshi: ""},
                // {mode: "fcl", geshi: ""},
                // {mode: "forth", geshi: ""},
                // {mode: "fortran", geshi: "fortran"},
                // {mode: "gas", geshi: ""},
                // {mode: "gfm", geshi: ""},
                // {mode: "gherkin", geshi: ""},
                {mode: "go", geshi: "go"},
                {mode: "groovy", geshi: "groovy"},
                // {mode: "haml", geshi: ""},
                {mode: "handlebars", geshi: "twig"},
                {mode: "haskell", geshi: "haskell"},
                {mode: "haskell-literate", geshi: "haskell"},
                {mode: "haxe", geshi: "haxe"},
                {mode: "htmlembedded", geshi: "html5"},
                {mode: "htmlmixed", geshi: "html5"},
                // {mode: "http", geshi: ""},
                {mode: "idl", geshi: "idl"},
                {mode: "javascript", geshi: "javascript"},
                // {mode: "jinja2", geshi: ""},
                {mode: "jsx", geshi: "javascript"},
                {mode: "julia", geshi: "julia"},
                // {mode: "livescript", geshi: ""},
                {mode: "lua", geshi: "lua"},
                // {mode: "markdown", geshi: ""},
                {mode: "mathematica", geshi: "mathematica"},
                // {mode: "mbox", geshi: ""},
                // {mode: "meta.js", geshi: ""},
                {mode: "mirc", geshi: "mirc"},
                // {mode: "mllike", geshi: ""},
                // {mode: "modelica", geshi: ""},
                // {mode: "mscgen", geshi: ""},
                // {mode: "mumps", geshi: ""},
                {mode: "nginx", geshi: "nginx"},
                {mode: "nsis", geshi: "nsis"},
                // {mode: "ntriples", geshi: ""},
                {mode: "octave", geshi: "octave"},
                {mode: "oz", geshi: "oz"},
                {mode: "pascal", geshi: "pascal"},
                // {mode: "pegjs", geshi: ""},
                {mode: "perl", geshi: "perl"},
                {mode: "php", geshi: "php"},
                // {mode: "pig", geshi: ""},
                {mode: "powershell", geshi: "powershell"},
                {mode: "properties", geshi: "properties"},
                // {mode: "protobuf", geshi: ""},
                // {mode: "pug", geshi: ""},
                // {mode: "puppet", geshi: ""},
                {mode: "python", geshi: "python"},
                {mode: "q", geshi: "q"},
                // {mode: "r", geshi: ""},
                {mode: "rpm", geshi: "rpmspec"},
                // {mode: "rst", geshi: ""},
                {mode: "ruby", geshi: "ruby"},
                {mode: "rust", geshi: "rust"},
                {mode: "sas", geshi: "sas"},
                {mode: "sass", geshi: "sass"},
                {mode: "scheme", geshi: "scheme"},
                {mode: "shell", geshi: "bash"},
                // {mode: "sieve", geshi: ""},
                // {mode: "slim", geshi: ""},
                {mode: "smalltalk", geshi: "smalltalk"},
                {mode: "smarty", geshi: "smarty"},
                // {mode: "solr", geshi: ""},
                // {mode: "soy", geshi: ""},
                {mode: "sparql", geshi: "sparql"},
                // {mode: "spreadsheet", geshi: ""},
                {mode: "sql", geshi: "sql"},
                // {mode: "stex", geshi: ""},
                // {mode: "stylus", geshi: ""},
                {mode: "swift", geshi: "swift"},
                {mode: "tcl", geshi: "tcl"},
                // {mode: "textile", geshi: ""},
                // {mode: "tiddlywiki", geshi: ""},
                // {mode: "tiki", geshi: ""},
                // {mode: "toml", geshi: ""},
                // {mode: "tornado", geshi: ""},
                // {mode: "troff", geshi: ""},
                // {mode: "ttcn", geshi: ""},
                // {mode: "ttcn-cfg", geshi: ""},
                // {mode: "turtle", geshi: ""},
                {mode: "twig", geshi: "twig"},
                {mode: "vb", geshi: "vb"},
                {mode: "vbscript", geshi: "vbscript"},
                // {mode: "velocity", geshi: ""},
                {mode: "verilog", geshi: "verilog"},
                {mode: "vhdl", geshi: "vhdl"},
                {mode: "vue", geshi: "twig"},
                // {mode: "webidl", geshi: ""},
                {mode: "xml", geshi: "xml"},
                // {mode: "xquery", geshi: ""},
                // {mode: "yacas", geshi: ""},
                {mode: "yaml", geshi: "yaml"},
                // {mode: "yaml-frontmatter", geshi: ""},
                {mode: "z80", geshi: "z80"},
            ];
            
            return el( element.Fragment,
                {},
                el( editor.InspectorControls,
                    {
                        key: "controls"
                    },
                    // Language drop down
                    el(
                        components.SelectControl,
                        {
                            label: "Language",
                            onChange: function( value ) {
                                let lang = languages.filter(function(language) { 
                                    return language.mode == value 
                                })[0];
                                setAttributes({
                                    mode: value,
                                    geshi: lang.geshi
                                });
                            },
                            value: props.attributes.mode,
                            // Convert languages to list of {value: mode, label: mode}
                            options: languages.flatMap(function(lang) { return {
                                value: lang.mode,
                                label: lang.mode
                            } })
                        },
                    ),
                    // Line input
                    el(
                        components.TextControl,
                        {
                            type: 'integer',
                            label: "Starting line number. Leave blank for no lines.",
                            value: props.attributes.startingLine,
                            onChange: function( newdata ) {
                                newdata = newdata.replace(/([^0-9]+)/gi, '');

                                setAttributes({
                                    startingLine: newdata 
                                });
                            },
                            style: { width: '100%' }
                        }
                    ),
                    // Highlight input
                    el(
                        components.TextControl,
                        {
                            type: 'string',
                            pattern: '',
                            label: "Highlight the specified lines. e.g 3-5, 10, 12",
                            value: props.attributes.highlightLines,
                            onChange: function( newdata ) {
                                newdata = newdata.replace(/([^0-9,\-]*)/gi, '');

                                setAttributes({ 
                                    highlightLines: newdata 
                                });
                            }
                        }
                    ),
                ),
                el( 'div', 
                    {
                        className: 'notice-box notice-' + props.attributes.type
                    },
                    el(
                        CodeMirrorTabControl, props.attributes
                    )
                ) // End return
            );
        },
        save: function(props) {
            // How our block renders on the frontend
            return el('pre', {
                lang: props.attributes.geshi,
                line: props.attributes.startingLine,
                highlight: props.attributes.highlightLines
            }, props.attributes.content);
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor,
    window.wp.components,
    window.wp.CodeMirror
) );
