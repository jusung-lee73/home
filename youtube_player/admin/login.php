<?php
require_once __DIR__.'/../config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$err='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (hash_equals(ADMIN_PASSWORD, (string)($_POST['password'] ?? ''))) {
        $_SESSION['yt_admin']=true; header('Location: index.php'); exit;
    }
    $err='비밀번호가 올바르지 않습니다.';
}
?>
<!doctype html><html lang="ko"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>관리자 로그인</title><link rel="stylesheet" href="../assets/admin.css"></head><body><main class="login"><form method="post"><h1>관리자 로그인</h1><?php if($err):?><p class="err"><?=h($err)?></p><?php endif;?><input type="password" name="password" placeholder="관리자 비밀번호" autofocus><button>로그인</button></form></main></body></html>
