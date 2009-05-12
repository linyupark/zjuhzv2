<?php

	/**
	 * 群账单控制器
	 *
	 */
	class Space_Group_BillController extends Zend_Controller_Action
	{
		function init()
		{
			$this->view->gid = $this->_getParam('id');
			$this->view->tab = $this->getRequest()->getControllerName();
		}
		
		/**
		 * 添加账目
		 *
		 */
		function additemAction()
		{
			$this->getHelper('viewRenderer')->setNoRender(true);
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->getRequest()->getParams();
				$params = Filter_Group::bill($params);
				if(!Logic_Space_Group_Member::isMemeber($params['gid'], Cmd::uid()))
				Alp_Sys::msg('role', '非群组成员不能进行此操作');
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Group_Bill::insert($params);
					echo 'success';
					exit();
				}
				echo Alp_Sys::allMsg();
			}
		}
		
		function indexAction()
		{
			$uid = Cmd::uid();
			$gid = $this->view->gid;
			$group = Logic_Space_Group::info($gid);
			if(Logic_Space_Group::isAllowedVisit($gid, $uid))
			{	
				$this->view->group = $group;
				$this->view->sorts = Logic_Space_Group_Bill::getSort($gid);
				$this->view->sort = $this->_getParam('sort');
			}
		}
		
		function listAction()
		{
			$gid = $this->view->gid;
			$sort = urldecode($this->_getParam('sort'));
			// 获取指定分类
			$db = DbModel::Space();
			$page = $this->_getParam('p', 1);
			$pagesize = 15;
			if($sort) $where = ' AND `sort` = "'.$sort.'"';
			$row = $db->fetchRow('
				SELECT COUNT(`id`) AS `numrows` 
				FROM `tb_group_bill` 
				WHERE `gid` = '.$gid.$where);
			$limit = $pagesize;
			if($row['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="javascript:bill_list(%d,\''.urlencode($sort).'\')">',
					'href_close' => '</a>',
					'cur_page' => $page,
					'num_rows' => $row['numrows']
				));
				$this->view->pagination = Alp_Page::$page_str;
				$limit = Alp_Page::$offset.','.$pagesize;
			}
			$rows = $db->fetchAll('
				SELECT bill.*,b.username FROM `tb_group_bill` AS `bill` 
				LEFT JOIN zjuhzv2_user.`tb_base` AS `b` ON b.uid = bill.uid
				WHERE bill.`gid` = '.$gid.$where.' ORDER BY `time` DESC LIMIT '.$limit);
			$this->view->rows = $rows;
		}
	}
	
?>