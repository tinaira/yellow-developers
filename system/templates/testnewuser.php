<?php if(PHP_SAPI == "cli") $yellow->page->error(500, "Static website not supported!") ?>
<?php list($user, $email, $password) = getUser($yellow, $_REQUEST["status"], $_REQUEST["email"], $_REQUEST["password"]) ?>
<?php $yellow->snippet("header") ?>
<?php $yellow->snippet("sitename") ?>
<?php $yellow->snippet("navigation") ?>
<div class="content">
<h1><?php echo $yellow->page->getHtml("title") ?></h1>
<?php if(empty($user)): ?>
<p>This page creates an user account.<p>
<form class="user-form" action="<?php echo $yellow->page->getLocation() ?>" method="post">
<p class="user-email"><label for="email">Email:</label><br /><input type="text" class="form-control" name="email" id="email"></p>
<p class="user-password"><label for="password">Password:</label><br /><input type="text" class="form-control" name="password" id="password"></p>
<input type="hidden" name="status" value="create" />
<input type="submit" value="Create" class="btn" />
</form>
<?php else: ?>
<p>
1. Go to your Yellow installation, where the yellow.php is.<br />
2. Open file <code>system/config/user.ini</code> and add the following line to the end:<br />
</p>
<p><?php echo "<code>".htmlspecialchars($user)."</code>" ?></p>
<p><?php echo "Now you can login with email <code>".htmlspecialchars($email)."</code> and password <code>".htmlspecialchars($password)."</code>."?></p>
<?php endif ?>
</div>
<?php $yellow->snippet("footer") ?>
<?php function getUser($yellow, $status, $email, $password)
{
	if($status == "create")
	{
		$email = empty($email) ? "demo@demo.com" : $email;
		$password = empty($password) ? "demo" : $password;
		$algorithm = $yellow->config->get("webinterfaceUserHashAlgorithm");
		$cost = $yellow->config->get("webinterfaceUserHashCost");
		$hash = $yellow->toolbox->createHash($password, $algorithm, $cost);
		$email = strreplaceu(',', '-', $email);
		$hash = strreplaceu(',', '-', $hash);
		$name = strreplaceu(',', '-', empty($name) ? $yellow->config->get("sitename") : $name);
		$language = strreplaceu(',', '-', empty($language) ? $yellow->config->get("language") : $language);
		$home = strreplaceu(',', '-', empty($home) ? "/" : $home);
		$user .= "$email,$hash,$name,$language,$home\n";
		$yellow->page->header("Last-Modified: ".$yellow->toolbox->getHttpTimeFormatted(time()));
		$yellow->page->header("Cache-Control: no-cache, must-revalidate");
	} else {
		$user = $email = $password = "";		
	}
	return array($user, $email, $password);
}
?>