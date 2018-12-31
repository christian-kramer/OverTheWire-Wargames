<?php

$results = [
    "G+glEae6W/1XjA7vRm21nNyEco/c+J2TdR0Qp8dcjPKriAqPE2++uYlniRMkobB1vfoQVOxoUVz5bypVRFkZR5BPSyq/LC12hqpypTFRyXA=",
    "G+glEae6W/1XjA7vRm21nNyEco/c+J2TdR0Qp8dcjPIYiwNnSJY7KHJGU+XjuMzVvfoQVOxoUVz5bypVRFkZR5BPSyq/LC12hqpypTFRyXA=",
    "G+glEae6W/1XjA7vRm21nNyEco/c+J2TdR0Qp8dcjP	KEMZKNASy09t5ooTNAbaX0vfoQVOxoUVz5bypVRFkZR5BPSyq/LC12hqpypTFRyXA=",
    "G%2BglEae6W%2F1XjA7vRm21nNyEco%2Fc%2BJ2TdR0Qp8dcjPL%2FSLZaDj43BJkP%2BO1omSf3b2g2thDg1retAaVoMIzmyadz8xhQlKoBQI8fl9A304VnjFdz7MKPhw5PTrxsgHCk"
];

foreach ($results as $result)
{
    $newresults[] = bin2hex(base64_decode($result));
}

file_put_contents('raw_decoded', implode("\n", $newresults));

?>