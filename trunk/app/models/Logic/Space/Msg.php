<?php

	class Logic_Space_Msg extends DbModel 
	{	
		/**
		 * 保证无重复信息
		 *
		 * @param unknown_type $type
		 * @param unknown_type $sender
		 * @param unknown_type $incept
		 */
		public static function unique($type, $sender, $incept)
		{
			return parent::Space()->fetchRow('SELECT `mid` FROM `tb_msg` 
									WHERE `type` = ? AND `sender` = ? AND `incept` = ? ', array($type, $sender, $incept));
		}
		
		/**
		 * 返回新信息数目
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function hasnew($uid, $type = null)
		{
			$where = '';
			if($type != null) $where = ' AND `type` = "'.$type.'"';
			$row = parent::Space()->fetchRow('SELECT COUNT(`mid`) AS `numrow` 
												FROM `tb_msg` WHERE `incept` = ? AND `isread` = 0'.$where, $uid);
			return ($row == false) ? 0 : $row['numrow'];
		}
		
		/**
		 * 变为已读信息
		 *
		 * @param unknown_type $mid
		 */
		public static function reading($mid)
		{
			parent::Space()->update('tb_msg', array('isread' => 1), 'mid = '.$mid);
		}
	}

?>