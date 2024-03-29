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

NoAjaxQuery();

if (Registry::get('logged')) {

    $act = requestFilter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {

        //################### Загрузка плей листа ###################//
        default:

            //Если поиск
            $query = requestFilter('query');
            $query = strtr($query, array(' ' => '%')); //Заменяем пробелы на проценты чтоб поиск был точнее
            $do_load = intFilter('doload');

            $get_user_id = intFilter('get_user_id');
            if ($get_user_id == $user_id or !$get_user_id)
                $get_user_id = $user_id;

            if (!empty($query)) {

                $sql_query = "WHERE MATCH (name, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR name LIKE '%{$query}%'";
                $search = true;

            } else {

                $sql_query = "WHERE auser_id = '{$get_user_id}'";
                $search = false;

            }

            //Выводим из БД
            $limit_select = 20;
            $page_cnt = intFilter('page_cnt');
            if ($page_cnt > 0)
                $page_cnt = $page_cnt * $limit_select;
            else
                $page_cnt = 0;

            $sql_ = $db->super_query("SELECT aid, url, artist, name FROM `audio` {$sql_query} ORDER by `adate` DESC LIMIT {$page_cnt}, {$limit_select}", true);

            //Если есть отвеот из БД
            if ($sql_) {

                $jid = $page_cnt;

                $tpl->load_template('audio_player/track.tpl');
                foreach ($sql_ as $row) {

                    $jid++;
                    $tpl->set('{jid}', $jid);

                    $tpl->set('{aid}', $row['aid']);
                    $tpl->set('{url}', $row['url']);
                    $tpl->set('{artist}', stripslashes($row['artist']));
                    $tpl->set('{name}', stripslashes($row['name']));

                    if ($get_user_id == $user_id and !$search) {

                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                        $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");

                    } else {

                        $tpl->set('[not-owner]', '');
                        $tpl->set('[/not-owner]', '');
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");

                    }

                    $tpl->compile('audios');

                }

                if (!$page_cnt and !$do_load) {

                    $tpl->load_template('audio_player/player.tpl');

                    $tpl->set('{audios}', $tpl->result['audios']);
                    $tpl->set('{user-id}', $user_id);

                    if ($jid == $limit_select) $tpl->set('{jQbut}', '');
                    else $tpl->set('{jQbut}', 'no_display');

                    $tpl->compile('content');

                } else
                    $tpl->result['content'] = $tpl->result['audios'];

            } else
                if ($do_load and !$page_cnt) {

                    $query = str_replace('%', ' ', $query);

                    $tpl->result['content'] = '<div class="info_center" style="padding-top:145px;padding-bottom:125px">По запросу <b>' . $query . '</b> не найдено ни одной аудиозаписи.</div>';

                } else
                    $config = settings_get();
                    if (!$page_cnt)
                        $tpl->result['content'] = '<div class="info_center" style="padding-top:145px;padding-bottom:125px"><center><img src="/templates/' . $config['temp'] . '/images/snone.png" style="marign-bottom:60px;margin-top:-80px" /></center><div>Здесь Вы можете хранить Ваши аудиозаписи.<br />Для того, чтобы загрузить Вашу первую аудиозапись, <a href="/audio17" onClick="audio.addBox(1); return false;">нажмите здесь</a>.</div></div>';

            AjaxTpl($tpl);

    }

    $tpl->clear();
    $db->free();

}

