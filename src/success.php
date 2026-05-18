cat > success.php << 'EOF'
<?php
session_start();
// 漏洞：没有验证 2fa_verified 状态
$_SESSION['logged_in'] = true;
$_SESSION['username'] = $_SESSION['username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>登录成功 | VulnLogin-Lab</title>
<style>
body { font-family: system-ui; background: #f1f5f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
.card { background: #fff; border-radius: 12px; padding: 2rem; width: 400px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.success { color: #166534; font-size: 3rem; }
</style>
</head>
<body>
<div class="card">
<div class="success">✅</div>
<h1>登录成功</h1>
<p>欢迎，<strong><?php echo $_SESSION['username']; ?></strong></p>
<p style="color:#64748b; font-size:0.85rem;">你已成功进入系统后台</p>
<a href="/" style="color:#3b82f6;">返回首页</a>
</div>
</body>
</html>
EOF