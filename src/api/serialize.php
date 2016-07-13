<?php
function get_dic($filename) 
{
//    echo "get dic $filename";
    //    $filename = 'starface_out';
    $fd = fopen($filename, 'r');
    if (!$fd) {
        return array();
    }

    $face_dir = array();
    while(($line = fgets($fd)) !== false) {
        $items = explode("\t", $line);
        $arr = json_decode($items[0], true);

        $star_name = $items[1];

        $result = $arr['DATA'];
        $result_arr = json_decode($result, true);

        $feature_arr = $result_arr['features'];

        if (isset($face_dir[$star_name])) {
            $face_dir[$star_name] = array_merge($face_dir[$star_name], $feature_arr);
        } else {
            $face_dir[$star_name] = $feature_arr;
        }
    }
    fclose($fd);
    return $face_dir;
}


$data = file_get_contents("serialize.out");
if (empty($data)) {
    $star = gettimeofday(true);
    $dic = get_dic("starface_out");
    echo "get_dic time ", gettimeofday(true) - $star;
    file_put_contents("serialize.out", serialize());
    return;
} else {
    $star = gettimeofday(true);
    $arr = unserialize($data);
    echo "unserialize ", gettimeofday(true) - $star;
    return;
}
 
?>
