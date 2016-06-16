<?php
namespace iDeliveryFood\controllers;

use iDeliveryFood\models\RestoModel;
use iDeliveryFood\models\UserModel;
use iDeliveryFood\controllers\BaseController;

class RestoController extends BaseController {   
    private $restoModel;
    private $userModel;
            
    function __construct() {
        parent::__construct();
        $this->restoModel = new RestoModel();
        $this->userModel = new UserModel();
    }
    
    public function index($id) {
        $openid = isset($_GET['openid']) ? $_GET['openid'] : $_SESSION['openid'];
        $user = $this->userModel->findUserById($openid);
        if (!$user) {
            $arrayUser = [
                'id' => $openid,
                'username' => '',
                'pic' => rand(1,149).'.png',
                'location' => '',
                'last_login' => date('Y-m-d H:i:s'),
                'created' => date('Y-m-d H:i:s')
            ];
            $user = $this->userModel->insertOneUser($arrayUser);
        } else {
            $arrayUser = [
                'id' => $openid,
                'last_login' => date('Y-m-d H:i:s')
            ];
            $this->userModel->updateUser($arrayUser);
        }
        $_SESSION['openid'] = $user['id'];
        
        $resto = $this->restoModel->findRestoById($id);
        
        if ($resto) {
            echo $this->twig->render('resto.html', array('resto' => $resto, 'user_id' => $_SESSION['openid'], 'username' => $user['username']));
        }
    }
    
    public function addReview() {
        $data = array();
        $data['resto_id'] = $_POST['resto_id'];
        $data['user_id'] = $_POST['user_id'];
        $data['username'] = $_POST['username'];
        $data['text'] = $_POST['text'];
        $data['created'] = date('Y-m-d H:i:s');

        //修改用户名
        $this->userModel->updateUser([
            'id' => $data['user_id'],
            'username' => $data['username']
        ]);
        $result = $this->restoModel->insertRestoReview($data);
        print(json_encode($result));
    }
    
    public function addLike() {
        $data = array();
        $data['target_id'] = $_POST['target_id'];
        $data['user_id'] = $_POST['user_id'];
        $data['type'] = $_POST['type'];
        $data['created'] = date('Y-m-d H:i:s');
        
        $result = $this->restoModel->insertLike($data);
        print(json_encode($result));
    }
    
    public function getUserReview() {
        $result = $this->restoModel->findRestoReviewByUserIdAndRestoId($_POST['user_id'], $_POST['resto_id']);
        print(json_encode($result));
    }
}


