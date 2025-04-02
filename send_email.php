<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $to = "hk@ioehub.com"; // 수신자 이메일
    $subject = "IoEHub 문의 도착";
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // 입력값 검증
    if (empty($name) || empty($email) || empty($message)) {
      throw new Exception("모든 필드를 입력해주세요.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception("유효한 이메일 주소를 입력해주세요.");
    }

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "이름: $name\n이메일: $email\n\n문의 내용:\n$message";

    // PHP mail() 함수 실행
    $mailResult = mail($to, $subject, $body, $headers);

    if ($mailResult) {
      echo json_encode([
        'success' => true,
        'message' => '이메일이 전송되었습니다.'
      ]);
    } else {
      // mail() 함수 실패 시 에러 정보 수집
      $error = error_get_last();
      throw new Exception("이메일 전송 실패: " . ($error ? $error['message'] : '알 수 없는 오류'));
    }
  } catch (Exception $e) {
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage(),
      'error' => [
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
      ]
    ]);
  }
} else {
  echo json_encode([
    'success' => false,
    'message' => '잘못된 요청 방식입니다.'
  ]);
}
?> 