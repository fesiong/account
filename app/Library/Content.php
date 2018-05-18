<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 2016/11/26
 * Time: 下午7:08
 */

namespace Fesion\Library;

use Fesion\Models\Answer;
use Fesion\Models\Article;
use Fesion\Models\Category;
use Fesion\Models\CategoryParent;
use Fesion\Models\CategoryRelated;
use Fesion\Models\CategoryTopic;
use Fesion\Models\ContentRelated;
use Fesion\Models\Course;
use Fesion\Models\History;
use Fesion\Models\MedicalNote;
use Fesion\Models\ModelsBase;
use Fesion\Models\Posts;
use Fesion\Models\RelatedTag;
use Fesion\Models\Relation;
use Fesion\Models\Service;
use Fesion\Models\Thanks;
use Fesion\Models\ThanksDoctor;
use Fesion\Models\Topic;
use Fesion\Models\User;
use Fesion\Models\Vote;
use Phalcon\Mvc\User\Component;

class Content extends Component
{
    public static function getStaticDI(){
        $content = new Content();
        return $content->getDI();
    }

    public static function setPosts($item_id, $item_type, $item = null){
        if(!$item) {
            switch ($item_type) {
                default:
                    $item_type = Posts::TYPE_TOPIC;
                case Posts::TYPE_TOPIC:
                    $item = Topic::findFirst(intval($item_id));
                    break;
                case Posts::TYPE_BLOG:
                    $item = Article::findFirst(intval($item_id));
                    break;
                case Posts::TYPE_ARTICLE:
                    $item = MedicalNote::findFirst(intval($item_id));
                    break;
                case Posts::TYPE_THANKS:
                    $item = Thanks::findFirst(intval($item_id));
                    break;
                case Posts::TYPE_COURSE:
                    $item = Course::findFirst(intval($item_id));
                    break;
                case Posts::TYPE_SERVICE:
                    $item = Service::findFirst(intval($item_id));
                    break;
            }
        }
        if(!$item){
            return false;
        }
        if(!$posts = Posts::findFirst([
            'item_id = :item_id: AND item_type = :item_type:',
            'bind' => [
                'item_id'   => intval($item_id),
                'item_type' => $item_type
            ]
        ])){
            $posts = new Posts();
            $posts->item_type = $item_type;
            $posts->item_id   = intval($item_id);
        }
        $posts->add_time     = $item->add_time;
    //    $posts->category_id  = $item->category_id;
        $posts->views        = $item->views;
        $posts->uid          = $item->uid;
        $posts->site_id      = $item->site_id;

        switch ($item_type) {
            default:
            case Posts::TYPE_TOPIC:
                $posts->update_time  = $item->update_time;
                $posts->anonymous    = $item->anonymous;
                $posts->status       = $item->status;
                $posts->answer_count = $item->answer_count;
                $posts->agree_count  = $item->focus_count;
                break;
            case Posts::TYPE_BLOG:
                $posts->update_time  = time();
                $posts->anonymous    = 0;
                $posts->status       = 1;
                $posts->answer_count = $item->comment_count;
                $posts->agree_count  = $item->agree_count;
                break;
            case Posts::TYPE_ARTICLE:
                $posts->update_time  = time();
                $posts->anonymous    = $item->personal;
                $posts->status       = $item->status;
                $posts->answer_count = $item->comment_count;
                $posts->agree_count  = $item->agree_count;
                break;
            case Posts::TYPE_THANKS:
                $posts->update_time  = time();
                $posts->anonymous    = 0;
                $posts->status       = $item->status;
                $posts->answer_count = $item->comment_count;
                $posts->agree_count  = $item->agree_count;
                break;
            case Posts::TYPE_COURSE:
                $posts->update_time  = $item->update_time;
                $posts->anonymous    = 0;
                $posts->status       = 1;
                $posts->answer_count = $item->answer_count;
                $posts->agree_count  = $item->focus_count;
                break;
            case Posts::TYPE_SERVICE:
                $posts->update_time  = $item->update_time;
                $posts->anonymous    = $item->anonymous;
                $posts->status       = $item->status;
                $posts->answer_count = $item->answer_count;
                $posts->agree_count  = $item->focus_count;
                break;
        }

        return $posts->save();
    }

