cat > 2.10-2fa-bypass.php << 'EOF'
<?php
session_start();
$message = '';
$step = 1; // 1=登录表单, 2=2FA验证

// 模拟正确账号密码
$valid_user = 'admin';
$valid_pass = 'Admin@2024!';
$valid_2fa = '654321';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === $valid_user && $_POST['password'] === $valid_pass) {
            $_SESSION['temp_user'] = $valid_user;
            $_SESSION['2fa_required'] = true;
            $step = 2;
            $message = "第一步验证通过，请输入2FA验证码";
        } else {
            $message = "❌ 用户名或密码错误";
        }
    } elseif (isset($_POST['2fa_code']) && isset($_SESSION['2fa_required'])) {
        if ($_POST['2fa_code'] === $valid_2fa) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $_SESSION['temp_user'];
            unset($_SESSION['2fa_required']);
            header('Location: success.php');
            exit;
        } else {
            $message = "❌ 2FA验证码错误";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.10 双因素认证绕过 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:420px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
.alert-danger{background:#fee2e2;border:1px solid #ef4444;color:#991b1b;font-size:.8rem;line-height:1.5}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer}
.result{margin-top:1rem;padding:.75rem;border-radius:6px;background:#fef9c3;color:#92400e}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
code{background:#f1f5f9;padding:.15rem .3rem;border-radius:3px}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.10 双因素认证(2FA)绕过</h2>
<p class="sub">通过直接访问成功页面绕过2FA验证</p>
<div class="alert alert-info">
💡 账号密码：<code>admin</code> / <code>Admin@2024!</code><br>
2FA验证码：<code>654321</code>（模拟）
</div>
<div class="alert alert-danger">
⚠️ <strong>绕过方法：</strong>输入正确账号密码后，不要输入2FA验证码，<br>直接在浏览器地址栏访问 <code><strong>/success.php</strong></code>
</div>

<?php if ($step === 1): ?>
<form method="POST">
<input type="text" name="username" placeholder="用户名 (admin)" required>
<input type="password" name="password" placeholder="密码 (Admin@2024!)" required>
<button type="submit">下一步：2FA验证</button>
</form>
<?php else: ?>
<form method="POST">
<p style="margin-bottom:0.5rem;font-weight:600;">📱 请输入2FA验证码</p>
<input type="text" name="2fa_code" placeholder="6位验证码 (654321)">
<button type="submit">验证</button>
</form>
<?php endif; ?>

<?php if($message):?>
<div class="result"><?php echo $message;?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
<code>success.php</code> 没有检查 <code>2fa_verified</code> 状态。<br>
攻击者知道账号密码后，可直接访问 <code>/success.php</code>，完全跳过2FA。<br>
<strong>修复：</strong>在 success.php 中验证 2FA 状态，未通过则重定向回登录页。
</div>
</div>
</body>
</html>
EOF