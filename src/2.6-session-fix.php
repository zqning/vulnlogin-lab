<?php
session_start();
$message = '';

// 模拟用户数据库
$valid_users = ['admin' => 'Admin@2024!'];

// 漏洞点：登录成功后不重新生成 Session ID
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($valid_users[$username]) && $valid_users[$username] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        // 漏洞：没有 session_regenerate_id()
        $message = "✅ 登录成功！Session ID 未更新。";
    } else {
        $message = "❌ 用户名或密码错误";
    }
}

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$current_sid = session_id();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>2.6 会话固定 | VulnLogin-Lab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { background: #fff; border-radius: 12px; padding: 2rem; width: 450px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 0.5rem; }
        .sub { color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem; }
        .alert { padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-info { background: #dbeafe; border: 1px solid #3b82f6; color: #1e40af; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
        button { width: 100%; padding: 0.6rem; background: #3b82f6; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
        .sid-box { background: #1e293b; color: #4ade80; padding: 0.75rem; border-radius: 6px; font-family: monospace; font-size: 0.75rem; word-break: break-all; margin-top: 0.5rem; }
        .vuln-note { margin-top: 1rem; padding: 1rem; background: #fee2e2; border: 1px solid #ef4444; border-radius: 6px; font-size: 0.8rem; line-height: 1.6; }
        .back { display: inline-block; margin-bottom: 1rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; }
        .logout-btn { display: inline-block; margin-bottom: 1rem; color: #dc2626; text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="card">
    <a href="/" class="back">← 返回首页</a>
    <?php if ($is_logged_in): ?>
        <a href="?logout=1" class="logout-btn">🚪 退出登录</a>
    <?php endif; ?>
    <h2>2.6 会话固定漏洞</h2>
    <p class="sub">登录成功后不重新生成 Session ID</p>
    <div class="alert alert-info">
        💡 <strong>当前 Session ID：</strong>
        <div class="sid-box"><?php echo $current_sid; ?></div>
    </div>
    <?php if (!$is_logged_in): ?>
    <form method="POST">
        <input type="text" name="username" placeholder="用户名 (admin)" required>
        <input type="password" name="password" placeholder="密码 (Admin@2024!)" required>
        <button type="submit">登 录</button>
    </form>
    <?php else: ?>
    <div style="padding: 1rem; background: #dcfce7; border-radius: 6px; color: #166534;">
        ✅ 已登录为 <strong><?php echo $_SESSION['username']; ?></strong>
    </div>
    <div class="vuln-note">
        ⚠️ <strong>漏洞分析：</strong><br>
        登录前后 Session ID <strong>完全相同</strong>。<br>
        攻击者可以先获取一个 Session ID，诱骗受害者使用此 ID 登录，<br>之后攻击者即可使用同一 Session ID 冒充受害者。
    </div>
    <?php endif; ?>
    <?php if ($message): ?>
    <div style="margin-top:1rem; padding:0.75rem; border-radius:6px; background:#fef9c3;"><?php echo $message; ?></div>
    <?php endif; ?>
</div>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: 2.6-session-fix.php');
    exit;
}
?>