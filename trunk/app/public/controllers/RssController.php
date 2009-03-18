<?php

	/**
	 * RSS阅读显示控制
	 *
	 */
	class RssController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
			
			$params = $this->getRequest()->getParams();
			$uid = Alp_String::decrypt($params['ucode'], 2);
			if(!Logic_User_Base::check($uid)) echo 'none data';
			else // 有效用户开始进行内容获取
			{
				//$friends = Logic_Space_Friends::ids($uid);
				$select = DbModel::Space()->select();
				$limit = (int)$params['limit'] > 30 ? 30 : $params['limit']; // 限制最高上限
				$data = array();
				// 新闻 -----------------------------------
				if(count($params['news']) > 0)
				{
					$select->from(array('t' => 'tb_tbar'))
						   ->where('t.type = "news" AND t.private IN (2,3,4)')
						   ->where('t.deny = 0')
						   ->joinLeft(array('n' => 'tb_news'), 'n.tid = t.tid')
						   ->joinLeft(array('s' => 'tb_news_sort'), 's.sort = n.sort')
						   ->where('n.sort IN (?)', $params['news'])
						   ->order('t.pubtime DESC')
						   ->limit($limit);
					$result = $select->query()->fetchAll();
					if(count($result) > 0)
					$data['news'] = $result;
				}
				// 互助 -----------------------------------
				if(count($params['help']) > 0)
				{
					$select->reset();
					$select->from(array('t' => 'tb_tbar'))
						   ->where('t.type = "help" AND t.private IN (2,3,4)')
						   ->where('t.deny = 0')
						   ->joinLeft(array('h' => 'tb_help'), 'h.tid = t.tid')
						   ->joinLeft(array('s' => 'tb_help_sort'), 's.sort = h.sort')
						   ->where('h.sort IN (?)', $params['help'])
						   ->order('t.pubtime DESC')
						   ->limit($limit);
					$result = $select->query()->fetchAll();
					if(count($result) > 0)
					$data['help'] = $result;
				}
				// 其他帖子 ---------------------------------
				if($params['t'] == 'on')
				{
					$select->reset();
					$select->from(array('t' => 'tb_tbar'))
						   ->where('t.type NOT IN ("news","help") AND t.private IN (2,3,4)')
						   ->where('t.deny = 0')
						   ->order('t.pubtime DESC')
						   ->limit($limit);
					$result = $select->query()->fetchAll();
					if(count($result) > 0)
					$data['bar'] = $result;
				}
				// 回帖 --------------------------
				if($params['r'] == 'on')
				{
					$select->reset();
					$tids = Logic_Space_Bar::ids($uid);
					if(count($tids) > 0)
					{
						$in_tid = array();
						foreach ($tids as $t)
						{
							$in_tid[] = $t['tid'];
						}
						$select->from(array('c' => 'tb_comment'))
							   ->joinLeft(array('t' => 'tb_tbar'), 't.tid = c.tid', array('t.title','t.type'))
							   ->where('c.tid IN (?)', $in_tid)
							   ->where('c.deny = 0')
							   ->order('c.time DESC')
							   ->limit($limit);
						$result = $select->query()->fetchAll();
						if(count($result) > 0)
						$data['reply'] = $result;
					}
				}
				// 聚合为feed -------------------------------------
				if(count($data) > 0)
				{
					$channel = '';
					Zend_Debug::dump($data);
					foreach ($data as $part => $rows)
					{
						
						if($part == 'news') // 新闻channel
						{
							foreach ($rows as $v)
							{
								Alp_Feed::addRssItem(array(
									'title' => stripslashes($v['title']),
									'link' => 'http://'.DOMAIN.'/space_bar/news/view?tid='.$v['tid'],
									'description' => stripslashes($v['content'])
								));
							}
							$channel .= Alp_Feed::generateChannel(array(
								'title' => '杭州浙江大学校友会 - 新闻频道',
								'link' => 'http://'.DOMAIN.'/space_bar/?type=news',
								'description' => ''
							));
							Alp_Feed::rest();
						}
						if($part == 'help') // 互助channel
						{
							foreach ($rows as $v)
							{
								Alp_Feed::addRssItem(array(
									'title' => stripslashes($v['name'].' - '.$v['title']),
									'link' => 'http://'.DOMAIN.'/space_bar/help/view?tid='.$v['tid'],
									'description' => stripslashes($v['content'])
								));
							}
							$channel .= Alp_Feed::generateChannel(array(
								'title' => '杭州浙江大学校友会 - 互助频道',
								'link' => 'http://'.DOMAIN.'/space_bar/?type=help',
								'description' => ''
							));
							Alp_Feed::rest();
						}
						if($part == 't') // 其他帖子channel
						{
							foreach ($rows as $v)
							{
								Alp_Feed::addRssItem(array(
									'title' => stripslashes($v['title']),
									'link' => 'http://'.DOMAIN.'/space_bar/'.$v['type'].'/view?tid='.$v['tid'],
									'description' => '....'
								));
							}
							$channel .= Alp_Feed::generateChannel(array(
								'title' => '杭州浙江大学校友会 - 帖子',
								'link' => 'http://'.DOMAIN.'/space_bar/?where=all&order=time&type=topic',
								'description' => ''
							));
							Alp_Feed::rest();
						}
						if($part == 'r') // 其他帖子channel
						{
							foreach ($rows as $v)
							{
								Alp_Feed::addRssItem(array(
									'title' => stripslashes($v['title']),
									'link' => 'http://'.DOMAIN.'/space_bar/'.$v['type'].'/view?tid='.$v['tid'],
									'description' => stripslashes($v['content'])
								));
							}
							$channel .= Alp_Feed::generateChannel(array(
								'title' => '杭州浙江大学校友会 - 回帖',
								'link' => 'http://'.DOMAIN.'/space_bar/?where=pub&order=rtime&type=topic',
								'description' => ''
							));
							Alp_Feed::rest();
						}
					}
					echo '<?xml version="1.0" encoding="UTF-8" ?><rss>'.$channel.'</rss>';
				}
			}
		}
	}

?>