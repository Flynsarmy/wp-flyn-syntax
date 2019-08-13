/**
 * Internal dependencies
 */
function CodeMirrorVisualControl(props)
{
	/**
	 * WordPress dependencies
	 */
	var el = wp.element.createElement;
	const { BaseControl } = wp.components;

	// Getting 'this' in functional components https://github.com/reactjs/rfcs/issues/105#issuecomment-513453491
	const ref = React.useRef(null);

	// Declare a new state variable to hold {settings: object, codemirror: object}
	const [editor, setEditor] = React.useState();

	React.useEffect(function() {
		var $this = ref.current;
		var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
		editorSettings.codemirror = _.extend(
			{},
			editorSettings.codemirror,
			{
				mode: props.mode,
				lint: false
			}
		);

		if ( !editor )
		{
			// New modes expect a 'CodeMirror' global var. So alias wp.CodeMirror
			window.CodeMirror = wp.CodeMirror;

			// /wp-admin/js/code-editor.js
			let theEditor = wp.codeEditor.initialize(ref.current.querySelector('textarea'), editorSettings);

			theEditor.codemirror.on('change', function(instance, changeObj) {
				props.setAttributes({
					content: instance.getValue()
				});
			});

			setEditor(theEditor);
			$(ref.current).data('initiated', true);
		}
		else
		{
			// Mode isn't loaded yet. Load it asynchronously
			if ( !(props.mode in wp.CodeMirror.modes) )
			{
				jQuery.ajax({
					url: "/wp-content/plugins/flyn-syntax/assets/vendor/codemirror/mode/"+props.mode+"/"+props.mode+".js",
					dataType: 'script',
					success: function() {
						editor.codemirror.setOption('mode', props.mode);
					},
					async: true
				});
			}
			else
				editor.codemirror.setOption('mode', props.mode);
		}
	});

	return el(
		'BaseControl',
		{
			ref: ref
		},
		el(
			'textarea', {}, props.content
		)
	);
}