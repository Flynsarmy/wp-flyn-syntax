import { BaseControl } from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';
import { clone, extend } from 'lodash';
import $ from 'jquery';

const Visual = ( props ) => {
	// Getting 'this' in functional components https://github.com/reactjs/rfcs/issues/105#issuecomment-513453491
	const ref = useRef( null );

	// Declare a new state variable to hold {settings: object, codemirror: object}
	const [ editor, setEditor ] = useState();

	// Set up editor
	useEffect( () => {
		if ( editor || ! ref.current ) {
			return;
		}

		const editorSettings = wp.codeEditor.defaultSettings
			? clone( wp.codeEditor.defaultSettings )
			: {};
		editorSettings.codemirror = extend( {}, editorSettings.codemirror, {
			mode: props.attributes.mode,
			firstLineNumber: props.attributes.startingLine,
			lineWrapping: false,
			lint: false,
		} );

		// New modes expect a 'CodeMirror' global var. So alias wp.CodeMirror
		window.CodeMirror = wp.CodeMirror;

		// /wp-admin/js/code-editor.js
		const theEditor = wp.codeEditor.initialize(
			ref.current,
			editorSettings
		);

		theEditor.codemirror.on( 'change', function (
			instance /*, changeObj*/
		) {
			props.setAttributes( {
				content: instance.getValue(),
			} );
		} );

		setEditor( theEditor );
		$( ref.current ).data( 'initiated', true );
	}, [ ref.current ] );

	// Set mode, add to editor if needed
	useEffect( () => {
		if ( ! editor ) {
			return;
		}

		// Mode isn't loaded yet. Load it asynchronously
		if ( ! ( props.attributes.mode in wp.CodeMirror.modes ) ) {
			$.ajax( {
				url:
					'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/' +
					props.attributes.mode +
					'/' +
					props.attributes.mode +
					'.min.js',
				dataType: 'script',
				success() {
					editor.codemirror.setOption(
						'mode',
						props.attributes.mode
					);
				},
				async: true,
			} );
		} else editor.codemirror.setOption( 'mode', props.attributes.mode );
	}, [ props.attributes.mode, editor ] );

	// Update starting line number when changed on the right
	useEffect( () => {
		if ( ! editor ) {
			return;
		}

		editor.codemirror.setOption(
			'firstLineNumber',
			parseInt( props.attributes.startingLine )
		);
	}, [ props.attributes.startingLine ] );

	return (
		<BaseControl>
			<textarea ref={ ref }>{ props.attributes.content }</textarea>
		</BaseControl>
	);
};

export default Visual;
