<?php
/**
 * EntityManager
 * @ver 2.0 Sybil 05/15/2009
 * v2.1 fixed dataFilter hash problem
 * v2.2 07/06/2009 add M/S support
 * v2.3 08/20/2009 fixed count $arg DataFilter problem
 * @version 2.3 Sybil 08/20/2009
 * @author GuangXiN <rek@rek.me>
 */
class EntityManager {
	public static $cache = true;
	public static function useCache() {
		if(self::$cache === true) {
			self::$cache = new MCache;
			#echo 'Use MCache';
		} else if(self::$cache === false) {
			#echo 'Use NoCache';
			self::$cache = new NoCache;
		}
	}
	public static function add(&$entity) {
		self::useCache();
		if(!$entity instanceof DataEntity) {
			throw new ArgumentException('entity');
		}
		$r = $entity->add();
		$mc = self::$cache;
		$mc->set(get_class($entity).'-'.$entity->id, $entity);
		return $r;
	}
	public static function update(&$entity) {
		self::useCache();
		if(!$entity instanceof DataEntity) {
			throw new ArgumentException('entity');
		}
		$r = $entity->update();
		$mc = self::$cache;
		$mc->set(get_class($entity).'-'.$entity->id, $entity);
		return $r;
	}
	public static function getById(&$entity, $id, $realTime=false) {
		if($realTime) self::$cache = false;
		self::useCache();
		$mc = self::$cache;
		return $mc->get(get_class($entity).'-'.$id, array($entity, 'getById'), array($id, $realTime));
	}
	public static function getArray(&$entity, $arg, $realTime=false) {
		if($realTime) self::$cache = false;
		self::useCache();
		$mc = self::$cache;
		if(is_string($arg)) {
			$sql = $arg;
		} else if($arg instanceof DataFilter){
			$sql = $arg->getSQL();
		}
		return $mc->get(get_class($entity).'|'.hash('md4', $sql), array($entity, 'getArray'), array($arg, $realTime));
	}
	public static function count(&$entity, $arg=null, $realTime=false) {
		if($realTime) self::$cache = false;
		self::useCache();
		$mc = self::$cache;
		if(is_string($arg)) {
			$sql = $arg;
		} else if($arg instanceof DataFilter) {
			$sql = $arg->getSQL();
		}
		return $mc->get(get_class($entity).'|COUNT'.hash('md4', $sql), array($entity, 'count'), array($arg, $realTime));
	}
	public static function remove(&$entity, $id, $justMark=true) {
		self::useCache();
		$mc = self::$cache;
		$mc->del(get_class($entity).'-'.$id);
		return $entity->remove($id, $justMark);
	}
}
?>