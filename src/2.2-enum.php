<?php
require_once 'db.php';
$message = '';
$messageType = '';
$userExists = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (userExists($username)) {
        $userExists = true;
        if ($password === $users[$username]) {
            $message = "✅ 登录成功！欢迎 {$username}";
            $messageType = 'success';
        } else {
            // 漏洞点：明确提示"密码错误"，证实用户存在
            $message = "❌ 密码错误";
            $messageType = 'error';
        }
    } else {
        // 用户不存在时给出不同提示
        $message = "❌ 用户不存在";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>2.2 用户名枚举 | VulnLogin-Lab</title>
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
    <h2>2.2 用户名枚举</h2>
    <p class="sub">根据错误信息差异判断用户是否存在</p>
    <div class="alert alert-info">💡 分别输入 admin 和 nonexist，观察错误提示的差异</div>
    <form method="POST">
        <input type="text" name="username" placeholder="用户名" required>
        <input type="password" name="password" placeholder="密码">
        <button type="submit">登 录</button>
    </form>
    <?php if ($message): ?>
    <div class="result <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($userExists && $messageType === 'error'): ?>
    <div class="vuln-note">
        ⚠️ <strong>漏洞触发！</strong><br>
        后端返回了"密码错误"而非"用户不存在"，攻击者可以确认 <code><?php echo htmlspecialchars($username); ?></code> 这个用户是存在的，随后可对此账号进行爆破。
    </div>
    <?php endif; ?>
</div>
</body>
</html>