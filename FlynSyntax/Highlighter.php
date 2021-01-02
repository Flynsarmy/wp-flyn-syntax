<?php

namespace FlynSyntax;

use GeSHi;

class Highlighter
{
    // GeSHi language to highlight
    public string $language = '';

    // Starting line number. If set to 0, no lines will display
    public int $firstLine = 0;

    // List of line numbers to highlight
    public array $highlightLines = [];

    // The syntax highlighter
    public GeSHi $geshi;

    // An optional caption to display above the code block
    public string $caption = '';

    // Code block to highlight
    public string $code = '';

    /**
     * Constructor method
     *
     * @param array $match [
     *      lang => string,        GeSHi language
     *      line => int,           Starting line number. 0 for no line numbers
     *      highlight => string,   Highlight line ranges
     *      [src => string],       Caption to display above code block
     *      code => string,        Code to highlight
     * ]
     */
    public function __construct(array $match = [])
    {
        $this->language         = strtolower(trim($this->arrayGet($match, 'lang', '')));
        $this->firstLine        = intval(trim($this->arrayGet($match, 'line', 0)));
        //$escaped              = trim($match[3]);
        $lines = $this->parseHighlightLines($this->arrayGet($match, 'highlight', ''));
        $lines = $this->getRelativeLines($lines, $this->firstLine);
        $this->highlightLines   = $lines;
        $this->caption          = $this->caption($this->arrayGet($match, 'src', ''));
        $this->code             = htmlspecialchars_decode(trim($this->arrayGet($match, 'code', '')));

        $this->geshi = $this->initGeshi($this->language, $this->code);
    }

    /**
     * Set up GeSHi - our syntax highlighter.
     *
     * @param string $language
     * @param string $code
     * @return GeSHi
     */
    public function initGeshi(string $language, string $code): GeSHi
    {
        $geshi = new GeSHi($code, $language);
        $geshi->enable_classes();
        $geshi->enable_keyword_links(false);

        if (!empty($this->highlightLines)) {
            $geshi->highlight_lines_extra($this->highlightLines);
        }

        // This is required because the function doesn't exist in PHPUnit
        if (function_exists('do_action_ref_array')) {
            do_action_ref_array('flyn_syntax_init_geshi', [&$geshi]);
        }

        return $geshi;
    }

    /**
     * Converts our provided list of highlight ranges into an array of line
     * numbers to highlight.
     *
     * @param string $lineRanges    e.g 1-5, 9, 7, 19-30
     * @return array All line numbers to highlight
     */
    public function parseHighlightLines(string $lineRanges): array
    {
        $lines = [];

        // Nothing to do here
        if (strlen($lineRanges) === 0) {
            return $lines;
        }

        // Break up into individual ranges
        $ranges = explode(',', $lineRanges);
        $ranges = array_map('trim', $ranges);

        foreach ($ranges as $range) {
            // A single line number
            if (is_numeric($range)) {
                $lines[] = intval($range);
            // A range. e.g 4-7
            } elseif (strpos($range, "-") !== false) {
                // Break up the range into start and end line integers
                $startEnd = explode('-', $range, 2);
                $startEnd = array_map('intval', $startEnd);
                // Make sure they're valid line numbers
                if ($startEnd[0] > 0 && $startEnd[1] > $startEnd[0]) {
                    // Add them to our lines array
                    $lines = array_merge($lines, range($startEnd[0], $startEnd[1]));
                }
            }
        }

        return $lines;
    }

    /**
     * Returns a list of lines to highlight relative to the starting line.
     * When we start on line 3, highlight="4-5" means second and third lines.
     *
     * @param array $lines          List of lines to highlight
     * @param integer $firstLine    First line number
     * @return array                List of lines to highlight
     */
    public function getRelativeLines(array $lines, int $firstLine): array
    {
        if ($firstLine === 0) {
            return $lines;
        }

        return array_map(function ($highlightLine) use ($firstLine) {
            return $highlightLine - ($firstLine - 1);
        }, $lines);
    }

    /**
     * Render a code block
     *
     * @return string
     */
    public function render(): string
    {
        $output  = '<style>' . $this->geshi->get_stylesheet() . '</style>';
        $output .= "\n" . '<div class="flyn_syntax">';
        $output .= '<table>';

        if (!empty($this->caption)) {
            $output .= '<caption>' . $this->caption . '</caption>';
        }

        $output .= '<tr>';

        if ($this->firstLine) {
            $output .= '<td class="line_numbers">' . $this->lineNumbers($this->code, $this->firstLine) . '</td>';
        }

        $output .= '<td class="code">';
        $output .= $this->geshi->parse_code();
        $output .= '</td></tr></table>';
        $output .= '</div>' . "\n";

        return $output;
    }

    /**
     * Displays the code blocks line numbers
     *
     * @param string $code
     * @param integer $firstLine
     * @return string
     */
    public function lineNumbers(string $code, int $firstLine): string
    {
        $line_count = count(explode("\n", $code));
        $output     = '<pre>';

        for ($i = $firstLine; $i < $line_count + $firstLine; $i++) {
            $output .= $i . "\n";
        }

        $output .= '</pre>';

        return $output;
    }

    /**
     * Adds a caption above the code block
     *
     * @param string $url
     * @return string
     */
    protected function caption(string $url): string
    {
        $parsed  = parse_url($url);
        $path    = pathinfo($parsed['path']);
        $caption = '';

        if (!isset($path['filename'])) {
            return "";
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
     * Helper function for returning array values if they exist or a given
     * default if they don't.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function arrayGet(array $array, $key, $default = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }
}
