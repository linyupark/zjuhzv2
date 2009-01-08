<?php

	/**
	 * 投票调查帖
	 *
	 */
	class Space_Bar_VoteController extends Zend_Controller_Action
	{		
		function dovoteAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->_getAllParams();
				if(count($params['oid']) == 0)
				Alp_Sys::msg('option', '没有选择投票选项');
				else
				{
					if(count($params['oid']) > $params['max_select'])
					Alp_Sys::msg('option', '投票上线不得超过'.$params['max_select'].'项');
					else
					{
						Logic_Space_Bar_Vote::send($params['tid'], $params['oid']);
						if(Alp_Sys::getMsg() == null)
						{
							echo Zend_Json::encode(array('result'=>'success'));
							exit();
						}
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
		
		/**
		 * 投票查看
		 *
		 */
		function viewAction()
		{
			$page = $this->_getParam('p', 1);
			$tid = $this->_getParam('tid');
			$row = Logic_Space_Bar_Vote::view($tid);
			if(!$row[0]) // 无效帖子
			$this->_forward('error', 'error', 'public');
			if(!Logic_Space_Bar::isAllowed($row[0]['private'], $row[0]['puber'])) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public');
			
			$voters = unserialize($row[0]['voters']);
			$options = unserialize($row[0]['options']);
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$mychoice = '我选择了:';
				foreach ($voters[Cmd::uid()] as $opt)
				{
					$mychoice .= '"'.$options[$opt].'" ';
				}
				echo $mychoice;
			}
			else
			{
				Logic_Space_Bar::click($tid);
				$this->view->row = $row[0];
				$this->view->page = $page;
				$this->view->voters = $voters;
			}
		}
		
		/**
		 * 投票列表
		 *
		 */
		function indexAction()
		{
			$where = $this->_getParam('where', 'all');
			$order = $this->view->order;
			$page = $this->_getParam('p', 1); // 默认显示页
			$select = DbModel::Space()->select()->from(array('bar' => 'zjuhzv2_space.tb_tbar'))->where('`group` = ?', 0);
			switch ($where)
			{
				case 'pub' : // 我发布的帖子
					$select->where('bar.type = ?', 'vote');
					$select->where('puber = ?', Cmd::uid());
				break;
				case 'join' : // 我参与的帖
					$row = Logic_Space_Bar::getJoin(Cmd::uid());
					if($row != false)
					{
						$tid_arr = unserialize($row['tid']);
						if(count($tid_arr) > 0)
						{
							$i = 0;
							foreach ($tid_arr as $tid => $time)
							{
								if($i == 0) $select->where('vote.tid = ?', $tid);
								else $select->orWhere('vote.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('vote.tid = ?', 0);
					}
					else $select->where('vote.tid = ?', 0);
				break;
				case 'fav' : // 我的收藏帖
					$row = Logic_Space_Bar::getFav('vote', Cmd::uid());
					if($row != false)
					{
						$tid_arr = unserialize($row['vote']);
						if(count($tid_arr) > 0 && $tid_arr != false)
						{
							$i = 0;
							foreach ($tid_arr as $tid => $time)
							{
								if($i == 0) $select->where('vote.tid = ?', $tid);
								else $select->orWhere('vote.tid = ?', $tid);
								$i++;
							}
						}
						else $select->where('vote.tid = ?', 0);
					}
					else $select->where('vote.tid = ?', 0);
				break;
				default: // 所有帖子
					$select->where('bar.type = ?', 'vote');
				break;
			}
			switch ($order)
			{
				case 'time' : // 发布时间
					$select->order('bar.pubtime DESC');
				break;
				case 'rtime' : // 回复时间
					$select->order('bar.replytime DESC');
				break;
				case 'reply' : // 回复数
					$select->order('bar.reply DESC');
				break;
				case 'click' : // 点击率
					$select->order('bar.click DESC');
				break;
				default : // 被顶数
					$select->order('bar.rate DESC');
				break;
			}
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$select->joinLeft(array('vote' => 'zjuhzv2_space.tb_vote'), 'bar.tid = vote.tid');
			$rows = $select->query()->fetchAll();
			$pagesize = Alp_Page::$pagesize = 10;
			if(count($rows) > $pagesize)
			{
				Alp_Page::create(array(
					'href_open' => '<a href="?type=vote&order='.$order.'&where='.$where.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => count($rows),
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$rows = $select->query()->fetchAll();
				$this->view->pagination = Alp_Page::$page_str;
			}
			$this->view->rows = $rows;
			$this->view->where = $where;
		}
	}

?>