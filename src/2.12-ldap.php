cat > 2.12-ldap.php << 'EOF'
<?php
$message = '';
$messageType = '';
$ldap_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 模拟LDAP查询拼接
    $ldap_query = "(&(uid={$username})(userPassword={$password}))";

    // 检测LDAP注入模式
    $injected = false;
    $patterns = ['/\)\(&\)/', '/\)\(|\(/', '/\)\)/', '/\*\)/'];

    foreach ($patterns as $p) {
        if (preg_match($p, $username)) {
            $injected = true;
            break;
        }
    }

    if ($injected) {
        $message = "✅ LDAP注入成功！绕过认证，以admin身份登录";
        $messageType = 'success';
    } elseif ($username === 'admin' && $password === 'Admin@2024!') {
        $message = "✅ 正常登录成功";
        $messageType = 'success';
    } else {
        $message = "❌ 认证失败";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.12 LDAP注入 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:460px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#8b5cf6;color:#fff;border:none;border-radius:6px;cursor:pointer}
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
<h2>2.12 LDAP注入</h2>
<p class="sub">通过注入LDAP查询语法绕过认证</p>
<div class="alert alert-info">
💡 <strong>测试Payload：</strong><br>
方法一：<code>admin)(&)</code> （用户名框）<br>
方法二：<code>admin)(|(password=*))(&)</code> （用户名框）
</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名（注入点）" required>
<input type="password" name="password" placeholder="密码（任意值）">
<button type="submit">登 录</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<?php if($ldap_query):?>
<div class="code-box">📝 构造的LDAP查询：<br><?php echo htmlspecialchars($ldap_query);?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞原理：</strong><br>
<code>admin)(&)</code> 使得查询变为：<br>
<code>(&(uid=admin)(&))(userPassword=xxx))</code><br>
其中 <code>(&)</code> 是永真条件，密码部分被忽略。
</div>
</div>
</body>
</html>
EOF