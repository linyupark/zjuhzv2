<?php

    class Plugins_Acl extends Zend_Controller_Plugin_Abstract
    {
        // 禁止访问的
        public function preDispatch(Zend_Controller_Request_Abstract $request)
        {
            $acl = new Zend_Acl();
            
            $role = Zend_Registry::get('sess')->role;
            if($role == null) $role = 'guest';
            
            $resource = $request->getModuleName();
            $controller = $request->getControllerName();
            
            // 资源自动注册
            $acl->add(new Zend_Acl_Resource($resource));
            
            // 角色列表
            $acl->addRole(new Zend_Acl_Role('guest')); // 游客
            $acl->addRole(new Zend_Acl_Role('black'), 'guest'); // 冻结用户,被警告用户
            $acl->addRole(new Zend_Acl_Role('bench'), 'guest'); // 待审核成员(候补)
            $acl->addRole(new Zend_Acl_Role('member'), 'guest'); // 正式成员
            $acl->addRole(new Zend_Acl_Role('power'), 'member'); // 原筹备组成员,理事
            $acl->addRole(new Zend_Acl_Role('master')); // 管理员
            
            // 访问控制定义
            $acl->allow('guest', 'public');
            
            // 无权限转向
            if(!$acl->isAllowed($role, $resource, $action))
            {
                $request->setModuleName('public');
                $request->setControllerName('error');
                $request->setActionName('deny');
                $request->setParam('role', $role);
            }
        }
    }

?>