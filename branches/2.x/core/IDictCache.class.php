<?php
/**
 * IDictCache
 * @version 0.1 04/09/2009
 * @author GuangXiN <rek@rek.me>
*/
interface IDictCache {
	function get($key, $callback=null, $state=null, $timeout=null);
	function set($key, $value, $timeout=null);
	function del($key=null); // delete all if $key is null
}
?>