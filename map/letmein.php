<?php
header('Content-type:text/html;charset=utf-8');
require_once('cls_db.php');
session_start();

$db = new db();

if (isset($_POST['a']) && trim($_POST['a'] == 'ajaxCode')) {
    $mobile = isset($_POST['mobile']) ? addslashes(trim($_POST['mobile'])) : '';
    if ($mobile) {
        $sql = "SELECT id FROM u WHERE mobile='".$mobile."'";
        $row = $db->get_one($sql);
        $uid = $row['id'];
        if ($uid) {
            $rndCode = rand(1001, 9999);
            if ($rndCode) {
                $msg = '【大舟山】尊敬的用户，您好！您的登录验证码为'.$rndCode.'。本次登录时间：'.date("Y-m-d H:i:s");
                $data = array(
                    'mobile' => $mobile,
                    'msg' => $msg
                );
                $_SESSION['code'] = '';
                $_SESSION['code'] = $rndCode;
                
                $sms253 = new sendSMS253($data);
                $result = $sms253->send();
                if ($result == 1) {
                    $state = 1;
                    $msg = '验证码已发送!';
                } else {
                    $state = 0;
                    $msg = '验证码发送失败!';
                }
            } else {
                $state = 0;
                $msg = '生成随机密码失败!';
            }
        } else {
            $state = 0;
            $msg = '获取用户信息失败!';
            
        }
    } else {
        $state = 0;
        $msg = '请输入正确的手机号码!';
    }
    $d['state'] = $state;
    $d['msg'] = $msg;
    echo json_encode($d);
    exit;    
}

if (isset($_GET['q']) && intval($_GET['q']) == 1) {
    $_SESSION['user_name'] = '';
    $_SESSION['uid'] = '';
    $_SESSION['is_login'] = 0;
    session_unset();
    session_destroy();
    header('Location:login.html');
    exit;
}

/*if (isset($_SESSION['is_login']) && $_SESSION['is_login'] == 1)*/
if(true) {
    header('Location:naoh.php');
    exit;
} else {
    if (isset($_POST['a']) && trim($_POST['a'] == 'letmein')) {        
        $username = trim($_POST['username']);
        $pwd = trim($_POST['pwd']);
        $username = addslashes($username);
        $sql = "SELECT id FROM u WHERE username='".$username."'";
        $row = $db->get_one($sql);
        if ($row) {
            $uid = $row['id'];
            if ($pwd === $_SESSION['code']) {
                $_SESSION['is_login'] = 1;
                $_SESSION['username'] = $username;
                $_SESSION['uid'] = $uid;
                $_SESSION['code'] = '';
                header('Location:naoh.php');
                exit;
            } else {
                header('Location:error.html');
                exit;
            }
        } else {
            header('Location:error.html');
            exit;
        }
        
        /*
        $username = addslashes($username);
        $pwd = addslashes($pwd);
        $username = str_replace("_", "\_", $username);
        $pwd = str_replace("%", "\%", $pwd);
                
        if ($username && $pwd) {
            $sql = "SELECT s, pwd FROM u WHERE username='".$username."'";
            $row = $db->get_one($sql);
            $db_pwd = $row['pwd'];
            $s = $row['s'];
            if (md5($pwd.$s) === $db_pwd) {
                $_SESSION['is_login'] = 1;
                $_SESSION['username'] = $username;
                header('Location:naoh.php');
                exit;
            } else {
                header('Location:error.html');
                exit;
            }
        } else {
            header('Location:error.html');
            exit;
        }*/
    } else {
        header('Location:letmein.php');
        exit;
    }
}