    public static function removePosts($item_id, $item_type){
        if($posts = Posts::findFirst([
            'item_id = :item_id: AND item_type = :item_type:',
            'bind' => [
                'item_id'   => intval($item_id),
                'item_type' => intval($item_type)
            ]
        ])){
            $posts->delete();
        }
        if($relation = Relation::findFirst([
            'item_id = :item_id: AND item_type = :item_type:',
            'bind' => [
                'item_id'   => intval($item_id),
                'item_type' => intval($item_type)
            ]
        ])){
            $relation->delete();
        }
    }

    /**
     * 获取回应用户排行榜
     * @param $day
     * @param $limit
     * @return mixed
     */
    public static function getAnswerMostUsers($day = null,$limit = 10) {
        $cache_key = 'answer_most_users_'.md5($day . '_'.$limit);

        if(!$result = static::getStaticDI()->get('dataCache')->get($cache_key, 600)){
            $uids   = [];
            $total  = [];
            $thanks = [];
            if($day){
                $where = 'add_time > '. strtotime('-' . $day . ' day');
                $answers = Answer::query()->andWhere($where)
                    ->columns([
                        'uid', 'count(uid) as total'
                    ])
                    ->groupBy('uid')
                    ->orderBy('total desc')
                    ->limit($limit)
                    ->execute();

                if(count($answers)){
                    foreach ($answers as $key => $val){
                        $uids[]           = $val->uid;
                        $total[$val->uid] = $val->total;
                    }

                    $votes = Vote::query()->andWhere($where)
                        ->andWhere('to_uid IN(' . implode(',', $uids) . ')')
                        ->columns([
                            '*', 'count(to_uid) as total'
                        ])
                        ->groupBy('uid')
                        ->orderBy('total desc')
                        ->limit($limit)
                        ->execute();

                    if(count($votes)){
                        foreach ($votes as $key => $val){
                            $thanks[$val->uid] = $val->total;
                        }
                    }

                    $users = User::find([
                        'uid IN(' . implode(',', $uids) . ')'
                    ]);
                }
            }else{
                $users = User::find([
                    'status = 1',
                    'order' => 'answer_count desc',
                    'limit' => $limit
                ]);
            }

            if(count($users)){
                foreach ($users as $user){
                    $item['slug']         = $user->slug;
                    $item['url']          = $user->getUrl();
                    $item['user_name']    = $user->user_name;
                    $item['avatar']       = $user->getAvatar();
                    $item['answer_count'] = isset($total[$user->uid]) ? $total[$user->uid] : $user->answer_count;
                    $item['agree_count']  = isset($thanks[$user->uid]) ? $thanks[$user->uid] : $user->agree_count;
                    $result[$user->uid]   = (object) $item;
                }
            }

            static::getStaticDI()->get('dataCache')->save($cache_key, $result, 600);
        }

        return $result;
    }

    /**
     * 被感谢人员排行榜
     * @param null $day
     * @param int  $limit
     * @return mixed
     */
    public static function getThanksMostUsers($day = null, $limit = 10) {
        $cache_key = 'answer_most_users_'.md5($day.$limit);

        if(!$result = static::getStaticDI()->get('dataCache')->get($cache_key, 600)){
            $uids   = [];
            $total  = [];
            $flower = [];
            $agree  = [];
            if($day){
                $where = 'add_time > '. strtotime('-' . $day . ' day');
            }

            $thanks_doctor = ThanksDoctor::query()->andWhere($where)
                ->columns([
                    'doctor_uid', 'count(thanks_id) as total', 'sum(flowers) as flower'
                ])
                ->groupBy('doctor_uid')
                ->orderBy('total desc')
                ->limit($limit)
                ->execute();
            if(count($thanks_doctor)){
                foreach ($thanks_doctor as $key => $val){
                    $uids[]                   = $val->doctor_uid;
                    $total[$val->doctor_uid]  = $val->total;
                    $flower[$val->doctor_uid] = $val->flower;
                }

                if(!$where){
                    $where = 'to_uid IN(' . implode(',', $uids) . ')';
                }else{
                    $where .= ' AND to_uid IN(' . implode(',', $uids) . ')';
                }
                $votes = Vote::query()->andWhere($where)
                    ->columns([
                        '*', 'count(to_uid) as total'
                    ])
                    ->groupBy('uid')
                    ->orderBy('total desc')
                    ->limit($limit)
                    ->execute();

                if(count($votes)){
                    foreach ($votes as $key => $val){
                        $agree[$val->uid] = $val->total;
                    }
                }

                $users = User::find([
                    'uid IN(' . implode(',', $uids) . ')'
                ]);
            }

            if(count($users)){
                foreach ($users as $user){
                    $item['slug']       = $user->slug;
                    $item['url']        = $user->getUrl();
                    $item['user_name']  = $user->user_name;
                    $item['avatar']     = $user->getAvatar();
                    $item['thanks']     = intval($total[$user->uid]);
                    $item['flower']     = isset($flower[$user->uid]) ? $flower[$user->uid] : $user->flowers;
                    $item['agree']      = isset($agree[$user->uid]) ? $agree[$user->uid] : $user->agree_count;
                    $result[$user->uid] = (object) $item;
                }
            }

            static::getStaticDI()->get('dataCache')->save($cache_key, $result, 600);
        }

        return $result;
    }

