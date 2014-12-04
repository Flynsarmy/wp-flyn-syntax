<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Insert Code Block</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css" media="screen">
        body {
            overflow: hidden;
        }

        #editor {
            position: absolute;
            top: 50px;
            right: 0;
            bottom: 0;
            left: 0;
        }

        #menu label {margin-right:20px}
    </style>
</head>
<body>
    <div id="menu">

        <label for="lang">
            Lang:
            <select name="lang" id="lang" onchange="setEditorMode(this.options[this.selectedIndex].dataset.aceLang, this)">
                <option value="4cs" data-ace-lang="text">4cs</option>
                <option value="6502acme" data-ace-lang="text">6502acme</option>
                <option value="6502kickass" data-ace-lang="text">6502kickass</option>
                <option value="6502tasm" data-ace-lang="text">6502tasm</option>
                <option value="68000devpac" data-ace-lang="text">68000devpac</option>
                <option value="abap" data-ace-lang="abap">abap</option>
                <option value="actionscript" data-ace-lang="actionscript">actionscript</option>
                <option value="actionscript3" data-ace-lang="actionscript">actionscript3</option>
                <option value="ada" data-ace-lang="ada">ada</option>
                <option value="aimms" data-ace-lang="text">aimms</option>
                <option value="algol68" data-ace-lang="text">algol68</option>
                <option value="apache" data-ace-lang="apache_conf">apache</option>
                <option value="applescript" data-ace-lang="applescript">applescript</option>
                <option value="apt_sources" data-ace-lang="text">apt_sources</option>
                <option value="arm" data-ace-lang="text">arm</option>
                <option value="asm" data-ace-lang="text">asm</option>
                <option value="asp" data-ace-lang="text">asp</option>
                <option value="asymptote" data-ace-lang="text">asymptote</option>
                <option value="autoconf" data-ace-lang="text">autoconf</option>
                <option value="autohotkey" data-ace-lang="autohotkey">autohotkey</option>
                <option value="autoit" data-ace-lang="text">autoit</option>
                <option value="avisynth" data-ace-lang="text">avisynth</option>
                <option value="awk" data-ace-lang="text">awk</option>
                <option value="bascomavr" data-ace-lang="text">bascomavr</option>
                <option value="bash" data-ace-lang="batchfile">bash</option>
                <option value="basic4gl" data-ace-lang="text">basic4gl</option>
                <option value="bf" data-ace-lang="text">bf</option>
                <option value="bibtex" data-ace-lang="text">bibtex</option>
                <option value="blitzbasic" data-ace-lang="text">blitzbasic</option>
                <option value="bnf" data-ace-lang="text">bnf</option>
                <option value="boo" data-ace-lang="text">boo</option>
                <option value="c" data-ace-lang="c_cpp">c</option>
                <option value="c_loadrunner" data-ace-lang="text">c_loadrunner</option>
                <option value="c_mac" data-ace-lang="c_cpp">c_mac</option>
                <option value="c_winapi" data-ace-lang="c_cpp">c_winapi</option>
                <option value="caddcl" data-ace-lang="text">caddcl</option>
                <option value="cadlisp" data-ace-lang="text">cadlisp</option>
                <option value="cfdg" data-ace-lang="text">cfdg</option>
                <option value="cfm" data-ace-lang="text">cfm</option>
                <option value="chaiscript" data-ace-lang="text">chaiscript</option>
                <option value="chapel" data-ace-lang="text">chapel</option>
                <option value="cil" data-ace-lang="text">cil</option>
                <option value="clojure" data-ace-lang="clojure">clojure</option>
                <option value="cmake" data-ace-lang="text">cmake</option>
                <option value="cobol" data-ace-lang="cobol">cobol</option>
                <option value="coffeescript" data-ace-lang="coffee">coffeescript</option>
                <option value="cpp-qt" data-ace-lang="c_cpp">cpp-qt</option>
                <option value="cpp-winapi" data-ace-lang="c_cpp">cpp-winapi</option>
                <option value="cpp" data-ace-lang="c_cpp">cpp</option>
                <option value="csharp" data-ace-lang="csharp">csharp</option>
                <option value="css" data-ace-lang="css">css</option>
                <option value="cuesheet" data-ace-lang="text">cuesheet</option>
                <option value="d" data-ace-lang="d">d</option>
                <option value="dart" data-ace-lang="dart">dart</option>
                <option value="dcl" data-ace-lang="text">dcl</option>
                <option value="dcpu16" data-ace-lang="text">dcpu16</option>
                <option value="dcs" data-ace-lang="text">dcs</option>
                <option value="delphi" data-ace-lang="text">delphi</option>
                <option value="diff" data-ace-lang="diff">diff</option>
                <option value="div" data-ace-lang="text">div</option>
                <option value="dos" data-ace-lang="text">dos</option>
                <option value="dot" data-ace-lang="dot">dot</option>
                <option value="e" data-ace-lang="ejs">e</option>
                <option value="ecmascript" data-ace-lang="text">ecmascript</option>
                <option value="eiffel" data-ace-lang="eiffel">eiffel</option>
                <option value="email" data-ace-lang="text">email</option>
                <option value="epc" data-ace-lang="text">epc</option>
                <option value="erlang" data-ace-lang="erlang">erlang</option>
                <option value="euphoria" data-ace-lang="text">euphoria</option>
                <option value="ezt" data-ace-lang="text">ezt</option>
                <option value="f1" data-ace-lang="text">f1</option>
                <option value="falcon" data-ace-lang="text">falcon</option>
                <option value="fo" data-ace-lang="text">fo</option>
                <option value="fortran" data-ace-lang="text">fortran</option>
                <option value="freebasic" data-ace-lang="text">freebasic</option>
                <option value="freeswitch" data-ace-lang="text">freeswitch</option>
                <option value="fsharp" data-ace-lang="text">fsharp</option>
                <option value="gambas" data-ace-lang="text">gambas</option>
                <option value="gdb" data-ace-lang="text">gdb</option>
                <option value="genero" data-ace-lang="text">genero</option>
                <option value="genie" data-ace-lang="text">genie</option>
                <option value="gettext" data-ace-lang="text">gettext</option>
                <option value="glsl" data-ace-lang="glsl">glsl</option>
                <option value="gml" data-ace-lang="text">gml</option>
                <option value="gnuplot" data-ace-lang="text">gnuplot</option>
                <option value="go" data-ace-lang="golang">go</option>
                <option value="groovy" data-ace-lang="groovy">groovy</option>
                <option value="gwbasic" data-ace-lang="text">gwbasic</option>
                <option value="haskell" data-ace-lang="haskell">haskell</option>
                <option value="haxe" data-ace-lang="text">haxe</option>
                <option value="hicest" data-ace-lang="text">hicest</option>
                <option value="hq9plus" data-ace-lang="text">hq9plus</option>
                <option value="html4strict" data-ace-lang="html">html4strict</option>
                <option value="html5" data-ace-lang="html">html5</option>
                <option value="icon" data-ace-lang="text">icon</option>
                <option value="idl" data-ace-lang="text">idl</option>
                <option value="ini" data-ace-lang="ini">ini</option>
                <option value="inno" data-ace-lang="text">inno</option>
                <option value="intercal" data-ace-lang="text">intercal</option>
                <option value="io" data-ace-lang="io">io</option>
                <option value="ispfpanel" data-ace-lang="text">ispfpanel</option>
                <option value="j" data-ace-lang="text">j</option>
                <option value="java" data-ace-lang="java">java</option>
                <option value="java5" data-ace-lang="java">java5</option>
                <option value="javascript" data-ace-lang="javascript">javascript</option>
                <option value="jcl" data-ace-lang="text">jcl</option>
                <option value="jquery" data-ace-lang="text">jquery</option>
                <option value="kixtart" data-ace-lang="text">kixtart</option>
                <option value="klonec" data-ace-lang="text">klonec</option>
                <option value="klonecpp" data-ace-lang="text">klonecpp</option>
                <option value="latex" data-ace-lang="latex">latex</option>
                <option value="lb" data-ace-lang="text">lb</option>
                <option value="ldif" data-ace-lang="text">ldif</option>
                <option value="lisp" data-ace-lang="lisp">lisp</option>
                <option value="llvm" data-ace-lang="text">llvm</option>
                <option value="locobasic" data-ace-lang="text">locobasic</option>
                <option value="logtalk" data-ace-lang="text">logtalk</option>
                <option value="lolcode" data-ace-lang="text">lolcode</option>
                <option value="lotusformulas" data-ace-lang="text">lotusformulas</option>
                <option value="lotusscript" data-ace-lang="text">lotusscript</option>
                <option value="lscript" data-ace-lang="text">lscript</option>
                <option value="lsl2" data-ace-lang="lsl">lsl2</option>
                <option value="lua" data-ace-lang="lua">lua</option>
                <option value="m68k" data-ace-lang="text">m68k</option>
                <option value="magiksf" data-ace-lang="text">magiksf</option>
                <option value="make" data-ace-lang="makefile">make</option>
                <option value="mapbasic" data-ace-lang="text">mapbasic</option>
                <option value="matlab" data-ace-lang="matlab">matlab</option>
                <option value="mirc" data-ace-lang="text">mirc</option>
                <option value="mmix" data-ace-lang="text">mmix</option>
                <option value="modula2" data-ace-lang="text">modula2</option>
                <option value="modula3" data-ace-lang="text">modula3</option>
                <option value="mpasm" data-ace-lang="text">mpasm</option>
                <option value="mxml" data-ace-lang="text">mxml</option>
                <option value="mysql" data-ace-lang="mysql">mysql</option>
                <option value="nagios" data-ace-lang="text">nagios</option>
                <option value="netrexx" data-ace-lang="text">netrexx</option>
                <option value="newlisp" data-ace-lang="text">newlisp</option>
                <option value="nginx" data-ace-lang="text">nginx</option>
                <option value="nimrod" data-ace-lang="text">nimrod</option>
                <option value="nsis" data-ace-lang="text">nsis</option>
                <option value="oberon2" data-ace-lang="text">oberon2</option>
                <option value="objc" data-ace-lang="objectivec">objc</option>
                <option value="objeck" data-ace-lang="objectivec">objeck</option>
                <option value="ocaml-brief" data-ace-lang="ocaml">ocaml-brief</option>
                <option value="ocaml" data-ace-lang="ocaml">ocaml</option>
                <option value="octave" data-ace-lang="text">octave</option>
                <option value="oobas" data-ace-lang="text">oobas</option>
                <option value="oorexx" data-ace-lang="text">oorexx</option>
                <option value="oracle11" data-ace-lang="text">oracle11</option>
                <option value="oracle8" data-ace-lang="text">oracle8</option>
                <option value="oxygene" data-ace-lang="text">oxygene</option>
                <option value="oz" data-ace-lang="text">oz</option>
                <option value="parasail" data-ace-lang="text">parasail</option>
                <option value="parigp" data-ace-lang="text">parigp</option>
                <option value="pascal" data-ace-lang="pascal">pascal</option>
                <option value="pcre" data-ace-lang="text">pcre</option>
                <option value="per" data-ace-lang="text">per</option>
                <option value="perl" data-ace-lang="perl">perl</option>
                <option value="perl6" data-ace-lang="perl">perl6</option>
                <option value="pf" data-ace-lang="text">pf</option>
                <option value="php-brief" data-ace-lang="php">php-brief</option>
                <option value="php" data-ace-lang="php" selected='selected'>php</option>
                <option value="pic16" data-ace-lang="text">pic16</option>
                <option value="pike" data-ace-lang="text">pike</option>
                <option value="pixelbender" data-ace-lang="text">pixelbender</option>
                <option value="pli" data-ace-lang="text">pli</option>
                <option value="plsql" data-ace-lang="text">plsql</option>
                <option value="postgresql" data-ace-lang="text">postgresql</option>
                <option value="postscript" data-ace-lang="text">postscript</option>
                <option value="povray" data-ace-lang="text">povray</option>
                <option value="powerbuilder" data-ace-lang="text">powerbuilder</option>
                <option value="powershell" data-ace-lang="powershell">powershell</option>
                <option value="proftpd" data-ace-lang="text">proftpd</option>
                <option value="progress" data-ace-lang="text">progress</option>
                <option value="prolog" data-ace-lang="prolog">prolog</option>
                <option value="properties" data-ace-lang="properties">properties</option>
                <option value="providex" data-ace-lang="text">providex</option>
                <option value="purebasic" data-ace-lang="text">purebasic</option>
                <option value="pycon" data-ace-lang="text">pycon</option>
                <option value="pys60" data-ace-lang="text">pys60</option>
                <option value="python" data-ace-lang="python">python</option>
                <option value="q" data-ace-lang="text">q</option>
                <option value="qbasic" data-ace-lang="text">qbasic</option>
                <option value="qml" data-ace-lang="text">qml</option>
                <option value="racket" data-ace-lang="text">racket</option>
                <option value="rails" data-ace-lang="ruby">rails</option>
                <option value="rbs" data-ace-lang="text">rbs</option>
                <option value="rebol" data-ace-lang="text">rebol</option>
                <option value="reg" data-ace-lang="text">reg</option>
                <option value="rexx" data-ace-lang="text">rexx</option>
                <option value="robots" data-ace-lang="text">robots</option>
                <option value="rpmspec" data-ace-lang="text">rpmspec</option>
                <option value="rsplus" data-ace-lang="text">rsplus</option>
                <option value="ruby" data-ace-lang="ruby">ruby</option>
                <option value="rust" data-ace-lang="rust">rust</option>
                <option value="sas" data-ace-lang="sass">sas</option>
                <option value="scala" data-ace-lang="scala">scala</option>
                <option value="scheme" data-ace-lang="scheme">scheme</option>
                <option value="scilab" data-ace-lang="text">scilab</option>
                <option value="scl" data-ace-lang="text">scl</option>
                <option value="sdlbasic" data-ace-lang="text">sdlbasic</option>
                <option value="smalltalk" data-ace-lang="text">smalltalk</option>
                <option value="smarty" data-ace-lang="smarty">smarty</option>
                <option value="spark" data-ace-lang="text">spark</option>
                <option value="sparql" data-ace-lang="text">sparql</option>
                <option value="sql" data-ace-lang="sql">sql</option>
                <option value="standardml" data-ace-lang="text">standardml</option>
                <option value="stonescript" data-ace-lang="text">stonescript</option>
                <option value="systemverilog" data-ace-lang="text">systemverilog</option>
                <option value="tcl" data-ace-lang="tcl">tcl</option>
                <option value="teraterm" data-ace-lang="text">teraterm</option>
                <option value="text" data-ace-lang="text">text</option>
                <option value="thinbasic" data-ace-lang="text">thinbasic</option>
                <option value="tsql" data-ace-lang="text">tsql</option>
                <option value="typoscript" data-ace-lang="typescript">typoscript</option>
                <option value="unicon" data-ace-lang="text">unicon</option>
                <option value="upc" data-ace-lang="text">upc</option>
                <option value="urbi" data-ace-lang="text">urbi</option>
                <option value="uscript" data-ace-lang="text">uscript</option>
                <option value="vala" data-ace-lang="vala">vala</option>
                <option value="vb" data-ace-lang="vbscript">vb</option>
                <option value="vbnet" data-ace-lang="vbscript">vbnet</option>
                <option value="vbscript" data-ace-lang="vbscript">vbscript</option>
                <option value="vedit" data-ace-lang="text">vedit</option>
                <option value="verilog" data-ace-lang="verilog">verilog</option>
                <option value="vhdl" data-ace-lang="vhdl">vhdl</option>
                <option value="vim" data-ace-lang="text">vim</option>
                <option value="visualfoxpro" data-ace-lang="text">visualfoxpro</option>
                <option value="visualprolog" data-ace-lang="text">visualprolog</option>
                <option value="whitespace" data-ace-lang="text">whitespace</option>
                <option value="whois" data-ace-lang="text">whois</option>
                <option value="winbatch" data-ace-lang="text">winbatch</option>
                <option value="xbasic" data-ace-lang="text">xbasic</option>
                <option value="xml" data-ace-lang="xml">xml</option>
                <option value="xorg_conf" data-ace-lang="text">xorg_conf</option>
                <option value="xpp" data-ace-lang="text">xpp</option>
                <option value="yaml" data-ace-lang="yaml">yaml</option>
                <option value="z80" data-ace-lang="text">z80</option>
                <option value="zxbasic" data-ace-lang="text">zxbasic</option>
            </select>
        </label>

        <label for="line">
            Line:
            <input type="text" name="line" id="line" value="1" onchange="if(!/^[0-9]*$/.test(this.value))this.value='';" size="3" title="Starting line number. Leave blank for no lines." />
        </label>


        <label for="line">
            <input type="checkbox" name="escaped" id="escaped" value="true" title="Check this if the code entered below is already escaped." />
            Already Escaped?
        </label>

        <label for="highlight">
            Highlight:
            <input type="text" name="highlight" id="highlight" value="" onchange="if(!/^[0-9,\- ]*$/.test(this.value))this.value='';" size="10" title="Highlight the specified lines. e.g 3-5, 10, 12" />
        </label>
    </div>
    <div id="editor"></div>

    <script src="<?= plugins_url('/vendor/ace-builds/src-min-noconflict/ace.js', __DIR__.'/../index.php') ?>" type="text/javascript" charset="utf-8"></script>
    <script src="<?= plugins_url('/vendor/ace-builds/src-min-noconflict/ext-settings_menu.js', __DIR__.'/../index.php') ?>" type="text/javascript" charset="utf-8"></script>
    <script src="<?= plugins_url('/vendor/ace-builds/src-min-noconflict/ext-modelist.js', __DIR__.'/../index.php') ?>" type="text/javascript" charset="utf-8"></script>
    <script>
        var editor = ace.edit("editor");

        ace.require('ace/ext/settings_menu').init(editor);
        editor.commands.addCommands([{
            name: "showSettingsMenu",
            bindKey: {win: "Ctrl-k", mac: "Command-k"},
            exec: function(editor) {
                editor.showSettingsMenu();
            },
            readOnly: true
        }]);

        editor.setTheme("ace/theme/monokai");
        editor.getSession().setMode("ace/mode/javascript");
        editor.session.setMode("ace/mode/php");

        function setEditorMode(modeVal, elem)
        {
            editor.session.setMode({
                path: "ace/mode/"+modeVal,
                v: Date.now()
            });
        }
    </script>
</body>
</html>