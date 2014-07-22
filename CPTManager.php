<?php
namespace Torounit\WPLibrary;

/**
 * Custom post type & taxonomy manager
 *
 * @see http://2inc.org/blog/2012/03/16/1322/
 * @author Toro_Unit, Takashi Kitajima.
 * @package WPLibrary
 *
 */



Class CPTManager {

    private $post_types = [];
    private $post_types_in_dashboard = [];
    private $taxonomies = [];

    /**
     * Execute register.
     *
     * @access private
     */
    public function __construct()
    {
        add_action( 'init',function () { $this->registerTaxonomy(); } );
        add_action( 'init', function () { $this->registerPostType(); } );
        add_action( 'right_now_content_table_end',function () { $this->rightNowContentTableEnd(); } );
    }

    /**
     * Merge multidimensional array.
     *
     * Static Method.
     *
     * @access private
     * @param  Array $arg      base array.
     * @param  Array $override override $arg.
     * @return Array
     */
    private static function arrayMerge(Array $args, Array $override)
    {
        foreach ($override as $key => $val) {
            if ( isset( $args[$key] ) && is_array( $val ) ) {
                $args[$key] = self::arrayMerge( $args[$key] , $val );
            } else {
                $args[$key] = $val;
            }
        }

        return $args;
    }

    /**
     * Add post type.
     *
     * @access public
     * @param String $name    post type label.
     * @param String $slug    post type internal name.
     * @param Array  $support support.
     * @param Array  $option  option.
     */
    public function addPostType($name, $slug, Array $supports = [], Array $options = [])
    {
        $post_type = array(
            'name' => $name,
            'slug' => $slug,
            'supports' => $supports,
            'options' => $options
        );

        $this->post_types[] = $post_type;
        $this->post_types_in_dashboard[] = $slug;
    }

    /**
     * Wrapper register_post_type.
     *
     * @access private
     */
    private function registerPostType()
    {
        foreach ($this->post_types as $cpt) {
            if ( empty( $cpt['supports'] ) ) {
                $cpt['supports'] = array( 'title', 'editor' );
            }
            $labels = array(
                'name' => $cpt['name'],
                'singular_name' => $cpt['name'],
                'add_new_item' => $cpt['name'].'を追加',
                'add_new' => '新規追加',
                'new_item' => '新規追加',
                'edit_item' => $cpt['name'].'を編集',
                'view_item' => $cpt['name'].'を表示',
                'not_found' => $cpt['name'].'は見つかりませんでした',
                'not_found_in_trash' => 'ゴミ箱に'.$cpt['name'].'はありません。',
                'search_items' => $cpt['name'].'を検索',
            );
            $default_options = array(
                'public' => true,
                'has_archive' => true,
                'hierarchical' => false,
                'labels' => $labels,
                'menu_position' => 20,
                'supports' => $cpt['supports'],
                'rewrite' => array(
                    'slug' => $cpt['slug'],
                    'with_front' => false
                )
            );
            $args = self::arrayMerge( $default_options, $cpt['options'] );
            // 関連するカスタムタクソノミーがある場合は配列に持たせる
            $_taxonomies = [];
            foreach ($this->taxonomies as $custom_taxonomy) {
                $post_type = (is_array($custom_taxonomy['post_type'])) ? $custom_taxonomy['post_type'] : array($custom_taxonomy['post_type']);
                if ( in_array( $cpt['slug'], $post_type ) ) {
                    $_taxonomies[] = $custom_taxonomy['slug'];
                }
            }
            if ( !empty( $_taxonomies ) ) {
                $argsTaxonomies = array(
                    'taxonomies' => $_taxonomies
                );
                $args = array_merge( $args, $argsTaxonomies );
            }
            register_post_type( $cpt['slug'], $args );
        }
    }

    /**
     * Show Custom post type in dashboard
     *
     * @access private
     * @param String $post_type
     */
    private function rightNowContentTableEnd()
    {
        foreach ($this->post_types_in_dashboard as $custom_post_type) {
            global $wp_post_types;
            $num_post_type = wp_count_posts( $custom_post_type );
            $num = number_format_i18n( $num_post_type->publish );
            $text = _n( $wp_post_types[$custom_post_type]->labels->singular_name, $wp_post_types[$custom_post_type]->labels->name, $num_post_type->publish );
            $capability = $wp_post_types[$custom_post_type]->cap->edit_posts;

            if ( current_user_can( $capability ) ) {
                $num = "<a href='edit.php?post_type=" . $custom_post_type . "'>$num</a>";
                $text = "<a href='edit.php?post_type=" . $custom_post_type . "'>$text</a>";
            }

            echo '<tr>';
            echo '<td class="first b b_' . $custom_post_type . '">' . $num . '</td>';
            echo '<td class="t ' . $custom_post_type . '">' . $text . '</td>';
            echo '</tr>';
        }
    }

    /**
     * Add taxonomy.
     *
     * @access public
     * @param String $name      label.
     * @param String $slug      internal name.
     * @param Array  $post_type post type.
     * @param Array  $option    option.
     */
    public function addTaxonomy($name, $slug, $post_type, $options = [])
    {
        if (is_array($post_type)) {
            $post_type = array($post_type);
        }

        $taxonomy = array(
            'name' => $name,
            'slug' => $slug,
            'post_type' => $post_type,
            'options' => $options
        );
        $this->taxonomies[] = $taxonomy;
    }

    /**
     * Wrapper register_taxonomy.
     *
     * @access private
     */
    private function registerTaxonomy()
    {
        foreach ($this->taxonomies as $ct) {
            $default_options = array(
                'hierarchical' => false,
                'public' => true,
                'rewrite' => array(
                    'with_front' => false
                )
            );
            $ct['options'] = array_merge( $default_options, $ct['options'] );
            $ct['options']['label'] = $ct['name'];
            $ct['options']['singular_name'] = $ct['name'];
            register_taxonomy(
                $ct['slug'],
                $ct['post_type'],
                $ct['options']
            );
        }
    }
}
