<?php

namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Admin;
use Fesion\Models\User;
use Fesion\Models\UserGroup;

class UserController extends ControllerBase
{
    public function loginAction()
    {
        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');
        $admin_token = $this->request->getPost('admin_token');

        if(Helper::isMobile($user_name)){
            $user = User::findFirstByMobile($user_name);
        } else if(filter_var($user_name, FILTER_VALIDATE_EMAIL)){
            $user = User::findFirstByEmail($user_name);
        } else {
            $user = User::findFirstByUser_name($user_name);
        }

        if(!$user){
            Helper::json_output(-1, '用户名或者密码错误');
        }
        if (!$admin = Admin::findFirst($user->uid)) {
            Helper::json_output(-1, '不是管理员,不能登录');
        }

        if (!$this->security->checkHash($password, $user->password)) {
            Helper::json_output(-1, '用户名或密码错误,请重新输入');
        } else if (!$this->security->checkHash($admin_token, $admin->password)) {
            Helper::json_output(-1, '用户名或密码错误,请重新输入');
        }

        //成功登陆
        $admin->updateToken();

        Helper::json_output(0, '登入成功', [
            'token' => $admin->token,
        ]);
    }

    public function logoutAction(){
        if($this->admin) {
            $this->admin->updateToken();
        }

        Helper::json_output(0, '退出成功');
    }

    public function getUserAction($id = 0){
        if($id){
            $groups = UserGroup::find();
            $groups = $groups->toArray();
            if(!is_numeric($id)){
                Helper::json_output(0, null, ['groups' => $groups]);
            }
            $user = User::findFirst($id);
            $user = $this->_formatUser($user);
            $user['groups'] = $groups;
        }else{
            $user = $this->_formatUser($this->user_info);
        }
        Helper::json_output(0, null, $user);
    }

    public function changePasswordAction(){
        $old_password = $this->request->getPost('old_password');
        $password     = $this->request->getPost('password', 'trim');
        $re_password  = $this->request->getPost('re_password');

        if(strlen($password) < 6 || $password != $re_password){
            Helper::json_output(-1, '密码过短或两次输入的密码不一致');
        }

        if(!$this->security->checkHash($old_password, $this->admin->password)){
            Helper::json_output(-1, '旧授权密码错误');
        }

        $this->admin->password = $this->security->hash($password);
        $this->admin->save();

        Helper::json_output(0, '密码修改成功');
    }

    public function userListAction(){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        $q     = $this->request->get('q');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        $where = ["status != " . User::DELETE];
        if($q){
            $where[] = "(user_name like :q: or mobile like :q:)";
            $bind['q'] = $q;
        }

        $users = User::find([
            implode(" and ", $where),
            'bind'   => $bind,
            'order'  => 'uid desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = User::count([
            implode(" and ", $where),
            'bind'   => $bind
        ]);

        Helper::json_output(0, null, $users->toArray(), [
            'count' => $counter
        ]);
    }

