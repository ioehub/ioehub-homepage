<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // 서버 설정
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your.email@gmail.com'; // Gmail 주소
    $mail->Password   = '앱 비밀번호'; // 앱 비밀번호 (2차 인증 사용 시)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // 받는 사람
    $mail->setFrom('your.email@gmail.com', 'IoEHub Contact');
    $mail->addAddress('받을주소@email.com');

    // 내용
    $mail->isHTML(false);
    $mail->Subject = 'IoEHub 문의 도착';
    $mail->Body    = "이름: " . $_POST['name'] . "\n"
                   . "이메일: " . $_POST['email'] . "\n\n"
                   . "메시지:\n" . $_POST['message'];

    $mail->send();
    echo '이메일이 성공적으로 전송되었습니다!';
} catch (Exception $e) {
    echo "이메일 전송에 실패했습니다. 에러: {$mail->ErrorInfo}";
}
?>
