<?php
// DB 연결 및 세션 시작
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require 'db.php';

// 로그인 상태 확인
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // 로그인되지 않은 상태이므로 로그인 페이지로 리다이렉트
    header('Location: login.php');
    exit();
}

// 클릭한 항목의 고유번호를 받아옴
$num = isset($_GET['num']) ? $_GET['num'] : null;

// 고유번호에 해당하는 데이터 조회
$query = "SELECT * FROM history WHERE Num = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $num);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>주문 상세 정보</title>
    <!-- Bootstrap CDN 추가 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        h2{
			font-size: 1.6rem;
		}
		h3{
			margin-top: 30px;
			font-size: 1.3rem;
		}
		table {
			width: 100%;
			border: 1px solid #444444;
		}
		th, td {
			border: 1px solid #b1b1b1;
		}
		@font-face {
			font-family: 'NanumSquareNeo-Variable';
			src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/noonfonts_11-01@1.0/NanumSquareNeo-Variable.woff2') format('woff2');
			font-weight: normal;
			font-style: normal;
		}
		* {
			font-family: 'NanumSquareNeo-Variable';
		}
    </style>
</head>
<body>
    <div class="container mt-3 mb-5">
		<div class="text-center">
			<h2 class="mb-3 d-inline-block">주문 상세 정보</h2>
			<button onclick="window.close()" href="#" class="btn btn-outline-danger ms-auto" style="margin-top:-10px">닫기</button>
		</div>
        <!-- 상세 정보 카드 -->
		<div class="card mt-3">
			<div class="card-header">주문정보</div>
			<div class="card-body">
				<div class="card-text">
					<p class="card-text">공급사: <?php echo $order['Suppliers']; ?>
					<br>주문번호: <?php echo $order['OrderNumber']; ?>
					<br>수령자 이름: <?php echo $order['recvName']; ?>
					<br>전송 시간: <?php echo $order['DateTime']; ?></p>
				</div>
			</div>
		</div>
		<div class="card mt-3">
			<div class="card-header">발주전문</div>
			<div class="card-body">
                <div class="card-text">
                    <?php echo $order['html']; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
