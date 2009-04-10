<?php

	class Logic_Mix extends DbModel
	{
		public static function dellink($id)
		{
			parent::getSqlite('mix.s3db')->delete('tb_links', 'id = '.$id);
		}
		
		public static function updatelink($params)
		{
			$db = parent::getSqlite('mix.s3db');
			$db->update('tb_links', array(
				'serid' => $params['serid'],
				'name' => $params['name'],
				'url' => $params['url'],
				'logo' => $params['logo'],
				'home' => $params['home']
			), 'id = '.$params['id']);
		}
		
		public static function addlink($params)
		{
			$db = parent::getSqlite('mix.s3db');
			$db->insert('tb_links', array(
				'serid' => $params['serid'],
				'name' => $params['name'],
				'url' => $params['url'],
				'logo' => $params['logo'],
				'home' => $params['home']
			));
			return $db->lastInsertId();
		}
	}

?>