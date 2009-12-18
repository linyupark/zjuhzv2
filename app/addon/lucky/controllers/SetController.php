<?php

	class Addon_Lucky_SetController extends Zend_Controller_Action
	{
        const PASSWORD = '111222333';

        function init()
        {
            $this->params = $this->getRequest()->getParams();
            if(Cmd::getSess('lucky_master') != 'yes' && $this->params['action'] != 'index')
            $this->_redirect('/addon_lucky/set');
        }

        function indexAction()
        {
            $this->view->headTitle('抽奖列表');
            if($this->params['key'] == self::PASSWORD)
            {
                Cmd::setSess('lucky_master', 'yes');
            }
            if(Cmd::getSess('lucky_master') != 'yes')
            {
                echo 'password error';
                exit;
            }
            $partys = Logic_Addon_Lucky::getPartyList();
            $this->view->partys = $partys;
        }

        function formAction()
        {
            $party_id = isset($this->params['id']) ? (int)$this->params['id'] : null;
            if( ! $party_id)
            {
                $this->view->headTitle('创建新抽奖');
                $this->view->title = '创建新抽奖';
            }
            else
            {
                $this->view->headTitle('修改抽奖');
                $this->view->title = '修改抽奖';
                $party = Logic_Addon_Lucky::getParty($party_id);
                $this->view->party = $party;
            }
        }

        function postAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $params = Filter_Addon::lucky($this->params);
            if(Alp_Sys::getMsg() == null)
            {
                Logic_Addon_Lucky::save($params);
                if(Alp_Sys::getMsg() == null)
                {
                    echo 'success';
                    exit;
                }
            }
            echo Alp_Sys::allMsg('* ', "\n");
        }
    }