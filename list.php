<?php
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

// 검색어를 받아서 쿼리에 추가
$search = isset($_POST['search']) ? $_POST['search'] : '';
$searchQuery = $search ? "WHERE Suppliers LIKE '%$search%' OR OrderNumber LIKE '%$search%' OR recvName LIKE '%$search%' OR title LIKE '%$search%'" : '';

// 전체 행 수 조회
$countResult = $mysqli->query("SELECT COUNT(*) AS count FROM history $searchQuery");
$totalCount = $countResult->fetch_assoc()['count'];
$countResult->close();

// 페이징 설정
$results_per_page = 10;
$total_pages = ceil($totalCount / $results_per_page);
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages));
$offset = ($current_page - 1) * $results_per_page;

// 데이터베이스에서 데이터 조회
$query = "SELECT * FROM history $searchQuery ORDER BY Num DESC LIMIT ?, ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $offset, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>발주 내역</title>
    <!-- Bootstrap CDN 추가 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
	<style>
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
    <div class="container-xl mt-5">
		<div><a href="index.php" class="text-decoration-none" style="display:inline-flex;">
			<span class="material-symbols-outlined">arrow_back</span>
			<p class="d-inline-block ms-auto" style="font-size:20px;">뒤로가기</p>
		</a></div>
        <h1 class="mb-3">발주 내역</h1>

        <!-- 검색 기능 추가 -->
        <form method="POST" action="" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="검색어 입력" value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">검색</button>
            </div>
        </form>

        <!-- 주문 내역 테이블 -->
        <table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>공급사</th>
					<th>주문번호</th>
					<th>수령자 이름</th>
					<th>메일 제목</th>
					<th>전송 시간</th>
					<th>자세히보기</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($orders as $order) : ?>
					<tr onclick="window.open('detail.php?num=<?php echo $order['Num']; ?>', '_blank');">
						<td><?php echo $order['Num']; ?></td>
						<td><?php echo $order['Suppliers']; ?></td>
						<td><?php echo $order['OrderNumber']; ?></td>
						<td><?php echo $order['recvName']; ?></td>
						<td><?php echo $order['title']; ?></td>
						<td><?php echo $order['DateTime']; ?></td>
						<td><span class="badge text-bg-success">상세정보확인</span></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

        <!-- 페이징 -->
        <nav style="display: table; margin-left: auto; margin-right: auto;">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS CDN 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
