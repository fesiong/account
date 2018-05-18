<?php
namespace Fesion\Controllers;

use Fesion\Library\CategoryTree;
use Fesion\Library\Helper;
use Fesion\Models\Account;
use Fesion\Models\Category;

class AccountController extends ControllerBase
{
    public function categoryListAction()
    {
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
        $categories = Category::find([
            'uid = ' . $this->user_id,
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Category::count('uid = ' . $this->user_id);

        $categories = $categories->toArray();

        Helper::json_output(0, null, $categories, [
            'count' => $counter
        ]);
    }

    public function saveCategoryAction(){
        $id       = $this->request->getPost('id', 'int', 0);
        $title    = $this->request->getPost('title');

        if(!$title){
            Helper::json_output(-1, '请填写标题');
        }

        if($id AND !$category = Category::findFirstById($id)){
            Helper::json_output(-1, '该类别不存在');
        }
        if($category && $category->uid != $this->user_id){
            Helper::json_output(-1, '该类别不存在');
        }
        if(!$category){
            $category = new Category();
            $category->uid = $this->user_id;
        }
        $category->title    = $title;
        $category->save();

        Helper::json_output(0, '类别信息已保存');
    }

    public function removeCategoryAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '类别不存在');
        }
        $ids = array_filter($ids, function($id){
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '类别不存在');
        }

        $servers = Category::find("uid = '{$this->user_id}' and id IN(" . implode(',', $ids) . ")");
        $servers->delete();

        Helper::json_output(0, '删除成功');
    }

    public function accountListAction()
    {
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
        $accounts = Account::find([
            'uid = ' . $this->user_id,
        //    'order'  => 'id desc',
        //    'limit'  => $limit,
        //    'offset' => $offset
        ]);

        //$counter = Account::count('uid = ' . $this->user_id);
        $tree = new CategoryTree($accounts->toArray());
        $accounts = $tree->getTreeArray();

        $category_ids = array_column($accounts, 'category_id');
        $category_ids = array_filter($category_ids);

        $categories = [];
        if(count($category_ids)){
            if($categoriesTmp = Category::find("id IN(" . implode(',', $category_ids) . ")")){
                foreach ($categoriesTmp as $category){
                    $categories[$category->id] = $category->toArray();
                }
            }
        }
        foreach($accounts as $key => $val){
            $accounts[$key]['category_title'] = $categories[$accounts[$key]['category_id']]['title'];
        }

        Helper::json_output(0, null, $accounts, [
            'count' => 0
        ]);
    }

    public function saveAccountAction(){
        $id       = $this->request->getPost('id', 'int', 0);
        $title    = $this->request->getPost('title');
        $user_name = $this->request->getPost('user_name');
        $password  = $this->request->getPost('password');
        $category_id = $this->request->getPost('category_id');
        $parent_id = $this->request->getPost('parent_id');
        $end_time  = $this->request->getPost('end_time');
        $message   = $this->request->getPost('message');

        if(!$title){
            Helper::json_output(-1, '请填写标题');
        }

        if($id AND !$account = Account::findFirstById($id)){
            Helper::json_output(-1, '该账号不存在');
        }
        if($account && $account->uid != $this->user_id){
            Helper::json_output(-1, '该账号不存在');
        }
        if(!$account){
            $account = new Account();
            $account->uid = $this->user_id;
        }
        $account->title    = $title;
        $account->user_name = $user_name;
        $account->password  = $password;
        $account->category_id = $category_id;
        $account->parent_id   = $parent_id;
        $account->end_time    = strtotime($end_time);
        $account->message     = $message;
        $account->save();

        Helper::json_output(0, '账号信息已保存');
    }

    public function removeAccountAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '账号不存在');
        }
        $ids = array_filter($ids, function($id){
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '账号不存在');
        }

        $accounts = Account::find("uid = '{$this->user_id}' and id IN(" . implode(',', $ids) . ")");
        $accounts->delete();

        Helper::json_output(0, '删除成功');
    }

    public function getAccountAction($id){

        $account = [];
        if($account = Account::findFirstById($id)){
            if($account->uid != $this->user_id){
                Helper::json_output(-1, '账号不存在');
            }
            $account = $account->toArray();
        }

        $categories = Category::find("uid = {$this->user_id}");
        $account['categories'] = $categories->toArray();
        $parents = Account::find("uid = {$this->user_id} and parent_id = 0");
        $account['parents']    = $parents->toArray();
        if($account['end_time']) {
            $account['end_time'] = date('Y-m-d', $account['end_time']);
        }

        Helper::json_output(0, null, $account);
    }
}

