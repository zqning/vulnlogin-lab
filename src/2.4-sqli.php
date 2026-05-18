<?php
$message = '';
$messageType = '';
$sql_shown = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 漏洞点：直接拼接 SQL 语句，没有参数化查询
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $sql_shown = $sql;
    
    // 模拟数据库查询
    // 硬编码的"数据库"验证逻辑，模拟SQL注入效果
    $valid_users = ['admin' => 'Admin@2024!', 'user' => 'User#1234'];
    
    // 模拟SQL注入万能密码的逻辑
    $injected = false;
    
    // 检测常见万能密码模式
    $injection_patterns = [
        "/'.*OR.*'1'='1/i",
        "/'.*OR.*1=1/i",
        "/'.*--/i",
        "/'.*#/i",
        "/'.*\|\|.*\+/i",
        "/OR.*1=1/i",
        "/admin'.*/i",
    ];
    
    foreach ($injection_patterns as $pattern) {
        if (preg_match($pattern, $username) || preg_match($pattern, $password)) {
            $injected = true;
            break;
        }
    }
    
    if ($injected) {
        $message = "✅ 登录成功！欢迎 admin（通过SQL注入绕过）";
        $messageType = 'success';
    } elseif (isset($valid_users[$username]) && $valid_users[$username] === $password) {
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
    <title>2.4 SQL注入万能密码 | VulnLogin-Lab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { background: #fff; border-radius: 12px; padding: 2rem; width: 450px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 0.5rem; }
        .sub { color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem; }
        .alert { padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-info { background: #dbeafe; border: 1px solid #3b82f6; color: #1e40af; }
        .alert-warning { background: #fef9c3; border: 1px solid #f59e0b; color: #92400e; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
        button { width: 100%; padding: 0.6rem; background: #dc2626; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
        .result { margin-top: 1rem; padding: 0.75rem; border-radius: 6px; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        .sql-box { margin-top: 1rem; padding: 0.75rem; background: #1e293b; color: #4ade80; border-radius: 6px; font-family: monospace; font-size: 0.8rem; word-break: break-all; }
        .vuln-note { margin-top: 1rem; padding: 1rem; background: #fee2e2; border: 1px solid #ef4444; border-radius: 6px; font-size: 0.8rem; line-height: 1.6; }
        .back { display: inline-block; margin-bottom: 1rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; }
        .payload-list { font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; }
        .payload-list code { background: #f1f5f9; padding: 0.15rem 0.3rem; border-radius: 3px; }
    </style>
</head>
<body>
<div class="card">
    <a href="/" class="back">← 返回首页</a>
    <h2>2.4 SQL注入万能密码</h2>
    <p class="sub">通过构造永真条件绕过登录验证</p>
    <div class="alert alert-info">
        💡 <strong>常用Payload：</strong>
        <div class="payload-list">
            <code>' OR '1'='1</code> &nbsp;
            <code>admin' --</code> &nbsp;
            <code>admin' #</code> &nbsp;
            <code>' OR 1=1 --</code>
        </div>
    </div>
    <form method="POST">
        <input type="text" name="username" placeholder="用户名（如 admin' OR '1'='1）" required>
        <input type="password" name="password" placeholder="密码（随便填）">
        <button type="submit">SQL 注入登录</button>
    </form>
    <?php if ($message): ?>
    <div class="result <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($sql_shown): ?>
    <div class="sql-box">📝 实际执行的SQL：<br><?php echo htmlspecialchars($sql_shown); ?></div>
    <?php endif; ?>
    <?php if ($injected ?? false): ?>
    <div class="vuln-note">
        ⚠️ <strong>注入成功！</strong><br>
        后端SQL语句直接拼接用户输入，未做任何过滤。<br>
        <strong>修复建议：</strong>使用参数化查询（Prepared Statement）或ORM框架。
    </div>
    <?php endif; ?>
</div>
</body>
</html>