    /**
     * 获取相关主题
     * @param Topic $topic
     * @param int   $limit
     * @return bool|\Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getRelatedTopic(Topic $topic, $limit = 5){
        if(!$topic){
            return false;
        }
        $categories = $topic->getCategories();
        $cat_ids = [];
        if(count($categories)){
            foreach ($categories as $key => $val){
                $cat_ids[] = $val->id;
            }
        }
        if(!$cat_ids){
            $where = 'status = 1 and id != ' . $topic->id;
        }else{
            $relations = Relation::find([
                'category_id IN (' . implode(',', $cat_ids) . ') AND item_type = ' . Posts::TYPE_TOPIC . ' AND item_id != ' . $topic->id,
                'order' => 'id desc',
                'group' => 'item_id',
                'limit' => $limit,
            ]);
            $topic_ids = [];
            foreach ($relations as $key => $val){
                $topic_ids[] = $val->item_id;
            }

            if($topic_ids){
                $where = 'status = 1 and id IN (' . implode(',', $topic_ids) . ')';
            }
        }

        $list = Topic::find([
            $where,
            'order' => 'id DESC',
            'limit' => $limit
        ]);

        return $list;
    }

    public static function getRelatedService(Service $service, $limit = 5){
        if(!$service){
            return false;
        }
        if(!$service->category_id){
            $where = 'status = 1 AND id != ' . $service->id . ' AND item_type = ' . $service->item_type;
        }else{
            $where = 'status = 1 AND id != ' . $service->id . ' AND item_type = ' . $service->item_type . ' AND category_id = ' . $service->category_id;
        }

        $list = Service::find([
            $where,
            'order' => 'id DESC',
            'limit' => $limit
        ]);

        return $list;
    }

    /**
     * 根据内容获取该内容的顶级话题
     * @param mixed $item
     * @return Category $top;
     */
    public static function getTopCategory($item){
        $topCategoryIds = [];
        $categoryParents = CategoryParent::find();
        $parent_catids = [];
        foreach ($categoryParents as $key => $val){
            $parent_catids[$val->category_id] = $val->category_id;
        }

        $categories = $item->getCategories();
        $top = null;
        if(count($categories)){
            foreach ($categories as $key => $val){
                $topCategoryIds = array_merge($topCategoryIds, static::getTopCategoryIds($val->id));
            }
        }

        if(count($topCategoryIds)){
            $topCategories = Category::find('id IN(' . implode(',', $topCategoryIds) . ')');
        }

        if(count($topCategories)){
            $slugs = [];
            foreach ($topCategories as $key => $val){
                if(in_array($val->slug, [
                    'cleftlipandpalate',
                ])){
                    $slugs[] = $val->slug;
                    $top     = $val;
                }
            }
        }else{
            if(!$top AND count($categories)){
                $top = $categories->getFirst();
            }
        }

        return $top;
    }

