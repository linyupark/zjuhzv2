<?php

	/**
	 * 帖子管理
	 *
	 */
	class Console_BarController extends Zend_Controller_Action
	{
		/**
		 * 转移帖子
		 *
		 */
		function mvbarAction()
		{
			$type = $this->_getParam('for');
			if($type == 'news')
			$sorts = Logic_Space_Bar_News::getSorts();
			if($type == 'help')
			$sorts = Logic_Space_Bar_Help::getSorts();
			
			$target = $this->_getParam('tsid');
			$sid = $this->_getParam('sid');
			
			//  把当前要转移的分类过滤掉
			if(count($sorts) > 0)
			{
				foreach ($sorts as $i => $s)
				{
					if($s['sort'] == $sid)
					{
						$this->view->sname = $s['name'];
						unset($sorts[$i]);
					}
				}
			}
			
			if($target != null) // 具体转移操作
			{
				$this->getHelper('viewRenderer')->setNoRender();
				if($type == 'news')
				Logic_Space_Bar_News::mv($sid, $target);
				if($type == 'help')
				Logic_Space_Bar_Help::mv($sid, $target);
				if(Alp_Sys::getMsg() == null)
				echo 'success';
				else echo Alp_Sys::msg('exception');
			}
			else
			{
				$this->view->sorts = $sorts;
				$this->view->sid = $sid;
				$this->view->for = $type;
			}
		}
		
		/**
		 * 改发布时间
		 *
		 */
		function mtimeAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$pubtime = $this->_getParam('pubtime');
			$tid = $this->_getParam('tid');
			$time = strtotime($pubtime);
			if($time != false)
			{
				DbModel::Space()->update('tb_tbar', 
				array('pubtime' => $time), 
				'tid = '.$tid);
				echo '修改成功';
			}
			else echo '时间格式错误';
		}
		
		/**
		 * 修改类名
		 *
		 */
		function mnameAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$type = $this->_getParam('for');
			$sid = $this->_getParam('sid');
			$sortname = $this->_getParam('sortname');
			if($type == 'news')
			Filter_Space::newsSort($sortname);
			if($type == 'help')
			Filter_Space::helpSort($sortname);
			if(Alp_Sys::getMsg() == null)
			{
				DbModel::Space()->update('tb_'.$type.'_sort', 
				array('name' => trim($sortname)), 
				'sort = '.$sid);
				echo 'success';
			}
			else echo Alp_Sys::allMsg('* ', "\n");
		}
		
		/**
		 * 求助归类管理
		 *
		 */
		function helpsortAction()
		{
			$sorts = Logic_Space_Bar_Help::getSorts();
			$this->view->sorts = $sorts;
		}
		
		/**
		 * 帖子归类管理
		 *
		 */
		function newssortAction()
		{
			$sorts = Logic_Space_Bar_News::getSorts();
			$this->view->sorts = $sorts;
		}
		
		/**
		 * 修改帖子属性
		 *
		 */
		function updateAction()
		{
			if($this->getRequest()->isXmlHttpRequest()):
			$this->getHelper('viewRenderer')->setNoRender();
			$type = $this->_getParam('t');
			$tid = $this->_getParam('tid');
			Logic_Api::barcmd($tid, $type);
			if(Alp_Sys::getMsg() == null){ echo 'success'; }
			endif;
		}
		
		/**
		 * 帖子
		 *
		 */
		function barAction()
		{
			$params = $this->getRequest()->getParams();
			$range = $params['range'] ? $params['range'] : 'news';
			$key = trim(urldecode($params['key']));
			$page = $this->_getParam('p', 1);
			$pagesize = 10;
			$select = DbModel::Space()->select();
			$select->from(array('b'=>'tb_tbar'), array('numrows' => new Zend_Db_Expr('COUNT(b.tid)')));
			
			// 帖子显示范围
			if($range != 'all' && $range != 'deny' && $range != 'ding')
			$select->where('b.type = ?', $range);
			if($range == 'ding') $select->where('b.ding = 1');
			if($range == 'deny') $select->where('b.deny = 1');
			if($range != 'deny') $select->where('b.deny = 0');
			
			// 如果有模糊查询
			if(!empty($key))
			{
				$select->where('b.title LIKE "%'.$key.'%"');
			}
			
			$row = $select->query()->fetchAll();
			$numrows = $row[0]['numrows'];
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/console/bar/?tab=bar&range='.$range.'&key='.$key.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->joinLeft(array('g' => 'tb_group'), 'g.gid = b.group', 
							  array('gname' => 'g.name', 'gtype' => 'g.type'));
							  
			$select->order('b.ding DESC')->order('b.pubtime DESC');
			
			$this->view->numrows = $numrows;
			$this->view->range = $range;
			$this->view->key = $key;
			$this->view->rows = $select->query()->fetchAll();
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
		
		function indexAction()
		{
			$this->view->tab = $this->_getParam('tab', 'bar');
		}
	}
	
?>