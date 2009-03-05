<?php

	/**
	 * 可以兼容v1版本的一些命令，过渡使用，版本替换后更新
	 *
	 */
	class Cmdv1
	{
		/**
		 * 连接远程数据库返回所有用户有用信息
		 *
		 */
		static function alluser()
		{
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			    'host'     => 'www.zjuhz.com',
			    'username' => 'zjuhz_mysql_dev',
			    'password' => 'www_zjuhz_com_@[dev]_20080505#',
			    'dbname'   => 'zjuhz_user'
			));
			$db->query('SET NAMES "utf8"');
			$select = $db->select();
			$select->from(array('base' => 'tbl_user'), array(
					'userid' => 'uid', 'username', 'password', 'realName', 
					'sex', 'birthday', 'year', 'hometown_c', 'location_c', 'regTime'
				))
				->joinLeft(array('contact' => 'tbl_user_contact'), 'base.uid = contact.uid')
				->joinLeft(array('devote' => 'zjuhz_devote.tbl_user'), 'base.uid = devote.uid', 
					array('devote.point'));
			return $select->query()->fetchAll();
		}
		
		static function myid()
		{
			return Zend_Registry::get('sessCommon')->login['uid'];
		}
		
		/**
		 * 是否为群组的管理人员
		 *
		 * @param unknown_type $gid
		 * @return unknown
		 */
		static function isGroupManager($gid)
		{
			if(Zend_Registry::get('sessGroup')->my[$gid]['role'] == 'manager' ||
	           Zend_Registry::get('sessGroup')->my[$gid]['role'] == 'creater')
	         return true;
	         else return false;
		}
	}

?>