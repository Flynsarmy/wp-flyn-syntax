/**
 * Internal dependencies
 */
function CodeMirrorTabControl(props)
{
    /**
     * WordPress dependencies
     */
    var el = wp.element.createElement;

    // Declare a new state variable to hold {settings: object, codemirror: object}
    const [viewMode, setViewMode] = React.useState('visual');

    function updateViewMode(e)
    {
        setViewMode(e.target.innerHTML.toLowerCase());
    }
    
    return el(
		'div',
		{
            class: 'codemirror-tabs'
        },
        el(
            'ul', {class: 'controls'},
            el('li', {onClick: updateViewMode, class: viewMode == 'visual' ? 'active' : ''}, "Visual"),
            el('li', {onClick: updateViewMode, class: viewMode == 'generated' ? 'active' : ''}, "Generated")
        ),
        el(
            viewMode == 'visual' ? CodeMirrorVisualControl : CodeMirrorGeneratedControl, props
        )
	);
}