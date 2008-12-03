<?php

	class Logic_Space_Bar_News extends DbModel
	{
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
					'group' => $params['group'],
					'private' => $params['private'],
					'nicky' => $params['nicky'],
					'public' => 0 //默认需要审核后才发布
				));
				$tid = $db->lastInsertId();
				$db->insert('tb_news', array(
					'tid' => $tid,
					'content' => $params['content'],
					'sort' => $params['sort'],
					'tags' => serialize($params['tags'])
				));
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