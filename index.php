<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="Tomas Litera">
		<title>XSS Examples</title>
	</head>
	<body>
	<h1>XSS examples</h1>
	<h2>Non-persistent</h2>
	<h3>Comment Form - POST</h3>
	<p>&lt;script&gt;alert("Hacked! Anonymous see ya!")&lt;/script&gt;</p>
	<form action="index.php" method="post">
		<input type="text" name="comment" value="">
		<input type="submit" name="submit" value="Submit">
	</form>
 
<?php

if(isset($_POST["comment"])) {
	echo 'Commented: '.$_POST["comment"];
}

?>
	<hr />
	<h3>Search Form - GET</h3>
	<p>
		http://<?php echo $_SERVER['HTTP_HOST']?>/xss-examples/?query=&lt;script&gt;alert("Hacked! Anonymous see ya!")&lt;/script&gt;
	</p>
	<p>
		http://<?php echo $_SERVER['HTTP_HOST']?>/xss-examples/?query=%3Cscript%3Ealert(%22Hacked!%20Anonymous%20see%20ya!%22)%3C%2Fscript%3E
	</p>
	<p>
		http://<?php echo $_SERVER['HTTP_HOST']?>/xss-examples/?query=%3c%73%63%72%69%70%74%3e%77%69%6e%64%6f%77%2e%6f%6e%6c%6f%61%64%20%3d%20%66%75%6e%63%74%69%6f%6e%28%29%20%7b%76%61%72%20%6c%69%6e%6b%3d%64%6f%63%75%6d%65%6e%74%2e%67%65%74%45%6c%65%6d%65%6e%74%73%42%79%54%61%67%4e%61%6d%65%28%22%61%22%29%3b%6c%69%6e%6b%5b%30%5d%2e%68%72%65%66%3d%22%68%74%74%70%3a%2f%2f%61%74%74%61%63%6b%65%72%2d%73%69%74%65%2e%63%6f%6d%2f%22%3b%7d%3c%2f%73%63%72%69%70%74%3e
	</p>
	<form action="index.php" method="get">
		<input type="text" name="query" value="">
		<input type="submit" name="submit" value="Submit">
	</form>
<?php
// Get search results based on the query
if(isset( $_GET["query"])) {
	echo "You searched for: " . $_GET["query"];
}
?>
	<hr />
	<h2>Persistent</h2>
	<h3>Comment Form - FILE</h3>
	<p>&lt;script&gt;alert("Hacked! Anonymous see ya!")&lt;/script&gt;</p>
	<form action="index.php" method="post">
		<input type="text" name="comment-file" value="">
		<input type="submit" name="submit" value="Submit">
	</form>

<?php
if(isset($_POST["comment-file"])) {file_put_contents("comments.txt", $_POST["comment-file"], FILE_APPEND);}

echo 'Saved in file: '.file_get_contents("comments.txt");

?>
	<hr />
	<h2>Validation, sanitization, normalization</h2>
	<h3>US Phone number</h3>
	<p>1-770-937-9735</p>
	<form action="index.php" method="post">
		<input type="text" name="phone" value="">
		<input type="submit" name="submit" value="Submit">
	</form>
<?php
// validate a US phone number
if(isset($_POST['phone']) && preg_match('/1?\W*([2-9][0-8][0-9])\W*([2-9][0-9]{2})\W*([0-9]{4})(\se?x?t?(\d*))?/', $_POST['phone'])) {
    echo $_POST['phone'] . " is valid format (validation only).";
} else {
	echo "Not valid number.";
}

if(isset($_POST['phone'])) {
	// normalize and validate a US phone number
	$phone = preg_replace('/[^d]/', "", $_POST['phone']);
	$len = strlen($phone);
	if ($len == 7 || $len == 10 || $len == 11) {
	    echo $phone . " is valid format (normalization and validation).";
	}
}

?>
	<hr />
	<h3>Escaped search query</h3>

<?php

if(isset( $_GET["query"])) {
	// escape output sent to the browser
	echo "(escaped query) You searched for: " . htmlspecialchars($_GET["query"]);
}

?>
	<hr />
	<h3>Sanitized comments</h3>

<?php

// sanitize HTML from the comment
if(isset($_POST["comment"])) {
	$comment = strip_tags($_POST["comment"]);
} else {
	$comment = NULL;
}

// validate comment
if(isset($comment)) {
	$comment = trim($comment);
}
if(empty($comment)) {
    echo "<p>Must provide a comment</p>";
}
 
// sanitize comment
//$comment = strip_tags($comment);
 
// comment is now safe for storage
file_put_contents("comments-clear.txt", $comment, FILE_APPEND);
 
// escape comments before display
$comments = file_get_contents("comments-clear.txt");
echo 'Clear comments: '.htmlspecialchars($comments);
?>
	<hr />
	<h2>Sanitization, validation and normalization functions for PHP:</h2>
	<ul>
		<li>
			<a href="http://php.net/manual/en/function.htmlspecialchars.php">htmlspecialchars</a>
		</li>
		<li>
			<a href="http://php.net/manual/en/function.trim.php">trim</a>
		</li>
		<li>
			<a href="http://php.net/manual/en/function.strip-tags.php">strip_tags</a>
		</li>
		<li>
			<a href="http://php.net/manual/en/function.urldecode.php">urldecode</a>
		</li>
		<li>
			<a href="http://php.net/manual/en/function.stripcslashes.php">strip_slashes</a>
		</li>
		<li>
			<a href="http://php.net/manual/en/function.preg-match.php">preg_match</a>
		</li>
	</ul>

	<h2>Sources:</h2>
	<ul>
		<li>
			<a href="http://www.thegeekstuff.com/2012/02/xss-attack-examples/">http://www.thegeekstuff.com/2012/02/xss-attack-examples/</a>
		</li>
		<li>
			<a href="http://blog.astrumfutura.com/2012/03/a-hitchhikers-guide-to-cross-site-scripting-xss-in-php-part-1-how-not-to-use-htmlspecialchars-for-output-escaping/">http://blog.astrumfutura.com/2012/03/a-hitchhikers-guide-to-cross-site-scripting-xss-in-php-part-1-how-not-to-use-htmlspecialchars-for-output-escaping/</a>
		</li>
		<li>
			<a href="http://excess-xss.com/">http://excess-xss.com/</a>
		</li>
		<li>
			<a href="http://phpsecurity.readthedocs.org/en/latest/Cross-Site-Scripting-(XSS).html">http://phpsecurity.readthedocs.org/en/latest/Cross-Site-Scripting-(XSS).html</a>
		</li>
		<li>
			<a href="http://www.sitepoint.com/php-security-cross-site-scripting-attacks-xss/">http://www.sitepoint.com/php-security-cross-site-scripting-attacks-xss/</a>
		</li>
		<li>
			<a href="http://www.ryannedolan.info/teaching/cs4830/examples/vulnerability-examples/xss-examples">http://www.ryannedolan.info/teaching/cs4830/examples/vulnerability-examples/xss-examples</a>
		</li>
	</ul>

	</body>
</html>