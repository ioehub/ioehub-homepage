<?php
// 에러 보고 활성화
error_reporting(E_ALL);
ini_set('display_errors', 0); // 브라우저에 에러 표시하지 않음
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// JSON 헤더 설정
header('Content-Type: application/json; charset=utf-8');

// Composer autoload 파일 존재 확인
if (!file_exists('vendor/autoload.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'Composer autoload 파일을 찾을 수 없습니다. composer install을 실행해주세요.',
        'error' => [
            'file' => __FILE__,
            'line' => __LINE__
        ]
    ]);
    exit;
}

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// 환경 변수 파일 존재 확인
if (!file_exists('.env')) {
    echo json_encode([
        'success' => false,
        'message' => '.env 파일을 찾을 수 없습니다.',
        'error' => [
            'file' => __FILE__,
            'line' => __LINE__
        ]
    ]);
    exit;
}

try {
    // 환경 변수 로드
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '환경 변수 로드 실패: ' . $e->getMessage(),
        'error' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
    exit;
}

// SMTP 설정 디버깅
$smtp_debug = [
    'host' => $_ENV['SMTP_HOST'] ?? 'not set',
    'port' => $_ENV['SMTP_PORT'] ?? 'not set',
    'user' => isset($_ENV['SMTP_USER']) ? (strlen($_ENV['SMTP_USER']) > 0 ? "set (" . substr($_ENV['SMTP_USER'], 0, 3) . "...)" : "empty") : "not set",
    'pass' => isset($_ENV['SMTP_PASS']) ? (strlen($_ENV['SMTP_PASS']) > 0 ? "set (length: " . strlen($_ENV['SMTP_PASS']) . ")" : "empty") : "not set"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 입력값 검증
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
            throw new Exception("모든 필드를 입력해주세요.");
        }

        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("유효한 이메일 주소를 입력해주세요.");
        }

        $mail = new PHPMailer(true);

        // SMTP 디버그 모드 활성화
        $mail->SMTPDebug = 2; // 디버깅 정보 출력
        $mail->Debugoutput = function($str, $level) {
            file_put_contents('smtp_debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND);
        };

        // SMTP 설정
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 30; // 타임아웃 30초로 설정

        // 발신자 설정
        $mail->setFrom($email, $name);
        $mail->addReplyTo($email, $name);
        
        // 수신자 설정
        $mail->addAddress('hk@ioehub.com', 'IoEHub');

        // 이메일 내용
        $mail->isHTML(false);
        $mail->Subject = 'IoEHub 문의 도착';
        $mail->Body = "이름: $name\n이메일: $email\n\n문의 내용:\n$message";

        // 이메일 전송
        if ($mail->send()) {
            echo json_encode([
                'success' => true,
                'message' => '이메일이 성공적으로 전송되었습니다.'
            ]);
        } else {
            throw new Exception("이메일 전송 실패: " . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error' => [
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'smtp_settings' => $smtp_debug
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
