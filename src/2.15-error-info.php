cat > 2.15-error-info.php << 'EOF'
<?php
$message = '';
$error_detail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 模拟数据库连接
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = 'SuperSecret@2024!';
    $db_name = 'vulnlab';

    // 漏洞点：错误信息直接暴露给用户
    if ($username === 'admin' && $password === 'Admin@2024!') {
        $message = "✅ 登录成功";
    } else {
        $message = "❌ 登录失败";
        $error_detail = "Database Error: Connection failed for user '{$db_user}'@'{$db_host}' (using password: YES) in /var/www/html/2.15-error-info.php on line 12\n";
        $error_detail .= "Query: SELECT * FROM {$db_name}.users WHERE username='{$username}' AND password=MD5('{$password}')\n";
        $error_detail .= "PHP Version: " . phpversion() . "\n";
        $error_detail .= "Server: Apache/2.4.56 (Debian)";
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.15 错误信息泄露 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:500px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.error-box{margin-top:.5rem;padding:.75rem;background:#1e293b;color:#f87171;border-radius:6px;font-family:monospace;font-size:.7rem;white-space:pre-wrap;line-height:1.5;max-height:200px;overflow-y:auto}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.15 错误信息泄露</h2>
<p class="sub">登录失败时暴露敏感的系统和数据库信息</p>
<div class="alert alert-info">💡 输入任意错误的用户名和密码，观察错误详情</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名（随便填）" required>
<input type="password" name="password" placeholder="密码">
<button type="submit">登 录</button>
</form>
<?php if($message):?>
<div class="result" style="background:#fee2e2;color:#991b1b;"><?php echo $message;?></div>
<?php endif;?>
<?php if($error_detail):?>
<div class="error-box"><?php echo htmlspecialchars($error_detail);?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>泄露信息分析：</strong><br>
• 数据库用户名：<code>root</code><br>
• 数据库名：<code>vulnlab</code><br>
• SQL查询语句结构（可用于注入）<br>
• PHP版本号<br>
• 服务器软件及版本<br>
<strong>修复：</strong>生产环境关闭 <code>display_errors</code>，使用通用错误页面。
</div>
</div>
</body>
</html>
EOF