<?php

namespace iDeliveryFood\models;

use iDeliveryFood\models\BaseModel;

class RestoModel extends BaseModel {

    function __construct() {
        parent::__construct();
    }

    public function findRestoById($id) {
        $objResto = $this->db->resto[$id];

        if (!$objResto) {
            return false;
        }

        $resto = $this->transferRestoObjToArray($objResto);

        return $resto;
    }
    
    public function transferRestoObjToArray($objResto) {
        $resto = array();
        $resto['id'] = $objResto['id'];
        $resto['name'] = $objResto['name'];
        $resto['profil_pic'] = $objResto['profil_pic'];
        $resto['menu_pics'] = explode(";", $objResto['menu_pics']);
        $resto['address'] = $objResto['address'];
        $resto['tel'] = explode(";", $objResto['tel']);
        $resto['wechat'] = $objResto['wechat'];
        $resto['email'] = $objResto['email'];
        $resto['presentation'] = $objResto['presentation'];
        $resto['info1'] = $objResto['info1'];
        $resto['info2'] = $objResto['info2'];
        $resto['info3'] = $objResto['info3'];
        $resto['info4'] = $objResto['info4'];
        $resto['tag'] = $this->findRestoTagByRestoId($objResto['id']);
        $resto['foods'] = $this->findFoodByRestoId($objResto['id']);
        $resto['reviews'] = $this->findReviewByRestoId($objResto['id']);
        $resto['haveReview'] = !empty($resto['reviews']);

        return $resto;
    }
    
    public function findFoodByRestoId($id) {
        $objFoods = $this->db->resto_food()
            ->select("id, name, pic, price")
            ->where("resto_id = ?", $id)
        ;

        if (!$objFoods->fetch()) {
            return false;
        }
        
        $foods = array();
        foreach ($objFoods as $objFood) {
            $food['id'] = $objFood['id'];
            $food['name'] = $objFood['name'];
            $food['price'] = $objFood['price'];
            $food['pic'] = $objFood['pic'];
            $food['likes'] = $this->findTotalLikesByIdAndType($food['id'], "food");
            $food['liked'] = $this->findLikeByUser($food['id'], "food", $_SESSION['openid']) !== false ? true : false;
            $foods[] = $food;
            
        }
        
        return $foods;
    }
    
    public function findTotalLikesByIdAndType($id, $type) {
        $count = $this->db->resto_like()
            ->where("target_id = ?", $id)
            ->where("type = ?", $type)
            ->count('*')
        ;

        return $count;
    }
    
    public function findLikeByUser($food_id, $type, $user_id) {
        $objLike = $this->db->resto_like()
            ->select("*")
            ->where("type = ?", $type)
            ->where("target_id = ?", $food_id)
            ->where("user_id = ?", $user_id)
        ;

        if (!$objLike->fetch()) {
            return false;
        }
        
        return $objLike;
    }
    
    public function findReviewByRestoId($id) {
        $objReviews = $this->db->resto_review()
            ->select("id, text, created, user_id, username")
            ->where("resto_id = ?", $id)
            ->order("created DESC")
        ;

        if (!$objReviews->fetch()) {
            return false;
        }
        
        $reviews = array();
        foreach ($objReviews as $objReview) {
            $reviews[] = $this->transferReviewObjToArray($objReview);
        }
        return $reviews;
    }
    
    public function transferReviewObjToArray($objReview) {
        $review = array();
        $review['id'] = $objReview['id'];
        $review['text'] = $objReview['text'];
        $review['user_id'] = $objReview['user_id'];
        $review['username'] = $objReview['username'];
        $review['pic'] = $this->db->user[$objReview['user_id']]['pic'];
        $review['created'] = $objReview['created'];
        $review['likes'] = $this->findTotalLikesByIdAndType($review['id'], "review");
        $review['liked'] = $this->findLikeByUser($review['id'], "review", $_SESSION['openid']) !== false ? true : false;

        return $review;
    }
    
    public function findRestoTagByRestoId($restoId) {
        $objTags = $this->db->resto_tag()
            ->select("tag")
            ->where("resto_id = ?", $restoId)
        ;
        
        if (!$objTags->fetch()) {
            return false;
        }
        
        $tags = array();
        foreach ($objTags as $objTag) {
            $tags[] = $objTag['tag'];
        }
        
        return $tags;
    }

    public function findRestosByFields($arrWhere = null) {
        if (!isset($arrWhere['status'])) {
            $arrWhere['status'] = 1;
        }
        
        $objRestos = $this->db->resto()
            ->select("*")
            ->where($arrWhere)
        ;

        if (!$objRestos->fetch()) {
            return false;
        }
        
        $restos = array();
        foreach ($objRestos as $objResto) {
            $restos[] = $this->transferRestoObjToArray($objResto);
        }
        
        return $restos;
    }

    public function findRestosByTag($tag) {
        $restoTags = $this->db->resto_tag()
            ->select("*")
            ->where('tag = ?', $tag)
        ;
        if (!$restoTags->fetch()) {
            return false;
        }
        
        $restos = array();
        foreach ($restoTags as $restoTag) {
            $objResto = $this->db->resto[$restoTag['resto_id']];
            $restos[] = $this->transferRestoObjToArray($objResto);
        }

        return $restos;
    }

    public function findRestosByZipcode($zipcode) {
        $restoDlvrs = $this->db->resto_delivery()
            ->select("*")
            ->where('zipcode = ?', $zipcode)
        ;
        if (!$restoDlvrs->fetch()) {
            return false;
        }
        
        $restos = array();
        foreach ($restoDlvrs as $restoDlvr) {
            $objResto = $this->db->resto[$restoDlvr['resto_id']];
            $restos[] = $this->transferRestoObjToArray($objResto);
        }

        return $restos;
    }
    
    public function findRestoReviewByUserid($userId) {
        $restoReview = $this->db->resto_review()
            ->select("*")
            ->where('user_id = ?', $userId)
        ;
        if (!$restoReview->fetch()) {
            return false;
        }
        
        return true;
    }
    
    public function findRestoReviewByUserIdAndRestoId($userId, $restoId) {
        $restoReview = $this->db->resto_review()
            ->select("*")
            ->where('user_id = ?', $userId)
            ->where('resto_id = ?', $restoId)
        ;
        if (!$restoReview->fetch()) {
            return false;
        }
        
        return true;
    }
    
    public function insertOneResto($arrayResto) {
        return $this->db->resto()->insert($arrayResto);
    }
    
    public function insertMultiResto($arrayRestos) {
        return $this->db->resto()->insert_multi($arrayRestos);
    }
    
    public function insertRestoReview($arrayReview) {
        $review = $this->db->resto_review()->insert($arrayReview);
        return $this->transferReviewObjToArray($review);
    }
    
    public function insertLike($arrayLike) {
        return $this->db->resto_like()->insert($arrayLike);
    }
}
