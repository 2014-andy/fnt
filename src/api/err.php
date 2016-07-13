<?php
class ErrDes
{
    const OK            = 0;
    const UNKOWN_ERR    = 1;
    const PARAM_ERR     = 2;
    const HANDLER_ERR   = 3;

    public static $errmsg = array(
        self::OK => "OK",
        self::UNKOWN_ERR => "UNKOWN_ERR",
        self::PARAM_ERR => "PARAM_ERR",
        self::HANDLER_ERR => "HANDLER_ERR",
    );

    public static function GetDes($code) 
    {
        if(isset(self::$errmsg[$code])) {
            return self::$errmsg[$code];
        }

        return self::$errmsg[self::UNKOWN_ERR];
    }
}

?>
