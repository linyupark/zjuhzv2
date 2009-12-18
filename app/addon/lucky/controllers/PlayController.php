<?php

class Addon_Lucky_PlayController extends Zend_Controller_Action
{
    function init()
    {
        $id = $this->_getParam('id', 0);
        $party = Logic_Addon_Lucky::getParty($id);
        if( ! $party)
        {
            $this->_redirect('/');
        }
        $this->party = $party;
    }

    function luckymenAction()
    {
        $uid = $this->_getParam('uid');
        if($uid && Cmd::role() == 'master')
        {
            DbModel::getSqlite(Logic_Addon_Lucky::DBNAME)
            ->delete('luckyman', 'id = '.(int)$uid);
        }
        $luckymen = Logic_Addon_Lucky::getLuckymen($this->party['id']);
        $this->view->luckymen = $luckymen;
    }

    function indexAction()
    {
        $id = $this->party['id'];
        $uid = Cmd::uid();
        if(strtotime($this->party['start_at']) > time() || strtotime($this->party['stop_at']) < time())
        {
            $this->view->start_at = $this->party['start_at'];
            $this->view->stop_at = $this->party['stop_at'];
            $this->render('expired');
        }
        elseif(Logic_Addon_Lucky::isPlayed($id, $uid))
        {
            $this->view->party = $this->party;
            $this->render('played');
        }
        elseif(Logic_Addon_Lucky::luckyLimit($id, $uid, $this->party['lucky_limit']))
        {
            $this->render('toogreed');
        }
        else
        {
            $this->view->luckymen = Logic_Addon_Lucky::getLuckymen($id);
            $this->view->party = $this->party;
        }
    }

    function rollAction()
    {
        sleep(3);
        $user_profile = Cmd::getSess('profile');
        $uid = Cmd::uid();
        $this->getHelper('viewRenderer')->setNoRender();
        if( ! isset($_COOKIE['lucky_try_num']))
        {
            setcookie('lucky_try_num', 0);
        }
        $try_num = (int)$_COOKIE['lucky_try_num'];
        // 防止直接javascript刷
        if($try_num >= 3)
        {
            echo '超过指定次数';
            exit;
        }
        setcookie('lucky_try_num', $try_num+1);

        if($user_profile['role'] == 'bench')
        {
            echo '抱歉，未审核校友不能进行抽奖';
            exit;
        }

        // 标注为已经刷票
        if( ! Logic_Addon_Lucky::isPlayed($this->party['id'], $uid))
        {
            Logic_Addon_Lucky::played($this->party['id'], $uid);
        }

        // 领取过多
        if(Logic_Addon_Lucky::luckyLimit($this->party['id'], $uid, $this->party['lucky_limit']))
        {
            echo '抱歉，您已经抽中'.$this->party['lucky_limit'].'次了';
            exit;
        }

        // 没票了
        if(count(Logic_Addon_Lucky::getLuckymen($this->party['id'])) == $this->party['lucky_num'])
        {
            echo '抱歉，名额已满';
            exit;
        }

        $rate = $this->party['lucky_rate'];
        $result = mt_rand(1, $rate);
        
        if($result == 1)
        {
            $info = '姓名：'.$user_profile['username'].'/'.
                    '手机：'.$user_profile['mobile'].'/'.
                    '固话：'.$user_profile['tel'].'/'.
                    '地址：'.$user_profile['address'];
                    
            Logic_Addon_Lucky::bingo($this->party['id'], $uid, $info);
            echo '<img src="/im/lucky/bingo.jpg" />';
        }
        else echo '<img src="/im/lucky/nolucky.gif" />';
    }
}
