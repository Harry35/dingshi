<?php
namespace iDeliveryFood;
/**
 *
 * 自动载入函数
 * Created by Lane.
 * User: lane
 * Date: 14-10-15
 * Time: 下午6:13
 * E-mail: lixuan868686@163.com
 * WebSite: http://www.lanecn.com
 */
class Autoloader{
    const NS_PREFIX_PROJECT = 'iDeliveryFood\\';
    const NS_PREFIX_LANEWECHAT = 'LaneWeChat\\';

    /**
     * 向PHP注册在自动载入函数
     */
    public static function register(){
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * 根据类名载入所在文件
     */
    public static function autoload($className){
        $projectPrefixLen = strlen(self::NS_PREFIX_PROJECT);
        $libWechatPrefixLen = strlen(self::NS_PREFIX_LANEWECHAT);

        if(strncmp(self::NS_PREFIX_PROJECT, $className, $projectPrefixLen) === 0){
            //default load
            $filePath = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, $projectPrefixLen));
            $filePath = realpath(__DIR__ . (empty($filePath) ? '' : DIRECTORY_SEPARATOR) . $filePath . '.php');

        }elseif(strncmp(self::NS_PREFIX_LANEWECHAT, $className, $libWechatPrefixLen) === 0){
            //load for linewechat
            $filePath = 'libs'.DIRECTORY_SEPARATOR.strtolower($className);
            $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);
            $filePath = realpath(__DIR__ .(empty($filePath) ? '' : DIRECTORY_SEPARATOR) . $filePath . '.lib.php');
        }else{
            $filePath = null;
        }

        if(file_exists($filePath)){
            require_once $filePath;
        }else{
            echo $filePath;
        }
    }
}