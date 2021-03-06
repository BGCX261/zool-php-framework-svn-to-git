<?php

namespace zool\tools;

/**
 *
 * Enter description here ...
 * @author Zsolt Lengyel
 *
 */
class ZWatch{

	private $start;

	public function __construct(){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->start = $mtime;
	}

	public function stop(){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		return ($endtime - $this->start);
	}

	public function stopAndEcho(){
	  echo '<p>'. $this->stop(). ' secs from start.<p>';
	}

}
