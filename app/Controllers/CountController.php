<?php
namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Server;
use Fesion\Models\Site;
use Fesion\Models\SiteCount;

class CountController extends ControllerBase
{
    public function siteAction()
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

        Helper::json_output(0, null, [], [
            'count' => 0
        ]);
    }

    public function contentAction()
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

        //统计内容
        $yesterdayVisits = SiteCount::query()
            ->columns("sum(topic_count) as topic, sum(category_count) as category, sum(answer_count) as answer, sum(comment_count) as comment, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("count_date = " . date("Ymd", strtotime('yesterday')))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteCount::query()
            ->columns("sum(topic_count) as topic, sum(category_count) as category, sum(answer_count) as answer, sum(comment_count) as comment, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("count_date >= " . date("Ymd", strtotime('-7 day')))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteCount::query()
            ->columns("sum(topic_count) as topic, sum(category_count) as category, sum(answer_count) as answer, sum(comment_count) as comment, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("count_date >= " . date("Ymd", strtotime('-30 day')))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

        foreach ($yesterdayVisits as $key => $visit){
            $yesterday[$visit->site_id] = $visit->toArray();
        }
        foreach ($weekVisits as $key => $visit){
            $week[$visit->site_id] = $visit->toArray();
        }
        foreach ($monthVisits as $key => $visit){
            $month[$visit->site_id] = $visit->toArray();
        }
        foreach ($sites as $key => $val){
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);

        Helper::json_output(0, null, [], [
            'count' => 0
        ]);
    }

    public function userAction()
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

        //统计内容
        $yesterdayVisits = SiteCount::query()
            ->columns("sum(user_count) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("count_date = " . date("Ymd", strtotime('yesterday')))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $weekVisits = SiteCount::query()
            ->columns("sum(user_count) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("count_date >= " . date("Ymd", strtotime('-7 day')))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();
        $monthVisits = SiteCount::query()
            ->columns("sum(user_count) as total, site_id")
            ->andWhere("site_id IN(" . implode(',', $siteIds) . ")")
            ->andWhere("count_date >= " . date("Ymd", strtotime('-30 day')))
            ->groupBy("site_id")
            ->orderBy('site_id ASC')->execute();

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
            $sites[$key]['yesterday'] = $yesterday[$val['id']];
            $sites[$key]['week'] = $week[$val['id']];
            $sites[$key]['month'] = $month[$val['id']];
        }

        Helper::json_output(0, null, $sites, [
            'count' => $counter
        ]);

        Helper::json_output(0, null, [], [
            'count' => 0
        ]);
    }
}

