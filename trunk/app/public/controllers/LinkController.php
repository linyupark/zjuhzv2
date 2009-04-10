<?php

	class LinkController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$links = DbModel::getSqlite('mix.s3db')->fetchAll('SELECT * FROM `tb_links` ORDER BY `serid` ASC');
			$this->view->links = $links;
		}
	}

?>