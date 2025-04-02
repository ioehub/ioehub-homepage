홈페이지에서 **이메일을 보내는 문의 페이지(Contact Form)**를 구현하려면,

프론트엔드에서는 **HTML 폼**을 만들고, 백엔드 또는 외부 서비스로 **메일을 전송**해야 해요.

Apache2는 정적 서버라 백엔드가 없기 때문에,

**PHP 또는 외부 이메일 API (예: EmailJS, Formspree, Google Apps Script 등)**를 써야 합니다.

---

## ✅ 방법 1: PHP로 이메일 전송 (Apache2 + PHP 가능할 때)

### 🔹 1. `contact.html`

```html
html
복사편집
<form action="send_email.php" method="POST">
  <input type="text" name="name" placeholder="이름" required />
  <input type="email" name="email" placeholder="이메일" required />
  <textarea name="message" placeholder="문의 내용을 입력하세요" required></textarea>
  <button type="submit">보내기</button>
</form>

```

### 🔹 2. `send_email.php`

```php
php
복사편집
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $to = "your@email.com"; // 수신자 이메일
  $subject = "홈페이지 문의 도착";
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $message = htmlspecialchars($_POST['message']);

  $headers = "From: $email\\r\\n";
  $headers .= "Reply-To: $email\\r\\n";

  $body = "이름: $name\\n이메일: $email\\n\\n문의 내용:\\n$message";

  if (mail($to, $subject, $body, $headers)) {
    echo "이메일이 전송되었습니다.";
  } else {
    echo "이메일 전송에 실패했습니다.";
  }
}
?>

```

> 📌 send_email.php는 Apache2 서버에서 PHP가 작동하는 경우에만 사용 가능해요.

---

## ✅ 방법 2: 외부 서비스 (백엔드 없이)

### 🔹 [Formspree](https://formspree.io/)

1. 가입 후 이메일 주소 인증
2. 아래처럼 form 작성

```html
html
복사편집
<form action="<https://formspree.io/f/yourFormId>" method="POST">
  <input type="text" name="name" placeholder="이름" required>
  <input type="email" name="email" placeholder="이메일" required>
  <textarea name="message" placeholder="문의 내용" required></textarea>
  <button type="submit">보내기</button>
</form>

```

> 👉 Formspree는 무료 요금제로도 사용 가능하고, 따로 백엔드가 없어도 돼요!

---

## ✅ 방법 3: EmailJS (JavaScript로 메일 전송)

프론트엔드에서 직접 이메일을 보낼 수 있도록 해주는 서비스입니다.

> HTML + JS로 연동 가능 (무료 요금제 있음)

---

## 💡 추천: 어떤 걸 선택해야 할까?

|상황|추천 방법|
|---|---|
|PHP 가능한 Apache 서버|✅ `send_email.php` 방식 (자체 처리)|
|PHP 안됨, 정적 페이지만 사용|✅ Formspree / EmailJS 같은 외부 서비스|
|React, Next.js 사용 중|EmailJS, Nodemailer 등 JS 기반 추천|

---

