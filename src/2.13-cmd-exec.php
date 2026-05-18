cat > 2.13-cmd-exec.php << 'EOF'
<?php
$message = '';
$messageType = '';
$cmd_shown = '';
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 漏洞点：用户名直接拼接到系统命令中
    $cmd = "echo 'User {$username} logged in at $(date)' >> /tmp/login.log";
    $cmd_shown = $cmd;

    // 模拟执行
    if (strpos($username, '|') !== false || strpos($username, ';') !== false || strpos($username, '`') !== false) {
        // 检测到命令注入
        $message = "⚠️ 命令注入成功！检查 /tmp/ 目录";
        $messageType = 'success';
        // 实际执行恶意命令（靶场环境安全）
        $test_cmd = "echo 'User {$username} logged in at $(date)' >> /tmp/login.log 2>&1; echo '---INJECTED---'";
        $output = shell_exec($test_cmd . ' 2>&1');
    } elseif ($username === 'admin' && $password === 'Admin@2024!') {
        $message = "✅ 正常登录成功";
        $messageType = 'success';
        shell_exec($cmd . ' 2>&1');
    } else {
        $message = "❌ 登录失败，但命令已执行";
        $messageType = 'error';
        shell_exec($cmd . ' 2>&1');
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.13 命令执行 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:480px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
.alert-danger{background:#fee2e2;border:1px solid #ef4444;color:#991b1b}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#dc2626;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold}
.result{margin-top:1rem;padding:.75rem;border-radius:6px}
.success{background:#dcfce7;color:#166534}
.error{background:#fee2e2;color:#991b1b}
.code-box{margin-top:.5rem;padding:.75rem;background:#1e293b;color:#4ade80;border-radius:6px;font-family:monospace;font-size:.75rem;word-break:break-all;max-height:150px;overflow-y:auto}
.vuln-note{margin-top:1rem;padding:1rem;background:#fee2e2;border:1px solid #ef4444;border-radius:6px;font-size:.8rem;line-height:1.6}
.back{display:inline-block;margin-bottom:1rem;color:#3b82f6;text-decoration:none;font-size:.85rem}
</style>
</head>
<body>
<div class="card">
<a href="/" class="back">← 返回首页</a>
<h2>2.13 登录命令执行</h2>
<p class="sub">用户名被直接拼接到系统命令中执行</p>
<div class="alert alert-danger">
⚠️ <strong>高危漏洞：</strong>此页面会实际执行系统命令。
</div>
<div class="alert alert-info">
💡 <strong>测试Payload：</strong><br>
<code>admin'|echo 'hack' > /tmp/hacked.txt |'</code><br>
<code>admin; ls -la /tmp;</code>
</div>
<form method="POST">
<input type="text" name="username" placeholder="用户名（命令注入点）" required>
<input type="password" name="password" placeholder="密码">
<button type="submit">⚡ 执行登录</button>
</form>
<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<?php if($cmd_shown):?>
<div class="code-box">📝 实际执行的命令：<br><?php echo htmlspecialchars($cmd_shown);?></div>
<?php endif;?>
<?php if($output):?>
<div class="code-box">📤 命令输出：<br><?php echo htmlspecialchars($output);?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
开发者使用 <code>echo 'User {$username}...'</code> 记录日志，<br>但未对用户名做任何过滤，攻击者可通过管道符 <code>|</code>、分号 <code>;</code> 等注入任意命令。<br>
<strong>修复：</strong>使用 <code>escapeshellarg()</code> 过滤用户输入。
</div>
</div>
</body>
</html>
EOF