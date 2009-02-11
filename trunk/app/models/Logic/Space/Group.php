<?php

	/**
	 * 群组数据逻辑层
	 *
	 */
	class Logic_Space_Group extends DbModel 
	{	
		/**
		 * 新群信息
		 *
		 * @param unknown_type $num
		 * @return unknown
		 */
		public static function fresh($num)
		{
			return parent::Space()->fetchAll('
				SELECT * FROM `tb_group` ORDER BY `createtime` DESC LIMIT '.$num);
		}
		
		/**
		 * 更新最后访问时间
		 *
		 */
		public static function visit($gid, $uid)
		{
			parent::Space()->update('tb_group_member', 
				array('lastvisit' => time()), "gid = {$gid} AND uid = {$uid}");
		}
		
		/**
		 * 是否有群组访问权限
		 *
		 */
		public static function isAllowedVisit($gid, $uid)
		{
			$group = self::info($gid);
			if(!$group) return false;
			if($group['type'] == 'close')
			return Logic_Space_Group_Member::isMemeber($gid, $uid);
			
			return true;
		}
		
		/**
		 * 获取群组信息
		 *
		 * @param unknown_type $gid
		 * @return unknown
		 */
		public static function info($gid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group` WHERE `gid` = ?', $gid);
		}
		
		/**
		 * 建立新群组
		 *
		 */
		public static function create($params, $creater)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_group', array(
					'name' => $params['name'],
					'createtime' => time(),
					'intro' => $params['intro'],
					'type' => $params['type']
				));
				$gid = $db->lastInsertId();
				$db->insert('tb_group_member', array(
					'uid' => $creater,
					'gid' => $gid,
					'role' => 'creater'
				));
				$db->commit();
				return $gid;
				
			} catch (Exception $e){
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
	}
	
?>