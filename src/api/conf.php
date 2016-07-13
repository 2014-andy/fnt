<?php

return array(
			"zkCluster"=>"shjc",
			"fromTopic"=>"intelligent_cloud_face_detect_topic",
			"endTopic"=>"intelligent_cloud_face_@_@_@_lasttopic",
			"group"=>"default",
			"socketTimeout"=>30,
			"singleOffsetCommit"=>false,
			"maxTryTimes"=>3,
			"maxFetchSize"=>31457280,

            "redis"=>array(
                "host"=>"10.125.209.234",
                "port"=>5990,
                "auth"=>"051d55fc4c002a64",
                "expireTime"=>600,
            ),
			"handler"=>"FaceDetectHandler",
		);

?>
