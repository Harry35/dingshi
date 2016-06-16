<?php

namespace iDeliveryFood\models;

use iDeliveryFood\models\BaseModel;

class SearchKeywordModel extends BaseModel {

    private static $URL_ADDR_API = 'http://api-adresse.data.gouv.fr';


    public $error = null;

    function __construct() {
        parent::__construct();
    }
    
    public function getRestosByInput($input=null, $limit=7){
        $keywords=$this->db->search_keyword();
        $keywordsDic = array();
        $pattern='/((75|92|91|93|94|95|77|78)[0-9]{3}|';
        foreach($keywords as $word){
            $pattern.=$word['value'].'|';
            $keywordsDic[$word['value']] = $word;
        }
        $pattern=substr($pattern, 0, -1);
        $pattern.=')/i';

        if(preg_match_all($pattern, $input, $matches)){

            $matchedWords = array();
            foreach ($matches[0] as $value) {
                if(isset($keywordsDic[$value])){
                    $matchedWords[] = $keywordsDic[$value];  
                }else{
                    //zipcode case
                    $matchedWords[] = array(
                        'obj_table' => 'resto_delivery',
                        'obj_column' => 'zipcode',
                        'obj_cond' => null
                    );
                }
                
            }
            $restos = $this->getRestosByKeywords($matchedWords, $input);
            return array_slice($restos, 0, $limit);
        }else{
            return null;
        }

    }

    public function getRestosByKeywords($keywords, $input, $rand = true){
        $ret = array();
        $tables = array();
        $conds = array();
        foreach ($keywords as $word) {
            $tables[]=$word['obj_table'];

            if($word['obj_table'] == 'resto_delivery' && $word['obj_column'] == 'zipcode'){
                $zip = $this->str2zip($input);
                if(!isset($conds[$word['obj_table']])){
                    $conds[$word['obj_table']] = empty($zip)? null : 'zipcode ='.$zip.' or zipcode='.substr($zip, 0, 2);
                }else{
                    $conds[$word['obj_table']] .= empty($zip)? null : ' or zipcode ='.$zip.' or zipcode='.substr($zip, 0, 2);
                }
            }

            if($word['obj_cond']){
                if(!isset($conds[$word['obj_table']])){
                    $conds[$word['obj_table']] = $word['obj_column'].$word['obj_cond'];
                }else{
                    $conds[$word['obj_table']] .= ' or '.$word['obj_column'].$word['obj_cond'];           
                }
            }
        }

        //concatenate query 
        $query = 'select resto.* from resto ';
        foreach ($tables as $table) {
            if($table != 'resto'){
                $query .= 'left join '.$table.' on resto.id ='.$table.'.resto_id ';
            }
        }
        $where = implode($conds, ') and (');
        $query .= 'where ('.$where.') group by resto.id';
        if ($rand == true) {
            $query .= ' ORDER BY rand();';
        } else {
            $query .= ';';
        }
        
        $restos = $this->pdo->query($query,\PDO::FETCH_ASSOC)->fetchAll();

        foreach ($restos as $resto) {
            if (empty($resto['profil_pic'])) {
                $resto['profil_pic'] = 'default.png';
            }
            $ret[$resto['id']] = $resto;
            
        }

        return $ret;
    }

    public function str2zip($str){
        $str = trim($str);
        $patternZip = '/^(75|92|91|93|94|95|77|78)[0-9]{3}$/';
        if(preg_match($patternZip, $str)){
            return $str;
        }else{
            $url = self::$URL_ADDR_API.'/search/?q='.urlencode($str).'&autocomplete=0&limit=5';
            $response = file_get_contents($url);
            $response = json_decode($response, true);
            
            //get zip from response
            $ret = 0;
            foreach ($response['features'] as $feature) {
                $zip = $feature['properties']['postcode'];
                if(preg_match($patternZip, $zip)){
                    $ret = $zip;
                    break;
                }
            }

            if(empty($ret)){
                $this->error = '地址未找到，请重新输入';
            }
            return $ret;
        }
    }

    public function geocode2zip($lat, $lon){
        $url = self::$URL_ADDR_API.'/reverse/?lon='.$lon.'&lat='.$lat;
        $response = file_get_contents($url);
        $response = json_decode($response, true);

        $zip = 0;
        $feature = $response['features'][0];

        return $feature['properties']['postcode'];
    }

}
