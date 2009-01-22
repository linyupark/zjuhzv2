<?php
	/**
	 * 站内信索引页
	 *
	 */
	class Space_Msg_IndexController extends Zend_Controller_Action 
	{
		/**
		 * 站内信息列表
		 *
		 */
		function indexAction()
		{
			$uid = Cmd::uid();
			$type = $this->_getParam('type', 'pm');
			$page = $this->_getParam('p', 1);
			$pagesize = 10;
			
			$select = DbModel::Space()->select();
			$select->from(array('msg' => 'tb_msg', array('numrows' => new Zend_Db_Expr('COUNT(mid)'))));
			$select->where('msg.incept = ?', $uid);
			$select->where('msg.type = ?', $type);
			
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_msg/?type='.$type.'&p='.$page.'">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->order('time DESC');
			$select->joinLeft(array('user' => 'zjuhzv2_user.tb_base'), 'user.uid = msg.sender', 
							array('sname' => 'user.username', 'ssex' => 'user.sex'));
							
			// 根据类型进行联合查询
			switch ($type)
			{
				case 'friend' : // 好友请求
					$select->joinLeft(array('f' => 'tb_friends'), 'f.uid = msg.incept',
							array('ftype' => 'f.type'));
				break;
				
				default : 
				break;
			}
							
			$rows = $select->query()->fetchAll();
			
			// 将未读的变为已读
			foreach ($rows as $r)
			{
				if($r['isread'] == 0)
				Logic_Space_Msg::reading($r['mid']);
			}
			
			$this->view->rows = $rows;
			$this->view->type = $type;
		}
	}
?>