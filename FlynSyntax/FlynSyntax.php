<?php

namespace FlynSyntax;

use FlynSyntax\Highlighter;

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
     * Create a highlighted code block from a given unique identifier regex match
     * created in beforeFilter
     *
     * @param array $match         [ 0 => full_str, 1 => match_id ]
     * @return string
     */
    public function highlight(array $match_details): string
    {
        // Keep track of which <pre> tag we're up to
        $this->cache_match_num++;
        // We haven't generated any new cache yet.
        $this->cache_generated = false;

        // Do we have cache? Serve it!
        if (isset($this->cache[$this->cache_match_num])) {
            return '<!-- from cache -->' . $this->cache[$this->cache_match_num] . '<!-- end from cahe -->';
        }

        $i     = intval($match_details[1]);
        $match = $this->matches[$i];

        $highlighter = new Highlighter($match);

        $output = $highlighter->render();
        
        if ($this->cache_generate) {
            $this->cache_generated = true;
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
        $params = [
            'lang' => "lang=[\"'](?P<lang>[\w-]+)[\"']",
            'line' => "line=[\"'](?P<line>\d*)[\"']",
            'escaped' => "escaped=[\"'](?P<escaped>1|0|true|false)[\"']",
            'highlight' => "highlight=[\"'](?P<highlight>(?:\d+[,-])*\d+)[\"']",
            'src' => "src=[\"'](?P<src> [^\"']+)[\"']",
            'class' => "class=[\"']wp-block-flynsarmy-syntax-editor[\"']",
        ];
        // phpcs:disable Generic.Files.LineLength.TooLong
        $regex = "/\s*<pre(?:{$params['lang']}|{$params['line']}|{$params['escaped']}|{$params['highlight']}|{$params['src']}|{$params['class']}|\s)+>(?P<code>.*)<\/pre>\s*/siU";

        // <pre lang='somelang' line='1' escaped='1|true' highlight='1,2,3,4-7' src='my string'>
        return preg_replace_callback(
            $regex,
            function ($match) {
                // No language found? This isn't a code block. Return it unaltered.
                if (empty($match[1])) {
                    return $match[0];
                }
        
                $i = count($this->matches);
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
        
        $this->setupCache($post);
        $content = $this->afterFilter($content);

        // Update cache if we're generating and were there <pre> tags generated
        if ($post && $this->cache_generated && !empty($this->cache)) {
            update_post_meta($post->ID, 'flyn-syntax-cache-content', wp_slash($this->cache));
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

        $this->setupCache($post);
        $content = $this->afterFilter($content);

        // Update cache if we're generating and were there <pre> tags generated
        if (is_object($post) && $this->cache_generated && !empty($this->cache)) {
            update_post_meta($post->ID, 'flyn-syntax-cache-excerpt', wp_slash($this->cache));
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
        
        $this->setupCache($comment);
        $content = $this->afterFilter($content);

        // Update cache if we're generating and were there <pre> tags generated
        if (is_object($comment) && $this->cache_generated && !empty($this->cache)) {
            update_comment_meta($comment->comment_ID, 'flyn-syntax-cache-comment', wp_slash($this->cache));
        }

        $this->matches = [];

        return $content;
    }

    /**
     * Sets up the initial cache settings for a post in order to determine
     * whether or not we actually do need to cache or if a cache has already
     * been generated for this post.
     *
     * @param \WP_Post|null $post
     * @return void
     */
    public function setupCache($post)
    {
        // Reset cache settings on each filter - we might be showing
        // multiple posts on the one page
        $this->cache           = [];
        $this->cache_match_num = 0;
        $this->cache_generate  = false;

        if (!is_object($post)) {
            return;
        }

        $cache = get_post_meta($post->ID, 'flyn-syntax-cache-content', true);

        if (is_array($cache)) {
            $this->cache = $cache;
        } else {
            // Inform the highlight() method that we're regenning
            $this->cache_generate = true;
        }
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
