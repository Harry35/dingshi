<?php

namespace iDeliveryFood\models;

use iDeliveryFood\models\BaseModel;

class UserModel extends BaseModel {

    function __construct() {
        parent::__construct();
    }

    public function findUserById($id) {
        $objUser = $this->db->user[$id];

        if (!$objUser) {
            return false;
        }

        $user = $this->transferUserObjToArray($objUser);

        return $user;
    }
    
    public function transferUserObjToArray($objUser) {
        $user = array();
        $user['id'] = $objUser['id'];
        $user['username'] = $objUser['username'];
        $user['pic'] = $objUser['pic'];
        $user['location'] = $objUser['location'];
        $user['status'] = $objUser['status'];
        $user['last_login'] = $objUser['last_login'];
        $user['created'] = $objUser['created'];

        return $user;
    }

    public function insertOneUser($arrayUser) {
        if (empty($arrayUser['username'])) {
            $faker = \Faker\Factory::create();
            $arrayUser['username'] = $faker->name;
        }
        
        if (empty($arrayUser['created'])) {
            $arrayUser['created'] = date('Y-m-d H:i:s');
        }
        
        if (empty($arrayUser['last_login'])) {
            $arrayUser['last_login'] = date('Y-m-d H:i:s');
        }
        
        if (empty($arrayUser['username'])) {
            $faker = \Faker\Factory::create();
            $arrayUser['username'] = $faker->firstName(null);
        }
        
        return $this->db->user()->insert($arrayUser);
    }
    
    public function insertMultiUser($arrayUsers) {
        return $this->db->user()->insert_multi($arrayUsers);
    }
    
    public function updateUser($arrayUser) {
        if (empty($arrayUser['id'])) 
            return false;
        
        $user = $this->db->user[$arrayUser['id']];
        
        if (empty($arrayUser['last_login'])) {
            $arrayUser['last_login'] = date('Y-m-d H:i:s');
        }
        
        return $user->update($arrayUser);
    }
}
