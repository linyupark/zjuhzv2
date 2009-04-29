<?php

	/**
	 * 库控制(问题，答案)
	 *
	 */
	class Addon_Test_PoolController extends Zend_Controller_Action 
	{
		/**
		 * 问卷详细，提交
		 *
		 */
		function viewAction()
		{
			$uid = Cmd::uid();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$ans = array();
				for ($i = 1; $i <= $params['numrows']; $i++)
				{
					$ans[$i] = $params['ans_'.$i];
				}
				$answer = $this->getA($params['pool']);
				$bingo = $this->getBingo($ans, $answer);
				if(Alp_Sys::getMsg() != null)
				echo Alp_Sys::allMsg('* ', "\n");
				else
				{
					$db = DbModel::getSqlite('test.s3db');
					$row = $db->fetchRow('SELECT `retry` FROM `tb_answer_sheet` 
						WHERE `uid` = '.$uid.' AND `pool` = ?', $params['pool']);
					$db->beginTransaction();
					try {
						if($row) // 更新记录
						{
							$db->update('tb_answer_sheet', array(
								'answer' => serialize($ans),
								'retry' => new Zend_Db_Expr('retry + 1'),
								'result' => $bingo
							),'uid = '.$uid.' AND `pool` = "'.$params['pool'].'"');
						}
						else // 新记录
						{
							$db->insert('tb_answer_sheet', array(
								'uid' => $uid,
								'pool' => $params['pool'],
								'answer' => serialize($ans),
								'retry' => 0,
								'result' => $bingo
							));
						}
						$db->commit();
						echo '本次作答您得了'.$bingo.'分';
						
					} catch (Exception $e) {
						
						$db->rollback();
						echo $e->getMessage();
					}
				}
			}
			else 
			{
				$pool = urldecode($this->_getParam('the'));
				$rows = $this->getQ($pool);
				$this->view->rows = $rows;
			}
		}
		
		/**
		 * QP列表
		 *
		 */
		function listAction()
		{
			$db = DbModel::getSqlite('test.s3db');
			$rows = $db->fetchAll('SELECT uid,pool FROM `tb_questions_pool` 
				GROUP BY `pool` ORDER BY `qid` DESC');
			$this->view->rows = $rows;
		}
		
		/**
		 * 添加新题库
		 *
		 */
		function addAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = $this->filterQP($params);
				if(Alp_Sys::getMsg() == null)
				{
					$this->insertQP($params);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						return ;
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		function addformAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->getRequest()->getParams();
				$params['pool'] = Alp_Valid::of($params['pool'], 'pool', '问卷名称', 'trim|strip_tags|required');
				$params['qnum'] = Alp_Valid::of($params['qnum'], 'qnum', '题目数量', 'trim|numeric');
				if(Alp_Sys::getMsg() == null)
				{
					$this->view->params = $params;
				}
			}
		}
		
		###########################################################################
		
		// 计算得分
		function getBingo($u_ans, $r_ans)
		{
			$bingo = 0; // 答对数
			$total = count($r_ans); // 总分
			foreach ($u_ans as $qnum => $a)
			{
				if(is_array($a))
				{
					// 多选题的话得出有几题
					$flag = count($r_ans[$qnum]);
					foreach ($a as $i => $v)
					{
						if($v == $r_ans[$qnum][$i+1])
						$flag --;
					}
					// 全答对就加分
					if($flag == 0) $bingo ++;
				}
				else Alp_Sys::msg('ans_'.$qnum, '第'.$qnum.'题答案未填');
			}
			return round(($bingo/$total)*100);
		}
		
		function getQ($pool)
		{
			$db = DbModel::getSqlite('test.s3db');
			$rows = $db->fetchAll('SELECT * FROM `tb_questions_pool` 
					WHERE `pool` = ? ORDER BY `qid` ASC', $pool);
			return $rows;
		}
		
		function getA($pool)
		{
			$rows = $this->getQ($pool);
			if(count($rows) > 0)
			{
				foreach ($rows as $i => $r)
				{
					$answer[$i+1] = unserialize($r['answer']);
				}
			}
			return $answer;
		}
		
		function insertQP($params)
		{
			$db = DbModel::getSqlite('test.s3db');
			$db->beginTransaction();
			try{
				foreach ($params['questions'] as $i => $q)
				{
					$db->insert('tb_questions_pool', array(
						'question' => $q,
						'pool' => $params['pool'],
						'option' => serialize($params['options'][$i]),
						'answer' => serialize($params['answers'][$i]),
						'uid' => Cmd::uid()
					));
				}
				$db->commit();
				
			} catch (Exception  $e)
			{
				$db->rollback();
				Alp_Sys::msg('excption', $e->getMessage());
			}
		}

		function filterQP($params)
		{	
			// 过滤内容为空的问题
			$q = array();
			foreach ($params['question'] as $i => $question)
			{
				if($question) $q[$i+1] = $question;
			}
			if(count($q) == 0)
			{
				 Alp_Sys::msg('qnum', '题目不能全部为空');
				 return $params;
			}
			// 审核每个题目的答案选项，不能有空
			$opt = array();
			foreach ($q as $i => $v)
			{
				foreach ($params['opt_'.$i] as $ix => $o)
				{
					if(!empty($o)) $opt[$i][$ix+1] = $o;
				}
				if(count($opt[$i]) == 0)
				Alp_Sys::msg('onum_'.$i, '第'.$i.'题的选项不能为空');
			}
			// 每个题目起码有一个答案
			$ans = array();
			foreach ($q as $i => $v)
			{
				if(count($params['ans_'.$i]) == 0)
				Alp_Sys::msg('anum_'.$i, '第'.$i.'题没有选择正确答案');
				else 
				{
					foreach ($params['ans_'.$i] as $ix => $a)
					{
						if($a) $ans[$i][$ix+1] = $a;
					}
				}
			}
			return array(
				'questions' => $q,
				'options' => $opt,
				'answers' => $ans,
				'pool' => $params['pool']
			);
		}
	}

?>