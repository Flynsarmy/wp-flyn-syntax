<?php
/*
Plugin Name: Flyn-Syntax
Plugin URI: http://www.flynsarmy.com
Description: Syntax highlighting using <a href="http://qbnz.com/highlighter/">GeSHi</a> supporting a wide range of popular languages.
Version: 1.0
Author: Flyn San
Author URI: http://www.flynsarmy.com
Original Author: Steven A. Zahm, Ryan McGeary
License: GPL2

Copyright 2014  Flyn San  (email : flynsarmy@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
@todo integrate TinyMCE button support using one of these as a base:
	http://wordpress.org/extend/plugins/flyn-syntax-integration/
	http://wordpress.org/extend/plugins/flyn-syntax-button/
@todo Merge this add-on plugin functionality:  http://wordpress.org/extend/plugins/flyn-syntax-download-extension/

Look at these:	http://wordpress.org/extend/plugins/wp-synhighlight/
				http://wordpress.org/extend/plugins/wp-codebox/
 */

class Flyn_Syntax
{
    public $version = '1.0';

	/**
	* @var (object) WP_Syntax stores the instance of this class.
	*/
	public $token;

	public $matches = array();

	// Used for caching
	public $cache = array();
	public $cache_generate = false;
    public $cache_generated = false;
	public $cache_match_num = 0;

	/**
	 * A dummy constructor to prevent WP_Syntax from being loaded more than once.
	 *
	 * @access private
	 * @since 1.0
	 * @see WP_Syntax::instance()
	 * @see WP_Syntax();
	 */
	public function __construct()
    {
        $this->inludeDependencies();

        $this->token = md5( uniqid( rand() ) );
    }

    public function initFilters()
    {
        //Invalidate cache whenever new/updated posts/comments are made
        add_action( 'save_post', array( $this, 'invalidatePostCache' ) );
        add_action( 'comment_post', array( $this, 'invalidateCommentCache' ) );
        add_action( 'edit_comment', array( $this, 'invalidateCommentCache' ) );


        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

        // Update config for WYSIWYG editor to accept the pre tag and its attributes.
        add_filter( 'tiny_mce_before_init', array( $this, 'tinyMCEConfig') );

        // We want to run before other filters; hence, a priority of 0 was chosen.
        // Several formatting filters run at or around 6.
        add_filter( 'the_content', array( $this, 'beforeFilter' ), 0);
        add_filter( 'the_excerpt', array( $this, 'beforeFilter' ), 0);
        add_filter( 'comment_text', array( $this, 'beforeFilter' ), 0);

        // We want to run after other filters; hence, a priority of 99.
        add_filter( 'the_content', array( $this, 'afterFilterContent' ), 99);
        add_filter( 'the_excerpt', array( $this, 'afterFilterExcerpt' ), 99);
        add_filter( 'comment_text', array( $this, 'afterFilterComment' ), 99);
    }

	public function invalidatePostCache( $post_id )
	{
		delete_post_meta( $post_id, 'flyn-syntax-cache-content');
		delete_post_meta( $post_id, 'flyn-syntax-cache-excerpt');
	}

	public function invalidateCommentCache( $comment_id )
	{
		delete_comment_meta( $comment_id, 'flyn-syntax-cache-comment');
	}

	public function inludeDependencies()
    {
        if ( !defined('GESHI_VERSION') )
		    require_once __DIR__.'/vendor/geshi-1.0/src/geshi.php';
	}

	/**
	 * Enqueue the CSS and JS.
	 *
	 * @return void
	 */
	public function enqueue()
    {
		$url = plugin_dir_url( __FILE__ ) . 'assets/css/flyn-syntax.css';

		// Enqueue the CSS
		wp_register_style( 'flyn-syntax-css', $url, array(), $this->version );
        wp_enqueue_style( 'flyn-syntax-css' );
	}

	/**
	 * Update the TinyMCE config to add support for the pre tag and its attributes.
	 *
	 * @access private
	 * @since 0.9.13
	 * @param  (array) $init The TinyMCE config.
	 * @return (array)
	 */
	public function tinyMCEConfig( $init ) {

		$ext = 'pre[id|name|class|style|lang|line|escaped|highlight|src]';

		if ( isset( $init['extended_valid_elements'] ) ) {
			$init['extended_valid_elements'] .= "," . $ext;
		} else {
			$init['extended_valid_elements'] = $ext;
		}

		return $init;
	}

