<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if(!defined('MOZG'))
	die('Hacking attempt!');



include __DIR__ .'/../functions.php';
/*function GetVar($v) {
	if(ini_get('magic_quotes_gpc'))
		return stripslashes($v) ;
	return $v;
}*/

$domain_cookie = explode (".", clean_url( $_SERVER['HTTP_HOST'] ));
$domain_cookie_count = count($domain_cookie);
$domain_allow_count = -2;

if($domain_cookie_count > 2){

	if(in_array($domain_cookie[$domain_cookie_count-2], array('com', 'net', 'org') )) 
		$domain_allow_count = -3;
		
	if($domain_cookie[$domain_cookie_count-1] == 'ua' ) 
		$domain_allow_count = -3;
		
	$domain_cookie = array_slice($domain_cookie, $domain_allow_count);
}

$domain_cookie = ".".implode(".", $domain_cookie);

define('DOMAIN', $domain_cookie);

/*function langdate($format, $stamp){
	global $langdate;
	return strtr(@date($format, $stamp), $langdate);
}*/
/*function navigation($gc, $num, $type){
	$page = ( isset( $_GET['page'] )&& !empty( $_GET['page'] ) ) ? intval( $_GET['page'] ) : 1;
	$gcount = $gc;
	$cnt = $num;
	$items_count = $cnt;
	$items_per_page = $gcount;
	$page_refers_per_page = 5;
	$pages = '';		
	$pages_count = ( ( $items_count % $items_per_page != 0 ) ) ? floor( $items_count / $items_per_page ) + 1 : floor( $items_count / $items_per_page );
	$start_page = ( $page - $page_refers_per_page <= 0  ) ? 1 : $page - $page_refers_per_page + 1;
	$page_refers_per_page_count = ( ( $page - $page_refers_per_page < 0 ) ? $page : $page_refers_per_page ) + ( ( $page + $page_refers_per_page > $pages_count ) ? ( $pages_count - $page )  :  $page_refers_per_page - 1 );
			
	if($page > 1)
		$pages .= '<a href="'.$type.($page-1).'">&laquo;</a>';
	else
		$pages .= '';
				
	if ( $start_page > 1 ) {
		$pages .= '<a href="'.$type.'1">1</a>';
		$pages .= '<a href="'.$type.( $start_page - 1 ).'">...</a>';
			
	}
					
	for ( $index = -1; ++$index <= $page_refers_per_page_count-1; ) {
		if ( $index + $start_page == $page )
			$pages .= '<span>' . ( $start_page + $index ) . '</span>';
		else 
			$pages .= '<a href="'.$type.($start_page+$index).'">'.($start_page+$index).'</a>';
	} 
			
	if ( $page + $page_refers_per_page <= $pages_count ) { 
		$pages .= '<a href="'.$type.( $start_page + $page_refers_per_page_count ).'">...</a>';
		$pages .= '<a href="'.$type.$pages_count.'">'.$pages_count.'</a>';	
	} 
				
	$resif = $cnt/$gcount;
	if(ceil($resif) == $page)
		$pages .= '';
	else
		$pages .= '<a href="'.$type.($page+1).'">&raquo;</a>';

	if ( $pages_count <= 1 )
		$pages = '';
		
		if($pages)
			return '<div class="nav">'.$pages.'</div>';
}*/
function echoheader($box_width = false){
    global $config, $logged, $admin_link, $user_info;

    if ($logged and $user_info['user_group'] == 1)
        $exit_link = '<a href="' . $admin_link . '?act=logout">Выход</a>';
    else
        $exit_link = '';

    if (!$box_width)
        $box_width = 600;

    $act = requestFilter('mod');
    if ($act == 'webstats')
        $box_width = 800;

    echo <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
<title>Vii Engine - Панель управления</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<style type="text/css" media="all">
html,body{font-size:11px;background: linear-gradient(#0d789c, #c8eeb1,white, white) repeat-x;font-family:Tahoma;line-height:17px;}
a{color:#4274a5;text-decoration:underline}
a:hover{color:#4274a5;text-decoration:none}
.box{margin:auto;width:{$box_width}px;background:#fff;box-shadow:0px 1px 4px 1px #cfcfcf;-moz-box-shadow:0px 1px 4px 1px #cfcfcf;-webkit-box-shadow:0px 1px 4px 1px #cfcfcf;-khtml-box-shadow:0px 1px 4px 1px #cfcfcf;padding:10px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;margin-bottom:5px}
.head{background: linear-gradient(#1993b0, #1993b0,#3db9c2) repeat-x;height:49px;border-top-left-radius:5px;-moz-top-left-border-radius:5px;-webkit-top-left-border-radius:5px;-khtml-top-left-border-radius:5px;margin:-10px;border-top-right-radius:5px;-moz-top-right-border-radius:5px;-webkit-top-right-border-radius:5px;-khtml-top-right-border-radius:5px;margin:-10px;margin-bottom:5px}
.logo{background:url("/system/inc/images/logo.png") no-repeat;width:133px;height:48px;margin-left:5px}
.h1{font-size:13px;font-weight:bold;color:#4274a5;margin-top:5px;margin-bottom:5px;padding-bottom:2px;border-bottom:1px solid #e5edf5;padding-left:2px}
.clr{clear:both}
.fl_l{float:left}
.fl_r{float:right}
.inp{border:0px;font-size:11px;padding:5px 10px 5px 10px;background:#fff;border:1px solid #ccc;color:#777;margin-top:10px;}
.inpu{width:200px;box-shadow:inset 0px 1px 3px 0px #d2d2d2;border:1px solid #ccc;padding:4px;border-radius:3px;font-size:11px;font-family:tahoma;margin-bottom:5px;-moz-box-shadow:inset 0px 1px 3px 0px #d2d2d2;-webkit-box-shadow:inset 0px 1px 3px 0px #d2d2d2}
textarea{width:300px;height:100px;}
.fllogall{color:#555;margin-left:2px;float:left;width:280px;padding-top:2px}
.oneb{float:left;width:300px;font-size:17px;font-weight:700;color:#777;margin-top:5px;padding-top:3px;height:70px}
.oneb img{float:left;margin-right:7px}
.oneb div{font-size:11px;font-weight:normal;line-height:14px;margin-left:68px;margin-top:5px}
.tmenu{background:#f5f5f5;padding:5px;margin-top:-5px;margin-left:-10px;margin-right:-10px}
.tmenu a{float:right;margin-left:10px}
.foot{clear:both;text-align:center;padding:5px;color:#999;margin:-10px;width:600px;margin:auto}
.foot a{color:#444}
.foot a:hover{text-decoration:none}
.mgcler{clear:both;border-bottom:1px dashed #ccc;margin-bottom:5px}
.nav{text-align:center;clear:both;margin-top:5px;margin-bottom:5px}
.nav a{padding:3px 5px 3px 5px;font-size:13px;border:1px solid #ddd;margin-right:3px;text-decoration:none}
.nav a:hover{background:#f0f0f0}
.nav span{padding:3px 5px 3px 5px;font-size:13px;border:1px solid #ddd;margin-right:3px;text-decoration:none;font-weight:bold}
.tempdata{height:500px;width:200px;overflow:scroll;border:1px solid #ddd;padding:5px}
.tefolfer{background:url("/system/inc/images/directory.png") no-repeat 3px 3px;padding:5px;height:15px;padding-left:24px;cursor:pointer;color:#444;padding-top:3px;font-family:Verdana;}
.tefolfer:hover{background:#c8e5f5 url("/system/inc/images/directory.png") no-repeat 3px 3px;}
.tetpl{background:url("/system/inc/images/html.png") no-repeat 3px 3px;padding:5px;height:15px;padding-left:24px;padding-top:3px;cursor:pointer;color:#444;font-family:Verdana;}
.tetpl:hover{background:#c8e5f5 url("/system/inc/images/html.png") no-repeat 3px 3px;}
.tecss{background:url("/system/inc/images/css.png") no-repeat 3px 3px;padding:5px;height:15px;padding-top:3px;padding-left:24px;cursor:pointer;color:#444;font-family:Verdana;}
.tecss:hover{background:#c8e5f5 url("/system/inc/images/css.png") no-repeat 3px 3px;}
.tejs{background:url("/system/inc/images/script.png") no-repeat 3px 3px;padding:5px;height:15px;padding-top:3px;padding-left:24px;cursor:pointer;color:#444;font-family:Verdana;}
.tejs:hover{background:#c8e5f5 url("/system/inc/images/script.png") no-repeat 3px 3px;}
.edittable{height:490px;width:655px;border:1px solid #ddd;padding:10px;margin-left:10px}
.ftext{height:420px;width:645px;border:1px solid #ddd;line-height: 155%;margin-top:10px;padding:4px;font-family:verdana;font-size:12px;-moz-box-shadow:inset 0px 1px 3px 0px #d2d2d2;-webkit-box-shadow:inset 0px 1px 3px 0px #d2d2d2;box-shadow:inset 0px 1px 3px 0px #d2d2d2}
#loading_text{color:#fff;position:relative;background: url("/system/inc/images/showb.png");width:250px;margin:auto;margin-top: 250px;padding:10px;font-size:11px;font-family:Verdana;border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;text-align:center;}
#loading{z-index:100; position:fixed; padding:0; margin:0 auto; height:100%; min-height:100%; width:100%; overflow:hidden; display:none; left:0px; right:0px; bottom:0px; top:0px;background:url("../images/spacer.gif");}
</style>
<div class="box clr">
<div class="head"><a href="{$admin_link}"><div class="logo"></div></a></div>
HTML;
}
function echohtmlstart($title){
	echo <<<HTML
<div class="h1" style="margin-top:10px">{$title}</div>
HTML;
}
function echohtmlend()
{
    global $admin_link;

    $admin_link = $admin_link ?? '';
    if (Registry::get('logged')) {
        $stat_lnk = "<a href=\"{$admin_link}?mod=stats\" style=\"margin-right:10px\">статистика</a>";
        $exit_lnk = "<a href=\"{$admin_link}?act=logout\">выйти</a>";
    } else {
        $stat_lnk = null;
        $exit_lnk = null;
    }

    echo <<<HTML
<div class="clr"></div>
</div>
<div class="clr"></div>
<div class="foot"><div style="margin-bottom:-10px">
<a href="{$admin_link}" style="margin-right:10px">главная</a>
{$stat_lnk}
<a href="/" style="margin-right:10px" target="_blank">просмотр сайта</a>
{$exit_lnk}
</div>
<br />Vii Engine<br /></div>
</body>
</html>
HTML;
}
/*function msgbox($title, $text, $link = false){
	echoheader();
	echohtmlstart($title);
	echo '<center>'.$text.'<br /><a href="'.$link.'">Вернуться назад</a></center>';
	echohtmlend();
}*/
function echoblock($title, $description, $link, $icon){
	global $admin_link;
	
	echo <<<HTML
<a href="{$admin_link}?mod={$link}">
<div class="oneb">
<img src="/system/inc/images/{$icon}.png" alt="" title="" />{$title}
<div>{$description}</div>
</div>
</a>
HTML;
}
function htmlclear(){
	echo '<div class="clr"></div>';
}
/*function myBr($source){
	$find[] = "'\r'";
	$replace[] = "<br />";
	
	//$find[] = "'\n'";
	//$replace[] = "<br />";

	$source = preg_replace($find, $replace, $source);
	
	return $source;
}*/
/*function myBrRn($source){

	$find[] = "<br />";
	$replace[] = "\r";
	$find[] = "<br />";
	$replace[] = "\n";
	
	$source = str_replace($find, $replace, $source);
	
	return $source;
}*/

function ajax_utf8($source){
	return iconv('utf-8', 'windows-1251', $source);
}
/*function mozg_clear_cache_file($prefix) {
	@unlink(ENGINE_DIR.'/cache/'.$prefix.'.tmp');
}*/
/*function mozg_clear_cache(){
	$fdir = opendir(ENGINE_DIR.'/cache/');
	
	while($file = readdir($fdir))
		if($file != '.' and $file != '..' and $file != '.htaccess' and $file != 'system')
			@unlink(ENGINE_DIR.'/cache/'.$file);
}*/
/*function mozg_mass_clear_cache_file($prefix){
	$arr_prefix = explode('|', $prefix);
	foreach($arr_prefix as $file)
		@unlink(ENGINE_DIR.'/cache/'.$file.'.tmp');
}*/
function convert_unicode($t, $to = 'windows-1251') {
	$to = strtolower($to);
	if($to == 'utf-8'){
		return $t;
	} else {
		if(function_exists('iconv')) $t = iconv("UTF-8", $to . "//IGNORE", $t);
		else $t = "The library iconv is not supported by your server";
	}
	return $t;
}
function formatsize($file_size){
	if($file_size >= 1073741824){
		$file_size = round($file_size / 1073741824 * 100 ) / 100 ." Gb";
	} elseif($file_size >= 1048576){
		$file_size = round($file_size / 1048576 * 100 ) / 100 ." Mb";
    } elseif ($file_size >= 1024) {
        $file_size = round($file_size / 1024 * 100) / 100 . " Kb";
    } else {
        $file_size = $file_size . " b";
    }
    return $file_size;
}

function system_mozg_clear_cache_file($prefix)
{
    Filesystem::delete(ENGINE_DIR . '/cache/system/' . $prefix . '.php');
}

/**
 * @throws JsonException
 */
function compileAdmin($tpl): int
{
    $tpl->load_template('main.tpl');
    $config = settings_load();
    $admin_link = $config['home_url'] . $config['admin_index'];
    if (Registry::get('logged')) {
        $stat_lnk = "<a href=\"{$admin_link}?mod=stats\" onclick=\"Page.Go(this.href); return false;\" style=\"margin-right:10px\">статистика</a>";
        $exit_lnk = "<a href=\"{$admin_link}?act=logout\" onclick=\"Page.Go(this.href); return false;\">выйти</a>";
    } else {
        $stat_lnk = '';
        $exit_lnk = '';
    }

    $box_width = 600;

    $act = requestFilter('mod');
    if ($act == 'webstats' || $act == 'users')
        $box_width = 800;

    $tpl->set('{admin_link}', $admin_link);
    $tpl->set('{box_width}', $box_width);
    $tpl->set('{stat_lnk}', $stat_lnk);
    $tpl->set('{exit_lnk}', $exit_lnk);
    $tpl->set('{content}', $tpl->result['content']);
    $tpl->compile('main');
    if (requestFilter('ajax') == 'yes') {

        $metatags['title'] = $metatags['title'] ?? 'Панель управления';

        $result_ajax = array(
            'title' => $metatags['title'],
            'content' => $tpl->result['info'] . $tpl->result['content']
        );

//        $result_ajax = <<<HTML
//<script type="text/javascript">
//document.title = '{$metatags['title']}';
//</script>
//{$tpl->result['info']}{$tpl->result['content']}
//HTML;
        _e_json($result_ajax);
//        echo $result_ajax;
        return 1;
    } else {
        return print($tpl->result['main']);
    }
}

function initAdminTpl(): Templates
{
    $tpl = new Templates();
    $tpl->dir = ADMIN_DIR . '/tpl/';
    define('TEMPLATE_DIR', $tpl->dir);
//    $_DOCUMENT_DATE = false;
    return $tpl;
}
