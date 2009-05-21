<?php

	class Logic_Space_Bar extends DbModel 
	{
		/**
		 * 给发帖的人增加热心度
		 *
		 * @param unknown_type $uid
		 */
		public static function pubapt($uid)
		{
			$point = 1;
			parent::User()->update('tb_base', 
			array('point' => new Zend_Db_Expr('point + '.$point)), 'uid = '.$uid);
		    Cmd::setSess('apt_tip', array('pubbar' => $point));
		}
		
		/**
		 * 返回指定uid所发布的tids
		 *
		 */
		public static function ids($uid)
		{
			return parent::Space()->fetchAll('SELECT `tid` FROM `tb_tbar` WHERE `puber` = ?', $uid);
		}
		
		/**
		 * 回复总数返回评论分页数
		 *
		 * @param unknown_type $replys
		 */
		public static function subpage($row, $pagesize = 10)
		{
			$cpage = ceil($row['reply'] / $pagesize);
			$str = '';
			if($cpage > 1)
			{
				$str = '<img class="vm" src="'.Alp_Url::img('icon/mix/subpage.gif').'" />&nbsp;';
				for($cp = 1;$cp <= $cpage;$cp ++)
				{
					if($cp > 3 && $cp < ($cpage-3))
					$str .= '.';
					else $str .= '<a href="/space_bar/'.$row['type'].'/view?tid='.$row['tid'].'&p='.$cp.'">'.$cp.'</a>&nbsp;';
				}
			}
			return preg_replace('/([\.]{2})/', '.', $str);
		}
		
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
			$role = Cmd::role(); $uid = Cmd::uid(); $honor = Cmd::honor();
			if($role == 'master' || 
				$honor == '理事' || 
				$honor == '顾问' || 
				$honor == '副会长') return true;
			switch ($private)
			{
				case 0 : //只有自己看
					return $puber == $uid ?  true : false;
				break;
				case 1 : // 好友
					return (Logic_Space_Friends::hasFriend($puber, $uid) == 1 || $puber == $uid) ? 
					true : false;
				break;
				case 2 : // 群组
					return Logic_Space_Group_Member::isMemeber($group, $uid);
				break;
				case 3 : // 成员
					return $uid;
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
		public static function click($tid, $gid = 0)
		{
			if($gid == 0) $name = 'bar_history';
			else $name = 'group_'.$gid.'_history';
			
			$history = Cmd::getSess($name);
			if(!isset($history[$tid]))
			{
				parent::Space()->update('tb_tbar', array(
					'click' => new Zend_Db_Expr('click + 1')
				), 'tid = '.$tid);
			}
			$history[$tid] = time();
			Cmd::setSess($name, $history);
		}
		
		/**
		 * 顶帖子
		 *
		 * @param unknown_type $tid
		 * @param unknown_type $v
		 */
		public static function rate($tid, $v = 1)
		{
			$rate = Cmd::getSess('bar_rate');
			if(!$rate) $rate = array(); // 如果不存在则初始化
			if(!isset($rate[$tid]))
			{
				$rate[$tid] = time();
				Cmd::setSess('bar_rate', $rate);
				parent::Space()->update('tb_tbar', array(
					'rate' => new Zend_Db_Expr('rate + '.$v)
				), 'tid = '.$tid);
				return true;
			}
			else return false;
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
		public static function getJoin($uid)
		{
			return parent::Space()->fetchRow('SELECT `tid` FROM `tb_tjoin` WHERE `uid` = ?', $uid);
		}
		
		public static function isJoin($tid, $uid)
		{
			$r = self::getJoin($uid);
			if($r == false) return false;
			else
			{
				$tid_arr = unserialize($r['tid']);
				if(isset($tid_arr[$tid])) return true;
				else return false; 
			}
		}
		
		public static function join($tid, $uid)
		{
			$r = self::getJoin($uid);
			if($r == false)
			{
				parent::Space()->insert('tb_tjoin', array('uid' => $uid));
			}
			$join_arr = unserialize($r['tid']);
			$join_arr[$tid] = time();
			parent::Space()->update('tb_tjoin', array('tid' => serialize($join_arr)), 'uid = '.$uid);
		}
		
		/**
		 * 获取指定类型的收藏帖
		 *
		 * @param unknown_type $type
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function getFav($type, $uid)
		{
			return parent::Space()->fetchRow('SELECT `'.$type.'` FROM `tb_tfav` WHERE `uid` = ?', $uid);
		}
		
		/**
		 * 是否为收藏帖
		 *
		 * @param unknown_type $row
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isFav($row, $uid)
		{
			$r = self::getFav($row['type'], $uid);
			if($r == false) return false;
			else 
			{
				$fav_arr = unserialize($r[$row['type']]);
				if(isset($fav_arr[$row['tid']])) return true;
				else return false; 
			}
		}
		
		/**
		 * 收藏的帖子开关
		 *
		 * @param unknown_type $uid
		 */
		public static function fav($row, $uid)
		{
			$r = self::getFav($row['type'], $uid);
			if($r == false)
			{
				parent::Space()->insert('tb_tfav', array('uid' => $uid));
			}
			$fav_arr = unserialize($r[$row['type']]);
			if(self::isFav($row, $uid))
			{
				// 取消收藏
				unset($fav_arr[$row['tid']]);
				parent::Space()->update('tb_tfav', array(
					$row['type'] => serialize($fav_arr)
				), 'uid = '.$uid);
				return 'off';
			}
			else 
			{
				$fav_arr[$row['tid']] = time();
				parent::Space()->update('tb_tfav', array(
					$row['type'] => serialize($fav_arr)
				), 'uid = '.$uid);
				return 'on';
			}
		}
	}

?>