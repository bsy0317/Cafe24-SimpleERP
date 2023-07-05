<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

session_start();
require 'db.php';

// 로그인 상태 확인
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // 로그인되지 않은 상태이므로 로그인 페이지로 리다이렉트
    header('Location: login.php');
    exit();
}

// 공급사 등록 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $fax = $_POST['fax'];
        $fax_order = isset($_POST['fax_order']) ? 1 : 0;
        $memo = $_POST['memo'];

        // 입력 값 유효성 검사 (예시: 이름 필드는 필수 입력 사항으로 가정)
        if (empty($name)) {
			echo '<script>alert("이름은 필수 입력 사항입니다.");</script>';
			exit();
        }if (empty($email)) {
			echo '<script>alert("이메일은 필수 입력 사항입니다.");</script>';
            exit();
        }

        // 데이터베이스에 공급사 정보 등록
        $stmt = $mysqli->prepare('INSERT INTO suppliers (name, phone, email, fax, fax_order, memo) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssis', $name, $phone, $email, $fax, $fax_order, $memo);
        $stmt->execute();
        $stmt->close();

        header('Location: index.php');
        exit();
    }
}
$search = isset($_POST['search']) ? $_POST['search'] : '';
$searchQuery = $search ? "WHERE name LIKE '%$search%'" : '';

// 전체 공급사 수 조회
$countResult = $mysqli->query("SELECT COUNT(*) AS count FROM suppliers $searchQuery");
$totalCount = $countResult->fetch_assoc()['count'];
$countResult->close();

// 페이징 설정
$results_per_page = 10;
$total_pages = ceil($totalCount / $results_per_page);
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages));
$offset = ($current_page - 1) * $results_per_page;

// 공급사 목록 조회
$query = "SELECT * FROM suppliers $searchQuery ORDER BY id ASC LIMIT ?, ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $offset, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();
$suppliers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>공급사 관리</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,700,0,200" />
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
		.demo-up {
		  display: block;
		  position: relative;
		  color: #2ECC71;
		}
		.demo-slow {
		  display: block;
		  position: relative;
		  color: #F1C40F;
		}
		.demo-down {
		  display: block;
		  position: relative;
		  color: #E74C3C;
		}
		.demos {
		  display: block;
		}
		.demos div span:nth-child(2) {
		  margin-left: 30px;
		  line-height: 35px;
		}
		@media (max-width: 768px) {
			/* 네비게이션 버튼 정렬 */
			.row-cols-lg-auto {
				justify-content: center;
			}
			
			/* 테이블 글꼴 크기 조정 */
			table.table td, table.table th {
				font-size: 12px;
			}
		}
		/** Server status css **/
		.server-status {
		  left: 10px;
		  top: 50%;
		  margin-left: 0px;
		  margin-top: -5px;
		  position: absolute;
		  vertical-align: middle;
		  width: 10px;
		  height: 10px;
		  border-radius: 50%;
		}
		.server-status::before,
		.server-status::after {
		  left: 0;
		  top: 50%;
		  margin-left: -1px;
		  margin-top: -6px;
		  position: absolute;
		  vertical-align: middle;
		  width: 12px;
		  height: 12px;
		  border-radius: 50%;
		}
		.server-status[type="up"],
		.server-status[type="up"]::before,
		.server-status[type="up"]::after {
		  background: #2ECC71;
		}
		.server-status[type="down"],
		.server-status[type="down"]::before,
		.server-status[type="down"]::after {
		  background: #E74C3C;
		}
		.server-status[type="slow"],
		.server-status[type="slow"]::before,
		.server-status[type="slow"]::after {
		  background: #F1C40F;
		}

		.server-status::before {
		  content: "";
		  animation: bounce 1.5s infinite;
		}
		.server-status::after {
		  content: "";
		  animation: bounce 1.5s -0.4s infinite;
		}

		@keyframes bounce {
		  0% {
			transform: scale(1);
			-webkit-transform: scale(1);
			opacity: 1;
		  }
		  100% {
			transform: scale(2);
			-webkit-transform: scale(2);
			opacity: 0;
		  }
		}

		@-webkit-keyframes bounce {
		  0% {
			transform: scale(1);
			-webkit-transform: scale(1);
			opacity: 1;
		  }
		  100% {
			transform: scale(2);
			-webkit-transform: scale(2);
			opacity: 0;
		  }
		}
    </style>
