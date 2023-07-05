<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

require 'db.php';

// GET 요청으로 전달된 공급사 ID 가져오기
$id = $_GET['id'];

// 데이터베이스에서 공급사 삭제
$stmt = $mysqli->prepare('DELETE FROM suppliers WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

echo '<script>alert("삭제되었습니다!");</script>';
header('Location: index.php');
exit();
?>
