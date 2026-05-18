cat > 2.8-front-captcha.php << 'EOF'
<?php
require_once 'db.php';
$message = '';
$messageType = '';
// 漏洞：后端完全不校验验证码，只校验用户名密码
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (userExists($username) && $users[$username] === $password) {
        $message = "✅ 登录成功！欢迎 {$username}";
        $messageType = 'success';
    } else {
        $message = "❌ 用户名或密码错误";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.8 前端校验验证码 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:400px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.success{background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.captcha-box{background:#f1f5f9;padding:.5rem;text-align:center;font-family:monospace;font-size:1.5rem;border-radius:6px;margin-bottom:.8rem;letter-spacing:.5rem}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
<script>
// 漏洞点：验证码仅在前端校验
function validateForm() {
    var captcha = document.getElementById('captchaInput').value;
    if (captcha !== '1234') {
        alert('验证码错误！');
        return false;
    }
    return true;
}
</script>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.8 前端校验验证码</h2>
<p class="sub">验证码仅在前端JS校验，后端不校验</p>
<div class="alert alert-info">💡 用 Burp Suite 抓包，删除 captcha 参数或修改为任意值，观察是否仍能爆破。</div>
<form method="POST" onsubmit="return validateForm()">
<input type="text" name="username" placeholder="用户名" required>
<input type="password" name="password" placeholder="密码" required>
<div class="captcha-box">1234</div>
<input type="text" id="captchaInput" name="captcha" placeholder="请输入验证码">
<button type="submit">登 录</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
验证码 <code>1234</code> 仅在前端 JS 中校验。<br>
攻击者可直接发送 HTTP 请求（绕过浏览器），不携带或随意填写 captcha 参数，<br>后端不会拒绝，从而可以进行暴力破解。
</div>
</div>
</body>
</html>
EOF