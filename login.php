<!DOCTYPE html>
<html>
<head>
    <title>로그인</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">로그인</h2>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors','1');

			session_start();
			require 'db.php';

			// 로그인 처리
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if (isset($_POST['login'])) {
					$username = $_POST['username'];
					$password = $_POST['password'];

					// 사용자 인증 로직
					$stmt = $mysqli->prepare('SELECT * FROM account WHERE username = ?');
					$stmt->bind_param('s', $username);
					$stmt->execute();
					$result = $stmt->get_result();
					$account = $result->fetch_assoc();
					$stmt->close();

					if ($account && password_verify($password, $account['password'])) {
						$_SESSION['logged_in'] = true;
						echo 'alert("로그인 되었습니다.")';
						header('Location: index.php');
						exit();
					} else {
						echo '<div class="justify-content-center alert alert-danger mx-auto col-md-6">사용자명 또는 비밀번호가 올바르지 않습니다.</div>';
					}
				}
			}
		?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="username">사용자명:</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">비밀번호:</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
					<div class="form-group">
						<button type="submit" name="login" class="btn btn-primary form-control mt-3">로그인</button>
					</div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js" integrity="sha512-uaZ0UXmB7NHxAxQawA8Ow2wWjdsedpRu7nJRSoI2mjnwtY8V5YiCWavoIpo1AhWPMLiW5iEeavmA3JJ2+1idUg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>
</html>