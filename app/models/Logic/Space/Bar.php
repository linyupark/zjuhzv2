<?php

	class Logic_Space_Bar extends DbModel 
	{
		/**
		 * 是否允许查看
		 *
		 * @param unknown_type $private
		 * @param unknown_type $puber
		 * @param unknown_type $group
		 * @return unknown
		 */
		public static function isAllowed($private, $puber, $group = null)
		{
			switch ($private)
			{
				case 0 : //只有自己看
					return $puber == Cmd::uid() ?  true : false;
				break;
				case 1 : // 好友
					return Logic_Space_Friends::hasFriend($puber, Cmd::uid()) == false ? false : true;
				break;
				case 2 : // 群组
					return Logic_Space_Group_Member::has($group, Cmd::uid());
				break;
				case 3 : // 成员
					return Cmd::uid();
				break;
				default: // 公开
					return true;
				break;
			}
		}
		
		/**
		 * 更新帖子点击
		 *
		 * @param unknown_type $tid
		 */
		public static function click($tid)
		{
			$history = Cmd::getSess('bar_history');
			if(!$history) $history = array(); // 如果不存在则初始化
			if(!isset($history[$tid]))
			{
				$history[$tid] = time();
				Cmd::setSess('bar_history', $history);
			}
		}
		
		/**
		 * 帖子唯一性
		 *
		 * @param unknown_type $title
		 * @return unknown
		 */
		public static function unique($title)
		{
			return parent::Space()->fetchRow('SELECT `tid` FROM `tb_tbar` WHERE `title` = ?', $title);
		}
		
		/**
		 * 参与的帖子
		 *
		 * @param unknown_type $uid
		 */
		public static function join($uid)
		{
			return parent::Space()->fetchRow('SELECT `tid` FROM `tb_tjoin` WHERE `uid` = ?', $uid);
		}
		
		/**
		 * 收藏的帖子
		 *
		 * @param unknown_type $uid
		 */
		public static function fav($uid, $type)
		{
			return parent::Space()->fetchRow('SELECT `'.$type.'` FROM `tb_tfav` WHERE `uid` = ?', $uid);
		}
	}

?>