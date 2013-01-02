<?php
//##############################################################################
//## Powie - www.powie.de                                                     ##
//##############################################################################

if ( !defined('ABSPATH') )
    die('You are not allowed to call this page directly.');

global $wpdb;

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>TextBlox Picker</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo TB_PLUGIN_URL ?>tinymce.js"></script>
    <base target="_self" />
</head>

<script type="text/javascript">
jQuery(document).ready(function(){
	//nothing
});
</script>

<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">

<form name="textbloxform" action="#">

	<div id="tb_panel" class="panel current">
	<br />
	<table border="0" cellpadding="4" cellspacing="0">
      <tr>
        <td nowrap="nowrap"><label for="gallerytag">TextBlox:</label></td>
        <td><select id="tbid" name="textbloxid" style="width: 320px">
                <!--<option value="0" selected="selected">TextBlox Auswahl</option>-->
                <?php
                //taxonomy read
                $cats = get_terms(           	  'textblox_category'                );
                foreach ($cats as $term ) {
                	echo '<optgroup label="'.$term->name.'">';
	                //get all textblox from database
					$tbs = get_posts( array(
						'order'          => 'ASC',
						'orderby' 		 => 'title',
						'post_type'      => 'textblox',
						'textblox_category' => $term->name,
						'numberposts'    => -1) );
					foreach ( $tbs as $blox ) {
						echo '<option value="'.$blox->ID.'">'.$blox->post_title.'</option>';
					}
                	echo '</optgroup>';
                }
				//without terms
				echo '<optgroup label="ohne">';
				$tbs = get_posts( array(
									'order'          => 'ASC',
									'orderby' 		 => 'title',
									'post_type'      => 'textblox',
									'textblox_category' => '',
									'numberposts'    => -1) );
				foreach ( $tbs as $blox ) {
					$terms = get_the_terms($blox->ID,'textblox_category');
					if (!$terms) {
						echo '<option value="'.$blox->ID.'">'.$blox->post_title.'</option>';
					}
				}
				echo '</optgroup>';
                ?>
            </select>
        </td>
      </tr>
    </table>
    </div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'default'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'default'); ?>" onclick="insertTBshortcode();" />
		</div>
	</div>

</form>
</body>
</html>