    public static function getTopCategoryByCategoryId($category_id){
        $categoryParents = CategoryParent::find();
        $parent_catids = [];
        foreach ($categoryParents as $key => $val){
            $parent_catids[$val->category_id] = $val->category_id;
        }

        $top = null;
        $topCategoryIds = static::getTopCategoryIds($category_id);

        if(count($topCategoryIds)){
            $topCategories = Category::find('id IN(' . implode(',', $topCategoryIds) . ')');
        }

        if(count($topCategories)){
            $slugs = [];
            foreach ($topCategories as $key => $val){
                if(in_array($val->slug, [
                    'cleftlipandpalate'
                ])){
                    $slugs[] = $val->slug;
                    $top     = $val;
                }
            }
        }

        return $top;
    }

    public static function getTopCategoryIds($category_id){
        $topCategoryIds = [];
        $categoryParents = CategoryParent::find();
        $parent_catids = [];
        foreach ($categoryParents as $key => $val){
            $parent_catids[$val->category_id] = $val->category_id;
        }
        $parents = static::getCategoryParents($category_id);
        if(count($parents)){
            foreach ($parents as $k => $v){
                if(!in_array($v->id, $parent_catids)){
                    $topCategoryIds[$v->id] = $v->id;
                }else{
                    $topCategoryIds = array_merge($topCategoryIds, static::getTopCategoryIds($v->id));
                }
            }
        }

        return $topCategoryIds;
    }

    public static function getParentCategory($cat_id){
        $parentCategories = Content::getCategoryParents($cat_id);

        if(count($parentCategories)){
            return $parentCategories->getFirst();
        }

        return null;
    }

    public static function getSubCategoryIds($cat_id){
        $catIds[] = $cat_id;
        $categoryParents = CategoryParent::find('parent_id = ' . intval($cat_id));
        foreach ($categoryParents as $val){
            $catIds[] = $val->category_id;
            $catIds = array_merge($catIds, static::getSubCategoryIds($val->category_id));
        }

        return array_unique($catIds);
    }

    public static function saveCategoryRelation($item_type, $item_id, $categories_id){
        $categories = Relation::find([
            "item_type = " . $item_type . " AND item_id = " . $item_id
        ]);
        $has_categories = [];
        foreach ($categories as $key => $val){
            $has_categories[] = $val->category_id;
        }
        //这里的categories_id可能是标题,所以需要先转换下
        if(count($categories_id)) {
            foreach ($categories_id  as $key => $item){
                if(!is_numeric($item)){
                    if($item = Category::findFirstByTitle($item)){
                        $categories_id[$key] = $item->id;
                    }else{
                        unset($categories_id[$key]);
                    }
                }
            }
        }
        //要删除的。
        if(!count($categories_id)){
            $del_categories_ids = $has_categories;
            $new_categories_ids = [];
        }else{
            $del_categories_ids = array_diff($has_categories, $categories_id);
            //要添加的。
            $new_categories_ids = array_diff($categories_id, $has_categories);
        }
        //
        if(count($del_categories_ids)){
            $del_categories = Relation::find([
                "item_type = " . $item_type . " AND item_id = " . $item_id . " AND category_id IN(" . implode(',', $del_categories_ids) . ")"
            ]);
            if(count($del_categories)){
                $del_categories->delete();
            }
        }
        if(count($new_categories_ids)){
            foreach ($new_categories_ids as $key => $val){
                $relation = new Relation();
                $relation->category_id = $val;
                $relation->item_type   = $item_type;
                $relation->item_id     = $item_id;
                $relation->save();
                //更新话题时间
                if($category = Category::findFirst($val)){
                    $category->add_time      = time();
                    //更新讨论数量
                    $category->discuss_count = Relation::count([
                        'category_id = ' . $val
                    ]);
                    $category->save();
                }
            }
        }

        return true;
    }

