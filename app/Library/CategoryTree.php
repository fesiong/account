<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 2016/11/4
 * Time: 上午10:16
 */

namespace Fesion\Library;


use Fesion\Models\CategoryParent;

class CategoryTree
{
    public $categories;
    public $tree = [];
    public $deep = 1;
    //public $icon = array('│','├','└', '┌');
    public $icon = array('└&nbsp;&nbsp;','','', '');
    public $categoryParents;

    public function __construct($categories = null, $id_name = 'id', $parent_name = 'parent_id'){
        $this->categories = $categories;
        $this->parent_name = $parent_name;
        $this->id_name     = $id_name;
        //$this->categoryParents = CategoryParent::find();
    }

    public function getTree($rootId = 0,$add = '',$parent_end =true){
        $is_top = 1;
        $child_arr = $this->getChild($rootId);
        $space = $this->icon[3];
        if(is_array($child_arr)){
            $cnt = count($child_arr);
            foreach($child_arr as $key => $child){
                $cid = $child->{$this->id_name};
                if($this->deep >1){
                    if($is_top == 1 && $this->deep > 1){
                        $space = $this->icon[1];
                        //if(!$parent_end)
                        $add .=  $this->icon[0];
                        //else $add .= '&nbsp;&nbsp;';
                    }
                    if($is_top == $cnt){
                        $space = $this->icon[2];
                        $parent_end = true;
                    }else {
                        $space = $this->icon[1];
                        $parent_end = false;
                    }
                }
                $child->spacer = $add . $space;

                $this->tree[] = $child;
                $is_top++;

                $this->deep++;
                if($this->getChild($cid)){
                    $this->getTree($cid,$add,$parent_end);
                    $this->deep--;
                }
            }

        }
        return $this->tree;
    }

    public function getTreeArray($rootId = 0,$add = '',$parent_end =true){
        $is_top = 1;
        $child_arr = $this->getChildArray($rootId);
        $space = $this->icon[3];
        if(is_array($child_arr)){
            $cnt = count($child_arr);
            foreach($child_arr as $key => $child){
                $cid = $child[$this->id_name];
                if($this->deep >1){
                    if($is_top == 1 && $this->deep > 1){
                        $space = $this->icon[1];
                        //if(!$parent_end)
                        $add .=  $this->icon[0];
                        //else $add .= '&nbsp;&nbsp;';
                    }
                    if($is_top == $cnt){
                        $space = $this->icon[2];
                        $parent_end = true;
                    }else {
                        $space = $this->icon[1];
                        $parent_end = false;
                    }
                }
                $child['spacer'] = $add . $space;

                $this->tree[] = $child;
                $is_top++;

                $this->deep++;
                if($this->getChildArray($cid)){
                    $this->getTreeArray($cid,$add,$parent_end);
                    $this->deep--;
                }
            }

        }
        return $this->tree;
    }

    function getTreeNew(){
        $tmp = [];
        foreach($this->categories as $node){
            $tmp[$node->{$this->parent_name}][] = $node;
        }
        krsort($tmp);
        foreach($tmp as $pid => &$childs){
            foreach($childs as &$child){
                if(isset($tmp[$child->{$this->id_name}])){
                    $child->child = $tmp[$child->{$this->id_name}];
                }
            }
        }

        return $tmp[0];
    }

    public function getChildOld($root = 0){
        $child = [];
        $child_ids = [];

        if($root == 0){
            $parent_catids = $catids = [];
            foreach ($this->categoryParents as $key => $val){
                $parent_catids[$val->category_id] = $val->category_id;
            }
            foreach ($this->categories as $key => $val){
                $catids[$val->id] = $val->id;
            }

            $child_ids = array_diff($catids, $parent_catids);
        }else{
            foreach ($this->categoryParents as $key => $val){
                if($val->parent_id == $root){
                    $child_ids[] = $val->category_id;
                }
            }
        }

        if(count($child_ids)){
            foreach($this->categories as $id=>$a){
                if(in_array($a->id, $child_ids)){
                    $child[$a->id] = $a;
                }
            }
        }

        return $child?$child:false;
    }

    public function getChild($root = 0){
        $child = array();
        foreach($this->categories as $id=>$a){
            if($a->{$this->parent_name} == $root){
                $child[$a->{$this->id_name}] = $a;
            }
        }
        return $child?$child:false;
    }

    public function getChildArray($root = 0){
        $child = array();
        foreach($this->categories as $id=>$a){
            if($a[$this->parent_name] == $root){
                $child[$a[$this->id_name]] = $a;
            }
        }
        return $child?$child:false;
    }

    public function setCategories($categories){
        $this->categories = $categories;
    }
}