	public function substituteToken( &$match ) {
		// global $flyn_syntax_token, $flyn_syntax_matches;

        // No language found? This isn't a code block. Return it unaltered.
        if ( empty($match[1]) )
            return $match[0];

		$i = count( $this->matches );
		$this->matches[ $i ] = $match;

		return "\n\n<p>" . $this->token . sprintf( '%03d', $i ) . "</p>\n\n";
	}

	public function lineNumbers( $code, $start ) {

		$line_count = count( explode( "\n", $code ) );
		$output = '<pre>';

		for ( $i = 0; $i < $line_count; $i++ ) {
			$output .= ( $start + $i ) . "\n";
		}

		$output .= '</pre>';

		return $output;
	}

	public function caption( $url ) {

		$parsed = parse_url( $url );
		$path = pathinfo( $parsed['path'] );
		$caption = '';

		if ( ! isset( $path['filename'] ) ) {
			return;
		}

		if ( isset( $parsed['scheme'] ) ) {
			$caption .= '<a href="' . $url . '">';
		}

		if ( isset( $parsed["host"] ) && $parsed["host"] == 'github.com' )
		{
			$caption .= substr( $parsed['path'], strpos( $parsed['path'], '/', 1 ) ); /* strip github.com username */
		} else {
			$caption .= $parsed['path'];
		}

		/* $caption . $path["filename"];
		if (isset($path["extension"])) {
			$caption .= "." . $path["extension"];
		}*/

		if ( isset($parsed['scheme']) ) {
			$caption .= '</a>';
		}

		return $caption;
	}

    /**
     * Create a highlighted code block from a given unique identifier regex match
     * created in beforeFilter
     *
     * @param $match         [ 0 => full_str, 1 => match_id ]
     * @return string
     */
	public function highlight( $match ) {
		// Keep track of which <pre> tag we're up to
		$this->cache_match_num++;
        // We haven't generated any new cache yet.
        $this->cache_generated = false;

		// Do we have cache? Serve it!
		if ( isset($this->cache[$this->cache_match_num]) )
			return '<!-- from cache -->' . $this->cache[$this->cache_match_num] . '<!-- end from cahe -->';

		$i = intval( $match[1] );
		$match = $this->matches[ $i ];

		$language = strtolower( trim( $match[1] ) );
		$line = trim( $match[2] );
		$escaped = trim( $match[3] );
		$caption = $this->caption( $match[5] );
		$code = trim($match[6]);

		if ( $escaped == 'true' ) $code = htmlspecialchars_decode( $code );

		$geshi = new GeSHi( $code, $language );
		$geshi->enable_keyword_links( FALSE );

		do_action_ref_array( 'flyn_syntax_init_geshi', array( &$geshi ) );

		if ( ! empty( $match[4] ) ) {

			$linespecs = strpos( $match[4], ",") == FALSE ? array( $match[4] ) : explode( ',', $match[4] );
			$lines = array();

			foreach ( $linespecs as $spec ) {
				$range = explode( '-', $spec );
				$lines = array_merge( $lines, ( count( $range ) == 2) ? range( $range[0], $range[1]) : $range );
			}

			$geshi->highlight_lines_extra( $lines );
		}

		$output = "\n" . '<div class="flyn_syntax">';
		$output .= '<table>';

		if ( ! empty( $caption ) ) {
			$output .= '<caption>' . $caption . '</caption>';
		}

		$output .= '<tr>';

		if ( $line ) {
			$output .='<td class="line_numbers">' . $this->lineNumbers( $code, $line ) . '</td>';
		}

		$output .= '<td class="code">';
		$output .= $geshi->parse_code();
		$output .= '</td></tr></table>';
		$output .= '</div>' . "\n";

		if ( $this->cache_generate )
        {
            $this->cache_generated = true;
            $this->cache[$this->cache_match_num] = $output;
        }


		return $output;
	}

