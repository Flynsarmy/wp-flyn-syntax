import Generated from './Generated';
import Tab from './Tab';
import Visual from './Visual';
import { useState, Fragment } from '@wordpress/element';

const CodeMirrorControl = ( props ) => {
	// Declare a new state variable to hold {settings: object, codemirror: object}
	const [ viewMode, setViewMode ] = useState( 'visual' );

	return (
		<Fragment>
			<Tab viewMode={ viewMode } setViewMode={ setViewMode } />
			{ viewMode === 'visual' && <Visual { ...props } /> }
			{ viewMode !== 'visual' && <Generated { ...props } /> }
		</Fragment>
	);
};

export default CodeMirrorControl;
