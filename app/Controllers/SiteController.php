<?php
namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Server;
use Fesion\Models\Site;

class SiteController extends ControllerBase
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
        $servers = Site::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Server::count();

        $servers = $servers->toArray();

        Helper::json_output(0, null, $servers, [
            'count' => $counter
        ]);
    }

    public function saveSiteAction(){
        $id       = $this->request->getPost('id', 'int', 0);

        $title       = $this->request->getPost('title');
        $remark      = $this->request->getPost('remark');
        $sub_domain  = $this->request->getPost('sub_domain');
        $domains     = $this->request->getPost('domains');
        $end_time    = $this->request->getPost('end_time');
        $money       = $this->request->getPost('money');
        $category_id = $this->request->getPost('category_id');
        $server_id   = $this->request->getPost('server_id');
        if(!$title || !$sub_domain){
            Helper::json_output(-1, '请填写站点信息');
        }

        if($id AND !$site = Site::findFirstById($id)){
            Helper::json_output(-1, '站点不存在');
        }
        if(!$site){
            $site = new Site();
        }
        $site->title    = $title;
        $site->remark   = $remark;
        $site->money    = $money;
        $site->sub_domain  = $sub_domain;
        $site->domains     = $domains;
        $site->category_id = $category_id;
        $site->server_id   = $server_id;
        $site->end_time = strtotime($end_time);
        $site->save();

        Helper::json_output(0, '站点信息已保存');
    }

    public function getSiteAction($id){
        $servers = Server::find();
        if(!is_numeric($id)){

            Helper::json_output(0, null, ['servers' => $servers->toArray()]);
        }
        if($site = Site::findFirstById($id)){
            $site = $site->toArray();
            $site['end_time'] = date('Y-m-d', $site['end_time']);
            $site['servers'] = $servers->toArray();
            Helper::json_output(0, null, $site);
        }

        Helper::json_output(-1, '站点不存在');
    }

    public function removeSiteAction(){
        $ids = $this->request->getPost('ids');
        if(!count($ids)){
            Helper::json_output(-1, '站点不存在');
        }
        $ids = array_filter($ids, function($id){
            return is_numeric($id);
        });

        if(!count($ids)){
            Helper::json_output(-1, '站点不存在');
        }

        $site = Site::find("id IN(" . implode(',', $ids) . ")");
        $site->delete();

        Helper::json_output(0, '删除成功');
    }
}

