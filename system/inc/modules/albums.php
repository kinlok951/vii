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

echoheader();

$se_uid = intFilter('se_uid');
if (!$se_uid) $se_uid = '';

$se_user_id = intFilter('se_user_id');
if (!$se_user_id) $se_user_id = '';

$se_name = requestFilter('se_name', 25000, true);

$sort = $sort ?? null;
$where_sql = $where_sql ?? '';
$where_sql_2 = $where_sql_2 ?? '';
$admin_index = $admin_index ?? '';

if ($se_uid or $sort or $se_name or $se_user_id) {
    if ($se_uid) $where_sql .= "AND aid = '" . $se_uid . "' ";
    if ($se_user_id) $where_sql_2 .= "AND tb1.user_id = '" . $se_user_id . "' ";
    $query = strtr($se_name, array(' ' => '%')); //Замеянем пробелы на проценты чтоб поиск был точнее
    if ($se_name)
        $where_sql .= "AND name LIKE '%" . $query . "%' ";
}

//Выводим список людей
$page = intFilter('page');
$gcount = 20;
$limit_page = ($page - 1) * $gcount;

$sql_ = $db->super_query("SELECT tb1.user_id, name, adate, aid, photo_num, comm_num, tb2.user_name FROM `albums` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id {$where_sql} {$where_sql_2} ORDER by `adate` DESC LIMIT {$limit_page}, {$gcount}", true);

//Кол-во людей считаем
$where_sql_2 = str_replace('tb1.', '', $where_sql_2);
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `albums` WHERE aid != '' {$where_sql} {$where_sql_2}");

echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form action="{$admin_index}" method="GET">

<input type="hidden" name="mod" value="albums" />

<div class="fllogall">Поиск по ID альбома:</div>
 <input type="text" name="se_uid" class="inpu" value="{$se_uid}" />
<div class="mgcler"></div>

<div class="fllogall">Поиск по названию:</div>
 <input type="text" name="se_name" class="inpu" value="{$se_name}" />
<div class="mgcler"></div>

<div class="fllogall">Поиск по ID создателя:</div>
 <input type="text" name="se_user_id" class="inpu" value="{$se_user_id}" />
<div class="mgcler"></div>

<div class="fllogall">&nbsp;</div>
 <input type="submit" value="Найти" class="inp" style="margin-top:0px" />

</form>
HTML;

echohtmlstart('Список альбомов (' . $numRows['cnt'] . ')');

$users = '';
foreach ($sql_ as $row) {
    $row['name'] = stripslashes($row['name']);
    $row['adate'] = langdate('j M Y в H:i', strtotime($row['adate']));
    $users .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:130px;text-align:center;"><a href="/u{$row['user_id']}" target="_blank">{$row['user_name']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:299px;text-align:center;margin-left:1px" title="Комментариев: {$row['comm_num']}, Фотографий: {$row['photo_num']}"><a href="/albums/view/{$row['aid']}" target="_blank">{$row['name']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:110px;text-align:center;margin-left:1px">{$row['adate']}</div>
<div style="background:#fff;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-left:1px"><input type="checkbox" name="massaction_list[]" style="float:right;" value="{$row['aid']}" /></div>
<div class="mgcler"></div>
HTML;
}

echo <<<HTML
<script language="text/javascript" type="text/javascript">
function ckeck_uncheck_all() {
    var frm = document.edit;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
}
</script>
<form action="?mod=massaction&act=albums" method="post" name="edit">
<div style="background:#f0f0f0;float:left;padding:5px;width:130px;text-align:center;font-weight:bold;margin-top:-5px">Владелец</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:299px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Название</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:110px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Дата создания</div>
<div style="background:#f0f0f0;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px"><input type="checkbox" name="master_box" title="Выбрать все" onclick="javascript:ckeck_uncheck_all()" style="float:right;"></div>
<div class="clr"></div>
{$users}
<div style="float:right">
<select name="mass_type" class="inpu" style="width:260px">
 <option value="0">- Действие -</option>
 <option value="1">Удалить альбомы</option>
 <option value="2">Удалить фотографии из альбома</option>
</select>
<input type="submit" value="Выолнить" class="inp" />
</div>
</form>
<div class="clr"></div>
HTML;

$query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);
echo navigation($gcount, $numRows['cnt'], '?' . $query_string . '&page=');

htmlclear();
echohtmlend();