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
			$select->from(array('msg' => 'tb_msg'), array('numrows' => new Zend_Db_Expr('COUNT(mid)')));
			$select->where('msg.incept = ?', $uid);
			$select->where('msg.type = ?', $type);
			
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_msg/?type='.$type.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->order('mid DESC');
			$select->joinLeft(array('su' => 'zjuhzv2_user.tb_base'), 'su.uid = msg.sender', 
							array('sname' => 'su.username', 'ssex' => 'su.sex'));
							
			// 根据类型进行联合查询
			switch ($type)
			{
				case 'friend' : // 好友请求
					$select->joinLeft(array('f' => 'tb_friends'), 'f.friend = '.$uid.' AND f.uid = msg.sender',
							array('ftype' => 'f.type'));
				break;
				case 'sendbox' : // 发件箱
					$select->reset(Zend_Db_Select::WHERE);
					$select->where('msg.sbox = 1 AND msg.sender = '.$uid);
					$select->joinLeft(array('iu' => 'zjuhzv2_user.tb_base'), 'iu.uid = msg.incept', 
							array('iname' => 'iu.username', 'isex' => 'iu.sex'));
				break;
				case 'pm' : // 收件箱
					$select->columns(array('mid' => new Zend_Db_Expr('max(msg.mid)')));
					$select->where('msg.parent != 0')->group('msg.parent');
				break;
				default : 
				break;
			}
			
			$rows = $select->query()->fetchAll();
			
			if($type != 'sendbox' && $type != 'pm')
			{
				// 将未读的变为已读
				foreach ($rows as $r)
				{
					if($r['isread'] == 0)
					Logic_Space_Msg::reading($r['mid']);
				}
			}
			
			$this->view->rows = $rows;
			$this->view->type = $type;
		}
	}
?>