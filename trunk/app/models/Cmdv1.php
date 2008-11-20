<?php

	/**
	 * 可以兼容v1版本的一些命令，过渡使用，版本替换后更新
	 *
	 */
	class Cmdv1
	{
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