<?php

namespace Torounit\WPLibrary;

/**
 *
 * trait Singlton.
 *
 * */

trait Singleton
{
    private static $instance = null;


	final private function __construct()
    {
        static::initialize();
    }

    abstract protected function initialize(); # ここでコンストラクタの初期化実装

	/**
	 * @return self
	 */
	final public static function getInstance()
    {
	    if( self::$instance == null )
	    {
		    self::$instance = new self();
	    }
	    return self::$instance;
    }

	/**
	 * @throws Exception
	 */
	final private function __clone()
    {
        throw new Exception('You can not clone a singleton.');
    }
}
