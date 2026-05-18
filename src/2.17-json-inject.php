cat > 2.17-json-inject.php << 'EOF'
<?php
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        // 漏洞点1：接受布尔值true作为密码
        // 漏洞点2：接受额外的admin属性
        // 漏洞点3：接受数组形式的用户名

        // 检测方式一：password为布尔true
        if ($password === true) {
            if (isset($input['username']) && !empty($input['username']) && $input['username'] !== 'admi') {
                $message = "✅ 方式一绕过成功！（布尔值true）";
                $messageType = 'success';
            }
        }
        // 检测方式二：额外admin属性
        elseif (isset($input['admin']) && $input['admin'] === true) {
            $message = "✅ 方式二绕过成功！（admin属性）";
            $messageType = 'success';
        }
        // 检测方式三：数组注入
        elseif (is_array($username) && in_array('admin', $username)) {
            $message = "✅ 方式三绕过成功！（数组注入）";
            $messageType = 'success';
        }
        // 正常验证
        elseif ($username === 'admin' && $password === 'Admin@2024!') {
            $message = "✅ 正常登录成功";
            $messageType = 'success';
        }
        else {
            $message = "❌ 用户名或密码错误";
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.17 JSON注入 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:500px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
textarea{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.8rem;font-family:monospace;resize:vertical}
button{width:100%;padding:.6rem;background:#8b5cf6;color:#fff;border:none;border-radius:6px;cursor:pointer;margin-bottom:.4rem}
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
<h2>2.17 JSON注入</h2>
<p class="sub">通过构造特殊的JSON数据绕过登录验证</p>
<div class="alert alert-info">
💡 <strong>三种绕过Payload（用Burp Suite发送）：</strong>
<div class="code-box">
<strong>方式一：</strong>{"username":"admin","password":true}<br>
<strong>方式二：</strong>{"username":"a","password":"b","admin":true}<br>
<strong>方式三：</strong>{"username":["admin","test"],"password":"123"}
</div>
</div>
<textarea id="payload" rows="4" placeholder='{"username":"admin","password":"wrong"}'></textarea>
<button onclick="sendJSON()">🚀 发送JSON登录</button>
<button onclick="fillPayload(1)" style="background:#10b981;">填入Payload 1</button>
<button onclick="fillPayload(2)" style="background:#f59e0b;">填入Payload 2</button>
<button onclick="fillPayload(3)" style="background:#ef4444;">填入Payload 3</button>

<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
后端JSON解析时，未严格校验参数类型：<br>
• 接受布尔值 <code>true</code> 作为密码<br>
• 接受额外属性 <code>admin</code> 控制权限<br>
• 接受数组形式的用户名，只检查第一个元素<br>
<strong>修复：</strong>对输入做严格的类型校验和Schema验证。
</div>
</div>
<script>
function fillPayload(n) {
    const payloads = [
        '{"username":"admin","password":true}',
        '{"username":"a","password":"b","admin":true}',
        '{"username":["admin","test"],"password":"123"}'
    ];
    document.getElementById('payload').value = payloads[n-1];
}
async function sendJSON() {
    const p = document.getElementById('payload').value;
    try {
        JSON.parse(p);
    } catch(e) {
        alert('JSON格式错误');
        return;
    }
    const resp = await fetch('2.17-json-inject.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: p
    });
    const text = await resp.text();
    document.body.innerHTML = text;
}
</script>
</body>
</html>
EOF