<?php
require_once 'db.php';
session_start();
$message = '';
$messageType = '';

// 漏洞点：验证码永远固定为 1234
$fixed_captcha = '1234';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    
    if ($captcha !== $fixed_captcha) {
        $message = "❌ 验证码错误";
        $messageType = 'error';
    } elseif (userExists($username) && $users[$username] === $password) {
        $message = "✅ 登录成功！";
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
    <title>2.7 固定验证码 | VulnLogin-Lab</title>
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
        .captcha-box { background: #f1f5f9; padding: 0.5rem; text-align: center; font-family: monospace; font-size: 1.5rem; border-radius: 6px; margin-bottom: 0.8rem; letter-spacing: 0.5rem; color: #1e293b; }
        .vuln-note { margin-top: 1rem; padding: 1rem; background: #fee2e2; border: 1px solid #ef4444; border-radius: 6px; font-size: 0.8rem; line-height: 1.6; }
        .back { display: inline-block; margin-bottom: 1rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="card">
    <a href="/" class="back">← 返回首页</a>
    <h2>2.7 固定验证码</h2>
    <p class="sub">验证码恒为固定值，可被绕过</p>
    <div class="alert alert-info">💡 验证码永远都是同一个值，观察页面显示的验证码</div>
    <form method="POST">
        <input type="text" name="username" placeholder="用户名">
        <input type="password" name="password" placeholder="密码">
        <div class="captcha-box"><?php echo $fixed_captcha; ?></div>
        <input type="text" name="captcha" placeholder="请输入验证码">
        <button type="submit">登 录</button>
    </form>
    <?php if ($message): ?>
    <div class="result <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="vuln-note">
        ⚠️ <strong>漏洞分析：</strong><br>
        验证码 <code><?php echo $fixed_captcha; ?></code> 永远不会改变。<br>
        攻击者获取验证码后，可编写脚本自动爆破密码，验证码形同虚设。
    </div>
</div>
</body>
</html>