<?php

    class Plugins_Acl extends Zend_Controller_Plugin_Abstract
    {
        // 禁止访问的
        public function preDispatch(Zend_Controller_Request_Abstract $request)
        {
            $acl = new Zend_Acl();
            
            // 默认身份设置
            $role = Cmd::getSess('profile', 'role');
            if($role == null)
            {
            	$role = 'guest';
            	Cmd::setSess('profile', array('role' => $role));
            }
            
            $resource = $request->getModuleName();
            $controller = $request->getControllerName();
            
            // 资源列表
            $acl->add(new Zend_Acl_Resource('public'));
            $acl->add(new Zend_Acl_Resource('console'));
            $acl->add(new Zend_Acl_Resource('addon_vote'));
            $acl->add(new Zend_Acl_Resource('addon_partners'));
            $acl->add(new Zend_Acl_Resource('addon_console'));
            $acl->add(new Zend_Acl_Resource('addon_booking'));
            
            // 资源自动注册
            if(!$acl->has($resource))
            $acl->add(new Zend_Acl_Resource($resource));
            
            // 角色列表
            $acl->addRole(new Zend_Acl_Role('guest')); // 游客
            $acl->addRole(new Zend_Acl_Role('black'), 'guest'); // 冻结用户,被警告用户
            $acl->addRole(new Zend_Acl_Role('bench'), 'guest'); // 待审核成员(候补)
            $acl->addRole(new Zend_Acl_Role('member'), 'guest'); // 正式成员
            $acl->addRole(new Zend_Acl_Role('power'), 'member'); // 原筹备组成员,理事
            $acl->addRole(new Zend_Acl_Role('master')); // 管理员
            
            // 访问控制定义
            $acl->allow('guest', array('public','addon_vote','addon_partners','addon_console','addon_booking'));
            $acl->allow(array('bench','member'), null);
            $acl->deny(array('member','bench','power','black'), 'console');
            $acl->allow('master', null);
            
            // 无权限转向
            if(!$acl->isAllowed($role, $resource, $controller))
            {
                $request->setModuleName('public');
                $request->setControllerName('error');
                $request->setActionName('deny');
                $request->setParam('role', $role);
            }
        }
    }

?>