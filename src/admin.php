cat > admin.php << 'EOF'
<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /2.3-weakpass.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>管理后台 | VulnLogin-Lab</title>
<style>
body { font-family: system-ui; background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
.card { background: #1e293b; border-radius: 12px; padding: 2rem; width: 400px; text-align: center; border: 1px solid #334155; }
h1 { color: #38bdf8; }
.btn { display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #ef4444; color: #fff; border-radius: 6px; text-decoration: none; }
.back { display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #3b82f6; color: #fff; border-radius: 6px; text-decoration: none; }
</style>
</head>
<body>
<div class="card">
<h1>🔐 管理后台</h1>
<p>欢迎，<strong><?php echo $_SESSION['username'] ?? 'admin'; ?></strong></p>
<p style="color:#94a3b8; margin-top:1rem;">这是模拟的后台管理界面</p>
<p style="color:#94a3b8;">包含敏感数据和系统配置</p>
<a href="?logout=1" class="btn">退出登录</a>
<a href="/" class="back">返回首页</a>
</div>
</body>
</html>
<?php if (isset($_GET['logout'])) { session_destroy(); header('Location: /'); exit; } ?>
EOF