<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/8
 * Time: 13:39
 */

namespace app\common;
use yii\base\ErrorException;
use yii\base\Object;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class Ftp extends  Object {
    public $host;
    public $port;
    public $username;
    public $userpass;
    public $pasv=1;

    public $connection;

    public $destination;

    public function init(){
        if(!isset($this->host)||empty($this->host)){
            throw new InvalidConfigException("请配置ftp主机地址");
        }

        if(!isset($this->port)||empty($this->port)){
            $this->port=21;
        }
        if(!isset($this->destination)||empty($this->destination))
            $this->destination="/updateres";

        //开始链接ftp服务器
        $this->connection=ftp_connect($this->host);

        if(!is_resource($this->connection))
            throw new Exception("链接ftp服务器失败");

        if(!ftp_login($this->connection,$this->username,$this->userpass))
            throw new Exception("登录ftp服务器失败");
    }

    public function send($current,$newposition){
            ftp_pasv($this->connection,1);
            if(!is_file($current))
                throw new Exception("源文件不是文件");
            if(!file_exists($current))
                throw new Exception("源文件不存在");
            if(!ftp_put($this->connection,$this->destination."/".$newposition,$current,FTP_BINARY))
                throw new Exception("发送失败");
            return true;
    }

    public function close(){
            if(is_resource($this->connection))
                ftp_close($this->connection);
    }

    public function __destruct(){
        if(is_resource($this->connection))
            ftp_close($this->connection);
    }

}