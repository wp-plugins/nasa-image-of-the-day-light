<?php

/*
Plugin Name: Nasa Image of the Day Light
Description: Displays the latest "Nasa Image of the Day" image on your blog.
Version: 1.0
Author: junatik
License: GPL2
*/

define(niod_TITLE, 'Nasa Image of the Day');
define(niod_IMAGE_WIDTH, '220');
define(niod_DESC_CHARS, '500');
define(niod_NASA_RSS, 'http://www.nasa.gov/rss/image_of_the_day.rss');
define(niod_DIR, basename(dirname(__FILE__)));
define(niod_IMG_CACHE, ABSPATH.PLUGINDIR.'/'.niod_DIR.'/imgcache/');
include_once(ABSPATH . WPINC . '/rss.php');

function niod_GetImage($args)  {
  $wpurl = get_bloginfo('wpurl');
  $options = get_option('niod_widget');
  if($options == false)  {
    $options['niod_widget_url_title'] = niod_TITLE;
    $options['niod_image_width'] = niod_IMAGE_WIDTH;
    $options['niod_desc_chars'] = niod_DESC_CHARS;
    $options['niod_image_title'] = true;
  }
  $rss = fetch_rss(niod_NASA_RSS);
  $img_url = $rss->image['url'];
  $image = basename($img_url);
  $do = get_option('img_cache');
  $img_src = niod_IMG_CACHE.$image;
  if($image != $do)  {
    file_put_contents($img_src, file_get_contents($img_url));
    $tmb_file = niod_IMG_CACHE.'tmb_'.$do; 
    $org_file = niod_IMG_CACHE.$do;
    if(is_file($tmb_file))  { unlink($tmb_file); }
    if(is_file($org_file))  { unlink($org_file); }
    update_option('img_cache', $image);  
    update_option('img_title', $rss->items[0]['title']);
    update_option('img_link', $rss->items[0]['link']);
    update_option('img_description', $rss->items[0]['description']);
  }
  if($_POST['niod_image_width'] != $options['niod_image_width'])  {
    resize_image($img_src, $options['niod_image_width']);
  }
  $tmb = '/tmb_'.basename($img_src);
  $output = '<div id=thumbnail><a title="'.get_option('img_title').'" rel=lightbox[roadtrip] href="'.$wpurl.'/'.PLUGINDIR.'/'.niod_DIR.'/imgcache/'.basename($img_src).'"><img src="'.$wpurl.'/'.PLUGINDIR.'/'.niod_DIR.'/imgcache'.$tmb.'" border="0" style="border:1px solid #000" alt="'.get_option('img_title').'" /></a></div>';
  $img_title_html = '<a href="'.get_option('img_link').'" target="_blank">'.get_option('img_title').'</a>';
  if($options['niod_image_desc'])  {
    $output .= '<p>'.cut_text(get_option('img_description'), $options['niod_desc_chars']).'<br><a href="'.get_option('img_link').'" target="_blank">read more &raquo;</a></p>';
  }
  $lightbox = '<script type="text/javascript" src="'.$wpurl.'/'.PLUGINDIR.'/'.niod_DIR.'/js/prototype.js"></script>
  <script type="text/javascript" src="'.$wpurl.'/'.PLUGINDIR.'/'.niod_DIR.'/js/scriptaculous.js?load=effects,builder"></script>
  <script type="text/javascript" src="'.$wpurl.'/'.PLUGINDIR.'/'.niod_DIR.'/js/lightbox.js"></script>
  <link rel="stylesheet" href="'.$wpurl.'/'.PLUGINDIR.'/'.niod_DIR.'/css/lightbox.css" type="text/css" media="screen" />';
  $title = $options['niod_widget_url_title'];
  extract($args);	
  echo $before_widget;
  echo $before_title . $title . $after_title;
  echo $lightbox;
  if($options['niod_image_title'])  { echo $img_title_html; }
  echo $output;
  echo $after_widget;
}

function resize_image($img_src,$w)  {
  require_once('class.thumb.php');
  $img = new Thumb;
  $img->resize_image($img_src,dirname($img_src).'/tmb_'.basename($img_src),$w);
}

function cut_text($str,$length)  {
  while(substr($str,$length,1) !== " ")  {
    substr($str,$length,1);
    $length = $length - 1;
  }
  $str = substr($str,0,$length);
  $str .= ' ...';
  return $str;
}

function niod_widget_Admin()  {
  $options = $newoptions = get_option('niod_widget');	
  if($options == false)  {
    $newoptions['niod_widget_url_title'] = niod_TITLE;
    $newoptions['niod_image_width'] = niod_IMAGE_WIDTH;
    $newoptions['niod_desc_chars'] = niod_DESC_CHARS;
    $newoptions['niod_image_title'] =  $options['niod_image_title'] ? 'checked="checked"' : '';
    $newoptions['niod_image_desc'] =  $options['niod_image_desc'] ? 'checked="checked"' : '';
  }
  if($_POST['niod_widget-submit'])  {
    $newoptions['niod_widget_url_title'] = strip_tags(stripslashes($_POST['niod_widget_url_title']));
    $newoptions['niod_image_width'] = $_POST['niod_image_width'];
    $newoptions['niod_desc_chars'] = $_POST['niod_desc_chars'];
    $newoptions['niod_image_title'] = $_POST['niod_image_title'] ? 'checked="checked"' : '';
    $newoptions['niod_image_desc'] = $_POST['niod_image_desc'] ? 'checked="checked"' : '';
  }	
  if($options != $newoptions)  {
    $options = $newoptions;		
    update_option('niod_widget', $options);
  }
  $niod_widget_url_title = wp_specialchars($options['niod_widget_url_title']);
  $niod_image_width = $options['niod_image_width'];
  $niod_desc_chars = $options['niod_desc_chars'];
  $niod_image_title = $options['niod_image_title'];
  $niod_image_desc = $options['niod_image_desc'];

?>
<form method="post" action="">	
<p><label for="niod_widget_url_title"><?php _e('Title'); ?>: <input style="width: 180px;" id="niod_widget_url_title" name="niod_widget_url_title" type="text" value="<?php echo $niod_widget_url_title; ?>" /></label></p>
<p><label for="niod_image_width"><?php _e('Image Width'); ?>: <input id="niod_image_width" name="niod_image_width" size="3" maxlength="3" type="text" value="<?php echo $niod_image_width?>" /></label></p>
<p><label for="niod_image_title"><?php _e('Show Image Title'); ?>: <input id="niod_image_title" name="niod_image_title" type="checkbox" <?php echo $niod_image_title?> /></label></p>
<p><label for="niod_image_desc"><?php _e('Show Description'); ?>: <input id="niod_image_desc" name="niod_image_desc" type="checkbox" <?php echo $niod_image_desc?> /></label></p>
<p><label for="niod_desc_chars"><?php _e('Symbols in description'); ?>: <input id="niod_desc_chars" name="niod_desc_chars" size="3" maxlength="3" type="text" value="<?php echo $niod_desc_chars?>" /></label></p>
<br clear='all'></p>
<input type="hidden" id="niod_widget-submit" name="niod_widget-submit" value="1" />	
</form>
<?php
}
function niod_Init()  {
  register_sidebar_widget(__(niod_TITLE), 'niod_GetImage');
  register_widget_control(__(niod_TITLE), 'niod_widget_Admin', 250, 250);
}
add_action("plugins_loaded", "niod_Init");

?>
