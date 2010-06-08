<?php
/**
 * 为了向下兼容，保留此接口。Sybil使用PDO作为数据访问接口
 */
interface IDbOperator {
	function exec($sqlQuery);
	function getLastInsertId();
	function fetchNextRow(&$resutSet=null);
	function affectedRows();
}
?>