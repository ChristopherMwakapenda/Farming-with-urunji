<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
start_app_session();

$message=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  $username=trim((string)($_POST['username']??''));
  $password=(string)($_POST['password']??'');
  $confirm=(string)($_POST['confirm']??'');
  if(strlen($username)<3)throw new RuntimeException('Username must be at least 3 characters.');
  if(strlen($password)<8)throw new RuntimeException('Password must be at least 8 characters.');
  if($password!==$confirm)throw new RuntimeException('Passwords do not match.');
  $pdo=db();
  $sql=file_get_contents(__DIR__.'/database/mufms.sql');
  $pdo->exec($sql);
  $s=$pdo->prepare("SELECT id FROM users WHERE username=? LIMIT 1");$s->execute([$username]);
  if($s->fetch())throw new RuntimeException('That username already exists.');
  $s=$pdo->prepare("INSERT INTO users(username,password_hash,role) VALUES(?,?,?)");
  $s->execute([$username,password_hash($password,PASSWORD_DEFAULT),'admin']);
  file_put_contents(__DIR__.'/installed.lock','Installed on '.date('c')."\n");
  $message='Installation completed. You can now sign in. Delete setup.php from htdocs for extra security.';
 }catch(Throwable $e){$error=$e->getMessage();}
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>FWU Setup</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="login-shell"><div class="login-card"><div class="brand big"><img src="assets/urunji-logo.png" onerror="this.style.display='none'"><div><b>Farming with Urunji</b><span>Farm Management System</span></div></div>
<h1>First-time setup</h1><p class="muted">This creates the MySQL tables and your first administrator account.</p>
<?php if($message):?><div class="alert success"><?=htmlspecialchars($message)?></div><a class="btn primary wide" href="index.php?page=login">Go to login</a><?php else:?>
<?php if($error):?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif;?>
<div class="security-note">Before continuing, make sure <code>config.php</code> contains the exact MySQL host, database name, username and password supplied by InfinityFree.</div>
<form method="post" class="form"><label>Admin username<input name="username" value="admin" required></label><label>Admin password<input type="password" name="password" minlength="8" required></label><label>Confirm password<input type="password" name="confirm" minlength="8" required></label><button class="btn primary wide">Install FWU</button></form>
<?php endif;?></div></div></body></html>