    public function adminListAction(){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        $admins = Admin::find([
            '',
            'order'  => 'id asc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Admin::count();
        foreach ($admins as $key => $val){
            $data = array_merge($val->toArray(), $val->user->toArray());
            unset($data['password'], $data['token']);
            $datas[] = $data;
        }

        Helper::json_output(0, null, $datas, [
            'count' => $counter
        ]);
    }

    public function groupListAction(){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        $groups = UserGroup::find([
            '',
            'order'  => 'id asc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = UserGroup::count();

        Helper::json_output(0, null, $groups->toArray(), [
            'count' => $counter
        ]);
    }

    public function saveGroupAction(){
        $id         = $this->request->getPost('id', 'int', 0);
        $title      = $this->request->getPost('title');
        $permission = $this->request->getPost('permission');

        if(!$title){
            Helper::json_output(-1, '请填写用户组名称');
        }

        if($id AND !$group = UserGroup::findFirst($id)){
            Helper::json_output(-1, '用户组不存在');
        }

        if(!$group){
            $group = new UserGroup();
        }
        $group->title      = $title;
        $group->permission = $permission;
        $group->save();

        Helper::json_output(0, '用户组已保存');
    }

    public function saveUserAction(){
        $uid          = $this->request->getPost('uid', 'int', 0);
        $user_name    = $this->request->getPost('user_name');
        $mobile       = $this->request->getPost('mobile');
        $email        = $this->request->getPost('email');
        $password     = $this->request->getPost('password');
        $introduction = $this->request->getPost('introduction');
        $group_id     = $this->request->getPost('group_id', 'int', 0);
        $gender       = $this->request->getPost('gender', 'int', 0);
        $province     = $this->request->getPost('province');
        $city         = $this->request->getPost('city');
        $county       = $this->request->getPost('county');

        if(!$user_name){
            Helper::json_output(-1, '请填写用户名');
        }

        if($mobile && !Helper::isMobile($mobile)){
            Helper::json_output(-1, '手机号格式不正确');
        }
        if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)){
            Helper::json_output(-1, '邮箱格式不正确');
        }

        if($uid AND !$user = User::findFirst($uid)){
            Helper::json_output(-1, '用户不存在');
        }

        if(!$user){
            $user = new User();
        }
        $user->user_name    = $user_name;
        $user->mobile       = $mobile;
        $user->email        = $email;
        $user->introduction = $introduction;
        $user->group_id     = $group_id;
        $user->gender       = $gender;
        $user->province     = $province;
        $user->city         = $city;
        $user->county       = $county;

        if($password){
            $user->password = $this->security->hash($password);
        }
        $user->save();

        Helper::json_output(0, '用户已保存');
    }

    public function saveAdminAction(){
        $id       = $this->request->getPost('id', 'int', 0);
        $uid      = $this->request->getPost('uid', 'int', 0);
        $password = $this->request->getPost('password');

        if(!$uid){
            Helper::json_output(-1, '请填写用户ID');
        }

        if(!$user = User::findFirst($uid)){
            Helper::json_output(-1, '用户不存在');
        }

        if($id AND !$admin = Admin::findFirst($id)){
            Helper::json_output(-1, '管理员不存在');
        }

        if(!$admin){
            $admin = new Admin();
            $admin->uid = $uid;
        }
        if($password){
            $admin->password = $this->security->hash($password);
        }
        $admin->save();

        Helper::json_output(0, '管理员已保存');
    }

    public function getGroupAction($id){
        if(!is_numeric($id)){

            Helper::json_output(0, null, []);
        }
        if($group = UserGroup::findFirstById($id)){
            Helper::json_output(0, null, $group->toArray());
        }

        Helper::json_output(-1, '用户组不存在');
    }

    public function getAdminAction($id){
        if(!is_numeric($id)){

            Helper::json_output(0, null, []);
        }
        if($admin = Admin::findFirstById($id)){
            Helper::json_output(0, null, $admin->toArray());
        }

        Helper::json_output(-1, '用户组不存在');
    }

    public function removeGroupAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '用户组不存在');
        }
        $ids = array_filter($ids, function($id){
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '用户组不存在');
        }

        $groups = UserGroup::find("id IN(" . implode(',', $ids) . ")");
        $groups->delete();

        Helper::json_output(0, '删除成功');
    }

    public function removeAdminAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '管理员不存在');
        }
        $ids = array_filter($ids, function($id){
            //排除超级管理员
            if($id == 1){
                return false;
            }
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '管理员不存在');
        }

        $admins = Admin::find("id IN(" . implode(',', $ids) . ")");
        $admins->delete();

        Helper::json_output(0, '删除成功');
    }

    public function removeUserAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '用户不存在');
        }
        $ids = array_filter($ids, function($id){
            //排除超级管理员
            if($id == 1){
                return false;
            }
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '用户不存在');
        }

        $users = User::find("uid IN(" . implode(',', $ids) . ")");
        $users->delete();

        Helper::json_output(0, '删除成功');
    }
}

