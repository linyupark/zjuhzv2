<?php

	class Logic_Addon_League extends DbModel 
	{
		/**
		 * 非重复选项获取
		 *
		 */
		public static function opt($opt)
		{
			$db = parent::getSqlite('league.s3db');
			return $db->fetchAll('SELECT DISTINCT `'.$opt.'` FROM `tb_corp`');
		}
		
		public static function del($id)
		{
			parent::getSqlite('league.s3db')->delete('tb_corp', 'corp_id = '.$id);
		}
		
		public static function update($params)
		{
			$db = parent::getSqlite('league.s3db');
			$db->update('tb_corp', array(
				'uid' => $params['uid'],
				'region' => $params['region'],
				'trade' => $params['trade'],
				'name' => $params['name'],
				'web' => $params['web'],
				'url' => $params['url'],
				'attr' => $params['attr'],
				'intro' => $params['intro']
			), 'corp_id = '.$params['id']);
		}
		
		/**
		 * 增加新联盟企业
		 *
		 * @param unknown_type $params
		 */
		public static function insert($params)
		{
			$db = parent::getSqlite('league.s3db');
			$db->insert('tb_corp', array(
				'uid' => $params['uid'],
				'region' => $params['region'],
				'trade' => $params['trade'],
				'name' => $params['name'],
				'web' => $params['web'],
				'url' => $params['url'],
				'attr' => $params['attr'],
				'intro' => $params['intro']
			));
		}
	}

?>