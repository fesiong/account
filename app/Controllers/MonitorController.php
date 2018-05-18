<?php
namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Plugin;
use Fesion\Models\Server;
use Fesion\Models\Site;
use Fesion\Models\SiteAction;
use Fesion\Models\SiteAlive;
use Fesion\Models\SiteError;
use Fesion\Models\SiteLogin;
use Fesion\Models\SiteVisit;

class MonitorController extends ControllerBase
{
    public function visitAction()
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
        $sites = Site::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Site::count();

        $sites = $sites->toArray();
        $siteIds = array_column($sites, 'id');
        //统计流量
        $todayVisits = SiteVisit::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $yesterdayVisits = SiteVisit::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time >= " . strtotime(date("Y-m-d", strtotime('yesterday'))))
            ->andWhere("add_time < " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteVisit::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-7 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteVisit::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-30 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

        foreach ($todayVisits as $key => $visit){
            $today[$visit->site_id] = $visit->total;
        }
        foreach ($yesterdayVisits as $key => $visit){
            $yesterday[$visit->site_id] = $visit->total;
        }
        foreach ($weekVisits as $key => $visit){
            $week[$visit->site_id] = $visit->total;
        }
        foreach ($monthVisits as $key => $visit){
            $month[$visit->site_id] = $visit->total;
        }
        foreach ($sites as $key => $val){
            $sites[$key]['today'] = $today[$val['id']];
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);
    }

    public function actionAction()
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
        $sites = Site::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Site::count();

        $sites = $sites->toArray();
        $siteIds = array_column($sites, 'id');
        //统计流量
        $todayVisits = SiteAction::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $yesterdayVisits = SiteAction::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time >= " . strtotime(date("Y-m-d", strtotime('yesterday'))))
            ->andWhere("add_time < " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteAction::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-7 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteAction::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-30 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

        foreach ($todayVisits as $key => $visit){
            $today[$visit->site_id] = $visit->total;
        }
        foreach ($yesterdayVisits as $key => $visit){
            $yesterday[$visit->site_id] = $visit->total;
        }
        foreach ($weekVisits as $key => $visit){
            $week[$visit->site_id] = $visit->total;
        }
        foreach ($monthVisits as $key => $visit){
            $month[$visit->site_id] = $visit->total;
        }
        foreach ($sites as $key => $val){
            $sites[$key]['today'] = $today[$val['id']];
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);
    }

    public function errorAction()
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
        $sites = Site::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Site::count();

        $sites = $sites->toArray();
        $siteIds = array_column($sites, 'id');
        //统计流量
        $todayVisits = SiteError::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $yesterdayVisits = SiteError::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time >= " . strtotime(date("Y-m-d", strtotime('yesterday'))))
            ->andWhere("add_time < " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteError::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-7 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteError::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-30 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

        foreach ($todayVisits as $key => $visit){
            $today[$visit->site_id] = $visit->total;
        }
        foreach ($yesterdayVisits as $key => $visit){
            $yesterday[$visit->site_id] = $visit->total;
        }
        foreach ($weekVisits as $key => $visit){
            $week[$visit->site_id] = $visit->total;
        }
        foreach ($monthVisits as $key => $visit){
            $month[$visit->site_id] = $visit->total;
        }
        foreach ($sites as $key => $val){
            $sites[$key]['today'] = $today[$val['id']];
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);
    }

    public function loginAction()
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
        $sites = Site::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Site::count();

        $sites = $sites->toArray();
        $siteIds = array_column($sites, 'id');
        //统计流量
        $todayVisits = SiteLogin::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $yesterdayVisits = SiteLogin::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time >= " . strtotime(date("Y-m-d", strtotime('yesterday'))))
            ->andWhere("add_time < " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteLogin::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-7 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteLogin::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-30 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

        foreach ($todayVisits as $key => $visit){
            $today[$visit->site_id] = $visit->total;
        }
        foreach ($yesterdayVisits as $key => $visit){
            $yesterday[$visit->site_id] = $visit->total;
        }
        foreach ($weekVisits as $key => $visit){
            $week[$visit->site_id] = $visit->total;
        }
        foreach ($monthVisits as $key => $visit){
            $month[$visit->site_id] = $visit->total;
        }
        foreach ($sites as $key => $val){
            $sites[$key]['today'] = $today[$val['id']];
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);
    }

