<?php

	class Logic_Space_Msg extends DbModel 
	{	
		/**
		 * 返回新信息数目
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function hasnew($uid)
		{
			$row = parent::Space()->fetchRow('SELECT COUNT(`mid`) AS `numrow` 
												FROM `tb_msg` WHERE `incept` = ? AND `isread` = 0', $uid);
			return ($row == false) ? 0 : $row['numrow'];
		}
	}

?>