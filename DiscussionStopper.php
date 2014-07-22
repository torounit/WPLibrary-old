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
        update_option( "default_ping_status", false );
        update_option( "default_pingback_flag", false );
        update_option( "default_comment_status", false );
    }

}
