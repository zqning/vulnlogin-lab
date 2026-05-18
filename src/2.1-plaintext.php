<?php
$users = ['admin' => 'Admin@2024!', 'user' => 'User#1234', 'test' => 'TestP@ss1'];
$message = ''; $messageType = ''; $leak_user = ''; $leak_pass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (isset($users[$username])) {
        $leak_user = $username;
        $leak_pass = $users[$username];
        if ($password === $users[$username]) {
            $message = "✅ 登录成功！欢迎 {$username}";
            $messageType = 'success';
        } else {
            $message = "❌ 密码错误";
            $messageType = 'error';
        }
    } else {
        $message = "❌ 用户不存在";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.1 明文传输 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,sans-serif;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:420px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.25rem}.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-warning{background:#fffbeb;border:1px solid #f59e0b;color:#92400e}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:.9rem}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.success{background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.leak-box{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;line-height:1.7}
.leak-box .label{font-size:.8rem;font-weight:700;color:#ef4444}
.leak-box code{background:#fecaca;padding:.15rem .4rem;border-radius:3px;font-family:monospace}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>🔐 明文传输漏洞</h2>
<p class="sub">后端在 API 响应中直接返回用户密码</p>
<div class="alert alert-warning">
💡 <strong>测试：</strong>输入 <code>admin</code> + 错误密码，观察页面显示的密码。<br>
然后用 <strong>F12 → Network</strong> 查看响应体。
</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名 (admin)" required>
<input type="password" name="password" placeholder="密码 (随便填)">
<button type="submit">登 录</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType;?>"><?php echo $message;?></div>
<?php endif;?>
<?php if($leak_pass):?>
<div class="leak-box">
<span class="label">⚠️ 漏洞触发！后端返回了明文密码</span><br><br>
用户名：<code><?php echo htmlspecialchars($leak_user);?></code><br>
正确密码：<code><?php echo htmlspecialchars($leak_pass);?></code>
</div>
<?php endif;?>
</div>
</body>
</html>