<?php
session_start();

$_SESSION = array(); //空の配列で上書き
if (ini_get('session_use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name() . '', time() - 42000, 
    $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();//セッション削除

setcookie('email', '', time()-3600);

header('Location: login.php');
exit();

?>