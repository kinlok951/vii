/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

function StartCreatAlbum(){var name=$("#name").val();var descr=$("#descr").val();if(name!=0){$("#name").css('background','#fff');$('#box_loading').show();ge('box_but').disabled=true;$.post('/index.php?go=albums&act=create',{name:name,descr:descr,privacy:$('#privacy').val()},function(data){$('#box_loading').hide();if(data=='no_name'){$('.err_red').show().text(lang_empty);ge('box_but').disabled=false}else if(data=='no'){$('.err_red').show().text(lang_nooo_er);ge('box_but').disabled=false}else if(data=='max'){Box.Close('albums');Box.Info('load_album',lang_dd2f_no,lang_max_albums,280)}else{Box.Close('albums');Page.Go(data)}})}else{$("#name").css('background','#ffefef');setTimeout("$('#name').css('background', '#fff').focus()",800);$('#box_loading').hide()}}