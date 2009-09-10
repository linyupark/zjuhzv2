<?php

	/**
	 * Excel格式数据输出控制器
	 *
	 */
	class Api_Export_ExcelController extends Zend_Controller_Action 
	{
		function init()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 用户数据导出
		 *
		 */
		function userAction()
		{
			$uids = $this->_getParam('uids');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("zjuhz.com");
	        $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com");
	        $objPHPExcel->getProperties()->setTitle('用户资料');
	        $objPHPExcel->setActiveSheetIndex(0);
	        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ID');
	        $objPHPExcel->getActiveSheet()->SetCellValue('B1', '姓名');
	        $objPHPExcel->getActiveSheet()->SetCellValue('C1', '性别');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D1', '出生日期');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E1', '教育信息');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F1', '工作信息');
	        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Email');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'QQ');
	        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'MSN');
	        $objPHPExcel->getActiveSheet()->SetCellValue('J1', '家庭住址');
	        $objPHPExcel->getActiveSheet()->SetCellValue('K1', '电话');
	        $objPHPExcel->getActiveSheet()->SetCellValue('L1', '手机');
			foreach ($uids as $i => $r)
			{
				$row = $i + 1;
				$data = Logic_Api::user($r['uid']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $data[0]['uid']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $data[0]['username']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $data[0]['sex']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $data[0]['birthyear'].'-'.$data[0]['birthmonth'].'-'.$data[0]['birthday']);
	            $campus = '';
	            if($data[0]['campus'] != null)
	            {
	            	for($edu_i = 0; $edu_i < count($data); $edu_i ++)
	            	{
	            		if($data[$edu_i]['campus'] != $data[($edu_i-1)]['campus'] &&
						   !strstr($campus, $data[$edu_i]['campus']))
	            		$campus .= ($edu_i+1).'.'.$data[$edu_i]['year'].'入学'.$data[$edu_i]['campus'].$data[$edu_i]['class'];
	            	}
	            }
	            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $campus);
				$company = '';
	            if($data[0]['company'] != null)
	            {
	            	for($car_i = 0; $car_i < count($data); $car_i ++)
			    {
				$end = ($data[$car_i]['end']==0)?'至今':date('y/m', $data[$car_i]['end']);
				    if($data[$car_i]['company'] != $data[($car_i-1)]['company'] && !strstr($company, $data[$car_i]['company']))
				    $company .= ($car_i+1).'.'.$data[$car_i]['company'].'('.$data[$car_i]['department'].')'.$data[$car_i]['job'].
				    '从'.date('y/m', $data[$car_i]['start']).' - '.$end;
			    }
	            }
	            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $company);
	            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $data[0]['email']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $data[0]['qq']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $data[0]['msn']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$row, $data[0]['address']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$row, $data[0]['tel']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$row, $data[0]['mobile']);
			}
			$objPHPExcel->getActiveSheet()->setTitle('群组通讯录');
           	$this->stream($objPHPExcel, 'zjuhz_user');
		}
		
		/**
		 * 群组通讯录
		 *
		 */
		function groupAction()
		{
			$uid = Cmd::uid();
			$gid = $this->_getParam('id');
			$role = Logic_Space_Group_Member::role($gid, $uid);
			if($role == 'manager' || $role == 'creater')
			{
				$rows = DbModel::Space()->fetchAll('SELECT `uid` FROM `tb_group_member` 
					WHERE `gid` = '.$gid.' AND `role` IN ("member","manager","creater")');
				
				$objPHPExcel = new PHPExcel();
	            $objPHPExcel->getProperties()->setCreator("zjuhz.com");
	            $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com");
	            $objPHPExcel->getProperties()->setTitle('群组通讯录');
	            $objPHPExcel->setActiveSheetIndex(0);
	            $objPHPExcel->getActiveSheet()->SetCellValue('A1', '姓名');
	            $objPHPExcel->getActiveSheet()->SetCellValue('B1', '手机');
	            $objPHPExcel->getActiveSheet()->SetCellValue('C1', '电话');
	            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'QQ');
	            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'MSN');
	            $objPHPExcel->getActiveSheet()->SetCellValue('F1', '邮箱');
	            $objPHPExcel->getActiveSheet()->SetCellValue('G1', '所在城市');
	            $objPHPExcel->getActiveSheet()->SetCellValue('H1', '地址');
	            $row = 2;
				foreach ($rows as $v)
				{
					$data = Logic_Api::user($v['uid']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $data[0]['username']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $data[0]['mobile']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $data[0]['tel']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $data[0]['qq']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $data[0]['msn']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $data[0]['email']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $data[0]['city']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $data[0]['address']);
	                $row++;
				}
				$objPHPExcel->getActiveSheet()->setTitle('群组通讯录');
           		$this->stream($objPHPExcel, 'group_contact_'.$gid);
			}
		}
		
		/**
		 * 好友通讯录
		 *
		 */
		function friendsAction()
		{
			$uid = Cmd::uid();
			$sort = $this->_getParam('s');
			$friends = Logic_Space_Friends::fetch($uid, $sort);
			if(count($friends) > 0)
			{
				$objPHPExcel = new PHPExcel();
	            $objPHPExcel->getProperties()->setCreator("zjuhz.com");
	            $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com");
	            $objPHPExcel->getProperties()->setTitle('我的好友通讯录');
	            $objPHPExcel->setActiveSheetIndex(0);
	            $objPHPExcel->getActiveSheet()->SetCellValue('A1', '姓名');
	            $objPHPExcel->getActiveSheet()->SetCellValue('B1', '手机');
	            $objPHPExcel->getActiveSheet()->SetCellValue('C1', '电话');
	            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'QQ');
	            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'MSN');
	            $objPHPExcel->getActiveSheet()->SetCellValue('F1', '邮箱');
	            $objPHPExcel->getActiveSheet()->SetCellValue('G1', '所在城市');
	            $objPHPExcel->getActiveSheet()->SetCellValue('H1', '地址');
	            $row = 2;
				foreach ($friends as $v)
				{
					$data = Logic_Api::user($v['friend']);
					$access = unserialize($data[0]['access']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $data[0]['username']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $access['contact'] > 0 ? $data[0]['mobile'] : '保密');
	                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $access['contact'] > 0 ? $data[0]['tel'] : '保密');
	                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $access['contact'] > 0 ? $data[0]['qq'] : '保密');
	                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $access['contact'] > 0 ? $data[0]['msn'] : '保密');
	                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $access['contact'] > 0 ? $data[0]['email'] : '保密');
	                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $access['city'] > 0 ? $data[0]['city'] : '保密');
	                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $access['contact'] > 0 ? $data[0]['address'] : '保密');
	                $row++;
				}
				$objPHPExcel->getActiveSheet()->setTitle('我的好友通讯录');
           		$this->stream($objPHPExcel, 'friends_contact_'.$sort);
			}
		}
		
		/**
		 * 活动名单
		 *
		 */
		function eventsAction()
		{
			$members = Logic_Space_Bar_Events::members($this->_getParam('tid'));
			$objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("zjuhz.com");
            $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com");
            $objPHPExcel->getProperties()->setTitle('活动参加人员联系方式');
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', '姓名');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', '性别');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', '学院');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', '邮箱');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', '手机');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', '入学年份');
            $row = 2;
            foreach($members as $uid => $uname)
            {
            	$user = Logic_Api::user($uid);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $uname);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $user[0]['sex']);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $user[0]['campus']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $user[0]['email']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $user[0]['mobile']);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $user[0]['year']);
                $row++;
            }
            $objPHPExcel->getActiveSheet()->setTitle('活动参加人员联系方式');
           $this->stream($objPHPExcel, 'contact');
		}
		
		function stream($objPHPExcel, $name)
		{
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename={$name}.xls"); 
            header("Content-Transfer-Encoding: binary ");
            $objWriter->save('php://output');
		}
	}
	
?>