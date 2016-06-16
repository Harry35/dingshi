<?php
namespace iDeliveryFood\models;

use iDeliveryFood\models\BaseModel;

class TestModel extends BaseModel{
	function __construct(){
		parent::__construct();
	}

	public function test(){
            foreach ($this->db->user() as $application) { // get all applications
                echo $application['User']; // print application title
            }
	}
}

?>