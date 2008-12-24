<?php

	class Space_Bar_IndexController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->type = $this->_getParam('type', 'topic');
			$this->view->order = $this->_getParam('order', 'time'); // 排序方式
			$this->view->pub = $this->_getParam('pub'); // 发布什么
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
		
		function indexAction()
		{
		}
		
		/**
		 * 获取帖子浏览记录
		 *
		 */
		function historyAction()
		{
			$history = Cmd::getSess('bar_history');
			if($history != null)
			{
				arsort($history); // 根据浏览更新时间来排序
				$select = DbModel::Space()->select()->from('tb_tbar');
				$i = 0;
				foreach ($history as $tid => $time)
				{
					if($i == 9) break;
					if($i == 0) $select->where('tid = ?', $tid);
					else $select->orWhere('tid = ?', $tid);
					$i++;
				}
				$history = $select->query()->fetchAll();
			}
			$this->view->history = $history;
		}
	}

?>