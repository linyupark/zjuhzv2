<?php

	/**
	 * 群组账本数据逻辑处理
	 *
	 */
	class Logic_Space_Group_Bill extends DbModel 
	{	
		public static function insert($params)
		{
			parent::Space()->insert('tb_group_bill', array(
				'uid' => Cmd::uid(),
				'gid' => $params['gid'],
				'item' => $params['item'],
				'sort' => $params['sort'],
				'num' => $params['num'],
				'memo' => $params['memo'],
				'inout' => $params['inout'],
				'time' => time()
			));
		}
		
		public static function getSort($gid)
		{
			return parent::Space()->fetchAll('
				SELECT DISTINCT `sort` 
				FROM `tb_group_bill` 
				WHERE `gid` = ?
			', $gid);
		}
	}

?>