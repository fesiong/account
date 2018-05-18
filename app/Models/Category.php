<?php
/**
 * Created by PhpStorm.
 * 分类
 * User: fesiong
 * Date: 16/8/23
 * Time: 上午11:43
 */

namespace Fesion\Models;

/**
 * @method static Category findFirstById($params = null)
 */

class Category extends ModelsBase
{
    public $id;
    public $parent_id;
    public $title;
    public $description;
    /** @var string $type 分类类别 */
    public $type;
    public $sort;
    public $add_time;
    public $uid;

    public function initialize(){
        $this->hasOne(
            'parent_id',
            'Fesion\Models\Category',
            'id',
            [
                'alias'    => 'parent',
                'reusable' => true
            ]
        );
    }
}