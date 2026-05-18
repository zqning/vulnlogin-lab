cat > 2.19-race-condition.php << 'EOF'
<?php
session_start();
$message = '';
$messageType = '';
$counter_file = '/tmp/race_counter.txt';

if (!file_exists($counter_file)) {
    file_put_contents($counter_file, '0');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 漏洞点：先验证密码，再更新计数器，中间存在时间窗口
    $stored_pass = 'Admin@2024!';

    // 读取当前计数器
    $counter = (int)file_get_contents($counter_file);

    // 模拟延迟，扩大竞争窗口
    usleep(100000); // 100ms

    // 条件竞争漏洞：
    // 如果计数器>=5，即使密码错误也允许登录（模拟"宽松模式"）
    if ($counter >= 5 && $username === 'admin') {
        $message = "✅ 条件竞争成功！在计数器>=5时登录";
        $messageType = 'success';
        file_put_contents($counter_file, '0'); // 重置
    } elseif ($username === 'admin' && $password === $stored_pass) {
        $message = "✅ 正常登录成功";
        $messageType = 'success';
        $counter++;
        file_put_contents($counter_file, (string)$counter);
    } else {
        $message = "❌ 密码错误。当前计数：{$counter}/5";
        $messageType = 'error';
        $counter++;
        file_put_contents($counter_file, (string)$counter);
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.19 条件竞争 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:500px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
.alert-danger{background:#fee2e2;border:1px solid #ef4444;color:#991b1b}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
button{width:100%;padding:.6rem;background:#dc2626;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold;margin-bottom:.5rem}
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
<h2>2.19 条件竞争漏洞</h2>
<p class="sub">通过高并发请求在时间窗口内绕过登录</p>
<div class="alert alert-danger">
⚠️ <strong>测试方法：</strong>使用 Burp Suite Intruder 或 Python 脚本，<br>
对 <code>admin</code> 用户用错误密码并发发送 20+ 个请求。
</div>
<div class="alert alert-info">
💡 逻辑：密码错误时计数器+1，当计数器>=5时自动放行。<br>
并发请求可在计数器重置前同时通过验证。
</div>
<form method="POST">
<input type="text" name="username" value="admin" required>
<input type="password" name="password" placeholder="密码（故意填错）">
<button type="submit">单次登录（会失败）</button>
</form>

<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>

<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
单次错误密码登录会失败。<br>
但并发发送大量请求时，多个线程同时读取计数器，<br>
可能都读到 <code>counter=4</code>，各自+1后都变成5，<br>
导致多个请求同时通过验证。<br>
<strong>修复：</strong>使用原子操作、文件锁或数据库事务。
</div>
</div>
</body>
</html>
EOF