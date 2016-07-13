<?php

//require '/home/q/php/kafka_client/lib/kafka_client.php';
require_once(__DIR__."/util.php");

ini_set('date.timezone','Asia/Shanghai');
ini_set("memory_limit","512M");

abstract class Handler
{
    protected $conf;
    public function __construct()
    {
        $this->conf = include(__DIR__."/conf.php");
    }

    public function CheckResult(&$req)
    {
        /*
        $redis = new cache();

        $ret;
        $count = 120;
        $taskid = $req['taskid'];
        while($count--) {
//            $ret = array();
            // 查询 taskid
            $ret = $redis->ins($this->conf['redis'])->get($taskid);
            if(empty($ret)) {
                usleep(50*1000);
                continue;
            }
            $redis->ins($this->conf['redis'])->del($taskid);
            $redis->ins($this->conf['redis'])->del($req['taskid'] . 'blob');
            break;
        }
        return $ret;
         */

        $taskid = $req['taskid'];
        $ret = array();
        $redis = new Redis();     
        $redis->pconnect($this->conf['redis']['host'], $this->conf['redis']['port']);
        if(isset($this->conf['redis']['auth'])) {
            $redis->auth($this->conf['redis']['auth']);
        }

        $ret = $redis->ping();
        if(empty($ret)) {
            MyLog::ins()->info(sprintf("[taskid=%s] connet cache failed", $taskid));
            return $ret;
        }

        $count = 120;

        MyLog::ins()->info(sprintf("[taskid=%s] check cache", $taskid));

        while($count--) {
            $ret = array();
            // 查询 taskid
            $ret = $redis->get($taskid);
            if(empty($ret)) {
                usleep(50*1000);
                continue;
            }
            $redis->del($taskid);
            $redis->del($req['taskid'] . 'blob');
            break;
        }
        MyLog::ins()->info(sprintf("[taskid=%s] return %s", $taskid, json_encode($ret)));
        return $ret; 
    }
    public function proc(&$req)
    {
        $req['tasklist'] = $this->GetPipeline();

        if(!$this->Save($req)) {
            return false;
        }

        if($this->IsAsync($req)) {
            $ret = array();
            $ret['TASKID'] = $req['taskid'];

            return $ret; 

        } else {
            return $this->CheckResult($req);
        }
        return false;
    }
    private function IsAsync(&$req)
    {
        if(isset($req['async'])) {
            return true;

        } else {
            return false;
        }
    }

    public function Send(&$req)
    {
        $topic = "";
        isset($req['tasklist']['begin']) ? $topic = $req['tasklist']['begin'] : null;

        if(empty($topic)) {
            MyLog::ins()->info("no topic");
            return false;
        }
        $message = json_encode($req);
        while(true) {
            // echo $message,"\n";
            // break;
            $ret=Kafka_Producer::getInstance($this->conf["zkCluster"])->send($message,$topic);
            if($ret==true) {
                MyLog::ins()->info($req['taskid']."\tsendMessage send success\t".gettimeofday(true));
                break;
            }
        } 
        return true;
    }

    public function Save(&$req)
    {
        /*
        $taskid = $req['taskid'];
        $blob_id = $taskid . "blob";
        $topic = "";

        isset($req['tasklist']['begin']) ? $topic = $req['tasklist']['begin'] : null;

        if(empty($topic)) {
//            MyLog::ins()->info("no topic");
            return false;
        }

        $redis = new cache();

        $redis->ins($this->conf['redis'])->set($blob_id, json_encode($req));
        $redis->ins($this->conf['redis'])->expire($blob_id, 30);
        $redis->ins($this->conf['redis'])->rpush($topic, $blob_id);
        return true;
         */
        
        $redis = new Redis();     
        $redis->pconnect($this->conf['redis']['host'], $this->conf['redis']['port']);
        if(isset($this->conf['redis']['auth'])) {
            $redis->auth($this->conf['redis']['auth']);
        }

        $taskid = $req['taskid'];
        $blob_id = $taskid . "blob";
        $topic = "";
        isset($req['tasklist']['begin']) ? $topic = $req['tasklist']['begin'] : null;

        if(empty($topic)) {
            MyLog::ins()->info("no topic");
            return false;
        }

        // $taskid 中保留 带有图片的json
        // 不再为每一张人脸做图片切分
        $redis->set($blob_id, json_encode($req));
        $redis->expire($blob_id, 30);
        
        // topic 改为 listname
        $redis->rpush($topic, $blob_id);
        MyLog::ins()->info(sprintf("[taskid=%s] save cache", $taskid));
        return true;
         
    }
    // 获取 处理流程
    abstract public function GetPipeline();
}
?>
