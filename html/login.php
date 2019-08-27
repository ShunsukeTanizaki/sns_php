<?php
session_start();
require('dbconnect.php');

if ($_COOKIE['email'] !== '') {
  $email = $_COOKIE['email'];
}

if (!empty($_POST)) {
  $email = $_POST['email'];
  
  if ($_POST['email'] !== '' && $_POST['password'] !== '') {
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    $login->execute(array(
      $_POST['email'],
      sha1($_POST['password'])
    ));
    $member = $login->fetch();

    if ($member) {
      $_SESSION['id']   = $member['id'];
      $_SESSION['time'] = time();

      if ($_POST['save'] === 'on') {
        setcookie('email', $_POST['email'],time()+60*60*24*14); //14日間
      }

      header('Location: index.php');
      exit();
    } else {
      $error['login'] = 'feiled';
    }
  } else {
    $error['login'] = 'blank';
  }
}
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>ログイン</title>
</head>

<body>
<div id="wrap">
  <div id="header">
    <p>ようこそ！</p>
  </div>
  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" maxlength="255" placeholder="email" value="<?php print (htmlspecialchars($email, ENT_QUOTES)); ?>" />
          <?php if ($error['login'] === 'blank'): ?>
            <p class="error">メールアドレスとパスワードを入力してください</p>
          <?php endif; ?> 
          <?php if ($error['login'] === 'feiled'): ?>
            <p class="error">ログインに失敗しました</p>
          <?php endif; ?>  
        </dd>
        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35" maxlength="255" placeholder="password" value="<?php print (htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input type="submit" class="btn btn-mod btn-border btn-circle btn-small" value="ログイン" />
      </div>
      <div class="admissionProcedure">
      <p>入会手続きがまだの方はこちらからどうぞ。</p>
      <p><a href="join/" class="btn btn-mod btn-border btn-circle btn-small">入会手続きをする</a></p>
      </div>
    </form>
  </div>
  <div id="foot">
    <!-- <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p> -->
  </div>
</div>
</body>
</html>
