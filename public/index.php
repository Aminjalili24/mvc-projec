<?php
require_once (dirname(__DIR__).'/vendor/autoload.php');
require_once (dirname(__DIR__).'/bootstrap/app.php');

$post = '<h3>Post method </h3>
<form method="post" action="'.BASE_URL.'/store">
<input type="text" name="title">
<input type="submit" name="submit">
</form> ';

$put = '<h3>Put method </h3>
<form method="post" action="'.BASE_URL.'/update/1">
<input type="text" name="title">
<input type="hidden" name="_method" value="put">
<input type="submit" name="submit">
</form> ';

$delete = '<h3>Delete method </h3>
<form method="post" action="'.BASE_URL.'/delete">
<input type="text" name="title">
<input type="submit" name="submit">
</form> ';
echo '<br/><br/><br/>';
echo 'current route :'.CURRENT_ROUTE;
echo $put;