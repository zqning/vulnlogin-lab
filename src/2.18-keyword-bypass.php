cat > 2.18-keyword-bypass.php << 'EOF'
<?php
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 漏洞点：检查特殊关键词参数
    $bypass = $_POST['bypass'] ?? $_POST['debug'] ?? $_POST['pass'] ?? $_POST['verify'] ?? $_POST['allow'] ?? null;

    if ($bypass && ($bypass === 'true' || $bypass === '1' || $bypass === true)) {
        $message = "✅ 关键词绕过成功！无需正确密码";
        $messageType = 'success';
    } elseif ($username === 'admin' && $password === 'Admin@2024!') {
        $message = "✅ 正常登录成功";
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
<title>2.18 关键词绕过 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:460px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
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
<h2>2.18 关键词绕过</h2>
<p class="sub">通过添加特殊参数绕过登录验证</p>
<div class="alert alert-info">
💡 <strong>攻击方式（用Burp Suite添加参数）：</strong><br>
<div class="code-box">username=admin&password=wrong&bypass=true</div>
或尝试：<code>debug=true</code>、<code>pass=true</code>、<code>verify=true</code>、<code>allow=true</code>
</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名" required>
<input type="password" name="password" placeholder="密码（随便填）">
<button type="submit">登 录</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
开发者为了方便调试或后门，在代码中留下了关键词检查：<br>
<code>if ($bypass === 'true') { 直接登录 }</code><br>
攻击者发现后只需添加 <code>&bypass=true</code> 即可绕过认证。
</div>
</div>
</body>
</html>
EOF