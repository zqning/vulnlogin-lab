<?php
require_once 'db.php';
$message = '';
$messageType = '';
$attempts = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 漏洞点：没有验证码，没有频率限制，可以无限爆破
    if (userExists($username) && $password === $users[$username]) {
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
    <title>2.3 弱口令爆破 | VulnLogin-Lab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { background: #fff; border-radius: 12px; padding: 2rem; width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 0.5rem; }
        .sub { color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem; }
        .alert { padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-info { background: #dbeafe; border: 1px solid #3b82f6; color: #1e40af; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
        button { width: 100%; padding: 0.6rem; background: #3b82f6; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
        .result { margin-top: 1rem; padding: 0.75rem; border-radius: 6px; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        .vuln-note { margin-top: 1.5rem; padding: 1rem; background: #fef9c3; border-radius: 6px; font-size: 0.8rem; line-height: 1.6; }
        .back { display: inline-block; margin-bottom: 1rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="card">
    <a href="/" class="back">← 返回首页</a>
    <h2>2.3 弱口令爆破</h2>
    <p class="sub">无验证码、无频率限制，可对 admin 账号暴力破解</p>
    <div class="alert alert-info">💡 提示：admin 的密码是弱口令。尝试用 Burp Suite 或 hydra 进行爆破。</div>
    <form method="POST">
        <input type="text" name="username" placeholder="用户名（试试 admin）" required>
        <input type="password" name="password" placeholder="密码">
        <button type="submit">登 录</button>
    </form>
    <?php if ($message): ?>
    <div class="result <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="vuln-note">
        ⚠️ <strong>漏洞分析：</strong><br>
        • 无验证码保护<br>
        • 无登录频率限制<br>
        • 无账户锁定机制<br>
        • 用户 admin 使用弱口令<br>
        → 攻击者可编写字典进行暴力破解
    </div>
</div>
</body>
</html>