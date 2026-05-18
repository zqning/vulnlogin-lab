cat > 2.11-jwt.php << 'EOF'
<?php
$message = '';
$messageType = '';
$decoded_info = '';

// 简单的 JWT 模拟（仅用于演示）
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

// 模拟正常用户 JWT
$header = base64url_encode('{"alg":"HS256","typ":"JWT"}');
$payload = base64url_encode('{"sub":"user","name":"user","role":"user"}');
$secret = 'secret_key';
$signature = base64url_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
$normal_jwt = "$header.$payload.$signature";

// 处理登录
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $token = $input['token'] ?? '';

    if ($username === 'user' && $password === 'password1') {
        // 正常登录，返回JWT
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'token' => $normal_jwt,
            'message' => '登录成功，请保存JWT Token'
        ]);
        exit;
    }

    // 验证JWT（漏洞：接受None算法）
    if ($token) {
        $parts = explode('.', $token);
        if (count($parts) === 3) {
            $decoded_payload = json_decode(base64url_decode($parts[1]), true);
            // 漏洞点：不验证签名算法，直接信任payload
            if ($decoded_payload && isset($decoded_payload['role'])) {
                if ($decoded_payload['role'] === 'admin') {
                    $message = "✅ 越权成功！以管理员身份登录";
                    $messageType = 'success';
                    $decoded_info = json_encode($decoded_payload, JSON_PRETTY_PRINT);
                } else {
                    $message = "✅ 登录成功，角色：{$decoded_payload['role']}";
                    $messageType = 'success';
                }
            } else {
                $message = "❌ Token无效";
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>2.11 JWT漏洞 | VulnLogin-Lab</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui;background:#f1f5f9;min-height:100vh;display:flex;justify-content:center;align-items:center}
.card{background:#fff;border-radius:12px;padding:2rem;width:500px;box-shadow:0 4px 6px rgba(0,0,0,.1)}
h2{margin-bottom:.5rem}
.sub{color:#64748b;font-size:.85rem;margin-bottom:1.5rem}
.alert{padding:.75rem;border-radius:6px;font-size:.85rem;margin-bottom:1rem}
.alert-info{background:#dbeafe;border:1px solid #3b82f6;color:#1e40af}
input{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem}
textarea{width:100%;padding:.6rem;margin-bottom:.8rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.8rem;font-family:monospace;resize:vertical}
button{width:100%;padding:.6rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;margin-bottom:.5rem}
.btn-alt{background:#8b5cf6}
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
<h2>2.11 JWT漏洞 - None算法</h2>
<p class="sub">篡改JWT Token中的角色，使用None算法绕过签名验证</p>
<div class="alert alert-info">
💡 <strong>攻击步骤：</strong><br>
1. 用 <code>user</code> / <code>password1</code> 登录获取JWT<br>
2. 将 payload 中的 <code>"role":"user"</code> 改为 <code>"role":"admin"</code><br>
3. 将 header 中的 <code>"alg":"HS256"</code> 改为 <code>"alg":"None"</code><br>
4. 删除签名部分，仅保留 <code>header.payload.</code>（注意末尾的点）
</div>
<div class="alert alert-info">
📝 <strong>None算法JWT模板：</strong>
<div class="code-box">eyJhbGciOiJOb25lIiwidHlwIjoiSldUIn0.eyJzdWIiOiJhZG1pbiIsIm5hbWUiOiJhZG1pbiIsInJvbGUiOiJhZG1pbiJ9.</div>
</div>
<form id="loginForm">
<input type="text" id="username" placeholder="用户名 (user)" required>
<input type="password" id="password" placeholder="密码 (password1)">
<button type="button" onclick="doLogin()">🔑 正常登录获取JWT</button>
</form>
<hr style="margin:1rem 0;border-color:#e2e8f0">
<textarea id="tokenInput" rows="3" placeholder="在此粘贴修改后的JWT Token..."></textarea>
<button type="button" onclick="useToken()" class="btn-alt">🚀 使用Token登录</button>

<?php if($message):?>
<div class="result <?php echo $messageType==='success'?'success':'error';?>"><?php echo $message;?></div>
<?php endif;?>
<?php if($decoded_info):?>
<div class="code-box">解码后的Payload：<br><?php echo htmlspecialchars($decoded_info);?></div>
<?php endif;?>
<div class="vuln-note">
⚠️ <strong>漏洞分析：</strong><br>
后端未验证JWT签名算法，接受 <code>None</code> 算法。<br>
攻击者可篡改payload为admin角色，无需知道签名密钥。
</div>
</div>
<script>
async function doLogin() {
    const u = document.getElementById('username').value;
    const p = document.getElementById('password').value;
    const resp = await fetch('2.11-jwt.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username: u, password: p})
    });
    const data = await resp.json();
    if (data.token) {
        document.getElementById('tokenInput').value = data.token;
        alert('JWT已获取，请复制后篡改role为admin，算法改为None');
    } else {
        alert('登录失败');
    }
}
async function useToken() {
    const token = document.getElementById('tokenInput').value.trim();
    const resp = await fetch('2.11-jwt.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({token: token})
    });
    const text = await resp.text();
    document.body.innerHTML = text;
}
</script>
</body>
</html>
EOF