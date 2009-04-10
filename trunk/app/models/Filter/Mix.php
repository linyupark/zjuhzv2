<?php

	class Filter_Mix
	{
		public static function links($params)
		{
			$params['serid'] = Alp_Valid::of($params['serid'], 'serid', '序号', 'trim|numeric');
			$params['name'] = Alp_Valid::of($params['name'], 'name', '链接名称', 'trim|required');
			$params['url'] = Alp_Valid::of($params['url'], 'url', 'URL', 'trim|required');
			$params['logo'] = trim($params['logo']);
			return $params;
		}
	}

?>