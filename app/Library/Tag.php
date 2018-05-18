<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/18
 * Time: 下午8:58
 */

namespace Fesion\Library;

use Phalcon\Tag as cTag;

class Tag extends cTag
{
    private $_keywords;
    private $_description;
    /* ['title' => '', 'link' => '']*/
    private $crumb = [];

    public function setCrumb($title, $link = null){
        $this->crumb[] = [
            'title' => $title,
            'link'  => $link,
        ];
    }

    public function getCrumb(){
        return $this->crumb;
    }

    public function hasCrumb(){
        return !empty($this->crumb);
    }

    public function setKeywords($keywords){
        $this->_keywords = $keywords;
    }

    public function getKeywords($tags = true){
        if(!$tags){
            return $this->_keywords;
        }

        if($this->_keywords){
            return '<meta name="keywords" content="' . $this->_keywords . '">';
        }
    }

    public function setDescription($description){
        $this->_description = $description;
    }

    public function getDescription($tags = true){
        if(!$tags){
            return $this->_description;
        }

        if($this->_description) {
            return '<meta name="description" content="' . $this->_description . '">';
        }
    }
}