import { Panel } from "@wordpress/components";

const Tab = ({ viewMode, setViewMode }) => {
    return (
        <div className="codemirror-block">
            <Panel className="components-panel__header edit-post-sidebar-header edit-post-sidebar__panel-tabs">
                <ul className="codemirror-tabs">
                    <li>
                        <button
                            className={`components-button edit-post-sidebar__panel-tab ${
                                viewMode === "visual" ? "is-active" : ""
                            }`}
                            onClick={() => setViewMode("visual")}
                        >
                            Visual
                        </button>
                    </li>
                    <li>
                        <button
                            className={`components-button edit-post-sidebar__panel-tab ${
                                viewMode === "generated" ? "is-active" : ""
                            }`}
                            onClick={() => setViewMode("generated")}
                        >
                            Generated
                        </button>
                    </li>
                </ul>
            </Panel>
        </div>
    );
};

export default Tab;
