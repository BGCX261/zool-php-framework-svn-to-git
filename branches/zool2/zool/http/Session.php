<?php

namespace zool\http;

use zool\ComponentException;

/**
 * Featured session object. Use it only staticly.
 *
 * @author lzsolt
 *
 */
class Session{

	/**
	 *
	 * @var array
	 */
	private static $delegate = null;

	/**
	 *
	 * Initializes the static class.
	 * @throws ComponentException
	 */
	public static function init(){
		if(null == self::$delegate){

			self::$delegate = $_SESSION;

		}else{
			throw new ComponentException('Cannot init the class '. __CLASS__ .' again.');
		}
	}

	private function __construct(){}

	public static function get($key, $default = null){
		return array_key_exists($key, self::$delegate) ? self::$delegate[$key] : $default;
	}

	public static function set($key, $valiue){
		$previousValue = self::get($key);

		self::$delegate[$key] = $value;

		return $previousValue;
	}

}

Session::init();