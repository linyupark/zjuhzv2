<?php

	/**
	 * 群组账本数据逻辑处理
	 *
	 */
	class Logic_Space_Group_Bill extends DbModel 
	{	
		public static function mod($params)
		{
			parent::Space()->update('tb_group_bill', array(
				'uid' => Cmd::uid(),
				'item' => $params['item'],
				'sort' => $params['sort'],
				'num' => $params['num'],
				'inout' => $params['inout'],
				'memo' => $params['memo'],
			),'id = '.(int)$params['bid']);
		}
		
		public static function one($id)
		{
			return parent::Space()->fetchRow('
				SELECT * FROM `tb_group_bill` 
				WHERE `id` = ?
			', $id);
		}
		
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