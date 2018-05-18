<?php
namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Plugin;
use Fesion\Models\Server;

class PluginController extends ControllerBase
{
    public function listAction()
    {
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;
        $servers = Plugin::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Plugin::count();

        $servers = $servers->toArray();

        Helper::json_output(0, null, $servers, [
            'count' => $counter
        ]);
    }

    public function savePluginAction(){
        $id       = $this->request->getPost('id', 'int', 0);

        $title    = $this->request->getPost('title');
        $remark   = $this->request->getPost('remark');
        $location = $this->request->getPost('location');
        $version  = $this->request->getPost('version');
        $message  = $this->request->getPost('message');
        if(!$location || !$title){
            Helper::json_output(-1, '请填写功能信息');
        }

        if($id AND !$plugin = Plugin::findFirstById($id)){
            Helper::json_output(-1, '功能不存在');
        }
        if(!$plugin){
            $plugin = new Plugin();
        }
        $plugin->title    = $title;
        $plugin->remark   = $remark;
        $plugin->location = $location;
        $plugin->version  = $version;
        $plugin->message  = $message;
        $plugin->save();

        Helper::json_output(0, '功能信息已保存');
    }

    public function getPluginAction($id){
        if(!is_numeric($id)){
            Helper::json_output(0, null, []);
        }
        if($plugin = Plugin::findFirstById($id)){
            $plugin = $plugin->toArray();
            Helper::json_output(0, null, $plugin);
        }

        Helper::json_output(-1, '功能不存在');
    }

    public function removePluginAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '功能不存在');
        }
        $ids = array_filter($ids, function($id){
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '功能不存在');
        }

        $plugins = Plugin::find("id IN(" . implode(',', $ids) . ")");
        $plugins->delete();

        Helper::json_output(0, '删除成功');
    }
}

