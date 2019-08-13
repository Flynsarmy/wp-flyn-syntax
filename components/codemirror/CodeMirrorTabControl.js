/**
 * Internal dependencies
 */
function CodeMirrorTabControl(props)
{
    /**
     * WordPress dependencies
     */
    var el = wp.element.createElement;
    const { BaseControl } = wp.components;

    // Declare a new state variable to hold {settings: object, codemirror: object}
    const [isSetup, setIsSetup] = React.useState(false);
    const [viewMode, setViewMode] = React.useState('visual');

    // Getting 'this' in functional components https://github.com/reactjs/rfcs/issues/105#issuecomment-513453491
	const ref = React.useRef(null);

    React.useEffect(function() {
        var $this = ref.current;
        
        if ( !isSetup )
		{
            setIsSetup(true);
            
            jQuery(ref.current).find('LI').click(function() {
                setViewMode(jQuery(this).html().toLowerCase());
            });
		}
    });
    
    return el(
		'div',
		{
            ref: ref,
			class: 'codemirror-tabs'
        },
        el(
            'ul', {class: 'controls'},
            el('li', {class: viewMode == 'visual' ? 'active' : ''}, "Visual"),
            el('li', {class: viewMode == 'generated' ? 'active' : ''}, "Generated")
        ),
        el(
            viewMode == 'visual' ? CodeMirrorVisualControl : CodeMirrorGeneratedControl, props
        )
	);
}