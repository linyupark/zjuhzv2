<?php

	/**
	 * 可以兼容v1版本的一些命令，过渡使用，版本替换后更新
	 *
	 */
	class Cmdv1
	{
		/**
		 * 远程数据库对象获取
		 *
		 * @param unknown_type $dbname
		 * @return unknown
		 */
		static function remoteDb($dbname)
		{	
			$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			    'host'     => 'www.zjuhz.com',
			    'username' => 'zjuhz_mysql_dev',
			    'password' => 'www_zjuhz_com_@[dev]_20080505#',
			    'dbname'   => $dbname
			));
			return $db;
		}
		
		/**
		 * 获取群组的创建人，时间，介绍，成员信息，做迁移用
		 *
		 * @param unknown_type $gid
		 */
		static function group($gid)
		{
			$db = self::remoteDb('zjuhz_group');
			$data['group'] = $db->fetchRow('SELECT `creater`,`name`,`create_time`,`intro` 
				FROM `tbl_group` WHERE `group_id` = '.(int)$gid);
			$data['member'] = $db->fetchAll('SELECT `user_id`,`join_time`,`last_access` 
				FROM `tbl_group_member` WHERE `group_id` = '.(int)$gid);
			return $data;
		}
		
		/**
		 * 返回所有热心度加分记录
		 *
		 */
		static function alldevotelog()
		{
			$db = self::remoteDb('zjuhz_devote');
			return $db->fetchAll('SELECT * FROM `tbl_transaction` ORDER BY `time` ASC');
		}
		
		/**
		 * 连接远程数据库返回所有用户有用信息
		 *
		 */
		static function alluser()
		{
			$db = self::remoteDb('zjuhz_user');
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