<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG')) die('Hacking attempt!');
$row = $db->super_query("SELECT user_email, user_name, user_lastname, user_password FROM `users` WHERE user_id = '" . $user_info['user_id'] . "'");
//Если сохраянем
if (isset($_POST['save'])) {
    $old_pass = md5(md5(stripslashes($_POST['old_pass'])));
    $new_pass = md5(md5(stripslashes($_POST['new_pass'])));
    $user_name = requestFilter('name', 25000, true);
    $user_lastname = requestFilter('lastname', 25000, true);
    $user_email = requestFilter('email', 25000, true);
    $errors = array();
    //Проверка имени
    if (isset($user_name)) {
        if (strlen($user_name) >= 2) {
            if (!preg_match("/^[a-zA-Zа-яА-Я]+$/iu", $user_name)) $errors[] = 'Введите имя';
        } else $errors[] = 'Введите имя';
    } else $errors[] = 'Введите имя';
    //Проверка фамилии
    if (isset($user_lastname)) {
        if (strlen($user_lastname) >= 2) {
            if (!preg_match("/^[a-zA-Zа-яА-Я]+$/iu", $user_lastname)) $errors[] = 'Введите фамилию';
        } else $errors[] = 'Введите фамилию';
    } else $errors[] = 'Введите фамилию';
    //Проверка E-mail
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите коректный e-mail адрес';
    //Если меняем пароль
    if ($_POST['old_pass']) if ($old_pass == $row['user_password']) $newPassOk = true;
    else $errors[] = 'Старый пароль введен неправильно';
    foreach ($errors as $er) if ($er) $all_er.= '<li>' . $er . '</li>';
    if ($all_er) msgbox('Ошибка', $all_er, '?mod=mysettings');
    else {
        if ($newPassOk)
            $db->query("UPDATE `users` SET user_name = '" . $user_name . "', user_lastname = '" . $user_lastname . "', user_email = '" . $user_email . "', user_search_pref = '" . $user_name . " " . $user_lastname . "' WHERE user_id = '" . $user_info['user_id'] . "'");
        else
            $db->query("UPDATE `users` SET user_name = '" . $user_name . "', user_lastname = '" . $user_lastname . "', user_email = '" . $user_email . "', user_password = '" . $new_pass . "', user_search_pref = '" . $user_name . " " . $user_lastname . "' WHERE user_id = '" . $user_info['user_id'] . "'");
        //clear cache
        mozg_clear_cache_file('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);
        mozg_clear_cache();
        msgbox('Изменения сохранены', 'Ваша персональная информация была успешно сохранена', '?mod=mysettings');
    }
} else {
    echoheader();
    echohtmlstart('Редактирование собственного профиля');
    echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form method="POST" action="">

<div class="fllogall">E-mail:</div><input type="text" name="email" class="inpu" value="{$row['user_email']}" /><div class="mgcler"></div>

<div class="fllogall">Имя:</div><input type="text" name="name" class="inpu" value="{$row['user_name']}" /><div class="mgcler"></div>

<div class="fllogall">Фамилия:</div><input type="text" name="lastname" class="inpu" value="{$row['user_lastname']}" /><div class="mgcler"></div>

<div class="fllogall">Старый пароль:</div><input type="password" name="old_pass" class="inpu" /><div class="mgcler"></div>

<div class="fllogall">Новый пароль:</div><input type="text" name="new_pass" class="inpu" /><div class="mgcler"></div>

<div class="fllogall">&nbsp;</div><input type="submit" value="Сохранить" name="save" class="inp" style="margin-top:0px" />

</form>
HTML;
    htmlclear();
    echohtmlend();
}