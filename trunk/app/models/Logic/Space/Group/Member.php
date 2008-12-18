<?php

	class Logic_Space_Group_Member extends DbModel 
	{	
		/**
		 * 返回指定gid所有成员信息
		 *
		 * @param unknown_type $gid
		 */
		public static function fetchAll($gid)
		{
			$row = parent::Space()->fetchRow('SELECT `member` FROM `tb_group` WHERE `gid` = ?', $gid);
			return ($row == false) ? false : unserialize($row['member']);
		}
		
		/**
		 * 指定gid的群组是否有uid成员
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 */
		public static function has($gid, $uid)
		{
			$row = self::fetchAll($gid);
			if($row != false) return $row[$uid];
			else return false;
		}
	}
	
?>