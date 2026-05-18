cat > 2.16-param-pollution.php << 'EOF'
<?php
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    // 漏洞点：如果有多个password参数，只取最后一个
    $password = $_POST['password'] ?? '';

    // 如果是数组，取最后一个元素
    if (is_array($password)) {
        $password = end($password);
    }

    if ($username === 'admin' && $password === 'Admin@2024!') {
        $message = "✅ 正常登录成功";
        $messageType = 'success';
    } elseif ($username === 'admin' && $password !== 'Admin@2024!') {
        $message = "❌ 密码错误（正常流程）";
        $messageType = 'error';
    } else {
        $message = "❌ 登录失败";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.16 HTTP参数污染 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:480px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#f59e0b;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.success{background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.code-box{margin-top:.5rem;padding:.75rem;background:#1e293b;color:#4ade80;border-radius:6px;font-family:monospace;font-size:.75rem;word-break:break-all}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.16 HTTP参数污染</h2>
<p class="sub">通过传递多个同名参数绕过密码验证</p>
<div class="alert alert-info">
💡 <strong>攻击方式：</strong><br>
用 Burp Suite 抓包，修改POST数据为：<br>
<div class="code-box">username=admin&password=wrong&password=Admin@2024!</div>
后端只验证最后一个 <code>password</code> 参数。
</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名 (admin)" required>
<input type="password" name="password" placeholder="密码">
<button type="submit">登 录</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
当存在多个同名参数时，不同后端处理方式不同：<br>
• PHP：取最后一个值<br>
• JSP：取第一个值<br>
• ASP.NET：用逗号连接<br>
攻击者可利用此差异绕过WAF或业务逻辑。
</div>
</div>
</body>
</html>
EOF