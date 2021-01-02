const Generated = ( props ) => {
	const { geshi, startingLine, highlightLines, content } = props.attributes;

	const pre = document.createElement( 'pre' );
	pre.setAttribute( 'lang', geshi );
	pre.setAttribute( 'line', startingLine );
	pre.setAttribute( 'highlight', highlightLines );
	pre.setAttribute( 'escaped', 1 );
	pre.innerHTML = content;

	return (
		<div style={ { overflow: 'auto' } }>
			<pre>{ pre.outerHTML }</pre>
		</div>
	);
};

export default Generated;
