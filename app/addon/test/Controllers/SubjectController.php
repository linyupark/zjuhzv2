<?php

	/**
	 * 专题控制（某些特殊应用）
	 *
	 */
	class Addon_Test_SubjectController extends Zend_Controller_Action
	{
		/**
		 * 网络培训
		 *
		 */
		function intertrainAction()
		{
			$uid = Cmd::uid();
			$pool = urldecode($this->_getParam('pool'));
			$video = '/upload/video/4/14032009.flv';
			
			$db = DbModel::getSqlite('test.s3db');
			$row = $db->fetchRow('SELECT *  
				FROM `tb_answer_sheet` 
				WHERE `pool` = "'.$pool.'" AND `uid` = '.$uid);
			$this->view->row = $row;
			$this->view->video = $video;
			$this->view->pool = $pool;
		}
	}
	
?>