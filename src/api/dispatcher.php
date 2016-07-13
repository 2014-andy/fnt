<?php
require_once(__DIR__."/util.php");
require_once(__DIR__."/err.php");
require_once(__DIR__."/facehandler.php");
require_once(__DIR__."/getresult.php");

class Dispatcher 
{
    public static function HandlerReq(&$req)
    {
        $taskid = GenId::GetTaskId();
        // 添加taskid 到 req
        $req['taskid'] = $taskid;

        try {
            if(!Dispatcher::CheckReq($req)) {
                MyLog::ins()->info(sprintf("[taskid=%s] CheckReq err", $taskid));
                throw new Exception(ErrDes::GetDes(ErrDes::PARAM_ERR), ErrDes::PARAM_ERR);
            }
             
            $tag = $req['tag'];
            $res = Dispatcher::GetHandler($tag)->proc($req);
            if(false !== $res) {
                $data = array();
                $res = ResultUtls::SetSucc($res);
                $res['TASKID'] = $taskid;
                Dispatcher::Finish($res);
                return;
            } else {
                throw new Exception(ErrDes::GetDes(ErrDes::HANDLER_ERR), ErrDes::HANDLER_ERR);
            }
            
        } catch(Exception $e) {
            $res = ResultUtls::SetFail($e->getMessage(), $e->getCode());            
            $res['TASKID'] = $taskid;
            MyLog::ins()->info(sprintf("[taskid=%s] Exception return %s", $taskid, json_encode($res)));
            Dispatcher::Finish($res);
            return;
        }
    }

    public static function Finish(&$res)
    {
        echo json_encode($res);
        return;
    }

    public static function CheckReq(&$req)
    {
        if(!isset($req['tag'])) {
            return false;
        }

        if(!isset($req['imgdata']) && !isset($req['taskid'])) {
            return false;
        } 

        return true;
    }

    public static function GetHandler($tag)
    {
        static $HandlerMap = array(
//            "facedetect" => "FaceDetectHandler",
//            "facealignment" => "FaceAlignmentHandler",
//            "facefeature" => "FaceFeatureHandler",
//            "getresult" => "GetResultHandler",
//            "facedaf" => "FaceDAFHandler",
//            "faceda" => "FaceDAHandler",
            "default" =>"Handler",
        );

        if(isset($HandlerMap[$tag])) {
            MyLog::ins()->info(sprintf("[taskid=%s] GetHandler %s", $taskid, $tag));
            return new $HandlerMap[$tag];
        } else {
            return new $HandlerMap["default"];
        }
    }
}
?>
