<?php
	
	class Logic_Space_Bar_Comment extends DbModel 
	{
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
					$db->commit();
				} catch (Exception $e) {
					$db->rollback();
					Alp_Sys::msg('exception', $e->getMessage());
				}
			}
		}
	}

?>