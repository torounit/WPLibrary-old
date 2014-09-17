<?php

namespace Torounit\WPLibrary;

class DiscussionStopper
{
    use Singleton;

    protected function initialize()
    {
        add_action( "init", array($this,"stop") );
    }

    public function stop()
    {

        add_filter("comments_open", "__return_false");
        add_filter("pings_open", "__return_false");

    }

}
