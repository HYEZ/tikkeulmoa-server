<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
<body>
	Success!
	 
	<form method="post" enctype="multipart/form-data" action="/api/group">
		<input type="file" name="photo">
		<input type="text" name="name" value="1">
		<input type="text" name="pw" value="1">
		<input type="text" name="user_idx" value="1">
		<input type="submit" name="">
	</form>
	
</body>
</html>