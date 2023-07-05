<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 공급사 정보 업데이트
    if (isset($_POST['submit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $fax = $_POST['fax'];
        $fax_order = isset($_POST['fax_order']) ? 1 : 0;
        $memo = $_POST['memo'];

        // 데이터베이스에 공급사 정보 업데이트
        $stmt = $mysqli->prepare('UPDATE suppliers SET name = ?, phone = ?, email = ?, fax = ?, fax_order = ?, memo = ? WHERE id = ?');
        $stmt->bind_param('ssssisi', $name, $phone, $email, $fax, $fax_order, $memo, $id);
        $stmt->execute();
        $stmt->close();

        echo '<script>alert("수정완료!"); opener.location.reload(); window.close();</script>';
        exit();
    }
}

// GET 요청으로 전달된 공급사 ID 가져오기
$id = $_GET['id'];


// 공급사 정보 가져오기
$stmt = $mysqli->prepare('SELECT * FROM suppliers WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();
$stmt->close();

if (!$supplier) {
    die('공급사를 찾을 수 없습니다.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>공급사 정보 수정</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<style>
		.input-group{
			width: 100%;
		}
		.input-group-text{
			width: 25%;
			text-align: center;
			display: table;
			margin-left: auto;
			margin-right: auto; 
		}
		.form-control {
			width: 55%;
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
	<div class="container">
		<h2 class="text-center mb-4 mt-4">공급사 수정</h2>
		<form method="POST" action="edit.php?id=<?= $id ?>" class="form-inline">
			<input type="hidden" name="id" value="<?= $id ?>"></input>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">공급사 이름</span>
						<input type="text" class="form-control" id="name" name="name" value="<?php echo $supplier['name']; ?>">
					</div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">전화번호</span>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $supplier['phone']; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Email</span>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $supplier['email']; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Fax 번호</span>
                        <input type="text" class="form-control" id="fax" name="fax" value="<?php echo $supplier['fax']; ?>">
                    </div>
                    <div class="input-group mb-3">
						<div class="input-group-text">
							<input class="form-check-input" type="checkbox" class="form-control" id="fax_order" name="fax_order" <?php echo $supplier['fax_order'] ? 'checked' : ''; ?>>
						</div>
						<label class="form-control bg-light">Fax 또는 B2B 발주라면 체크</label>
                    </div>
                    <div class="input-group">
						<span class="input-group-text">메모</span>
                        <textarea class="form-control" id="memo" name="memo"><?php echo $supplier['memo']; ?></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary mt-3" style="width:100%">수정</button>
		</form>
	</div>
</body>
</html>
