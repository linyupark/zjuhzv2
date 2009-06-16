<?php

	/**
	 * 群组账本数据逻辑处理
	 *
	 */
	class Logic_Space_Group_Bill extends DbModel 
	{	
		/**
		 * 计算某分类下所有款物的逻辑处理
		 *
		 * @param unknown_type $sort
		 */
		public static function recount($gid, $sort)
		{
			$db = parent::Space();
			// 所有in收入
			$in = $db->fetchAll('
				SELECT SUM(`num`) AS `income`, `item` 
				FROM `tb_group_bill` 
				WHERE `gid` = '.(int)$gid.' AND `sort` = ? AND `inout` = "in" 
				GROUP BY `item` 
				', $sort);
			// 所有out支出
			$out = $db->fetchAll('
				SELECT SUM(`num`) AS `output`, `item`   
				FROM `tb_group_bill` 
				WHERE `gid` = '.(int)$gid.' AND `sort` = ? AND `inout` = "out" 
				GROUP BY `item` 
			', $sort);
			return array('in' => $in, 'out' => $out);
		}
		
		public static function mod($params)
		{
			parent::Space()->update('tb_group_bill', array(
				'handler' => $params['handler'],
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
				'handler' => $params['handler'],
				'uid' => Cmd::uid(),
				'gid' => $params['gid'],
				'item' => $params['item'],
				'sort' => $params['sort'],
				'num' => $params['num'],
				'memo' => $params['memo'],
				'inout' => $params['inout'],
				'time' => $params['time']
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