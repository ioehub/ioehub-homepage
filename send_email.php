<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $to = "hk@ioehub.com"; // 수신자 이메일
  $subject = "IoEHub 문의 도착";
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $message = htmlspecialchars($_POST['message']);

  $headers = "From: $email\r\n";
  $headers .= "Reply-To: $email\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

  $body = "이름: $name\n이메일: $email\n\n문의 내용:\n$message";

  if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => '이메일이 전송되었습니다.']);
  } else {
    echo json_encode(['success' => false, 'message' => '이메일 전송에 실패했습니다.']);
  }
  exit;
}
?> 