cat > 2.20-cookie-auth.php << 'EOF'
<?php
$message = '';

// 漏洞点：完全信任前端Cookie，后端不验证
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];

    // 模拟数据库用户
    $users = ['admin' => 'Admin@2024!', 'user' => 'User#1234'];

    if (isset($users[$username]) && $users[$username] === $password) {
        $message = "✅ 通过Cookie认证登录成功！欢迎 {$username}";
    } else {
        $message = "❌ Cookie中的凭证无效";
    }
}

// 处理登录表单
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'Admin@2024!') {
        // 正常登录设置Cookie
        setcookie('username', $username, time() + 3600, '/');
        setcookie('password', $password, time() + 3600, '/');
        $message = "✅ 登录成功！Cookie已设置";
    } else {
        $message = "❌ 用户名或密码错误";
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.20 前端Cookie越权 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:480px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;margin-bottom:.5rem}
.btn-evil{background:#dc2626}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.success{background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.code-box{margin-top:.5rem;padding:.75rem;background:#1e293b;color:#4ade80;border-radius:6px;font-family:monospace;font-size:.7rem;word-break:break-all;line-height:1.5}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.20 前端Cookie越权</h2>
<p class="sub">后端完全信任前端Cookie中的凭证信息</p>
<div class="alert alert-info">
💡 <strong>攻击方式：</strong><br>
打开浏览器 F12 → Application → Cookies，<br>
手动添加 <code>username=admin</code> 和 <code>password=Admin@2024!</code>，<br>
然后<strong>刷新页面</strong>即可绕过登录。
</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名 (admin)" required>
<input type="password" name="password" placeholder="密码 (Admin@2024!)" required>
<button type="submit">正常登录（设置Cookie）</button>
</form>

<?php if($message):?>
<div class="result <?php echo strpos($message,'✅')!==false?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>

<div style="margin-top:1rem;" class="code-box">
📋 当前Cookie值：<br>
username: <?php echo htmlspecialchars($_COOKIE['username'] ?? '未设置');?><br>
password: <?php echo htmlspecialchars($_COOKIE['password'] ?? '未设置');?>
</div>

<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
后端完全信任客户端Cookie中的 <code>username</code> 和 <code>password</code>，<br>
攻击者只需在浏览器中手动设置这两个Cookie，即可冒充任意用户登录。<br>
<strong>修复：</strong>使用服务端Session + 随机Token，不信任客户端可控数据。
</div>
</div>
</body>
</html>
EOF