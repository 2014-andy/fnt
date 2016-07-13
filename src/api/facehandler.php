<?php

require_once(__DIR__."/handler.php");

class FaceDetectHandler extends Handler
{
    public function GetPipeline()
    {
        // 从 begin 开始
        // 目前仅支持链式操作
        return array(
            "begin" => "intelligent_cloud_face_detect_topic",
        );
    } 
}

class FaceAlignmentHandler extends Handler
{
    public function GetPipeline()
    {
        // 从 begin 开始
        // 目前仅支持链式操作
        return array(
            "begin" => "intelligent_cloud_face_detect_topic",
            "intelligent_cloud_face_detect_topic" => "intelligent_cloud_face_alignment_topic",
        );
    } 
}

class FaceFeatureHandler extends Handler
{
    public function GetPipeline()
    {
        // 从 begin 开始
        // 目前仅支持链式操作
        return array(
            "begin" => "intelligent_cloud_face_detect_topic",
            "intelligent_cloud_face_detect_topic" => "intelligent_cloud_face_alignment_topic",
            "intelligent_cloud_face_alignment_topic" => "intelligent_cloud_face_feature_topic"
        );
    } 
}
class FaceDAFHandler extends Handler
{
    public function GetPipeline()
    {
        return array(
            "begin" => "intelligent_cloud_face_daf_topic",
        );
    }
}
class FaceDAHandler extends Handler
{
    public function GetPipeline()
    {
        return array(
            "begin" => "intelligent_cloud_face_da_topic",
        );
    }
}
?>

