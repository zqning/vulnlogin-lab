<?php
// 模拟数据库用户表（多个漏洞场景共用）
$users = [
    'admin' => 'Admin@2024!',
    'user'  => 'User#1234',
    'test'  => 'TestP@ss1',
    'guest' => 'guest123',
];

// 模拟数据库查询函数
function getUser($username) {
    global $users;
    return isset($users[$username]) ? $users[$username] : false;
}

function userExists($username) {
    global $users;
    return isset($users[$username]);
}
?>