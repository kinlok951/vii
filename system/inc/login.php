<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG'))
    die('Hacking attempt!');

header('Content-type: text/html; charset=utf-8');

$db = require_once ENGINE_DIR . '/data/db.php';
Registry::set('db', $db);

$_IP = $_SERVER['REMOTE_ADDR'];
$_BROWSER = $_SERVER['HTTP_USER_AGENT'];

//Если делаем выход
if (isset($_GET['act']) and $_GET['act'] == 'logout') {
    set_cookie("user_id", "", 0);
    set_cookie("password", "", 0);
    set_cookie("hid", "", 0);
    unset($_SESSION['user_id']);
    @session_destroy();
    @session_unset();
    $logged = false;
    Registry::set('logged', false);
    $user_info = array();
    header("Location: {$admin_link}");
    die();
}

//Если есть данные сесии
if (isset($_SESSION['user_id']) > 0) {
    $logged = true;
    Registry::set('logged', true);
    $logged_user_id = intval($_SESSION['user_id']);
    $user_info = $db->super_query("SELECT user_id, user_email, user_group, user_password FROM `users` WHERE user_id = '" . $logged_user_id . "'");

    //Если есть данные о сесии, но нет инфы о юзере, то выкидываем его
    if (!$user_info['user_id'])
        header("Location: {$admin_link}?act=logout");

//Если есть данные о COOKIE то проверяем
} elseif (isset($_COOKIE['user_id']) > 0 and $_COOKIE['password'] and $_COOKIE['hid']) {
    $cookie_user_id = intval($_COOKIE['user_id']);
    $user_info = $db->super_query("SELECT user_id, user_email, user_group, user_password, user_hid FROM `users` WHERE user_id = '" . $cookie_user_id . "' AND user_group = '1'");

    //Если пароль и HID совпадает то пропускаем
    if ($user_info['user_password'] == $_COOKIE['password'] and $user_info['user_hid'] == $_COOKIE['password'] . md5(md5($_IP))) {
        $_SESSION['user_id'] = $user_info['user_id'];

        //Вставляем лог в бд
        $db->query("UPDATE `log` SET browser = '" . $_BROWSER . "', ip = '" . $_IP . "' WHERE uid = '" . $user_info['user_id'] . "'");

        $logged = true;
        Registry::set('logged', true);
    } else {
        $user_info = array();
        $logged = false;
        Registry::set('logged', false);
    }
} else {
    $user_info = array();
    $logged = false;
    Registry::set('logged', false);
}

//Если данные поступили через пост и пользователь не авторизован
if (isset($_POST['log_in']) and !isset($_SESSION['user_id'])) {

    //Приготавливаем данные
    $email = requestFilter('email');
    $password = stripslashes($_POST['pass']);

    //Проверяем правильность e-mail
    if (!preg_match('/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i', $email)) {
        $error_log = 'Доступ отключён!';
    } else {

        //Считаем кол-во символов в пароле и email
        if (strlen($password) >= 0 and strlen($email) > 0) {
            $md5_pass = md5(md5($password));
            $check_user = $db->super_query("SELECT user_id FROM `users` WHERE user_email = '" . $email . "' AND user_password = '" . $md5_pass . "' AND user_group = 1");

            //Если есть юзер то пропускаем
            if ($check_user) {
                //Hash ID
                $hid = $md5_pass . md5(md5($_IP));

                //Устанавливаем в сессию ИД юзера
                $_SESSION['user_id'] = intval($check_user['user_id']);

                //Обновляем хэш входа
                $db->query("UPDATE `users` SET user_hid = '" . $hid . "' WHERE user_id = '" . $check_user['user_id'] . "'");

                //Записываем COOKIE
                set_cookie("user_id", intval($check_user['user_id']), 365);
                set_cookie("password", $md5_pass, 365);
                set_cookie("hid", $hid, 365);

                header("Location: {$admin_link}");
            } else
                $error_log = 'Доступ отключён!';
        } else
            $error_log = 'Доступ отключён!';
    }
}

if (!$logged) {
    echoheader();
    echohtmlstart('Вход в панель управления');
    echo <<<HTML
<form method="POST" action="">
 <div class="fllogall">E-mail:</div><input type="text" name="email" class="inpu" />&nbsp; <font color="red">{$error_log}</font>
 <div class="mgcler"></div>
 <div class="fllogall">Пароль:</div><input type="password" name="pass" class="inpu" />
 <div class="mgcler"></div>
 <div class="fllogall">&nbsp;</div><input type="submit" class="inp fl_l" name="log_in" value="Войти" style="margin-top:5px" />
</form>
<div class="clear"></div>
HTML;
    echohtmlend();
} else {
    if ($user_info['user_group'] == 1)
        include ADMIN_DIR . '/mod.php';
    else
        msgbox('Информация', 'У вас недостаточно прав для просмотра этого раздела. <a href="' . $admin_link . '?act=logout">Выйти</a>', '');
}