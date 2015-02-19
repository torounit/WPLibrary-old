<?php
namespace Torounit\WPLibrary;

/**
 * Assets initialize
 *
 * @package WPLibrary
 */
Class AssetsInit {

    use Singleton;

    public $styles;
    public $scripts;
    public $prefetch;


//	public function __construct() {
//		$this->addHook();
//	}

    /**
     * initialize
     *
     * @access protected
     * @see \Torounit\WPLibrary\Singleton::initialize
     */
    protected function initialize()
    {
        $styles = array();
        $scripts = array();
        $prefetch = array();

        $this->addHook();
    }

    /**
     * register hook.
     *
     * @access private
     */
    private function addHook()
    {
        add_action( "wp_enqueue_scripts", function () {
            $this->enqueueStyles();
            $this->enqueueScripts();
        } );

        add_action( "wp_head", function () {
            $this->dnsPrefetch();

        }, 1 );
    }

    /**
     *
     * create link[rel=dns-prefetch]
     *
     * @access private
     */
    private function dnsPrefetch()
    {
        if( !empty($this->prefetch) ):
            echo "<meta http-equiv='x-dns-prefetch-control' content='on'>\n";
            foreach($this->prefetch as $url):
                echo "<link rel='dns-prefetch' href='//$url'>\n";
            endforeach;
        endif;
    }

    /**
     *
     * Add DNS prefetch domain.
     *
     * @access public
     * @param String $url asset url.
     */
    public function addPrefetch($url)
    {
        $url = preg_replace("/^\/\//", "http://", $url);
        $param = parse_url($url);
        if(isset($param["host"])) {
            $host = $param["host"];
            $myhost = parse_url(home_url());
            $myhost = $myhost["host"];
            if($myhost != $host) {
                if ( empty($this->prefetch) or !in_array($host, $this->prefetch) ) {
                    $this->prefetch[] = $host;
                }
            }
        }
    }

    /**
     *
     * Add stylesheet.
     *
     * @access public
     * @param String $name stylesheet name.
     * @param String $url  stylesheet URL.
     */
    public function addStyle($name, $url)
    {
        $this->styles[$name] = array("name" => $name, "url" => $url);
        $this->addPrefetch($url);
    }

    /**
     * register queue stylesheets.
     *
     * @access private
     */
    private function enqueueStyles()
    {
        if (!empty($this->styles)) {
            foreach ($this->styles as $style) {
                wp_enqueue_style( $style["name"], $style["url"], array(), "1" );
            }
        }

    }

    /**
     * Add Javascript.
     *
     * @access public
     * @param String  $name      stylesheet name.
     * @param String  $url       stylesheet URL.
     * @param Array   $deps      dependency script (ex. jquery, underscore)
     * @param Boolean $in_footer allow place in footer
     */
    public function addScript($name, $url, $deps = array(), $in_footer = false)
    {
        $this->scripts[$name] = array("name" => $name, "url" => $url, "deps" => $deps, "in_footer" => $in_footer);
        $this->addPrefetch($url);
    }

    /**
     * register queue JavaScript.
     *
     * @access private
     */
    private function enqueueScripts()
    {
        if (!empty($this->scripts)) {
            foreach ($this->scripts as $script) {
                wp_deregister_script($script["name"]);
                wp_enqueue_script($script["name"], $script["url"], $script["deps"], null, $script["in_footer"] );
            }
        }

    }
}
