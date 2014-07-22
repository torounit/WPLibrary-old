<?php
namespace Torounit\WPLibrary;

class AddPage {
    private $rule      = null;
    private $title     = null;
    private $callback  = null;
    private $template  = null;
    private $filter    = false;

    function __construct($rule, $title, $callback)
    {
        if (!class_exists('WP_AddRewriteRules')) {
            wp_die("Class WP_AddRewriteRules is not exists.");
        }
        $this->rule  = $rule;
        $this->title = $title;
        $this->callback  = $callback;
    }

    public function init()
    {
        new AddRewriteRules(
            $this->rule,
            'custom-'.preg_replace('/\W/', '', $this->rule),
            function() {
                $this->display();
            }
            );
    }

    public function setFilter($filter = false)
    {
        $this->filter = $filter;
        return true;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return true;
    }


    private function pageTemplate($template)
    {
        $tpl = dirname($template)."/".$this->template;
        if (is_file($tpl)) {
            return $tpl;
        }
        return $template;
    }

    private function display()
    {
        if ($this->template) {
            add_filter('page_template', function($template){
                return $this->pageTemplate($template);
            });
        }

        $content = call_user_func($this->callback);

        if ($this->filter === false) {
            global $wp_filter;
            unset($wp_filter['the_content']);
        }

        global $wp_query;
        $wp_query->is_404 = null;
        $wp_query->is_page = 1;
        $wp_query->is_single = null;
        $wp_query->is_singular = 1;
        $wp_query->is_search = null;
        $wp_query->is_archive = null;
        $wp_query->is_date = null;
        $wp_query->is_day = null;
        $wp_query->is_home = null;
        $wp_query->post_count = 1;
        $wp_query->post->post_title = $this->title;
        $wp_query->posts = array();
        $wp_query->posts[0]->ID = 0;
        $wp_query->posts[0]->post_author = 1;
        $wp_query->posts[0]->post_title = $this->title;
        $wp_query->posts[0]->comment_status = 'close';
        $wp_query->posts[0]->ping_status = 'close';
        $wp_query->posts[0]->post_type = 'page';
        $wp_query->posts[0]->post_status = 'publish';
        $wp_query->posts[0]->post_parent = 0;
        $wp_query->posts[0]->post_content = $content;
        $wp_query->posts[0]->post_date = time();
    }

}


// EOF