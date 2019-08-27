<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) { //セッション　60分
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: login.php');
  exit();
}

if (!empty($_POST)) {
  if ($_POST['message'] !== '' || $_FILES['post_image']['name'] !== '') {

    if ($_POST['reply_post_id'] === '') {
      $replyPostId = 0;
    } else {
      $replyPostId = $_POST['reply_post_id'];
    }

    if ($_FILES['post_image']['name'] !== '') {
      $fimage = date('YmdHis') . $_FILES['post_image']['name'];
      move_uploaded_file($_FILES['post_image']['tmp_name'],'post_image/' . $fimage);
      $_SESSION['join']['post_image'] = $fimage;
    } else {
      $fimage = '';
    }

    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, post_image=?,created=NOW()');
    $message->execute(array(
    $member['id'],
    $_POST['message'],
    $replyPostId,
    $fimage
    ));

    header('Location: index.php'); //再読み込み重複防止処理
    exit();
  }
}

$page = $_REQUEST['page'];
if ($page == '') {
  $page = 1;
}
$page = max($page, 1);

$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5; //表示数

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


if (isset($_REQUEST['res'])) {
  //返信の処理
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
  $response->execute(array($_REQUEST['res']));

  $table = $response->fetch();
  $message = '@' . $table['name'] . ' ' . $table['message'].'>';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>掲示板</title>
<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="header">
    <p>掲示板</p>
      <!-- <a href="login.php"><button type="button" name="name" value="value">ログイン</button></a> -->
      <div id="login_user">
        <img src="member_picture/<?php print(htmlspecialchars($member['picture']. "", ENT_QUOTES)); ?>" class="icon" width="50" height="50" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" style="border-radius:50% ;"/>
        <p><?php print(htmlspecialchars($member['name']. "", ENT_QUOTES)); ?></p>
        <a href="logout.php"><button class="btn btn btn-mod btn-border btn-circle btn-small" type="button" name="name" value="value">ログアウト</button></a>
      </div>
  </div>
  <div id="content">
    <form action="" id="form" method="post" enctype="multipart/form-data">
      <h2>伝える</h2>
      <label>
        <span class="filelabel btn btn btn-mod btn-border btn-circle btn-small" title="ファイルを選択">写真を選択</span>
        <input type="file" class="file" id="filesend" name="post_image">
      </label>
      <div class="view_box"></div>
      <div id="send">
        <textarea id="targetbox" name="message" cols="36" rows="5" wrap="hard" placeholder="コメントを入力してください" style="white-space: pre-line;"><?php print (htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
        <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>" />
        <p><input class="btn btn-mod btn-border btn-circle btn-small" type="submit" value="投稿" /></p>
      </div>
    </form>
  </div>

  <div class="column">
    <?php foreach ($posts as $post): ?>
      <div class="msg">
        <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" class="icon" width="50" height="50" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" style="border-radius:50% ;"/>

        <p><span class="name"><?php print(htmlspecialchars($post['name'],ENT_QUOTES)); ?></span></p>

        <p class="day"><a href="view.php?id=<?php print(htmlspecialchars($post['id'])); ?>"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>

        <?php if ($post['reply_message_id'] > 0): ?>
          <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'])); ?>">返信元のメッセージ</a>
        <?php endif; ?>

        <?php if ($_SESSION['id'] == $post['member_id']): ?>
          [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'])); ?>" style="color: #F33;">削除</a>]
        <?php endif; ?>
        </p>
        <?php if ($post['post_image'] != '' ): ?>
        <img src="post_image/<?php print(htmlspecialchars($post['post_image'], ENT_QUOTES)); ?>" height="250"  alt="" id="post_image" />
          <?php endif; ?>
        <p id="post_message"><?php print nl2br(htmlspecialchars($post['message'], ENT_QUOTES)); ?></p>
        <a id="re" href="index.php?res=<?php print (htmlspecialchars($post['id'],ENT_QUOTES)); ?>">この投稿に返信</a>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="footer">
    <ul class="paging">
      <?php if($page > 1): ?>
        <li><a class="btn btn-mod btn-border btn-circle btn-small" href="index.php?page=<?php print($page-1); ?>">前のページへ</a></li>
      <?php else: ?>
        <li>前のページへ</li>
      <?php endif; ?>

      <?php if ($page < $maxPage): ?>
        <li><a class="btn btn-mod btn-border btn-circle btn-small" href="index.php?page=<?php print($page+1); ?>">次のページへ</a></li>
      <?php else: ?>
        <li>次のページへ</li>
      <?php endif; ?>
    </ul>
    <small>&copy; 2019 Shunsuke Tanizaki.</small>
  </div>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
