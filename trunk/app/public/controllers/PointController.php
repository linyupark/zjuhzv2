<?php

	/**
	 * 热度控制器
	 *
	 */
	class PointController extends Zend_Controller_Action 
	{	
		/**
		 * tab分配
		 *
		 */
		function indexAction()
		{
			if(Cmd::role() == 'guest') 
			$this->_forward('deny', 'error', 'public');
			$this->view->tab = $this->_getParam('tab', 'summary');
		}
		
		
		/**
		 * 自己的热心度摘要(排行，前后两位，全站总热度，百分比%)
		 *
		 */
		function summaryAction()
		{
			$uid = Cmd::uid();
			$base = Logic_User_Base::get($uid);
			$point = $base['point'];
			$sum = Logic_Api::sumpoint('user');
			$percent = Logic_Api::percentpoint($point, $sum);
			$this->view->sumpoint = $sum;
			$this->view->mypoint = $point;
			$this->view->percent = $percent;
			$this->view->rank = Logic_Api::rankpoint($point, 'user');
			$this->view->neb = Logic_User_Base::nebpoint($point);
		}
		
		/**
		 * 近期热心度加分记录显示(全站/我的)
		 *
		 */
		function logAction()
		{
			$range = $this->_getParam('range', 'self');
			$page = $this->_getParam('p', 1);
			$uid = Cmd::uid();
			if($range == 'self') $data = Logic_Api::pointlog($page, $uid);
			if($range == 'all') $data = Logic_Api::pointlog($page);
			if($data['numrows'] > $data['pagesize'])
			{
				Alp_Page::$pagesize = $data['pagesize'];
				Alp_Page::create(array(
					'href_open' => '<a href="/public/point/?tab=log&range='.$range.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $data['numrows'],
					'cur_page' => $page
				));
				$this->view->pagination = Alp_Page::$page_str;
			}
			$this->view->rows = $data['rows'];
			$this->view->range = $range;
		}
		
		/**
		 * 申请热心度
		 *
		 */
		function awardAction()
		{
            if($this->getRequest()->isXmlHttpRequest())
            {
                $this->getHelper('viewRenderer')->setNoRender();
                $params = $this->getRequest()->getParams();
                $uid = Cmd::uid();
                if(!$params['auid'] || (int)$params['point'] == 0 || trim($params['memo']) == '')
                {
                    echo '输入的数据有问题，请完整输入';
                }
                else
				{
						$params['uid'] = $uid;
						Logic_Log::apoint($params);
						echo 'done';
				}
            }
		}
		
		function forwhoAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				//$this->getHelper('viewRenderer')->setNoRender();
				
				$names = explode(' ', $this->_getParam('uname'));
				if(count($names) > 0)
				{
					$in = '';
					foreach($names as $v)
					{
						$in .= '"'.$v.'",';
					}
					$in = substr($in, 0, -1);
					$db = DbModel::User();
					$rows = $db->fetchAll('SELECT `username`,`uid`,`point`
								  FROM `tb_base` WHERE `username` IN ('.$in.')');
					$this->view->rows = $rows;
				}
			}
		}
		
		/**
		 * 申请热心度
		 *
		 */
		function alogAction()
		{
			$uid = Cmd::uid();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$id = $this->_getParam('id');
				DbModel::Log()->delete('tb_apoint', 'apid = '.(int)$id.' AND uid = '.$uid);
				echo 'done';
			}
			else
			{
				$rows = DbModel::Log()->fetchAll('SELECT * FROM `tb_apoint` WHERE `uid` = ? ORDER BY `time` DESC', $uid);
				$this->view->rows = $rows;
			}
		}
	}

?>