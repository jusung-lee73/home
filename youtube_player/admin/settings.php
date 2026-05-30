<?php
require_once __DIR__.'/../config.php';
require_admin();

$csrf = csrf_token();
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    set_setting('site_eyebrow', trim((string)($_POST['site_eyebrow'] ?? 'YOUTUBE PLAYER')));
    set_setting('site_title', trim((string)($_POST['site_title'] ?? '교육 영상 재생관')));
    set_setting('site_subtitle', trim((string)($_POST['site_subtitle'] ?? '아래 영상을 선택시 바로 재생됩니다.')));

    set_setting('max_videos', (string)max(1, (int)($_POST['max_videos'] ?? 6)));
    set_setting('autoplay', isset($_POST['autoplay']) ? '1' : '0');
    set_setting('mute', isset($_POST['mute']) ? '1' : '0');
    set_setting('show_list', isset($_POST['show_list']) ? '1' : '0');

    $saved = true;
}
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>환경설정</title>
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>

<nav>
  <strong>환경설정</strong>
  <span>
    <a href="index.php">영상관리</a>
    <a href="../index.php" target="_blank">재생관 보기</a>
    <a href="logout.php">로그아웃</a>
  </span>
</nav>

<main class="container">
  <section class="panel">
    <h2>재생 페이지 설정</h2>

    <?php if ($saved): ?>
      <p class="ok">저장되었습니다.</p>
    <?php endif; ?>

    <form method="post" class="form">
      <input type="hidden" name="csrf" value="<?=h($csrf)?>">

      <label>
        상단 작은 제목
        <input
          name="site_eyebrow"
          value="<?=h(get_setting('site_eyebrow', 'YOUTUBE PLAYER'))?>"
          placeholder="예: YOUTUBE PLAYER">
      </label>

      <label>
        메인 타이틀
        <input
          name="site_title"
          value="<?=h(get_setting('site_title', '교육 영상 재생관'))?>"
          placeholder="예: 교육 영상 재생관">
      </label>

      <label>
        서브타이틀
        <input
          name="site_subtitle"
          value="<?=h(get_setting('site_subtitle', '아래 영상을 선택시 바로 재생됩니다.'))?>"
          placeholder="예: 아래 영상을 선택시 바로 재생됩니다.">
      </label>

      <label>
        목록 표시 영상 수
        <input
          type="number"
          name="max_videos"
          min="1"
          value="<?=h(get_setting('max_videos', '6'))?>">
      </label>

      <label class="check">
        <input type="checkbox" name="autoplay" <?=get_setting('autoplay','1') === '1' ? 'checked' : ''?>>
        자동재생 시도
      </label>

      <label class="check">
        <input type="checkbox" name="mute" <?=get_setting('mute','0') === '1' ? 'checked' : ''?>>
        음소거 자동재생
      </label>

      <label class="check">
        <input type="checkbox" name="show_list" <?=get_setting('show_list','1') === '1' ? 'checked' : ''?>>
        목록 사용
      </label>

      <button type="submit">저장</button>
    </form>
  </section>
</main>

</body>
</html>