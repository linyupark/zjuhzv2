<?php

	class Space_Bar_CommentController extends Zend_Controller_Action 
	{
		private $params;
		private $pagesize = 5;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 查看帖子的评论
		 *
		 */
		function viewAction()
		{
		}
		
		function listAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$page = $this->_getParam('p', 1);
				Alp_Page::$pagesize = $this->pagesize; // 分页数
				$tid = $this->params['tid']; 
				
				$select = DbModel::Space()->select()->from(array('c'=>'zjuhzv2_space.tb_comment'));
				$select->where('tid = ?', $tid);
				$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'c.uid = u.uid',
								  array('uname'=>'u.username', 'unick'=>'u.nickname'));
				$rows = $select->query()->fetchAll();
				if(count($rows) > $this->pagesize)
				{
					Alp_Page::create(array(
						'href_open' => '<a href="javascript:load_comment(%d)">',
						'href_close' => '</a>',
						'num_rows' => count($rows),
						'cur_page' => $page
					));
					$select->limit($this->pagesize, Alp_Page::$offset);
					$rows = $select->query()->fetchAll();
					$this->view->pagination = Alp_Page::$page_str;
				}
				$this->view->comments = $rows;
				$this->view->page = $page;
				$this->view->offset = Alp_Page::$offset;
			}
		}
		 
		/**
		 * 回复、修改
		 *
		 */
		function postAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Space::comment($this->params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Comment::save($params);
					if(Alp_Sys::getMsg() == null)
					{
						$num = Logic_Space_Bar_Comment::num($params['tid']);
						$page = ceil($num / $this->pagesize);
						echo Zend_Json::encode(array(
							'result' => 'success',
							'page' => $page
						));
						exit();
					}
				}
				echo Zend_Json::encode(array('result' => Alp_Sys::allMsg('* ', "\n")));
			}
		}
		
		
		/**
		 * 阻止
		 *
		 */
		function denyAction()
		{
			
		}
	}

?>