<?php
namespace Torounit\WPLibrary;

/**
 * Expand body_class.
 *
 * Singletion.
 *
 * @package WPLibrary
 */
Class BodyClass {

    use Singleton;

    protected function initialize()
    {
        add_filter( "body_class", array($this, "addPageSlug"), 10 ,1);
        add_filter( "body_class", array($this, "addPostArchiveSlug"), 10 ,1);
        add_filter( "body_class", array($this, "addPostTypeSlug"), 10 ,1);
    }

    public function addPageSlug($classes)
    {
        global $post;
        if ( is_page() ) {
            $classes[] = $post->post_name;
        }

        return $classes;
    }

    public function addPostArchiveSlug($classes)
    {
        if (get_post_type() == "post" and !is_front_page()) {
            if ($id = get_option("page_for_posts")) {
                $page = get_page($id);
                $classes[] = $page->post_name;
            }
        }

        return $classes;
    }

    public function addPostTypeSlug($classes)
    {
        global $post;
        if (is_singular()) {
            $classes[] = $post->post_type."-slug-".$post->post_name;
        }

        return $classes;
    }

}
