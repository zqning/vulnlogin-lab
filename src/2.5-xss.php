<?php
session_start();
$message = '';

// 存储登录日志（模拟）
if (!isset($_SESSION['login_logs'])) {
    $_SESSION['login_logs'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 漏洞点：未对输入做 HTML 实体编码就直接存储
    $log_entry = "<tr><td>" . date('Y-m-d H:i:s') . "</td><td>" . $username . "</td><td>失败</td></tr>";
    array_unshift($_SESSION['login_logs'], $log_entry);
    if (count($_SESSION['login_logs']) > 20) array_pop($_SESSION['login_logs']);
    
    $message = "❌ 登录失败，但你的输入已被记录到日志。";
}

// 查看日志参数
$show_logs = isset($_GET['action']) && $_GET['action'] === 'logs';
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>2.5 XSS攻击 | VulnLogin-Lab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { background: #fff; border-radius: 12px; padding: 2rem; width: 550px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 0.5rem; }
        .sub { color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem; }
        .alert { padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-info { background: #dbeafe; border: 1px solid #3b82f6; color: #1e40af; }
        .alert-danger { background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
        button { width: 100%; padding: 0.6rem; background: #3b82f6; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .btn-log { width: 100%; padding: 0.6rem; background: #64748b; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
        .result { margin-top: 1rem; padding: 0.75rem; border-radius: 6px; background: #fee2e2; color: #991b1b; }
        .log-table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.8rem; }
        .log-table th, .log-table td { border: 1px solid #e2e8f0; padding: 0.5rem; text-align: left; }
        .log-table th { background: #f8fafc; }
        .vuln-note { margin-top: 1rem; padding: 1rem; background: #fef9c3; border-radius: 6px; font-size: 0.8rem; line-height: 1.6; }
        .back { display: inline-block; margin-bottom: 1rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="card">
    <a href="/" class="back">← 返回首页</a>
    <h2>2.5 XSS 攻击</h2>
    <p class="sub">在用户名中注入恶意脚本，当管理员查看日志时触发</p>
    <div class="alert alert-info">
        💡 <strong>测试Payload：</strong><br>
        <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code><br>
        <code>&lt;img src=x onerror=alert(document.cookie)&gt;</code>
    </div>
    <div class="alert alert-danger">
        ⚠️ 模拟场景：输入 XSS Payload 作为用户名，点击"查看登录日志"模拟管理员触发
    </div>
    <form method="POST">
        <input type="text" name="username" placeholder="用户名（注入XSS Payload）" required>
        <input type="password" name="password" placeholder="密码">
        <button type="submit">登 录</button>
    </form>
    <a href="?action=logs"><button type="button" class="btn-log">📋 查看登录日志（模拟管理员）</button></a>
    <?php if ($message): ?>
    <div class="result"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($show_logs && !empty($_SESSION['login_logs'])): ?>
    <table class="log-table">
        <tr><th>时间</th><th>用户名</th><th>状态</th></tr>
        <?php foreach ($_SESSION['login_logs'] as $log): ?>
            <?php echo $log; // 漏洞点：直接输出未转义的 HTML ?>
        <?php endforeach; ?>
    </table>
    <div class="vuln-note">
        ⚠️ <strong>漏洞触发！</strong><br>
        日志中的 XSS Payload 已被执行。在真实场景中，攻击者可利用此漏洞窃取管理员的 Cookie。
    </div>
    <?php endif; ?>
</div>
</body>
</html>