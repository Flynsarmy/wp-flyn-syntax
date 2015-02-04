# Flyn Syntax

> Flyn-Syntax provides clean syntax highlighting for embedding source code within pages or posts.

Flyn-Syntax provides clean syntax highlighting using
[GeSHi](https://github.com/GeSHi/geshi-1.0) -- supporting a wide range of popular
languages.  It supports highlighting with or without line numbers and maintains formatting while copying snippets of code
from the browser.

It avoids conflicts with other 3rd party plugins by running an early
pre-filter and a late post-filter that substitutes and pulls the code snippets
out first and then pushes them back in with highlighting at the end.  The
result is source code formatted and highlighted the way you intended.

Want to contribute? Flyn-Syntax can be found [on Github](https://github.com/Flynsarmy/wp-flyn-syntax). Fork and submit your pull requests today!

## Installation

1. `git clone --recursive https://github.com/Flynsarmy/wp-flyn-syntax /path/to/wp-content/plugins/flyn-syntax`
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Create a post/page that contains a code snippet following the syntax from the Usage below.

## Screenshots

1. PHP, no line numbers.   
![](https://raw.githubusercontent.com/Flynsarmy/wp-flyn-syntax/master/assets/images/screenshots/php-nolines.png)
1. Java, with line numbers.  
![](https://raw.githubusercontent.com/Flynsarmy/wp-flyn-syntax/master/assets/images/screenshots/java-lines.png)
1. Ruby, with line numbers starting at 18.  
![](https://raw.githubusercontent.com/Flynsarmy/wp-flyn-syntax/master/assets/images/screenshots/ruby-18-highlight.png)
1. Build in code editor.  
![](https://raw.githubusercontent.com/Flynsarmy/wp-flyn-syntax/master/assets/images/screenshots/code-editor.png)

## Usage

### Easy Mode

Click the *Insert code block* button added by this plugin in the post editor WYSIWYG. A modal will appear with a code editor. 

### Manual 
Wrap code blocks with `<pre lang='LANGUAGE' line='1' escaped='1|true' highlight='1,2,5-7' src='http://github.com/my/repo'>` and `</pre>` where 

1. `lang` is a [GeSHi](http://qbnz.com/highlighter/) supported language syntax. See below for a full list of supported languages. *(required)*
1. `line` shows line numbers starting with the given integer *(optional, default false)*
1. `escaped` is whether the code is already escaped *(optional, default false)*
1. `highlight` determines the line numbers to highlight. Individual lines or a range may be specified. *(optional)*
1. `src` adds a caption to the top of the code block *(optional)*

**Example 1: PHP, no line numbers**

    <pre lang="php">
    <div id="foo">
    <?php
      function foo() {
        echo "Hello World!\\n";
      }
    ?>
    </div>
    </pre>


**Example 2: Java, with line numbers**

    <pre lang="java" line="1">
    public class Hello {
      public static void main(String[] args) {
        System.out.println("Hello World!");
      }
    }
    </pre>

**Example 3: Ruby, with line numbers starting at 18**

    <pre lang="ruby" line="18">
    class Example
      def example(arg1)
        return "Hello: " + arg1.to_s
      end
    end
    </pre>

**Example 4: If your code already has html entities escaped, use `escaped="true"` as an option**

    <pre lang="xml" escaped="true">
    &lt;xml&gt;Hello&lt;/xml&gt;
    </pre>

**Example 5: PHP, with line numbers and highlighting lines 2, 4, 5, 6**

    <pre lang="php" line="1" highlight="2,4-6">
    <div id="foo">
    <?php
      function foo() {
        echo "Hello World!\\n";
      }
    ?>
    </div>
    </pre>

**Example 6: PHP, with a caption (file and/or file path of the source file)**

    <pre lang="php" src"https://github.com/shazahm1/Connections/blob/master/connections.php">
    <div id="foo">
    <?php
      function foo() {
        echo "Hello World!\\n";
      }
    ?>
    </div>
    </pre>

## Supported Languages

The following languages are most supported in the `lang` attribute:

4cs, 6502acme, 6502kickass, 6502tasm, 68000devpac, abap, actionscript, 
actionscript3, ada, aimms, algol68, **apache**, applescript, apt_sources, arm, asm, 
**asp**, asymptote, autoconf, autohotkey, autoit, avisynth, awk, bascomavr, **bash**, 
basic4gl, bf, bibtex, blitzbasic, bnf, boo, **c**, c_loadrunner, c_mac, c_winapi, 
caddcl, cadlisp, cfdg, cfm, chaiscript, chapel, cil, clojure, cmake, cobol, 
**coffeescript**, cpp-qt, cpp-winapi, **cpp**, **csharp**, **css**, cuesheet, d, dart, dcl, 
dcpu16, dcs, delphi, diff, div, dos, dot, e, ecmascript, eiffel, email, epc, 
erlang, euphoria, ezt, f1, falcon, fo, fortran, freebasic, freeswitch, fsharp, 
gambas, gdb, genero, genie, gettext, glsl, gml, gnuplot, go, groovy, gwbasic, 
haskell, haxe, hicest, hq9plus, **html4strict**, **html5**, icon, idl, ini, inno, 
intercal, io, ispfpanel, j, **java**, **java5**, **javascript**, jcl, **jquery**, kixtart, 
klonec, klonecpp, latex, lb, ldif, lisp, llvm, locobasic, logtalk, lolcode, 
lotusformulas, lotusscript, lscript, lsl2, lua, m68k, magiksf, make, mapbasic, 
matlab, mirc, mmix, modula2, modula3, mpasm, mxml, **mysql**, nagios, netrexx, 
newlisp, nginx, nimrod, nsis, oberon2, **objc**, objeck, ocaml-brief, ocaml, octave, 
oobas, oorexx, oracle11, oracle8, oxygene, oz, parasail, parigp, pascal, pcre, 
per, **perl**, perl6, pf, php-brief, **php**, pic16, pike, pixelbender, pli, plsql, 
postgresql, postscript, povray, powerbuilder, powershell, proftpd, progress, 
prolog, properties, providex, purebasic, pycon, pys60, **python**, q, qbasic, qml, 
racket, rails, rbs, rebol, reg, rexx, robots, rpmspec, rsplus, **ruby**, rust, sas, 
scala, scheme, scilab, scl, sdlbasic, smalltalk, smarty, spark, sparql, **sql**, 
standardml, stonescript, systemverilog, tcl, teraterm, text, thinbasic, tsql, 
typoscript, unicon, upc, urbi, uscript, vala, **vb**, vbnet, vbscript, vedit, 
verilog, vhdl, vim, visualfoxpro, visualprolog, whitespace, whois, winbatch, 
xbasic, **xml**, xorg_conf, xpp, **yaml**, z80, zxbasic

See the [GeSHi Documentation](http://qbnz.com/highlighter/geshi-doc.html)
for a full list of supported languages.

(Bold languages just highlight the more popular ones.)

## Styling Guidelines

Flyn-Syntax colors code using the default GeSHi colors. It uses inline styling 
to ensure code highlights work in RSS feeds and includes a default 
`flyn-syntax.css` stylesheet for basic layout.  If you don't like the default
styling, add the following to your themes *functions.php*:

    add_action('wp_print_styles', function() {
        wp_deregister_style('flyn-syntax-css');
    });

and copy the contents of `wp-content/plugins/flyn-syntax/flyn-syntax.css` to 
your themes *style.css* with your changes.

## Advanced Customization

Flyn-Syntax supports a `flyn_syntax_init_geshi` action hook to customize GeSHi
initialization settings.  Blog owners can handle the hook in a hand-made plugin
or somewhere else like this:

    <?php
    add_action('flyn_syntax_init_geshi', function(GeSHi $geshi) {
        $geshi->set_brackets_style('color: #000;');
        $geshi->set_keyword_group_style(1, 'color: #22f;');
    });
    ?>

This allows for a great possibility of different customizations. Be sure to
review the [GeSHi Documentation](http://qbnz.com/highlighter/geshi-doc.html).

## Changelog

**v1.1.2** *(2015-02-05)*

*  Fixed *escaped* attribute on generated `PRE` tag

**v1.1.1** *(2015-01-24)*

*  Remove 'already escaped?' option from code editor modal
*  Add newlines at the end of inserted code blocks so cursor isn't trapped in the *pre* element in visual mode

**v1.1** *(2014-12-04)* 

*  Added Ace code editor modal for creating code blocks

**v1.0** *(2014-12-03)* 

*  Initial release

This plugin was created from [WP-Syntax](https://wordpress.org/plugins/wp-syntax/) through frustration with its bugs and lack of updates.