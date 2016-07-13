<?php

require_once(__DIR__."/handler.php");

class GetResultHandler extends Handler
{
    public function proc(&$req)
    {
        if(!$this->ChechParam($req)) {
            return false;
        }

        return $this->GetResult($req);     
    }

    private function GetResult(&$req)
    {
        $ret = array();
        $redis = new Redis();     
        $redis->connect($this->conf['redis']['host'], $this->conf['redis']['port']);
        if(isset($this->conf['redis']['auth'])) {
            $redis->auth($this->conf['redis']['auth']);
        }
        $taskid = $req['task_id'];
        $subid = "";

        // 查询 taskid
        $ret = $redis->hgetall($taskid);
        if(empty($ret)) {
            return $ret;
        }
        
        // 查询 subid
        $subid = $ret['subid'];
        $tasknum = is_numeric($ret['tasknum']) ? intval($ret['tasknum']) : 0;
        if($tasknum > 0) {
            $keys = $redis->hkeys($subid);
            if(empty($ret) || count($keys) !== $tasknum) {
                return $ret;
            }           
        }

        $ret = $redis->hgetall($subid);
        if(empty($ret)) {
            return $ret;
        }

        // 清理缓存
        $redis->del($req['taskid']);
        $redis->del($subid);
        return $ret; 
    }

    private function ChechParam(&$req)
    {
        return isset($req['taskid']);
    }

    public function GetPipeline() {
        return array();
    }
}
?>