    public static function saveCategoryTopic($category_id, $topic_id, $type = ''){
        if(is_numeric($category_id)){
            //从category那里加入
            $relateds = CategoryTopic::find([
                "category_id = '$category_id'"
            ]);
            $has_topics = [];
            foreach ($relateds as $key => $val){
                $has_topics[] = $val->topic_id;
            }
            //要删除的。
            if(!count($topic_id)){
                $del_categories_ids = $has_topics;
                $new_categories_ids = [];
            }else{
                $del_categories_ids = array_diff($has_topics, $topic_id);
                //要添加的。
                $new_categories_ids = array_diff($topic_id, $has_topics);
            }
            //
            if(count($del_categories_ids)){
                $del_categories = CategoryTopic::find([
                    "category_id = " . $category_id . " AND topic_id IN(" . implode(',', $del_categories_ids) . ")"
                ]);
                if(count($del_categories)){
                    $del_categories->delete();
                }
            }
            if(count($new_categories_ids)){
                foreach ($new_categories_ids as $key => $val){
                    $relation = new CategoryTopic();
                    $relation->category_id = $category_id;
                    $relation->topic_id    = $val;
                    $relation->save();
                }
            }
        }elseif (is_numeric($topic_id)){
            //从topic那里加入
            $relateds = CategoryTopic::find([
                "topic_id = '$topic_id'"
            ]);
            $has_categories = [];
            foreach ($relateds as $key => $val){
                $has_categories[] = $val->category_id;
            }
            //要删除的。
            if(!count($category_id)){
                $del_categories_ids = $has_categories;
                $new_categories_ids = [];
            }else{
                $del_categories_ids = array_diff($has_categories, $category_id);
                //要添加的。
                $new_categories_ids = array_diff($category_id, $has_categories);
            }
            //
            if(count($del_categories_ids)){
                $del_categories = CategoryTopic::find([
                    "topic_id = " . $topic_id . " AND category_id IN(" . implode(',', $del_categories_ids) . ")"
                ]);
                if(count($del_categories)){
                    $del_categories->delete();
                }
            }
            if(count($new_categories_ids)){
                foreach ($new_categories_ids as $key => $val){
                    $relation = new CategoryTopic();
                    $relation->topic_id    = $topic_id;
                    $relation->category_id = $val;
                    $relation->save();
                }
            }
        }
        return true;
    }

    public static function getCategories($item_type, $item_id, $all = false){
        $relations = Relation::find([
            "item_type = " . $item_type . " AND item_id = " . intval($item_id)
        ]);
        if(count($relations)){
            foreach ($relations as $key => $val){
                $categories_id[] = $val->category_id;
            }
            $where = 'id IN(' . implode(',', $categories_id) . ')';
            if(!$all){
                $where .= " and status = 1";
            }
            $categories = Category::find([
                $where
            ]);
        }

        return $categories;
    }

    public static function getCategoryParents($category_id){
        $parents = CategoryParent::find([
            "category_id = " . intval($category_id)
        ]);
        if(count($parents)){
            foreach ($parents as $key => $val){
                $categories_id[] = $val->parent_id;
            }

            $categories = Category::find([
                'id IN(' . implode(',', $categories_id) . ')'
            ]);
        }

        return $categories;
    }

    /**
     * 第二版 一对多、多对多
     * @param       $item_id
     * @param       $relateds
     * @param       $item_type
     * @return bool
     */
    public static function saveContentRelated($item_id, $relateds, $item_type){
        $tags = [];
        $tagsTmp = RelatedTag::find("item_type = " . $item_type);
        foreach ($tagsTmp as $key => $val){
            $tags[$val->id] = $val;
        }
        //删除
        if(!count($relateds)){
            $other = ContentRelated::find([
                "item_type = :item_type: AND FIND_IN_SET(:item_id:, related_ids)",
                'bind' => [
                    'item_id'   => $item_id,
                    'item_type' => $item_type
                ]
            ]);
        }else{
            $other = ContentRelated::find([
                "item_type = :item_type: AND tag_id NOT IN(".implode(',', array_keys($relateds)).") AND FIND_IN_SET(:item_id:, related_ids)",
                'bind' => [
                    'item_id'   => $item_id,
                    'item_type' => $item_type
                ]
            ]);
        }
        if(count($other)){
            $other->delete();
        }
        //记录
        foreach($relateds as $tag_id => $related_ids){
            array_push($related_ids, $item_id);
            if($tags[$tag_id] == 2){
                //多对多
                $exists = ContentRelated::findFirst([
                    "item_type = :item_type: AND tag_id = :tag_id: AND FIND_IN_SET(:item_id:, related_ids)",
                    'bind' => [
                        'tag_id'    => $tag_id,
                        'item_id'   => $item_id,
                        'item_type' => $item_type
                    ]
                ]);
            }else{
                //一对多
                $others = ContentRelated::find([
                    "item_type = :item_type: AND tag_id = :tag_id: AND FIND_IN_SET(:item_id:, related_ids) AND item_id NOT IN(".implode(',', $related_ids).")",
                    'bind' => [
                        'tag_id'    => $tag_id,
                        'item_id'   => $item_id,
                        'item_type' => $item_type
                    ]
                ]);
                foreach($others as $key => $val){
                    $otherRelatedIds = array_filter(explode(',', $val->related_ids), function($item) use ($item_id) {
                        return $item != $item_id;
                    });
                    if(count($otherRelatedIds) <= 1){
                        $val->delete();
                    }else {
                        $val->related_ids = implode(',', $otherRelatedIds);
                        $val->save();
                    }
                }
                $exists = ContentRelated::findFirst([
                    "item_type = :item_type: AND tag_id = :tag_id: AND item_id = :item_id:",
                    'bind' => [
                        'tag_id'    => $tag_id,
                        'item_id'   => $item_id,
                        'item_type' => $item_type
                    ]
                ]);
            }
            if(count($related_ids) <= 1){
                continue;
            }
            if($exists){
                $exists->related_ids = implode(',', $related_ids);
                $exists->save();
            }else{
                $exists = new ContentRelated();
                $exists->tag_id      = $tag_id;
                $exists->related_ids = implode(',', $related_ids);
                $exists->item_id     = $item_id;
                $exists->item_type   = $item_type;
                $exists->save();
            }
        }
        //e
        return true;
    }

