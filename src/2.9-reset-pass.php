cat > 2.9-reset-pass.php << 'EOF'
<?php
require_once 'db.php';
$message = '';
$messageType = '';

// 漏洞：重置密码不需要旧密码，不需要验证用户身份
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (userExists($username)) {
        // 漏洞点：直接修改密码，无任何身份验证
        $users[$username] = $new_password;
        $message = "✅ 用户 {$username} 的密码已重置为：{$new_password}";
        $messageType = 'success';
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
<title>2.9 任意用户重置密码 | VulnLogin-Lab</title>
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
button{width:100%;padding:.6rem;background:#dc2626;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.success{background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.9 任意用户重置密码</h2>
<p class="sub">无需验证身份即可重置任意用户密码</p>
<div class="alert alert-danger">
⚠️ <strong>危险操作：</strong>此功能会直接修改用户密码。测试完后请手动恢复。
</div>
<div class="alert alert-info">💡 输入一个已存在的用户名（如 admin），即可直接修改其密码。</div>
<form method="POST">
<input type="text" name="username" placeholder="目标用户名（如 admin）" required>
<input type="password" name="new_password" placeholder="新密码" required>
<button type="submit">⚡ 直接重置密码</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
• 无需提供旧密码<br>
• 无需邮箱/手机验证码<br>
• 无需回答密保问题<br>
• 任何用户都可被重置密码<br>
<strong>修复：</strong>重置密码前必须通过邮件/短信验证身份。
</div>
</div>
</body>
</html>
EOF