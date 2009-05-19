<?php
	
	class Logic_Space_Bar_Comment extends DbModel 
	{	
		/**
		 * 回复加分
		 *
		 * @param unknown_type $uid
		 */
		public static function apt($uid)
		{
			$count = Cmd::getSess('cmt_num');
			if($count >= 5)
			{
				$point = 1;
				$count -= 5;
				parent::User()->update('tb_base', 
				array('point' => new Zend_Db_Expr('point + '.$point)), 'uid = '.$uid);
				Cmd::setSess('apt_tip', array('comment' => $point));
			}
			Cmd::setSess('cmt_num', $count+1);
		}
		
		/**
		 * 获取回帖总数
		 *
		 * @param unknown_type $tid
		 * @return unknown
		 */
		public static function num($tid)
		{
			$row = parent::Space()->fetchRow('SELECT COUNT(`id`) AS `numrows`  FROM `tb_comment` WHERE `tid` = ?', $tid);
			return $row['numrows'];
		}
		
		/**
		 * 屏蔽指定评论
		 *
		 * @param unknown_type $cid
		 * @return unknown
		 */
		public static function deny($cid)
		{
			if(Cmd::role() == 'master')
			{
				$r = parent::Space()->fetchRow('SELECT `deny` FROM `tb_comment` WHERE `id` = ?', $cid);
				$deny = ($r['deny'] == 0) ? 1 : 0;
				parent::Space()->update('tb_comment', array(
					'deny' => $deny
				), 'id = '.$cid);
				return $deny;
			}
			return false;
		}
		
		public static function save($params)
		{
			$db = parent::Space();
			if($params['cid'] == 0) // 插入
			{
				$db->beginTransaction();
				try {
					// 评论表
					$db->insert('tb_comment', array(
						'tid' => $params['tid'],
						'uid' => $params['uid'],
						'content' => $params['content'],
						'time' => time(),
						'nicky' => $params['nicky']
					));
					// 更新回复数
					$db->update('tb_tbar', array(
						'reply' => new Zend_Db_Expr('reply + 1'),
						'replyer' => $params['uid'],
						'rnicky' => $params['nicky'],
						'replytime' => time()
					), 'tid = '.$params['tid']);
					if(!Logic_Space_Bar::isJoin($params['tid'], $params['uid']))
					Logic_Space_Bar::join($params['tid'], $params['uid']);
					$db->commit();
				} catch (Exception $e) {
					$db->rollback();
					Alp_Sys::msg('exception', $e->getMessage());
				}
			}
			else // 修改
			{
				$db->update('tb_comment', array(
					'content' => $params['content'],
					'nicky' => $params['nicky']
				), 'id = '.$params['cid']);
			}
		}
	}

?>