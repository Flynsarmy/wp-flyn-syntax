/**
 * Internal dependencies
 */
function CodeMirrorGeneratedControl(props)
{
    /**
     * WordPress dependencies
     */
    var el = wp.element.createElement;

    var pre = document.createElement('pre');
    pre.setAttribute('lang', props.geshi);
    pre.setAttribute('line', props.startingLine);
    pre.setAttribute('highlight', props.highlightLines);
    pre.setAttribute('escaped', 1);
    pre.innerHTML = props.content;

    return el(
        'pre', {}, 
        pre.outerHTML
    );
}