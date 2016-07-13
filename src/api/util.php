<?php

require_once(__DIR__."/log4php/Logger.php");
#error_reporting(0);

class MyLog
{
    static private $pay ;
    static public function ins()
    {
        if(empty(self::$pay)) {
            Logger::configure(__DIR__.'/conf/log4php.xml');
            self::$pay = Logger::getRootLogger();
        }
        return self::$pay;
    } 
    private function __construct(){}
    private function __clone(){}
}

class GenId
{
    public static function GetTaskId()
    {
        mt_srand(microtime(TRUE)*1000);
        return md5(uniqid(mt_rand(),true));
    }
}

class ResultUtls 
{
    const RES_SUCC = "SUCC";
    const RES_FAIL = "FAIL";

    public static function SetSucc($data)
    {
        return ResultUtls::SetStatue(self::RES_SUCC, "OK", 0, $data);
    }

    public static function SetFail($des, $code = -1, $data = array())
    {
        return ResultUtls::SetStatue(self::RES_FAIL, $des, $code, $data);
    }

    public static function SetStatue($status, $des, $code, $data)
    {
        $res = array();
        $res['STATUS'] = $status;
        $res['MSG']    = $des;
        $res['ERRNO']  = $code;
        $res['DATA']   = $data;

        return $res;
    }
}

class cache
{
    static private $_redis;
    public function ins($conf) 
    {
        logInfo("ins");
        if(NULL === self::$_redis) {
            logInfo("new redis");
            self::$_redis = new Redis();
            self::$_redis->pconnect($conf['host'], $conf['port']);
            if(isset($conf['auth'])) {
                self::$_redis->auth($conf['auth']);
            }
            $ret = self::$_redis->ping();
            if(empty($ret)) {
                return NULL;
            }
        } else {
            logInfo("ok");
            return $this;
            $ret = self::$_redis->ping();
            if(empty($ret)) {
                logInfo("ping ".json_encode($ret));
                logInfo("new redis");
                self::$_redis->close();
                self::$_redis = new Redis();
                self::$_redis->connect($conf['host'], $conf['port']);
                if(isset($conf['auth'])) {
                    self::$_redis->auth($conf['auth']);
                }
            }
            
            $ret = self::$_redis->ping();
            if(empty($ret)) {
                logInfo("ping ".json_encode($ret));
                return NULL;
            }
        } 
        return $this;
    }

//    private function __construct(){}
    public function __destruct()
    {
//        self::$_redis->close();
    }
    
    public function get($key)
    {
        return self::$_redis->get($key);
    }
    public function set($key, $value)
    {
        return self::$_redis->set($key, $value);
    }
    public function rpush($listname, $key)
    {
        return self::$_redis->rpush($listname, $key);
    }
    public function del($key)
    {
        return self::$_redis->del($key);
    }
    public function expire($key, $time)
    {
        return self::$_redis->expire($key, $time);
    }
}

function http_post($url,$data,$header)
{
    $ch=curl_init();
    if($header)
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

    curl_setopt($ch, CURLOPT_USERAGENT, 'Internet Explorer 9.0 Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects

    $res=curl_exec($ch);
    $info=curl_getinfo($ch);
    curl_close($ch);
    if($info['http_code']!=200) {
        return false;
    }
    return $res;
}

function http_get($url, $header, $refer,$proxy)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if ($refer) curl_setopt($ch, CURLOPT_REFERER, $refer);
    if ($proxy) curl_setopt($ch,CURLOPT_PROXY,$proxy);

    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
    curl_setopt($ch, CURLOPT_USERAGENT, 'Internet Explorer 9.0 Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if($info['http_code'] != 200) {
        return false;
    }
    return $res;
}
?>
