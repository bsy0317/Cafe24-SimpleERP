<?php
$host = 'your_host';
$db = 'your_database';
$user = 'your_id';
$password = 'your_password';

// MySQL에 접속
$mysqli = new mysqli($host, $user, $password, $db);

// 접속 오류 처리
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// 문자셋 설정
$mysqli->set_charset('utf8');
?>

	<?php
		if(http_response_code() === 500){
			echo '<script>alert("서버에서 오류가 발생했습니다. 다시 시도해주세요.");</script>';
		}
    ?>

<!--
			-서비스 중대 약관-
1. 해당 서비스는 영리 목적으로 사용하실 수 없습니다.
2. 단 영리 목적으로 사용을 원할 경우, 별도의 라이선스 등록 과정이 필요합니다.
3. 소스코드 재 수정 및 재 배포를 금지합니다.
4. 이 서비스는 CC BY-NC-ND 4.0을 따릅니다.
-->
