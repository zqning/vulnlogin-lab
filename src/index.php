cat > /var/www/html/index.php << 'EOF'
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VulnLogin-Lab | 登录界面渗透测试靶场</title>
<style>
:root {
    --bg: #0f172a;
    --card-bg: #1e293b;
    --text: #e2e8f0;
    --accent: #38bdf8;
    --danger: #ef4444;
    --warning: #f59e0b;
    --success: #10b981;
    --purple: #8b5cf6;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
}
.header {
    text-align: center;
    padding: 3rem 1rem 2rem;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-bottom: 2px solid var(--accent);
}
.header h1 {
    font-size: 2.4rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #38bdf8, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.header .subtitle {
    color: #94a3b8;
    font-size: 1rem;
    margin-top: 0.5rem;
}
.warning-banner {
    background: var(--danger);
    color: #fff;
    text-align: center;
    padding: 0.7rem 1rem;
    font-weight: bold;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}
.stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    padding: 1.5rem;
    flex-wrap: wrap;
}
.stat-item {
    text-align: center;
    background: var(--card-bg);
    padding: 1rem 1.5rem;
    border-radius: 10px;
    border: 1px solid #334155;
    min-width: 100px;
}
.stat-num {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--accent);
}
.stat-label {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}
.container {
    max-width: 1100px;
    margin: 0 auto 3rem;
    padding: 0 1.5rem;
}
.category {
    margin-top: 2rem;
}
.category-title {
    font-size: 1.2rem;
    font-weight: 700;
    padding: 0.6rem 1rem;
    background: var(--card-bg);
    border-left: 4px solid var(--accent);
    border-radius: 0 8px 8px 0;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.category-title .icon { font-size: 1.3rem; }
.category-title .count {
    margin-left: auto;
    background: #334155;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    color: #94a3b8;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 0.8rem;
}
.card {
    background: var(--card-bg);
    border-radius: 10px;
    padding: 1.2rem;
    text-decoration: none;
    color: var(--text);
    border: 1px solid #334155;
    transition: all 0.25s ease;
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
}
.card:hover {
    transform: translateY(-2px);
    border-color: var(--accent);
    box-shadow: 0 8px 25px rgba(56,189,248,0.12);
    background: #233044;
}
.card-num {
    background: #334155;
    color: var(--accent);
    font-weight: bold;
    font-size: 0.75rem;
    padding: 0.25rem 0.55rem;
    border-radius: 6px;
    flex-shrink: 0;
    margin-top: 2px;
}
.card-content h3 {
    font-size: 0.95rem;
    margin-bottom: 0.2rem;
}
.card-content p {
    font-size: 0.8rem;
    color: #94a3b8;
    line-height: 1.4;
}
.card-content .tags {
    margin-top: 0.4rem;
    display: flex;
    gap: 0.3rem;
    flex-wrap: wrap;
}
.tag {
    font-size: 0.65rem;
    padding: 0.15rem 0.5rem;
    border-radius: 10px;
    font-weight: 600;
}
.tag-red { background: #7f1d1d20; color: #fca5a5; border: 1px solid #7f1d1d40; }
.tag-orange { background: #7c2d1220; color: #fdba74; border: 1px solid #7c2d1240; }
.tag-blue { background: #1e3a5f20; color: #93c5fd; border: 1px solid #1e3a5f40; }
.tag-green { background: #14532d20; color: #86efac; border: 1px solid #14532d40; }
.tag-purple { background: #3b076420; color: #c4b5fd; border: 1px solid #3b076440; }
.tools-section {
    margin-top: 2rem;
}
.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0.6rem;
}
.tool-card {
    background: var(--card-bg);
    border-radius: 8px;
    padding: 1rem;
    text-decoration: none;
    color: var(--text);
    border: 1px solid #334155;
    transition: all 0.2s;
    text-align: center;
    font-size: 0.9rem;
}
.tool-card:hover {
    border-color: var(--purple);
    background: #233044;
}
.tool-card .tool-icon { font-size: 1.5rem; display: block; margin-bottom: 0.3rem; }
.footer {
    text-align: center;
    padding: 2rem;
    color: #475569;
    font-size: 0.8rem;
    border-top: 1px solid #1e293b;
    margin-top: 2rem;
}
.footer a { color: var(--accent); text-decoration: none; }
</style>
</head>
<body>

<div class="warning-banner">
    ⚠️ 本靶场仅限本地授权安全测试与学习使用。禁止对未授权系统使用相关技术！所有漏洞均在模拟环境中复现。
</div>

<div class="header">
    <h1>🔐 VulnLogin-Lab</h1>
    <p class="subtitle">登录界面渗透测试靶场 —— 复现《登录界面的20种渗透思路》</p>
</div>

<div class="stats">
    <div class="stat-item">
        <div class="stat-num">20</div>
        <div class="stat-label">漏洞场景</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">3</div>
        <div class="stat-label">难度分类</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">PHP</div>
        <div class="stat-label">运行环境</div>
    </div>
</div>

<div class="container">

    <!-- 基础漏洞区 -->
    <div class="category">
        <div class="category-title">
            <span class="icon">📌</span> 一、基础漏洞区
            <span class="count">5个场景</span>
        </div>
        <div class="grid">
            <a href="/2.1-plaintext.php" class="card">
                <span class="card-num">2.1</span>
                <div class="card-content">
                    <h3>明文传输</h3>
                    <p>后端响应中返回明文密码，前端JS进行比对，抓包即可获取密码</p>
                    <div class="tags"><span class="tag tag-red">信息泄露</span><span class="tag tag-orange">抓包分析</span></div>
                </div>
            </a>
            <a href="/2.2-enum.php" class="card">
                <span class="card-num">2.2</span>
                <div class="card-content">
                    <h3>用户名可枚举</h3>
                    <p>根据"密码错误"与"用户不存在"的差异判断有效用户</p>
                    <div class="tags"><span class="tag tag-blue">信息收集</span><span class="tag tag-orange">爆破前置</span></div>
                </div>
            </a>
            <a href="/2.3-weakpass.php" class="card">
                <span class="card-num">2.3</span>
                <div class="card-content">
                    <h3>弱口令爆破</h3>
                    <p>无验证码、无频率限制，可使用Burp Suite对admin进行暴力破解</p>
                    <div class="tags"><span class="tag tag-red">暴力破解</span><span class="tag tag-orange">字典攻击</span></div>
                </div>
            </a>
            <a href="/2.4-sqli.php" class="card">
                <span class="card-num">2.4</span>
                <div class="card-content">
                    <h3>SQL注入万能密码</h3>
                    <p>使用 ' OR '1'='1 等Payload构造永真条件绕过登录</p>
                    <div class="tags"><span class="tag tag-red">SQL注入</span><span class="tag tag-purple">经典漏洞</span></div>
                </div>
            </a>
            <a href="/2.15-error-info.php" class="card">
                <span class="card-num">2.15</span>
                <div class="card-content">
                    <h3>错误信息泄露</h3>
                    <p>登录失败时暴露数据库连接信息、SQL语句、服务器版本</p>
                    <div class="tags"><span class="tag tag-blue">信息泄露</span><span class="tag tag-green">调试信息</span></div>
                </div>
            </a>
        </div>
    </div>

    <!-- 逻辑与流程绕过区 -->
    <div class="category">
        <div class="category-title">
            <span class="icon">🔄</span> 二、逻辑与流程绕过区
            <span class="count">8个场景</span>
        </div>
        <div class="grid">
            <a href="/2.7-fixed-captcha.php" class="card">
                <span class="card-num">2.7</span>
                <div class="card-content">
                    <h3>固定验证码</h3>
                    <p>验证码恒为固定值1234，获取后可编写脚本自动爆破</p>
                    <div class="tags"><span class="tag tag-orange">逻辑缺陷</span><span class="tag tag-blue">验证码绕过</span></div>
                </div>
            </a>
            <a href="/2.8-front-captcha.php" class="card">
                <span class="card-num">2.8</span>
                <div class="card-content">
                    <h3>前端校验验证码</h3>
                    <p>验证码仅在前端JS校验，后端不验证，抓包删除参数即可绕过</p>
                    <div class="tags"><span class="tag tag-red">前后端分离缺陷</span><span class="tag tag-blue">抓包绕过</span></div>
                </div>
            </a>
            <a href="/2.9-reset-pass.php" class="card">
                <span class="card-num">2.9</span>
                <div class="card-content">
                    <h3>任意用户重置密码</h3>
                    <p>重置密码接口未验证旧密码和用户身份，可直接修改任意账号</p>
                    <div class="tags"><span class="tag tag-red">高危漏洞</span><span class="tag tag-purple">权限绕过</span></div>
                </div>
            </a>
            <a href="/2.10-2fa-bypass.php" class="card">
                <span class="card-num">2.10</span>
                <div class="card-content">
                    <h3>双因素认证(2FA)绕过</h3>
                    <p>输入正确密码后直接访问success.php，跳过2FA验证步骤</p>
                    <div class="tags"><span class="tag tag-red">认证绕过</span><span class="tag tag-orange">逻辑漏洞</span></div>
                </div>
            </a>
            <a href="/2.16-param-pollution.php" class="card">
                <span class="card-num">2.16</span>
                <div class="card-content">
                    <h3>HTTP参数污染</h3>
                    <p>传递多个password参数，后端取最后一个值绕过验证</p>
                    <div class="tags"><span class="tag tag-blue">协议层攻击</span><span class="tag tag-purple">WAF绕过</span></div>
                </div>
            </a>
            <a href="/2.18-keyword-bypass.php" class="card">
                <span class="card-num">2.18</span>
                <div class="card-content">
                    <h3>关键词绕过</h3>
                    <p>添加 bypass=true 等调试参数直接绕过认证</p>
                    <div class="tags"><span class="tag tag-orange">后门漏洞</span><span class="tag tag-red">调试接口</span></div>
                </div>
            </a>
            <a href="/2.19-race-condition.php" class="card">
                <span class="card-num">2.19</span>
                <div class="card-content">
                    <h3>条件竞争</h3>
                    <p>高并发请求在计数器重置前同时通过验证</p>
                    <div class="tags"><span class="tag tag-red">高级漏洞</span><span class="tag tag-purple">并发攻击</span></div>
                </div>
            </a>
            <a href="/2.20-cookie-auth.php" class="card">
                <span class="card-num">2.20</span>
                <div class="card-content">
                    <h3>前端Cookie越权</h3>
                    <p>后端完全信任Cookie中的凭证，手动设置即可冒充任意用户</p>
                    <div class="tags"><span class="tag tag-red">权限绕过</span><span class="tag tag-blue">客户端信任</span></div>
                </div>
            </a>
        </div>
    </div>

    <!-- 高级利用区 -->
    <div class="category">
        <div class="category-title">
            <span class="icon">⚡</span> 三、高级利用区
            <span class="count">7个场景</span>
        </div>
        <div class="grid">
            <a href="/2.5-xss.php" class="card">
                <span class="card-num">2.5</span>
                <div class="card-content">
                    <h3>XSS攻击</h3>
                    <p>在用户名注入脚本，管理员查看登录日志时触发，窃取Cookie</p>
                    <div class="tags"><span class="tag tag-red">存储型XSS</span><span class="tag tag-purple">会话劫持</span></div>
                </div>
            </a>
            <a href="/2.6-session-fix.php" class="card">
                <span class="card-num">2.6</span>
                <div class="card-content">
                    <h3>会话固定</h3>
                    <p>登录前后Session ID不变，攻击者可预先设置ID等待受害者登录</p>
                    <div class="tags"><span class="tag tag-orange">会话管理</span><span class="tag tag-purple">中间人攻击</span></div>
                </div>
            </a>
            <a href="/2.11-jwt.php" class="card">
                <span class="card-num">2.11</span>
                <div class="card-content">
                    <h3>JWT漏洞 - None算法</h3>
                    <p>将签名算法改为None，篡改payload提升为admin权限</p>
                    <div class="tags"><span class="tag tag-red">令牌伪造</span><span class="tag tag-purple">权限提升</span></div>
                </div>
            </a>
            <a href="/2.12-ldap.php" class="card">
                <span class="card-num">2.12</span>
                <div class="card-content">
                    <h3>LDAP注入</h3>
                    <p>注入LDAP查询语法，构造永真条件绕过目录认证</p>
                    <div class="tags"><span class="tag tag-red">注入攻击</span><span class="tag tag-blue">目录服务</span></div>
                </div>
            </a>
            <a href="/2.13-cmd-exec.php" class="card">
                <span class="card-num">2.13</span>
                <div class="card-content">
                    <h3>命令执行</h3>
                    <p>用户名被拼接到系统命令中，通过管道符注入任意命令</p>
                    <div class="tags"><span class="tag tag-red">RCE</span><span class="tag tag-purple">高危漏洞</span></div>
                </div>
            </a>
            <a href="/2.14-lfi.php" class="card">
                <span class="card-num">2.14</span>
                <div class="card-content">
                    <h3>文件包含漏洞(LFI)</h3>
                    <p>通过theme参数的路径遍历读取任意文件</p>
                    <div class="tags"><span class="tag tag-red">路径遍历</span><span class="tag tag-blue">源码泄露</span></div>
                </div>
            </a>
            <a href="/2.17-json-inject.php" class="card">
                <span class="card-num">2.17</span>
                <div class="card-content">
                    <h3>JSON注入</h3>
                    <p>通过布尔值、额外属性、数组注入三种方式绕过JSON登录</p>
                    <div class="tags"><span class="tag tag-red">类型混淆</span><span class="tag tag-purple">API安全</span></div>
                </div>
            </a>
        </div>
    </div>

    <!-- 辅助页面 -->
    <div class="category tools-section">
        <div class="category-title">
            <span class="icon">🧰</span> 辅助页面
            <span class="count">3个</span>
        </div>
        <div class="tools-grid">
            <a href="/admin.php" class="tool-card">
                <span class="tool-icon">🖥️</span>模拟后台
            </a>
            <a href="/success.php" class="tool-card">
                <span class="tool-icon">✅</span>登录成功页
            </a>
            <a href="/2.1-plaintext.php" class="tool-card">
                <span class="tool-icon">🔑</span>明文传输(原始版)
            </a>
        </div>
    </div>
</div>

<div class="footer">
    <p>VulnLogin-Lab · 基于《登录界面的20种渗透思路》构建 · 仅供安全学习使用</p>
    <p style="margin-top:0.3rem;">参考原文：<a href="https://mp.weixin.qq.com/s/7kIGI5CyfDr8vBl_3Yfm7A" target="_blank">登录界面的20种渗透思路 —— 图文解析</a></p>
</div>

</body>
</html>
EOF