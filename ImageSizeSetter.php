<?php

namespace Torounit\WPLibrary;

Class ImageSizeSetter {

    private $image_sizes = array();

    public function __construct()
    {
        add_action( 'after_setup_theme', array($this, 'addImageSizes') );
        add_filter( 'image_size_names_choose', array($this, 'addImageSizeSelect') );
    }

    public function addSize($name, $width, $height, $crop, $selectable = false, $label = "")
    {
        $this->image_sizes[$name] = array(
            'label'       => $label, // 選択肢のラベル名
            'width'      => $width,    // 最大画像幅
            'height'     => $height,    // 最大画像高さ
            'crop'       => $crop,  // 切り抜きを行うかどうか
            'selectable' => $selectable   // 選択肢に含めるかどうか
        );
    }

    public function addImageSizes()
    {
        foreach ($this->image_sizes as $slug => $size) {
            add_image_size( $slug, $size['width'], $size['height'], $size['crop'] );
        }
    }

    public function addImageSizeSelect($size_names)
    {
        $custom_sizes = get_intermediate_image_sizes();
        foreach ($custom_sizes as $custom_size) {
            if ( isset( $this->image_sizes[$custom_size]['selectable'] ) && $this->image_sizes[$custom_size]['selectable'] ) {
                $size_names[$custom_size] = $this->image_sizes[$custom_size]['label'];
            }
        }
        return $size_names;
    }
}

// function add_selectable_image_size($name, $width = 0, $height = 0, $crop = false, $label = "") {
//  $asis = new Add__selectable__image__size();
//  if ($label == "") {
//      $label = $name;
//  }
//  $asis->add_image_size( $name, $width, $height, $crop , $label, true );
// }
