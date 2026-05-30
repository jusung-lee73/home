<?php
require_once __DIR__.'/../config.php'; require_admin(); verify_csrf();
$id=(int)($_POST['id']??0); $title=trim((string)($_POST['title']??'')); $url=trim((string)($_POST['youtube_url']??'')); $ytid=youtube_id_from_url($url); $slug=trim((string)($_POST['slug']??''));
$slug=preg_replace('~[^A-Za-z0-9_-]+~','-',$slug); $desc=trim((string)($_POST['description']??'')); $sort=(int)($_POST['sort_order']??0); $active=isset($_POST['is_active'])?1:0;
if($title===''||$url===''||$ytid===''||$slug==='') exit('필수값 또는 유튜브 링크가 올바르지 않습니다.');
if($id>0){$stmt=db()->prepare('UPDATE yt_videos SET title=?, youtube_url=?, youtube_id=?, slug=?, description=?, sort_order=?, is_active=? WHERE id=?');$stmt->execute([$title,$url,$ytid,$slug,$desc,$sort,$active,$id]);}
else{$stmt=db()->prepare('INSERT INTO yt_videos(title,youtube_url,youtube_id,slug,description,sort_order,is_active) VALUES(?,?,?,?,?,?,?)');$stmt->execute([$title,$url,$ytid,$slug,$desc,$sort,$active]);}
header('Location: index.php');
