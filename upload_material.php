<?php

if(isset($_POST['submit'])){

var_dump($_FILES);

	echo upload_meterial(
		array(
			'filename' => '/var/www/iDeliveryFood/img_help.jpg',
			'filelength' => $_FILES['fileToUpload']['size'],
			'content-type' => $_FILES['fileToUpload']['type']
		), 
		'Y3DVN_yx4bHaK0M88Al3yqMQalHr7njdRNVY9Fhz5ytro0Jl8xlTE-hN3rHD01R_--DFjtzI8APdMWj-IOimFNRaM1UiUhU3E9uV0ENXiaync8vvI_GCXx0qB8Zp5dZfFNAfABAUCP'
	);
}




function upload_meterial($file_info,$access_token){
  $url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$access_token}&type=image";
  $ch1 = curl_init ();
  $timeout = 5;

  $real_path="{$file_info['filename']}";
  var_dump($real_path);
  //$real_path=str_replace("/", "\\", $real_path);
  //$data= array("media"=>"@{$real_path}",'form-data'=>$file_info);
  $data= array("media"=>"@{$real_path}", 'filelength' => '808981');//'form-data'=>$file_info);
  curl_setopt ( $ch1, CURLOPT_URL, $url );
  curl_setopt ( $ch1, CURLOPT_POST, 1 );
  curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
  curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
  curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
  curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data);
  $result = curl_exec ( $ch1 );
  echo '<br/>';
  echo 'reulst is ==========>'.$result;
  curl_close ( $ch1 );
  if(curl_errno()==0){
    $result=json_decode($result,true);
    //var_dump($result);
    return $result['media_id'];
  }else {
    return false;
  }
}

?>

<form action="upload_material.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>