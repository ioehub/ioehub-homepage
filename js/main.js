// AOS 초기화
AOS.init({
  duration: 1000,
  once: true
});

// 테마 토글 함수
function toggleTheme() {
  const html = document.documentElement;
  if (html.classList.contains('dark')) {
    html.classList.remove('dark');
    html.classList.add('light');
  } else {
    html.classList.remove('light');
    html.classList.add('dark');
  }
}

// 모달 열기 함수
function openModal() {
  document.getElementById('contactModal').style.display = 'block';
}

// 모달 닫기 함수
function closeModal() {
  document.getElementById('contactModal').style.display = 'none';
}

// 모달 외부 클릭 시 닫기
window.onclick = function(event) {
  const modal = document.getElementById('contactModal');
  if (event.target == modal) {
    modal.style.display = 'none';
  }
}

// 폼 제출 처리
document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const submitButton = this.querySelector('button[type="submit"]');
  const originalButtonText = submitButton.textContent;
  
  // 버튼 비활성화 및 로딩 상태 표시
  submitButton.disabled = true;
  submitButton.textContent = '전송 중...';
  
  fetch('send_email_smtp.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      alert(data.message);
      closeModal();
      this.reset();
    } else {
      // 상세한 에러 메시지 표시
      let errorMessage = data.message;
      if (data.error) {
        errorMessage += '\n\n에러 상세 정보:';
        errorMessage += `\n- 파일: ${data.error.file}`;
        errorMessage += `\n- 라인: ${data.error.line}`;
        errorMessage += `\n- 코드: ${data.error.code}`;
      }
      alert(errorMessage);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    let errorMessage = '이메일 전송 중 오류가 발생했습니다.\n\n';
    errorMessage += '오류 원인:\n';
    errorMessage += '- 서버 연결 실패\n';
    errorMessage += '- PHP 설정 문제\n';
    errorMessage += '- 메일 서버 설정 문제\n\n';
    errorMessage += '해결 방법:\n';
    errorMessage += '1. 인터넷 연결을 확인해주세요\n';
    errorMessage += '2. 잠시 후 다시 시도해주세요\n';
    errorMessage += '3. 계속 문제가 발생하면 관리자에게 문의해주세요';
    alert(errorMessage);
  })
  .finally(() => {
    // 버튼 상태 복원
    submitButton.disabled = false;
    submitButton.textContent = originalButtonText;
  });
}); 