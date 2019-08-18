<?php
session_start();
require('../dbconnect.php');

if (!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}
if (!empty($_POST)) {
  $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW()');
  $statement->execute(array(
    $_SESSION['join']['name'],
    $_SESSION['join']['email'],
    sha1($_SESSION['join']['password']),
    $_SESSION['join']['image']
  ));
  unset($_SESSION['join']); //セッション変数を空にする

  header('Location: thanks.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>会員登録</title>

  <link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
  <div id="header">
   <p>会員登録</p>
  </div>

<div id="contents">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
  <input type="hidden" name="action" value="submit" />
  <dl>
  <dt>
    <?php if ($_SESSION['join']['image'] !== ''): ?>
    <img src="../member_picture/<?php print(htmlspecialchars ($_SESSION['join']['image'], ENT_QUOTES)); ?> " alt="" class="icon" width="100" height="100" style="border-radius:50% ;">
    <?php endif; ?></dt>
    <dd>
    </dd>
    <dt>お名前</dt>
    <dd>
    <?php print (htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES)); ?>
    </dd>
    <dt>メールアドレス</dt>
    <dd>
    <?php print (htmlspecialchars($_SESSION['join']['email'],ENT_QUOTES)); ?>
    </dd>
    <dt>パスワード</dt>
    <dd>
      ****
    </dd>
  </dl>
  <a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>
  <div class="confirmation">
    <input class="btn btn-mod btn-border btn-circle btn-small" type="submit" value="登録する" />
  </div>
</form>
</div>

</div>
</body>
</html>
