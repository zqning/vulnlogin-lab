/**
 * 漏洞 2.1 - 明文传输
 * 
 * 这个脚本模拟了真实场景中前端拿到后端返回的密码后做本地比对。
 * 正常情况下，前端绝不应该拿到用户的真实密码。
 */

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    const resultEl = document.getElementById('loginResult');
    const leakBox = document.getElementById('leakBox');

    // 隐藏之前的结果
    resultEl.className = 'result';
    resultEl.textContent = '';
    leakBox.classList.remove('show');

    try {
        // 发送登录请求
        const response = await fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password }),
        });

        const data = await response.json();

        // 显示登录结果
        resultEl.classList.add('show');
        if (data.success) {
            resultEl.classList.add('result-success');
            resultEl.textContent = data.message;
        } else {
            resultEl.classList.add('result-error');
            resultEl.textContent = data.message;
        }

        // ==========================================
        // 漏洞演示：后端返回了明文密码
        // ==========================================
        if (data.password) {
            // 在前端用返回的密码做比对（这是错误的设计！）
            // 但这里只做展示，实际验证已在后端完成
            document.getElementById('leakUser').textContent = username;
            document.getElementById('leakPass').textContent = data.password;
            document.getElementById('leakCred').textContent = `${username} / ${data.password}`;
            leakBox.classList.add('show');
        }

    } catch (error) {
        resultEl.classList.add('show', 'result-error');
        resultEl.textContent = '⚠️ 网络错误，请检查后端是否正常运行。';
        console.error('登录请求失败:', error);
    }
});