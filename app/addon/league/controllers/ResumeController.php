<?php

	class Addon_League_ResumeController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$uid = Cmd::uid();
			$this->view->profile = Cmd::getSess('profile');
			$data = DbModel::getSqlite('league.s3db')->fetchRow('SELECT * FROM `tb_resume` WHERE `uid` = ?', $uid);
			$this->view->data = $data;
			$this->view->uid = $uid;
		}
		
		function saveAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$params = $this->getRequest()->getParams();
			$profile = Cmd::getSess('profile');
			if(!$profile['mobile'] || !$profile['email'] || !$profile['address'])
			Alp_Sys::msg('base', '请填写必要的个人信息：手机、邮箱、住址等');
			$params['major'] = Alp_Valid::of($params['major'], 'major', '专业', 'trim|strip_tags|required');
			$params['grade'] = Alp_Valid::of($params['grade'], 'grade', '年级', 'trim|strip_tags|required');
			$params['edu'] = Alp_Valid::of($params['edu'], 'edu', '教育背景', 'trim|strip_tags');
			$params['train'] = Alp_Valid::of($params['train'], 'train', '培训经历', 'trim|strip_tags');
			$params['internship'] = Alp_Valid::of($params['internship'], 'internship', '实习经历', 'trim|strip_tags');
			$params['award'] = Alp_Valid::of($params['award'], 'award', '获奖情况', 'trim|strip_tags');
			$params['intro'] = Alp_Valid::of($params['intro'], 'intro', '个人描述', 'trim|strip_tags|required');
			$params['memo_1'] = Alp_Valid::of($params['memo_1'], 'memo_1', 'memo_1', 'trim|strip_tags');
			$params['memo_2'] = Alp_Valid::of($params['memo_2'], 'memo_2', 'memo_2', 'trim|strip_tags');
			
			if(Alp_Sys::getMsg() == null)
			{
				$db = DbModel::getSqlite('league.s3db');
				if($params['update']) // 更新
				{
					$db->update('tb_resume', array(
						'major' => $params['major'],
						'grade' => $params['grade'],
						'edu' => $params['edu'],
						'train' => $params['train'],
						'internship' => $params['internship'],
						'award' => $params['award'],
						'intro' => $params['intro'],
						'memo_1' => $params['memo_1'],
						'memo_2' => $params['memo_2']
					), 'uid = '.$params['update']);
				}
				else 
				{
					$db->insert('tb_resume', array(
						'uid' => Cmd::uid(),
						'major' => $params['major'],
						'grade' => $params['grade'],
						'edu' => $params['edu'],
						'train' => $params['train'],
						'internship' => $params['internship'],
						'award' => $params['award'],
						'intro' => $params['intro'],
						'memo_1' => $params['memo_1'],
						'memo_2' => $params['memo_2']
					));
				}
				echo 'success';
			}
			else echo Alp_Sys::allMsg('* ', "\n");
		}
	}

?>