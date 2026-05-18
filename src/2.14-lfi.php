cat > 2.14-lfi.php << 'EOF'
<?php
$message = '';
$content = '';
$theme = $_GET['theme'] ?? 'default';

// 漏洞点：直接用用户输入构建文件路径
$theme_path = "themes/{$theme}.php";

// 创建themes目录和默认主题
if (!is_dir('themes')) mkdir('themes');
if (!file_exists('themes/default.php')) {
    file_put_contents('themes/default.php', '<div style="background:#e8f5e9;padding:20px;border-radius:8px;"><h3>🌿 默认主题</h3><p>欢迎使用系统</p></div>');
}

if (isset($_GET['theme'])) {
    if (file_exists($theme_path)) {
        ob_start();
        include($theme_path);
        $content = ob_get_clean();
        $message = "✅ 主题加载成功：{$theme}";
    } else {
        $message = "❌ 主题文件不存在：{$theme_path}";
    }
}

// 处理登录
$logged_in = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['username'] ?? '') === 'admin' && ($_POST['password'] ?? '') === 'Admin@2024!') {
        $logged_in = true;
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.14 文件包含 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:500px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;margin-bottom:.5rem}
.btn-lfi{background:#8b5cf6}
.result{margin-top:1rem;padding:.75rem;border-radius:6px;background:#fef9c3;color:#92400e;font-size:.85rem}
.code-box{margin-top:.5rem;padding:.75rem;background:#1e293b;color:#4ade80;border-radius:6px;font-family:monospace;font-size:.75rem;word-break:break-all;max-height:200px;overflow-y:auto}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
pre{margin:0;white-space:pre-wrap}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.14 文件包含漏洞 (LFI)</h2>
<p class="sub">通过theme参数包含任意文件</p>
<div class="alert alert-info">
💡 <strong>测试Payload：</strong><br>
<code>?theme=default</code> （正常）<br>
<code>?theme=../2.14-lfi</code> （包含自身，查看源码）<br>
<code>?theme=../db</code> （尝试包含db.php）<br>
<code>?theme=../../../etc/passwd</code> （读取系统文件）
</div>
<?php if(!$logged_in):?>
<form method="POST">
<input type="text" name="username" placeholder="用户名 (admin)" required>
<input type="password" name="password" placeholder="密码 (Admin@2024!)">
<button type="submit">登 录</button>
</form>
<?php else:?>
<p style="color:#166534;font-weight:bold;margin-bottom:1rem;">✅ 已登录</p>
<?php endif;?>

<p style="margin-bottom:.5rem;font-weight:600;">选择主题：</p>
<a href="?theme=default"><button type="button" class="btn-lfi">🌿 默认主题</button></a>
<a href="?theme=../2.14-lfi"><button type="button" class="btn-lfi">🔍 包含本页面源码</button></a>

<?php if($message):?>
<div class="result"><?php echo $message;?></div>
<?php endif;?>
<?php if($content):?>
<div class="code-box"><pre><?php echo htmlspecialchars($content);?></pre></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
<code>$theme_path = "themes/{$theme}.php"</code> 直接拼接用户输入，<br>攻击者可通过 <code>../</code> 路径遍历包含任意文件。<br>
<strong>修复：</strong>使用白名单限制可选主题。
</div>
</div>
</body>
</html>
EOF