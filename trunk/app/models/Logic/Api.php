<?php

	class Logic_Api extends DbModel
	{
		public static function alive()
		{
			return time() - 900; // 15分钟等待时间
		}
		
		/**
		 * 热心度相邻排行校友
		 *
		 */
		public function nebpoint($point)
		{
			$db = parent::User();
			$leader = $db->fetchRow('SELECT `point`,`uid`,`sex`,`username` FROM `tb_base` 
				WHERE `point` > '.$point.' ORDER BY `point` ASC LIMIT 1');
			if($point > 0)
			$pursuer = $db->fetchRow('SELECT `point`,`uid`,`sex`,`username` FROM `tb_base` 
				WHERE `point` < '.$point.' ORDER BY `point` DESC LIMIT 1');
			return array('leader'=>$leader, 'pursuer'=>$pursuer);
		}
		
		/**
		 * 热度百分比
		 *
		 * @param unknown_type $point
		 * @param unknown_type $sum
		 * @return unknown
		 */
		public function percentpoint($point, $sum = null)
		{
			if($sum == null) $sum = self::sumpoint('user');
			$usernum = self::regnum();
			$hotusernum = round($usernum/50); // 2%的总人数可以100度
			$div = parent::User()->fetchRow('SELECT `point` FROM `tb_base` 
				ORDER BY `point` DESC LIMIT '.($hotusernum-1).',1');
			if($point > $div['point']) return 100;
			return round(($point/$div['point'])*100);
		}
		
		/**
		 * 热心度排行
		 *
		 * @param unknown_type $point
		 * @param unknown_type $from
		 * @return unknown
		 */
		public static function rankpoint($point, $from = 'user')
		{
			if($from == 'user')
			$row = parent::User()->fetchRow('SELECT COUNT(`uid`) AS `rank` FROM `tb_base` WHERE `point` > '.$point);
			if($from == 'group')
			$row = parent::Space()->fetchRow('SELECT COUNT(`gid`) AS `rank` FROM `tb_group` WHERE `point` > '.$point);
			return $row['rank'];
		}
		
		/**
		 * 热心度累计
		 *
		 * @param unknown_type $from
		 */
		public static function sumpoint($from = 'user')
		{	
			if($from == 'user')
			$row = parent::User()->fetchRow('SELECT SUM(`point`) AS `points` FROM `tb_base` WHERE `point` > 0');
			if($from == 'group')
			$row = parent::Space()->fetchRow('SELECT SUM(`point`) AS `points` FROM `tb_group` WHERE `point` > 0');
			return $row['points'];
		}
		
		/**
		 * 返回在线数据
		 *
		 * @return unknown
		 */
		public static function online()
		{
			$alive = self::alive();
			return parent::getSqlite('online.s3db')->fetchAll('SELECT * FROM `ol` WHERE `time` > '.$alive);
		}
		
		/**
		 * 返回在线人数值
		 *
		 * @return unknown
		 */
		public static function onlinex()
		{
			$alive = self::alive();
			$cache = Logic_Cache::factory('Core', 60);
			if(!$row = $cache->load('onlinex'))
			{
				$row = parent::getSqlite('online.s3db')->fetchRow('SELECT COUNT(`uid`) AS numrows FROM `ol` WHERE `time` > '.$alive);
				$cache->save($row);
			}
			return $row['numrows'];
		}
		
		/**
		 * 帖子的一些公用命令
		 *
		 * @param unknown_type $tid
		 * @param unknown_type $cmd
		 * @return unknown
		 */
		public static function barcmd($tid, $cmd)
		{
			$db = DbModel::Space();
			try {
				switch ($cmd)
				{
					case 'ding' : 
						$db->update('tb_tbar', array('ding' => 1), 'tid = '.$tid);
					break;
					case 'unding' : 
						$db->update('tb_tbar', array('ding' => 0), 'tid = '.$tid);
					break;
					case 'deny' : 
						$db->update('tb_tbar', array('deny' => 1), 'tid = '.$tid);
					break;
					case 'undeny' : 
						$db->update('tb_tbar', array('deny' => 0), 'tid = '.$tid);
					break;
				}
				return true;
			
			} catch (Exception $e) {
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
		
		/**
		 * 注册总人数
		 *
		 */
		public static function regnum()
		{
			$row = parent::User()->fetchRow('SELECT COUNT(`uid`) AS `numrows` FROM `tb_base`');
			return $row['numrows'];
		}
		
		/**
		 * 返回指定uid的用户所有信息
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function user($uid)
		{
			return parent::User()->fetchAll('SELECT * FROM `tb_base` 
				LEFT JOIN `tb_contact` ON `tb_base`.uid = `tb_contact`.uid 
				LEFT JOIN `tb_intro` ON `tb_base`.uid = `tb_intro`.uid 
				LEFT JOIN `tb_privacy` ON `tb_base`.uid = `tb_privacy`.uid 
				LEFT JOIN `tb_edu` ON `tb_base`.uid = `tb_edu`.uid 
				LEFT JOIN `tb_career` ON `tb_base`.uid = `tb_career`.uid 
				WHERE `tb_base`.uid = ?', $uid);
		}
	}

?>