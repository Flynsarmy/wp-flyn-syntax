import { registerBlockType } from "@wordpress/blocks";
import { Fragment } from "@wordpress/element";
import { InspectorControls } from "@wordpress/block-editor";
import { TextControl, PanelBody, SelectControl } from "@wordpress/components";
import CodeMirrorControl from "./components/codemirror/CodeMirrorControl";
import languages from "./languages";

registerBlockType("flynsarmy/syntax-editor", {
    title: "Code Block", // Block name visible to user
    icon: "editor-code", // Toolbar icon can be either using WP Dashicons or custom SVG
    category: "formatting", // Under which category the block would appear
    description: "Displays a syntax-highlighted code snippet.",
    attributes: {
        // The data this block will be storing
        // content: { type: 'string', source: 'children', selector: 'pre' } // Code content in pre tag
        content: {
            type: "string",
            default: ""
        },
        // CodeMirror mode
        mode: {
            type: "string",
            default: "php"
        },
        geshi: {
            type: "string",
            default: "php"
        },
        startingLine: {
            type: "integer",
            default: 1
        },
        highlightLines: {
            type: "string"
        }
    },
    edit(props) {
        const {
            //attributes: { content, mode, geshi, startingLine, highlightLines },
            //isSelected,
            setAttributes
        } = props;

        const onSetLanguage = val => {
            const language = languages.filter(lang => lang.mode === val)[0];
            setAttributes({
                mode: val,
                geshi: language.geshi
            });
        };

        const onSetStartingLine = newdata => {
            const startingLine = parseInt(newdata.replace(/([^0-9]+)/gi, ""));

            setAttributes({
                startingLine: isNaN(startingLine) ? 0 : startingLine
            });
        };

        const onSetHighlightLines = newdata => {
            setAttributes({
                highlightLines: newdata.replace(/([^0-9,\-]*)/gi, "")
            });
        };

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title="Code Settings">
                        <SelectControl
                            label="Language"
                            onChange={onSetLanguage}
                            value={props.attributes.mode}
                            // Convert languages to list of {value: mode, label: mode}
                            options={languages.flatMap(lang => {
                                return {
                                    value: lang.mode,
                                    label: lang.mode
                                };
                            })}
                        />
                        <TextControl
                            type="integer"
                            label="Starting line number."
                            value={props.attributes.startingLine}
                            help="Set to 0 to hide line numbers."
                            onChange={onSetStartingLine}
                            style={{ width: "100%" }}
                        />
                        <TextControl
                            type="string"
                            label="Highlight the specified lines."
                            value={props.attributes.highlightLines}
                            help="e.g 3-5, 10, 12"
                            onChange={onSetHighlightLines}
                            style={{ width: "100%" }}
                        />
                    </PanelBody>
                </InspectorControls>
                <CodeMirrorControl {...props} />
            </Fragment>
        );
    },
    save(props) {
        return (
            <pre
                lang={props.attributes.mode}
                line={props.attributes.startingLine}
                highlight={props.attributes.highlightLines}
            >
                {props.attributes.content}
            </pre>
        );
    }
});
