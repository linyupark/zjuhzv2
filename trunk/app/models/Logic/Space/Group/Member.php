<?php

	class Logic_Space_Group_Member extends DbModel 
	{	
		/**
		 * 是否为群组的创建者
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isCreater($gid, $uid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group_member` 
												WHERE `gid` = ? AND `uid` = ? AND `role` = "creater"', 
			array($gid, $uid));
		}
		
		/**
		 * 是否为群组成员
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isMemeber($gid, $uid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group_member` 
				WHERE `gid` = ? AND `uid` = ? AND `role` = "member" OR `role` = "creater" OR `role` = "manager"', 
			array($gid, $uid));
		}
		
		/**
		 * 是否为群组管理员
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isManager($gid, $uid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group_member` 
												WHERE `gid` = ? AND `uid` = ? AND `role` = "manager"', 
			array($gid, $uid));
		}
		
		/**
		 * 群组所有成员数量
		 *
		 */
		public static function num($gid)
		{
			$row = parent::Space()->fetchRow('SELECT COUNT(`uid`) AS `numrow` FROM `tb_group_member` 
				WHERE `gid` = ? AND `role` = "member" OR `role` = "creater" OR `role` = "manager"', 
			$gid);
			return $row['numrow'];
		}
		
		/**
		 * 罗列群组名单
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $role
		 */
		public static function all($gid, $role = 'member')
		{
			return parent::Space()->fetchAll('SELECT * FROM `tb_group_member` 
												WHERE `gid` = ? AND `role` = ?', 
			array($gid, $role));
		}

	}
	
?>