    public function aliveAction()
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
        $sites = Site::find([
            '',
            'order'  => 'id desc',
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $counter = Site::count();

        $sites = $sites->toArray();
        $siteIds = array_column($sites, 'id');
        //统计流量
        $todayVisits = SiteAlive::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $yesterdayVisits = SiteAlive::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time >= " . strtotime(date("Y-m-d", strtotime('yesterday'))))
            ->andWhere("add_time < " . strtotime(date("Y-m-d")))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteAlive::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-7 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteAlive::query()
            ->columns("count(id) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("add_time > " . strtotime('-30 day'))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

        foreach ($todayVisits as $key => $visit){
            $today[$visit->site_id] = $visit->total;
        }
        foreach ($yesterdayVisits as $key => $visit){
            $yesterday[$visit->site_id] = $visit->total;
        }
        foreach ($weekVisits as $key => $visit){
            $week[$visit->site_id] = $visit->total;
        }
        foreach ($monthVisits as $key => $visit){
            $month[$visit->site_id] = $visit->total;
        }
        foreach ($sites as $key => $val){
            $sites[$key]['today'] = $today[$val['id']];
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);
    }

    public function visitDetailAction($id){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        if($site = Site::findFirstById($id)){
            $list = SiteVisit::find([
                'site_id = ' . $site->id,
                'order'  => 'id desc',
                'limit'  => $limit,
                'offset' => $offset
            ]);
            $counter = SiteVisit::count('site_id = ' . $site->id);
            $list = $list->toArray();
            foreach ($list as &$val){
                $val['ip'] = long2ip($val['ip']);
            }

            Helper::json_output(0, null, $list, [
                'count' => $counter
            ]);
        }

        Helper::json_output(-1, '站点不存在');
    }

    public function actionDetailAction($id){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        if($site = Site::findFirstById($id)){
            $list = SiteAction::find([
                'site_id = ' . $site->id,
                'order'  => 'id desc',
                'limit'  => $limit,
                'offset' => $offset
            ]);
            $counter = SiteAction::count('site_id = ' . $site->id);
            $list = $list->toArray();
            foreach ($list as &$val){
                $val['ip'] = long2ip($val['ip']);
            }

            Helper::json_output(0, null, $list, [
                'count' => $counter
            ]);
        }

        Helper::json_output(-1, '站点不存在');
    }

    public function errorDetailAction($id){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        if($site = Site::findFirstById($id)){
            $list = SiteError::find([
                'site_id = ' . $site->id,
                'order'  => 'id desc',
                'limit'  => $limit,
                'offset' => $offset
            ]);
            $counter = SiteError::count('site_id = ' . $site->id);
            $list = $list->toArray();

            Helper::json_output(0, null, $list, [
                'count' => $counter
            ]);
        }

        Helper::json_output(-1, '站点不存在');
    }

    public function loginDetailAction($id){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        if($site = Site::findFirstById($id)){
            $list = SiteLogin::find([
                'site_id = ' . $site->id,
                'order'  => 'id desc',
                'limit'  => $limit,
                'offset' => $offset
            ]);
            $counter = SiteLogin::count('site_id = ' . $site->id);
            $list = $list->toArray();
            foreach ($list as &$val){
                $val['ip'] = long2ip($val['ip']);
            }

            Helper::json_output(0, null, $list, [
                'count' => $counter
            ]);
        }

        Helper::json_output(-1, '站点不存在');
    }

    public function aliveDetailAction($id){
        $page  = $this->request->get('page');
        $limit = $this->request->get('limit');
        if($limit < 1){
            $limit = $this->perPage;
        }
        if($page < 1){
            $page = 1;
        }
        $offset = ($page - 1)*$limit;

        if($site = Site::findFirstById($id)){
            $list = SiteAlive::find([
                'site_id = ' . $site->id,
                'order'  => 'id desc',
                'limit'  => $limit,
                'offset' => $offset
            ]);
            $counter = SiteAlive::count('site_id = ' . $site->id);
            $list = $list->toArray();

            Helper::json_output(0, null, $list, [
                'count' => $counter
            ]);
        }

        Helper::json_output(-1, '站点不存在');
    }
}

