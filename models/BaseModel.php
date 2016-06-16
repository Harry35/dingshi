<?php
namespace iDeliveryFood\models;

require "libs/NotORM/NotORM.php";

class BaseModel {
	
	protected $pdo;
	protected $db;

	function __construct(){
            $user= 'root';
            $pass = '92200';
            $db = 'iDeliveryFood_db';
            $this->pdo = new \PDO('mysql:host=localhost;charset=utf8;dbname='.$db, $user, $pass) or die('hehe');
            $this->db = new \NotORM($this->pdo);
	}
}

?>