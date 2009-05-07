<?php
	
	/**
	 * 新闻贴数据逻辑类
	 *
	 */
	class Logic_Space_Bar_News extends DbModel
	{
		/**
		 * 转移新闻分类
		 *
		 * @param unknown_type $sid
		 * @param unknown_type $tsid
		 */
		public static function mv($sid, $tsid)
		{
			$db = parent::Space();
			$sbar = $db->fetchAll('SELECT `tid` FROM `tb_news` WHERE `sort` = ?', $sid);
			$db->beginTransaction();
			try{
				foreach ($sbar as $b)
				{
					$db->update('tb_news', array('sort' => $tsid), 'tid = '.$b['tid']);
				}
				// 被转移的分类rate回归0,目标分类加rate
				$db->update('tb_news_sort', array('rate' => 0), 'sort = '.$sid);
				$db->update('tb_news_sort', array('rate' => new Zend_Db_Expr('rate + '.count($sbar))), 'sort = '.$tsid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 查看新闻
		 *
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('news' => 'tb_news'), 'bar.tid = news.tid');
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname','sign'));
			$select->joinLeft(array('s'=>'zjuhzv2_space.tb_news_sort'), 'news.sort = s.sort', 
							  array('sortname' => 's.name'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 获取分类
		 *
		 */
		public static function getSorts()
		{
			return parent::Space()->fetchAll('SELECT * FROM `tb_news_sort` ORDER BY `rate` DESC');
		}
		
		/**
		 * 创建新分类
		 *
		 * @param unknown_type $sort
		 */
		public static function createSort($sortname)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_news_sort', array(
					'name' => $sortname
				));
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', '创建失败，请确认没有重复创建，错误代码：'.$e->getMessage());
			}
		}
		
		/**
		 * 修改新闻帖
		 *
		 * @param unknown_type $params
		 */
		public static function mod($params, $tid)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				
				$db->update('tb_tbar', array(
					'title' => $params['title'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				), 'tid = '.$tid);
				
				$db->update('tb_news', array(
					'content' => $params['content'],
					'modtime' => time(),
					'tags' => serialize($params['tags']),
					'sort' => $params['sort']
				), 'tid = '.$tid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		
		/**
		 * 发布新闻贴
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function insert($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_tbar', array(
					'type' => $params['type'],
					'title' => $params['title'],
					'puber' => Cmd::uid(),
					'pubtime' => time(),
					'replytime' => time(),
					'group' => $params['group'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				));
				$tid = $db->lastInsertId();
				$db->insert('tb_news', array(
					'tid' => $tid,
					'content' => $params['content'],
					'sort' => $params['sort'],
					'tags' => serialize($params['tags'])
				));
				$db->update('tb_news_sort', array(
					'rate' => new Zend_Db_Expr('rate + 1')
				), 'sort = '.$params['sort']);
				$db->commit();
				return $tid;
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
	} 

?>