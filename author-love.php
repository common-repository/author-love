<?php
/*
Plugin Name: * author love
Plugin URI: http://digidownload.nl
Description: Finally ! A rating plugin for AUTHORS. Lets your visitors love your authors with ajax hearts and rating system. Show if authors are trustworthy, good reads, professionals in their fields or if they are a bit shady. See it in use at <a href='http://digidownload.nl'>digidownload.nl</a>
Author: pete scheepens
Author URI: http://digidownload.nl
Version: 1.7
Contact developer Pete Scheepens at info-at-portaljumper-dot-com
 */
 
add_action("wp_ajax_al_vote", "al_vote_func");
add_action("wp_ajax_nopriv_al_vote", "al_vote_func");

// create/update database tables
register_activation_hook(__FILE__,'al_db_init');

// create database
function al_db_init() {
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql2 = "CREATE TABLE " .$wpdb->prefix . "author_love (
			`id` int(11) NOT NULL auto_increment,
			`author_id` int(11) NOT NULL,
			`ip_address` varchar(50) NOT NULL,
			`rating` int(11) NOT NULL,
			`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
			PRIMARY KEY  (`id`), UNIQUE ( `author_id` , `ip_address`)			
		) AUTO_INCREMENT=1 ;";	
		
	dbDelta($sql2);	

al_check_options();
}

// fill options with default values
function al_check_options() {
$checkoptions = get_option('author_love_opts','');
   // fill defaults if empty
if (empty($checkoptions))
   {
   $aloptions['location'] = "br";
   $aloptions['amount'] = "6";
   $aloptions['image'] = "stars";
   $aloptions["title"] = '';
   $aloptions["single"] = 'no';
   $aloptions['trans_author'] = 'Author';
   $aloptions['trans_rating'] = 'Rating :';
   $aloptions['trans_votes'] = 'vote(s)';
   $aloptions['trans_yav'] = 'You have already voted!';
   update_option('author_love_opts', $aloptions);	
   }
}


// load css & js
function moomoo_load() {
wp_enqueue_script('jratings',plugins_url('/jquery/jRating.jquery.js', __FILE__));
wp_enqueue_style('al_css',plugins_url('/jquery/jRating.jquery.css', __FILE__)); 
} 

function jc_load() {	
  wp_enqueue_script('jquery');
}  

add_action('wp_enqueue_scripts', 'jc_load'); 
add_action('wp_footer', 'moomoo_load');

// insert the div block
add_filter('the_content', 'show_love');

function show_love ($content) {

$singlecheck = 'if (is_single() ) ';
$aloptions = get_option('author_love_opts');
if ($aloptions["single"] == "yes") $show = 1; else $show = '';
if ($aloptions['location'] == 'tl')  $display = return_author_love('0','left',$aloptions["title"] , $show ) . $content;
elseif ($aloptions['location'] == 'tr')  $display = return_author_love('0','right',$aloptions["title"] , $show ) . $content;
elseif ($aloptions['location'] == 'bl')  $display = $content . return_author_love('0','left',$aloptions["title"] , $show );
elseif ($aloptions['location'] == 'br')  $display = $content . return_author_love('0','right',$aloptions["title"] , $show );
else $display = $content;
return $display;
}

// insert the dynamic jq code
add_action('wp_footer', 'author_love_jq');

// admin options
add_action('admin_menu', 'author_love_menu');
function author_love_menu() {
add_options_page('author love', 'author love', 'manage_options', 'author_lovers','author_love_options_page');
}

function author_love_options_page() {
?>
<div style="float:left;width:60%;text-align:center">
<?PHP
if (!empty($_POST) && wp_verify_nonce($_POST['al_filed'],'author_love_form_submit') )
   {
      $aloptions['location'] = $_POST['location'];
      $aloptions['amount'] = $_POST['amount'];
      $aloptions['image'] = $_POST['image'];
      $aloptions['title'] = $_POST['title'];
      $aloptions['single'] = $_POST['single'];
      $aloptions['trans_author'] = $_POST['trans_author'];
      $aloptions['trans_rating'] = $_POST['trans_rating'];
      $aloptions['trans_votes'] = $_POST['trans_votes'];
      $aloptions['trans_yav'] = $_POST['trans_yav'];
      update_option('author_love_opts', $aloptions);	
     
      echo "<div style='background-color:yellow;text-align:center'><h2>your settings were saved !</h2></div>";
   
   }
   
$aloptions = get_option('author_love_opts');
?>

<h1>Author_love Settings Menu (free version)</h1>
   An AUTHOR rating system for WordPress<br/><br/>
   
<form method="post">
<strong>Where shall we show the rating blocks ?</strong><br/><small>when unchecked the ratings only show on single posts<br>when checked the ratings also show on the main pages.</small>
   <br />
   <input type="checkbox" name="single" value="yes" <?PHP if ($aloptions['single'] == 'yes') echo 'checked'; ?> > Show ratings on all posts/pages<br /><br />

<strong>Where should the rating block show up (in relation to your post content) ?</strong><br/>  
<input type="radio" name="location" value="ad" <?PHP if ($aloptions['location'] == 'ad') echo 'checked'; ?> > -DISABLED 
<input type="radio" name="location" value="br" <?PHP if ($aloptions['location'] == 'br') echo 'checked'; ?> > -bottom right <small> ( More options in the PREMIUM version. ) </small>
   <br/><br/>
   <strong>How many start would you like to show ? (1-30)</strong><br/><small>Regardless of how many stars you choose, the total value is always 100%.<br/> so with 3 stars 33% would fill 1 star. with 6 stars 33% would fill 2 stars etc.</small>
   <br />
   <input type="number" name="amount" min="1" max="30" value="<?PHP echo $aloptions['amount'] ; ?>" >
<br/><br/>
<strong>Which image do you want in the rating bar ?</strong><br/><small>More options in the PREMIUM version.</small>
   <br />
   <select name="image">
      <option value="-" disabled="disabled"> - choose one - </option>
      <option value="stars" <?PHP if ($aloptions['image'] == 'stars') echo 'selected'; ?> >Stars</option>
      <option value="hearts" disabled="disabled" >Hearts</option>
      <option value="arrows" disabled="disabled" >arrows</option>
      <option value="balls" <?PHP if ($aloptions['image'] == 'balls') echo 'selected'; ?>>balls</option>
   </select>
<br/><br/>
<strong>What do you want to show above the rating bar ?</strong><br/><small>More options in the PREMIUM version.</small>
   <br />
   <select name="title">
      <option value="-" disabled="disabled"> - choose one - </option>
      <option value="nothing" <?PHP if ($aloptions['title'] == 'nothing') echo 'selected'; ?> >nothing</option>
      <option value="user_nicename" <?PHP if ($aloptions['title'] == 'user_nicename') echo 'selected'; ?> >author nicename</option>
      <option value="user_login" disabled="disabled" >author login name (always available *)</option>
      <option value="user_email" disabled="disabled">author e-mail</option>
      <option value="user_url" disabled="disabled">author url</option>
      <option value="nickname" disabled="disabled">author nickname</option>
      <option value="first_name" disabled="disabled">author first name</option>
      <option value="last_name" disabled="disabled">author last name</option>
   </select>   
   
      <br/><br/>
  <strong>Translation</strong><br>
<small>This options is available in the PREMIUM version.</small><br><br />
      <?php wp_nonce_field('author_love_form_submit','al_filed'); ?>
<input type="submit" value="submit changes" style="background-color:yellow">
   </select>
</form>
<br><br />
<iframe width="420" height="315" src="http://www.youtube.com/embed/kOzj7Qzlavc" frameborder="0" allowfullscreen></iframe>
</div>
<div style="float:right;width:36%;padding:1%">
   <h3>Helpful</h3>
   Use the settings on the left to automatically show a rating block near your post content.<br>
To modify your themes yourself you can set automatic rendering at 'DISABLED' and insert the php function<br /> <span style="color:red">&lt;?PHP render_author_love('author_id'); ?&gt;</span>
<br/>You can leave the author-id empty, but only if used inside the loop. If used inside the loop the function will automatically pull the post-author of the post displayed at that time.
<br/><br/>
Another option is the function :<br />
<span style="color:red">&lt;?PHP return_author_love( $author_id="FALSE", $pos="left" ,$author_info=""); ?&gt;</span><br>
$author_id = empty || (or) Any valid author ID<br/>
$pos = left || right<br/>
$author_info = empty || author_nicename<br/>
<br/><br/>
<a href="http://digidownload.nl" title="see it in action">Live demo</a> (takes you to digidownload.nl)<br>
<a href="http://digidownload.nl/author/admin/" title="see it in action">Other versions</a> (takes you to digidownload.nl)<br><br>
Coding & style by: Pete Scheepens
 <br/><br />
<?php
echo "<img src='" .plugins_url( 'screenshot-1.jpg' , __FILE__ ). "' > ";
?>    
</div>
<div style="clear:both"></div>";
<?PHP
}

///////////////////////////////////////////////////////////// FUNCTIONS //////////////////////////////////////////////////////////////////

// response to ajax call
function al_vote_func() {
$nonce=$_REQUEST['nonce'];
   if (! wp_verify_nonce($nonce, 'al_vote') ) {
   $aResponse['server'] = 'Security violation';
   echo json_encode($aResponse);
   die();
   };
$id = intval($_POST['idBox']);
$rate = floatval($_POST['rate']);
$ip = $_POST['IP'];
global $wpdb;
if (!$wpdb->query($wpdb->prepare( "INSERT INTO " .$wpdb->prefix . "author_love (author_id,ip_address,rating) VALUES ('$id','$ip','$rate') " )) )
$W = "YOU ALREADY VOTED"; else $W = "thanks for voting";
$aResponse['type'] = 'success';
$aResponse['server'] = $W;
echo json_encode($aResponse);
die();
}   

// create dynamic JS in footer
function author_love_jq($content) {
$aloptions = get_option('author_love_opts');
if ($aloptions['image'] == 'hearts') $images = ",bigStarsPath:'/author-love/jquery/icons/heart.png'";
  if ($aloptions['image'] == "stars") $images = ",bigStarsPath:'/author-love/jquery/icons/star.png'";
   if ($aloptions['image'] == "arrows") $images = ",bigStarsPath:'/author-love/jquery/icons/arrow.png'";
   if ($aloptions['image'] == "balls") $images = ",bigStarsPath:'/author-love/jquery/icons/ball.png'";
   $link = admin_url('admin-ajax.php?action=al_vote');
   $nonce= wp_create_nonce  ('al_vote');
  echo '
<script type="text/javascript" src="' .plugins_url( 'jquery/jRating.jquery.js' , __FILE__ ). '">
</script>
<script type="text/javascript">
jQuery(document).ready(function(){jQuery(".authorlove").jRating({length:' . $aloptions['amount'] . ',url:\''. $link . '\',decimalLength:1' . $images . ',nonce:\''. $nonce . '\',phpPath:\''. $link . '\'}); });
</script>';
}


// render rating block
function render_author_love( $author_id="FALSE" )
{
global $wpdb;
if (empty($author_id) || $author_id == "FALSE") $author_id = get_the_author_meta('ID'); 
$average_rating = $wpdb->get_var( $wpdb->prepare(" SELECT AVG(rating) FROM " .$wpdb->prefix . "author_love WHERE author_id='$author_id' ") );
$average_rating = number_format($average_rating);
$count_rating = $wpdb->get_var( $wpdb->prepare(" SELECT COUNT(rating) FROM " .$wpdb->prefix . "author_love WHERE author_id='$author_id' ") );
$visitor_ip = str_replace(".","",getIp() );
$path = plugins_url();
$id = $average_rating . "_" . $author_id . "|" . $visitor_ip . "|" . $path;
echo "Author : $author_id <br>";
echo "<div style='width:140px;height:62px;overflow:hidden;background-color:white'>";
echo "<div class='authorlove' id='$id'></div>";
echo "<div class='serverResponse' style='width:140px;height:32px'><p>rating: $average_rating% - $count_rating votes</p>
</div></div>";
}

function return_author_love( $author_id="FALSE", $pos="left" ,$author_info="",$single="no")
{
$data = "";
global $wpdb;
if (empty($author_id) || $author_id == "FALSE") $author_id = get_the_author_meta('ID');
$author_info = get_the_author_meta('user_nicename'); 
$average_rating = $wpdb->get_var( $wpdb->prepare(" SELECT AVG(rating) FROM " .$wpdb->prefix . "author_love WHERE author_id='$author_id' ") );
$average_rating = number_format($average_rating);
$count_rating = $wpdb->get_var( $wpdb->prepare(" SELECT COUNT(rating) FROM " .$wpdb->prefix . "author_love WHERE author_id='$author_id' ") );
$visitor_ip = str_replace(".","",getIp() );
$path = plugins_url();
$id = $average_rating . "_" . $author_id . "|" . $visitor_ip . "|" . $path;
if ($single == "no" || empty($single))
{
 if (is_single() )  {
   $data .= "<div style='float:$pos;margin:3px;background-color:white'>";
      if (!empty($author_info)) $data .= "<div style='width:140px;height:24px;font-size:12px;overflow:hidden;text-align:center'>Author : $author_info</div>";
   $data .= "<div class='authorlove' id='$id' style='width:140px;height:30px;overflow:hidden;background-color:white'></div>";
   $data .= "<div class='serverResponse' style='width:140px;height:16px'><p>rating: $average_rating% - $count_rating votes</p></div>";
   $data .= "</div><div style='clear:both'></div>";
      }
 else $data = '';
}
else
{
   $data .= "<div style='float:$pos;margin:3px;background-color:white'>";
      if (!empty($author_info)) $data .= "<div style='width:140px;height:24px;font-size:12px;overflow:hidden;text-align:center'>Author : $author_info</div>";
   $data .= "<div class='authorlove' id='$id' style='width:140px;height:30px;overflow:hidden;background-color:white'></div>";
   $data .= "<div class='serverResponse' style='width:140px;height:16px'><p>rating: $average_rating% - $count_rating votes</p></div>";
   $data .= "</div><div style='clear:both'></div>";
}
return $data;
}


function getIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
 
    return $ip;
}
?>