    public static function saveCategoryParent($category_id, $parent_ids, $related_tags = []){
        $parents = CategoryParent::find([
            "category_id = " . $category_id
        ]);
        $has_parents = [];
        foreach ($parents as $key => $val){
            $has_parents[] = $val->parent_id;
        }
        //要删除的。
        $del_parent_ids = array_diff($has_parents, $parent_ids);
        //要添加的。
        $new_parent_ids = array_diff($parent_ids, $has_parents);
        //已存在的
        $old_parent_ids = array_intersect($has_parents, $parent_ids);

        //
        if(count($del_parent_ids)){
            $del_parents = CategoryParent::find([
                "category_id = " . $category_id . " AND parent_id IN(" . implode(',', $del_parent_ids) . ")"
            ]);
            if(count($del_parents)){
                $del_parents->delete();
            }
        }
        if(count($new_parent_ids)){
            foreach ($new_parent_ids as $key => $val){
                $parent = new CategoryParent();
                $parent->category_id = $category_id;
                $parent->parent_id   = $val;
                $parent->tag_id      = intval($related_tags[$val]);
                $parent->save();
            }
        }
        if(count($old_parent_ids)){
            $old_parents = CategoryParent::find([
                "category_id = " . $category_id . " AND parent_id IN(" . implode(',', $old_parent_ids) . ")"
            ]);
            if(count($old_parents)){
                foreach ($old_parents as $item){
                    $item->tag_id = intval($related_tags[$val]);
                    $item->save();
                }
            }
        }

        return true;
    }

    public static function removeAnswer(Answer $answer) {
        if(!$answer){
            return false;
        }
        //删除回应
        $answer->delete();
        $params = [
            'id = ' . $answer->item_id,
        ];

        switch ($answer->item_type){
            case 'topic':
                $item = Topic::findFirst($params);
                break;
            case 'course':
                $item = Course::findFirst($params);
                break;
            case 'service':
                $item = Service::findFirst($params);
        }
        if($item){
            $item->answer_count = Answer::count("item_type = '" . $answer->item_type . "' AND item_id = " . $answer->item_id);
            if($item->last_answer == $answer->id){
                $lastAnswer = Answer::findFirst([
                    "item_type = '" . $answer->item_type . "' AND item_id = " . $answer->item_id,
                    'order' => 'id desc'
                ]);
                $item->last_answer = $lastAnswer->id;
            }
            $item->save();
        }

        return true;
    }

    public static function saveHistory($item_type, $item, $uid){
        //记录操作日志
        $history = new History();
        $history->item_type = $item_type;
        $history->item_id   = $item->id;
        $history->title     = $item->title;
        $history->message   = $item->message;
        $history->uid       = $uid;
        return $history->save();
    }
}