<?php

namespace FlynSyntax;

class FlynSyntax
{
    public string $version = '1.1';

    public string $token;

    public array $matches = [];

    // Used for caching
    public array $cache = [];
    public bool $cache_generate  = false;
    public bool $cache_generated = false;
    public int $cache_match_num = 0;

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

        $this->token = md5(uniqid(rand()));
    }

    public function initFilters()
    {
        // Invalidate cache whenever new/updated posts/comments are made
        add_action('save_post', [$this, 'invalidatePostCache']);
        add_action('comment_post', [$this, 'invalidateCommentCache']);
        add_action('edit_comment', [$this, 'invalidateCommentCache']);

        add_action('admin_enqueue_scripts', [$this, 'adminEnqueue']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets']);

        // Update config for WYSIWYG editor to accept the pre tag and its attributes.
        add_filter('tiny_mce_before_init', [$this, 'tinyMCEConfig']);

        // We want to run before other filters; hence, a priority of 0 was chosen.
        // Several formatting filters run at or around 6.
        add_filter('the_content', [$this, 'beforeFilter'], 0);
        add_filter('the_excerpt', [$this, 'beforeFilter'], 0);
        add_filter('comment_text', [$this, 'beforeFilter'], 0);

        // We want to run after other filters; hence, a priority of 99.
        add_filter('the_content', [$this, 'afterFilterContent'], 99);
        add_filter('the_excerpt', [$this, 'afterFilterExcerpt'], 99);
        add_filter('comment_text', [$this, 'afterFilterComment'], 99);
    }

    public function invalidatePostCache(int $post_id)
    {
        delete_post_meta($post_id, 'flyn-syntax-cache-content');
        delete_post_meta($post_id, 'flyn-syntax-cache-excerpt');
    }

    public function invalidateCommentCache(int $comment_id)
    {
        delete_comment_meta($comment_id, 'flyn-syntax-cache-comment');
    }

    public function inludeDependencies()
    {
        if (!defined('GESHI_VERSION')) {
            require_once __DIR__ . '/../vendor/geshi/geshi/src/geshi.php';
        }
    }

    /**
     * Enqueue the backend CSS and JS.
     *
     * @return void
     */
    public function adminEnqueue(string $hook)
    {
        global $post;

        if (!$post) {
            return;
        }

        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        wp_enqueue_code_editor(['type' => 'text/html']);
        wp_enqueue_style(
            'flyn-syntax-backend',
            plugin_dir_url(__DIR__ . "/../index.php") . 'assets/css/backend.css',
            []
        );
    }

    /**
     * Enqueue the frontend CSS and JS.
     *
     * @return void
     */
    public function enqueue()
    {
        $url = plugin_dir_url(__DIR__ . "/../index.php") . 'assets/css/flyn-syntax.css';

        // Enqueue the CSS
        wp_register_style('flyn-syntax-css', $url, [], $this->version);
        wp_enqueue_style('flyn-syntax-css');
    }

    /**
     * Enqueue the CSS and JS.
     *
     * @return void
     */
    public function enqueueBlockEditorAssets()
    {
        $asset = require __DIR__ . '/../assets/js/build/block.min.asset.php';
        wp_enqueue_script(
            'flyn-syntax-js',
            plugins_url('assets/js/build/block.min.js', __DIR__ . "/../index.php"),
            $asset['dependencies'],
            $asset['version'],
        );
    }

    /**
     * Update the TinyMCE config to add support for the pre tag and its attributes.
     *
     * @access private
     * @since 0.9.13
     * @param  (array) $init The TinyMCE config.
     * @return (array)
     */
    public function tinyMCEConfig(array $init): array
    {
        $ext = 'pre[id|name|class|style|lang|line|escaped|highlight|src]';

        if (isset($init['extended_valid_elements'])) {
            $init['extended_valid_elements'] .= ',' . $ext;
        } else {
            $init['extended_valid_elements'] = $ext;
        }

        return $init;
    }

    public function lineNumbers($code, $start)
    {
        $line_count = count(explode("\n", $code));
        $output     = '<pre>';

        for ($i = 0; $i < $line_count; $i++) {
            $output .= ($start + $i) . "\n";
        }

        $output .= '</pre>';

        return $output;
    }

    public function caption($url)
    {
        $parsed  = parse_url($url);
        $path    = pathinfo($parsed['path']);
        $caption = '';

        if (!isset($path['filename'])) {
            return;
        }

        if (isset($parsed['scheme'])) {
            $caption .= '<a href="' . $url . '">';
        }

        if (isset($parsed['host']) && $parsed['host'] == 'github.com') {
            $caption .= substr($parsed['path'], strpos($parsed['path'], '/', 1)); /* strip github.com username */
        } else {
            $caption .= $parsed['path'];
        }

        /*
        $caption . $path["filename"];
        if (isset($path["extension"])) {
            $caption .= "." . $path["extension"];
        }
        */

        if (isset($parsed['scheme'])) {
            $caption .= '</a>';
        }

        return $caption;
    }

    /**
     * Create a highlighted code block from a given unique identifier regex match
     * created in beforeFilter
     *
     * @param array $match         [ 0 => full_str, 1 => match_id ]
     * @return string
     */
    public function highlight(array $match): string
    {
        // Keep track of which <pre> tag we're up to
        $this->cache_match_num++;
        // We haven't generated any new cache yet.
        $this->cache_generated = false;

        // Do we have cache? Serve it!
        if (isset($this->cache[$this->cache_match_num])) {
            return '<!-- from cache -->' . $this->cache[$this->cache_match_num] . '<!-- end from cahe -->';
        }

        $i     = intval($match[1]);
        $match = $this->matches[$i];

        $language  = strtolower(trim($match[1]));
        $line      = intval(trim($match[2]));
        //$escaped   = trim($match[3]);
        $highlight = $match[4];
        $caption   = $this->caption($match[5]);
        $code      = htmlspecialchars_decode(trim($match[6]));

        $geshi = new \GeSHi($code, $language);
        $geshi->enable_classes();
        $geshi->enable_keyword_links(false);

        do_action_ref_array('flyn_syntax_init_geshi', [&$geshi]);

        if (!empty($highlight)) {
            $linespecs = explode(',', $highlight);
            $lines     = [];

            foreach ($linespecs as $spec) {
                $range = explode('-', $spec);
                $lines = array_merge($lines, count($range) == 2 ? range($range[0], $range[1]) : $range);
            }

            // "highlight" attribute is relative.
            // When we start on line 3, highlight="4-5" means second and third lines.
            if ($line > 1) {
                $lines = array_map(function ($highlightLine) use ($line) {
                    return $highlightLine - ($line - 1);
                }, $lines);
            }

            $geshi->highlight_lines_extra($lines);
        }

        $output  = '<style>' . $geshi->get_stylesheet() . '</style>';
        $output .= "\n" . '<div class="flyn_syntax">';
        $output .= '<table>';

        if (!empty($caption)) {
            $output .= '<caption>' . $caption . '</caption>';
        }

        $output .= '<tr>';

        if ($line) {
            $output .= '<td class="line_numbers">' . $this->lineNumbers($code, $line) . '</td>';
        }

        $output .= '<td class="code">';
        $output .= $geshi->parse_code();
        $output .= '</td></tr></table>';
        $output .= '</div>' . "\n";

        if ($this->cache_generate) {
            $this->cache_generated                 = true;
            $this->cache[$this->cache_match_num] = $output;
        }

        return $output;
    }

    /**
     * Replace the <pre> tag with a <p>some_unique_identifier</p> string so that other filters
     * won't interfere with our code block.
     *
     * @param string $content      Post content
     * @return string
     */
    public function beforeFilter(string $content): string
    {
        // <pre lang='somelang' line='1' escaped='1|true' highlight='1,2,3,4-7' src='my string'>
        return preg_replace_callback(
            // phpcs:disable Generic.Files.LineLength.TooLong
            "/\s*<pre(?:lang=[\"']([\w-]+)[\"']|line=[\"'](\d*)[\"']|escaped=[\"'](1|0|true|false)[\"']|highlight=[\"']((?:\d+[,-])*\d+)[\"']|src=[\"']([^\"']+)[\"']|class=[\"']wp-block-flynsarmy-syntax-editor[\"']|\s)+>(.*)<\/pre>\s*/siU",
            function ($match) {
                // No language found? This isn't a code block. Return it unaltered.
                if (empty($match[1])) {
                    return $match[0];
                }
        
                $i                   = count($this->matches);
                $this->matches[$i] = $match;
        
                return "\n\n<p>" . $this->token . sprintf('%03d', $i) . "</p>\n\n";
            },
            $content
        );
    }

    public function afterFilterContent(string $content): string
    {
        if (empty($this->matches)) {
            return $content;
        }

        global $post;
        $the_post    = $post;
        $the_post_id = 0;

        // Reset cache settings on each filter - we might be showing
        // multiple posts on the one page
        $this->cache           = [];
        $this->cache_match_num = 0;
        $this->cache_generate  = false;

        if (is_object($the_post)) {
            $the_post_id = $post->ID;

            $cache = get_post_meta($the_post_id, 'flyn-syntax-cache-content', true);

            if (is_array($cache)) {
                $this->cache = $cache;
            } else {
                // Inform the highlight() method that we're regenning
                $this->cache_generate = true;
            }
        }

        $content = $this->afterFilter($content);

        // Update cache if we're generating and were there <pre> tags generated
        if ($the_post_id && $this->cache_generated && !empty($this->cache)) {
            update_post_meta($the_post_id, 'flyn-syntax-cache-content', wp_slash($this->cache));
        }

        $this->matches = [];

        return $content;
    }

    public function afterFilterExcerpt(string $content): string
    {
        if (empty($this->matches)) {
            return $content;
        }

        global $post;
        $the_post    = $post;
        $the_post_id = $post->ID;

        // Reset cache settings on each filter - we might be showing
        // multiple posts on the one page
        $this->cache           = [];
        $this->cache_match_num = 0;
        $this->cache_generate  = false;

        if (is_object($the_post)) {
            $cache = get_post_meta($the_post_id, 'flyn-syntax-cache-excerpt', true);

            if (is_array($cache)) {
                $this->cache = $cache;
            } else {
                // Inform the highlight() method that we're regenning
                $this->cache_generate = true;
            }
        }

        $content = $this->afterFilter($content);

        // Update cache if we're generating and were there <pre> tags generated
        if (is_object($the_post) && $this->cache_generated && $this->cache) {
            update_post_meta($the_post_id, 'flyn-syntax-cache-excerpt', wp_slash($this->cache));
        }

        $this->matches = [];

        return $content;
    }

    public function afterFilterComment(string $content): string
    {
        if (empty($this->matches)) {
            return $content;
        }

        global $comment;
        $the_post    = $comment;
        $the_post_id = $comment->comment_ID;

        if (is_object($the_post)) {
            $cache = get_comment_meta($the_post_id, 'flyn-syntax-cache-comment', true);

            if (is_array($cache)) {
                $this->cache = $cache;
            } else {
                // Inform the highlight() method that we're regenning
                $this->cache_generate = true;
            }
        }

        $content = $this->afterFilter($content);

        // Update cache if we're generating and were there <pre> tags generated
        if (is_object($the_post) && $this->cache_generated && !empty($this->cache)) {
            update_comment_meta($the_post_id, 'flyn-syntax-cache-comment', wp_slash($this->cache));
        }

        $this->matches = [];

        return $content;
    }

    public function afterFilter(string $content): string
    {
        // global $flyn_syntax_token;

        $content = preg_replace_callback(
            '/<p>' . $this->token . '(\d{3})<\/p>/si',
            [$this, 'highlight'],
            $content
        );

        return $content;
    }
}
