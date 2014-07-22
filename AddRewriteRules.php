<?php
namespace Torounit\WPLibrary;
class AddRewriteRules {
    private $rule     = null;
    private $query    = null;
    private $callback = null;

    function __construct($rule, $query, $callback)
    {
        $this->rule     = $rule;
        $this->query    = $query;
        $this->callback = $callback;
        add_filter('query_vars', array(&$this, 'queryVars'));
        add_action(
            'generateRewriteRules',
            array(&$this, 'generateRewriteRules')
        );
        add_action('wp', array(&$this, 'wp'));
    }

    public function generateRewriteRules($wp_rewrite)
    {
        $new_rules[$this->rule] = $wp_rewrite->index . '?' . (
            strpos($this->query, '=') === FALSE
            ? $this->query . '=1'
            : $this->query
        );
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }

    private function parseQuery($query)
    {
        $query = explode('&', $query);
        $query = explode(
            '=',
            is_array($query) && isset($query[0]) ? $query[0] : $query
        );
        return (is_array($query) && isset($query[0]) ? $query[0] : $query);
    }

    public function queryVars($vars)
    {
        $vars[] = $this->parseQuery($this->query);
        return $vars;
    }

    public function wp()
    {
        if (get_query_var($this->parseQuery($this->query))) {
            call_user_func($this->callback);
        }
    }
}

// eol