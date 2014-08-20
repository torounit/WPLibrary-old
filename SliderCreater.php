<?php

namespace Torounit\WPLibrary;

Class SliderCreater {

    private $id;
    private $title;
    private $size;
    private $post_type;

    public function __construct($id, $title, $size = "full", $post_type = "slide")
    {
        if (class_exists("ACF")) {

            $this->id = $id;
            $this->title = $title;
            $this->size = $size;
            $this->post_type = $post_type;

            $this->registerField();
            $this->registerPostType();
        }

    }

    private function createKey($value)
    {
        $value = md5($this->id.$value);

        return 'field_'. substr( $value, 0, 13);
    }

    private function registerField()
    {
        if (function_exists("register_field_group")) {

            $image_key = "";
            $url_key = "";

            register_field_group(array (
                'id' => $this->id,
                'title' => $this->title,
                'fields' => array (
                    array (
                        'key' => $this->createKey("image"),
                        'label' => 'image',
                        'name' => 'slideImage',
                        'type' => 'image',
                        'save_format' => 'id',
                        'preview_size' => 'slide',
                        //'library' => 'uploadedTo',
                        'library' => 'all',
                        ),
                    array (
                        'key' => $this->createKey("url"),
                        'label' => 'url',
                        'name' => 'slideUrl',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                        ),
                    ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => $this->post_type,
                            'order_no' => 0,
                            'group_no' => 0,
                            ),
                        ),
                    ),
                'options' => array (
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                        ),
                    ),
                'menu_order' => 0,
                ));
        }
    }

    private function registerPostType()
    {
        $cpt_manager = new CPTManager();
        $cpt_manager->addPostType( $this->title , $this->post_type, ["title", "page-attributes"], [ 'public' => false, 'show_ui' => true, 'menu_position' => 4 ]);
    }
}


