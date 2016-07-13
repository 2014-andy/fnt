<?php 
require_once(__DIR__."/dispatcher.php");
require_once(__DIR__."/util.php");

function HandlerWeb()
{
    $req = $_GET + $_POST;
    Dispatcher::HandlerReq($req);
}

function HandlerCli()
{
    while(($line = fgets(STDIN)) !== false) {
        $line = trim($line);

        if(empty($line)) {
            continue;
        }

        $arr['imgdata'] = $line;

        $info = $arr;

        Dispatcher::HandlerReq($info);
    }
}


if(PHP_SAPI != 'cli') {
    HandlerWeb();
} else {
    HandlerCli();
}
?>