    /**
     * Replace the <pre> tag with a <p>some_unique_identifier</p> string so that other filters
     * won't interfere with our code block.
     *
     * @param $content      Post content
     * @return mixed
     */
	public function beforeFilter( $content ) {

        // <pre lang='somelang' line='1' escaped='1|true' highlight='1,2,3,4-7' src='my string'>
		return preg_replace_callback(
			"/\s*<pre(?:lang=[\"']([\w-]+)[\"']|line=[\"'](\d*)[\"']|escaped=[\"'](1|0|true|false)[\"']|highlight=[\"']((?:\d+[,-])*\d+)[\"']|src=[\"']([^\"']+)[\"']|\s)+>(.*)<\/pre>\s*/siU",
			array( $this, 'substituteToken' ),
			$content
		);
	}

	public function afterFilterContent( $content )
	{
        if ( empty($this->matches) )
            return $content;

        global $post;
		$the_post = $post;
        $the_post_id = 0;

		//Reset cache settings on each filter - we might be showing
		//multiple posts on the one page
		$this->cache = array();
		$this->cache_match_num = 0;
		$this->cache_generate = false;

		if ( is_object($the_post) )
		{
            $the_post_id = $post->ID;

			$this->cache = get_post_meta($the_post_id, 'flyn-syntax-cache-content', true);

			if ( !is_array($this->cache) )
			{
				//Make sure $this->cache is an array
				$this->cache = array();
				//Inform the highlight() method that we're regenning
				$this->cache_generate = true;
			}
		}

		$content = $this->afterFilter( $content );

		//Update cache if we're generating and were there <pre> tags generated
		if ( $the_post_id && $this->cache_generated && $this->cache )
            update_post_meta($the_post_id, 'flyn-syntax-cache-content', wp_slash($this->cache));

        $this->matches = array();

		return $content;
	}

	public function afterFilterExcerpt( $content )
	{
        if ( empty($this->matches) )
            return $content;

		global $post;
		$the_post = $post;
		$the_post_id = $post->ID;

		//Reset cache settings on each filter - we might be showing
		//multiple posts on the one page
		$this->cache = array();
		$this->cache_match_num = 0;
		$this->cache_generate = false;

		if ( is_object($the_post) )
		{
			$this->cache = get_post_meta($the_post_id, 'flyn-syntax-cache-excerpt', true);

			if ( !is_array($this->cache) )
			{
				//Make sure $this->cache is an array
				$this->cache = array();
				//Inform the highlight() method that we're regenning
				$this->cache_generate = true;
			}
		}

		$content = $this->afterFilter( $content );

		//Update cache if we're generating and were there <pre> tags generated
		if ( is_object($the_post) && $this->cache_generated && $this->cache )
			update_post_meta($the_post_id, 'flyn-syntax-cache-excerpt', wp_slash($this->cache));

        $this->matches = array();

		return $content;
	}

	public function afterFilterComment( $content )
	{
        if ( empty($this->matches) )
            return $content;

        global $comment;
		$the_post = $comment;
		$the_post_id = $comment->comment_ID;

		if ( is_object($the_post) )
		{
			$this->cache = get_comment_meta($the_post_id, 'flyn-syntax-cache-comment', true);

			if ( !is_array($this->cache) )
			{
				//Make sure $this->cache is an array
				$this->cache = array();
				//Inform the highlight() method that we're regenning
				$this->cache_generate = true;
			}
		}

		$content = $this->afterFilter( $content );

		//Update cache if we're generating and were there <pre> tags generated
		if ( is_object($the_post) && $this->cache_generated && $this->cache )
			update_comment_meta($the_post_id, 'flyn-syntax-cache-comment', wp_slash($this->cache));

        $this->matches = array();

		return $content;
	}

	public function afterFilter( $content ) {
		// global $flyn_syntax_token;

		 $content = preg_replace_callback(
			 '/<p>' . $this->token . '(\d{3})<\/p>/si',
			 array( $this, 'highlight' ),
			 $content
		 );

		return $content;
	}

}

/**
 * Start the plugin.
 */
add_action( 'plugins_loaded', function() {
    $flynSyntax = new Flyn_Syntax;
    $flynSyntax->initFilters();
});