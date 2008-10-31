<?php

	class Logic_Addon_Partners extends DbModel 
	{
		/**
		 * 返回指定企业的LOGO图片
		 *
		 * @param unknown_type $cid
		 * @return unknown
		 */
		public static function logo($cid)
		{
			$logo = UPLOADROOT.'addon/partners/logos/'.$cid.'.gif';
			if(file_exists($logo))
			return '<img src="/upload/addon/partners/logos/"'.$cid.'.gif'.'" />';
			else return '<img src="/upload/addon/partners/logos/default.gif" />';
		}
		
		/**
		 * 返回企业信息
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function getCorps($uid = null)
		{
			$select = parent::getSqlite('partners.s3db')->select();
			$select->from('corporation');
			if($uid != null) 
			$select->where('uid = ?', $uid);
			
			return $select->query()->fetchAll();
		}
		
		/**
		 * 检查是否为有效的账号密码
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function checkUser($params)
		{
			return parent::getSqlite('partners.s3db')->fetchRow('
				SELECT `uid` FROM `user` WHERE `username` = ? AND `password` = ?', 
						array($params['username'], md5($params['password'])));
		}
		
		/**
		 * 建立新的赞助伙伴账号
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function insertUser($params)
		{
			$db = parent::getSqlite('partners.s3db');
			$db->beginTransaction();
			try {
				$db->insert('user', array(
					'username' => $params['username'],
					'password' => md5($params['password'])
				));
				$db->commit();
				return $db->lastInsertId();
				
			} catch(Exception $e) {
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return 0;
		}
	}

?>