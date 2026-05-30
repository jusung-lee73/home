<?php
require_once __DIR__.'/config.php';

$eyebrow = get_setting('site_eyebrow', 'YOUTUBE PLAYER');
$title = get_setting('site_title', '교육 영상 재생관');
$subtitle = get_setting('site_subtitle', '아래 영상을 선택시 바로 재생됩니다.');
$max = max(1, (int)get_setting('max_videos', '6'));

$stmt = db()->prepare('SELECT * FROM yt_videos WHERE is_active=1 ORDER BY sort_order ASC, id DESC LIMIT '.$max);
$stmt->execute();
$videos = $stmt->fetchAll();

if (count($videos) === 1) {
    header('Location: play.php?code='.rawurlencode($videos[0]['slug']));
    exit;
}
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=h($title)?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="hero">
  <div>
    <p class="eyebrow"><?=h($eyebrow)?></p>
<h1><?=h($title)?></h1>
<p><?=h($subtitle)?></p>
    <p>아래 영상을 선택시 바로 재생됩니다.</p>
  </div>

  <a href="admin/login.php" class="admin-btn">
    ⚙ 관리자
  </a>
</header>

<main class="wrap grid">
  <?php if (!$videos): ?>
    <div class="empty">
      등록된 영상이 없습니다. 관리자 페이지에서 영상을 등록하세요.
    </div>
  <?php endif; ?>

  <?php foreach($videos as $v): ?>
    <a class="card" href="play.php?code=<?=rawurlencode($v['slug'])?>">
      <img src="https://img.youtube.com/vi/<?=h($v['youtube_id'])?>/hqdefault.jpg" alt="">
      <div class="card-body">
        <strong><?=h($v['title'])?></strong>
        <span>▶ 영상 바로 재생</span>
      </div>
    </a>
  <?php endforeach; ?>
</main>

</body>
</html>