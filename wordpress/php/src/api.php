<?php
namespace Acme\Tools;
use DateTime;
class Foo
{
    public function doAwesomeThings()
    {
        echo 'Hi listeners';
        //var $test = new hello();
        //echo($test);
        $dt = new DateTime();
        //$message = self::hello();
        var_dump(self::hello());

    }

    public function hello()
    {

        $msg = "bonjour";
        return $msg;
    }
}
