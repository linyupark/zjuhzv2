<?php

	/**
	 * 群组数据逻辑层
	 *
	 */
	class Logic_Space_Group extends DbModel 
	{	
		/**
		 * 是否有群组访问权限
		 *
		 */
		public static function isAllowedVisit($gid, $uid)
		{
			$group = self::info($gid);
			switch ($group['type'])
			{
				case 'close' : 
					return Logic_Space_Group_Member::isManager($gid, $uid);
				break;
				default :
					return true;
				break;
			}
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