</head>
<body>
    <!-- 공급사 등록 팝업 -->
    <div class="modal fade" id="registerPopup" tabindex="-1" aria-labelledby="registerPopup" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">공급사 등록</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="alert alert-primary d-flex align-items-center alert-dismissible fade show" role="alert">
						<div><p class='mb-0' style='font-size:15px;'>카페24에서 등록한 공급사 이름과 <strong>반드시</strong> 일치해야 합니다.</p></div>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
					<form method="post" action="index.php">
						<div class="form-floating">
							<input type="text" class="form-control" name="name" id="name" required>
							<label for="floatingInputValue">이름</label>
						</div>
						<div class="form-floating mt-3">
							<input type="text" class="form-control" name="phone" id="phone">
							<label for="floatingInputValue">전화번호</label>
						</div>
						<div class="form-floating mt-3">
							<input type="email" class="form-control" name="email" id="email" required>
							<label for="floatingInputValue">Email</label>
						</div>
						<div class="form-floating mt-3">
							<input type="text" class="form-control" name="fax" id="fax">
							<label for="floatingInputValue">Fax 번호</label>
						</div>
						<div class="form-check form-switch mt-3">
							<input type="checkbox" class="form-check-input" name="fax_order" role="switch" id="fax_order">
							<label class="form-check-label" for="fax_order">Fax 발주 여부</label>
						</div>
						<div class="form-floating mt-3">
							<textarea class="form-control" name="memo" id="memo" rows="4" cols="50"></textarea>
							<label for="floatingTextarea">메모</label>
						</div>
						</div>
						<div class="modal-footer">
							<button type="submit" name="submit" class="btn btn-primary">등록</button>
							<button type="button" data-bs-dismiss="modal" class="btn btn-secondary">닫기</button>
						</div>
					</form>
				</div>
        </div>
    </div>
	<div class="modal fade" id="searchPopup" tabindex="-1" aria-labelledby="searchPopup" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">공급사 검색</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="index.php">
						<div class="form-group">
							<label for="name">공급사 이름:</label>
							<input type="text" class="form-control" name="search" id="search" required>
						</div>
						</div>
						<div class="modal-footer">
							<button type="submit" name="search_submit" class="btn btn-primary">검색</button>
							<button type="button" data-bs-dismiss="modal" class="btn btn-secondary">닫기</button>
						</div>
					</form>
				</div>
        </div>
    </div>
	
	<div class="container-xl">
		<div class="row mx-auto">
			<div class="row row-cols-lg-auto g-3 align-items-center">
				<span class="material-symbols-outlined" style="margin-top:5px;">library_add</span>
				<h2>공급사 목록</h2>
				<button data-bs-toggle="modal" data-bs-target="#registerPopup" class="btn btn-outline-primary col-md-2 mt-2">공급사 등록</button>
				<button data-bs-toggle="modal" data-bs-target="#searchPopup" class="btn btn-outline-success col-md-2 ms-2 mt-2">공급사 검색</button>
				<a href="list.php" class="btn btn-outline-dark col-md-2 ms-2 mt-2">발주기록</a>
				<div style="margin-left: auto">
					<div class="demos" id="service_status">
					</div>
				</div>
			</div>
			<hr class='mt-3' style="margin-bottom:30px;">
			<div class="h-100 d-flex align-items-center justify-content-center px-0">
			<table class="table table-hover table-bordered text-center">
				<thead class="thead-light">
					<tr>
						<th>ID</th>
						<th>이름</th>
						<th>전화번호</th>
						<th>Email</th>
						<th>Fax 번호</th>
						<th>Fax or B2B</th>
						<th>메모</th>
						<th>작업</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($suppliers as $supplier): ?>
						<tr>
							<td><?= $supplier['id'] ?></td>
							<td><?= $supplier['name'] ?></td>
							<td><?= $supplier['phone'] == "" ? '<p>-</p>':$supplier['phone'] ?></td>
							<td><?= $supplier['email'] == "" ? '<p>-</p>':$supplier['email'] ?></td>
							<td><?= $supplier['fax'] == "" ? '<p>-</p>':$supplier['fax'] ?></td>
							<td><?= $supplier['fax_order'] ? '<span class="badge text-bg-warning" style="font-size:15px">예</span>' : '<span class="badge text-bg-success" style="font-size:15px">아니오</span>' ?></td>
							
							<?php
								$memo = $supplier['memo'];
								if (strlen($memo) > 30) {
									$memo = substr($memo, 0, 30) . '...';
								}
							?>
							<td><?= $memo ?></td>
							<td>
								<a href="#" class="btn btn-primary edit-link" data-id="<?= $supplier['id'] ?>">
									<span class="material-symbols-outlined" style="font-size:11px; margin-top:5px;">stylus</span>
									수정
								</a>
								<a href="delete.php?id=<?= $supplier['id'] ?>" class="btn btn-danger">
									<span class="material-symbols-outlined" style="font-size:11px; margin-top:5px;">delete</span>
									삭제
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			</div>
			<nav aria-label="페이지 네비게이션">
				<ul class="pagination justify-content-center">
					<?php if ($total_pages > 1) { ?>
						<?php if ($current_page > 1) { ?>
							<li class="page-item"><a class="page-link" href="index.php?page=<?php echo $current_page - 1; ?>&search=<?php echo $search; ?>">이전</a></li>
						<?php } ?>
						<?php for ($i = max(1, $current_page - 2); $i <= min($current_page + 2, $total_pages); $i++) { ?>
							<li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>"><a class="page-link" href="index.php?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a></li>
						<?php } ?>
						<?php if ($current_page < $total_pages) { ?>
							<li class="page-item"><a class="page-link" href="index.php?page=<?php echo $current_page + 1; ?>&search=<?php echo $search; ?>">다음</a></li>
						<?php } ?>
					<?php } ?>
				</ul>
			</nav>
		</div>
	</div>

    <script src="https://code.jquery.com/jquery-3.7.0.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js" integrity="sha512-uaZ0UXmB7NHxAxQawA8Ow2wWjdsedpRu7nJRSoI2mjnwtY8V5YiCWavoIpo1AhWPMLiW5iEeavmA3JJ2+1idUg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
	<script>
		var requestTimeout = 2000; // 2초
		var url = "https://oracle.krr.kr:3388/check";
        $(document).ready(function() {
            // 수정 링크 클릭 시 팝업 창 열기
            $(".edit-link").click(function(e) {
                e.preventDefault();
                var supplierId = $(this).data('id');
                var url = 'edit.php?id=' + supplierId;
                window.open(url, '공급사 수정', 'width=600,height=600');
            });
			$.ajax({
				url: url,
				method: 'GET',
				timeout: requestTimeout,
				success: function(data) {
					// 서비스 이용 가능한 경우
					if(data.message.search("Service Available") != -1){
						$('#service_status').append('<div class="demo-up"><span class="server-status" type="up"></span><span>발주 서버 접속가능</span></div>');
					}else{
						$('#service_status').append('<div class="demo-down"><span class="server-status" type="down"></span><span>발주 서버 오류</span></div>');
					}
				},
				error: function(xhr, status, error) {
					if (status === 'timeout') {
						$('#service_status').append('<div class="demo-down"><span class="server-status" type="down"></span><span>발주 서버 응답없음</span></div>');
					} else {
						$('#service_status').append('<div class="demo-down"><span class="server-status" type="down"></span><span>발주 서버 오류</span></div>');
					}
				}
			});
		});
    </script>
</body>
</html>
