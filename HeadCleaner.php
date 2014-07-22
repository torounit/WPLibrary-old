<?php

namespace Torounit\WPLibrary;

class HeadCleaner
{
    use Singleton;

    protected function initialize()
    {
        add_action( "init", array($this,"setOptionValues") );
    }

    public function setOptionValues()
    {
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_generator');

    }

}
