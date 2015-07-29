<?php
/*
Plugin Name: Woocommerce Bulk Attribute Manager
Plugin URI: http://www.advancedstyle.com/
Description: Manage bulk woocommerce product variations and attribute options.
Author: David Barnes
Version: 1.0
Author URI: http://www.advancedstyle.com/
*/
class woocommerceBulkAttributes
{
	function __construct(){
		add_action('add_tag_form',array($this, 'attribute_form'), 99, 1);
		add_action('admin_init',array($this, 'attribute_catch'));
		//add_action('admin_init',array($this, 'deleteAll'));
		add_action('admin_menu', array($this, 'adminMenu'));
		if(isset($_GET['wbaprocess'])){
			add_action('admin_init',array($this, 'session'));
			switch($_GET['wbaprocess']){
				case 1:
					add_action('admin_init',array($this, 'setup'));
				break;
				case 2:
					add_action('admin_init',array($this, 'process'));
				break;
			}
		}
	}
	function attribute_form($taxonomy){
		if(isset($_GET['post_type']) && $_GET['post_type'] == 'product'){
			echo '</form>';
			
			echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
			echo '<h2>Bulk Insert</h2>';
			echo '<p>Each option should be on a separate line</p>';
			echo '<p><textarea name="woobiabulktags" rows="5" cols="40"></textarea></p>';
			echo '<p><input type="submit" value="Insert Terms" class="button-primary"></p>';
		}
	}
	
	function attribute_catch(){
		if(isset($_POST['woobiabulktags']) && trim($_POST['woobiabulktags']) != ''){
			$lines = explode("\n",$_POST['woobiabulktags']);
			foreach($lines as $line){
				$line = trim($line);
				if($line != ''){
					if(!term_exists($line, $_GET['taxonomy'])){
						wp_insert_term($line, $_GET['taxonomy']);
					}
				}
			}
			wp_redirect($_SERVER['REQUEST_URI']);
			exit();
		}
	}
	
	function deleteAll($product_id=0){
		global $wpdb;
		if($product_id > 0){
			if($attributes = $wpdb->get_results("SELECT attribute_name FROM  " . $wpdb->prefix . "woocommerce_attribute_taxonomies")){
				foreach($attributes as $attribute){
					wp_delete_object_term_relationships($product_id, 'pa_'.$attribute->attribute_name);
				}
			}
			$transient_name = 'wc_product_children_ids_' . $product_id;
			delete_transient($transient_name);
		}
				
		
		$ids = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE post_type='product_variation' ".($product_id > 0 ? "AND post_parent='".(int)$product_id."'" : ""),'ARRAY_A');
		if(!empty($ids)){
			foreach($ids as $id){
				$x = (int)$id['ID'];
				$wpdb->query("DELETE FROM ".$wpdb->posts." WHERE ID='".$x."'");
				$wpdb->query("DELETE FROM ".$wpdb->postmeta." WHERE post_id='".$x."'");
			}
		}
	}
	
	function adminMenu() {
		add_submenu_page( 'edit.php?post_type=product', 'Bulk Attribute Manager', 'Bulk Attribute Manager', 'edit_products', 'woocommerce_bulk_attributes', array($this, 'adminPage') ); 
	}
	
	function adminPage() {
		global $wpdb;
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
		?>
        <script type="application/javascript" language="javascript">
		jQuery(function($){
			$(document).ready(function(){
				var bulkajax = 0;
				$('#bulkattributes').submit(function(){
					$('.button-primary',this).val('Loading..Please wait');
					if(bulkajax != 0){
						bulkajax.abort();
					}
					bulkajax = $.post( "<?php echo add_query_arg('wbaprocess',1,$_SERVER['REQUEST_URI']);?>", $(this).serialize(), function( data ) {
						tb_show("Processing Attributes", "<?php echo add_query_arg(array('wbaprocess' => 2, 'TB_iframe'=> 'true'),$_SERVER['REQUEST_URI']);?>", "");
					});
					return false;
				});
				$(':checkbox').click(function(){
					if($(this).attr('name') == 'attribute_all'){
						var p = $(this).closest('li');
						if($(this).is(':checked')){
							$('ul :checkbox',p).prop('checked',true);
						}else{
							$('ul :checkbox',p).prop('checked',false);
						}
					}
				});
			});
		});
		</script>
        <div class='wrap'>
            <h2>Bulk Attributes</h2>
            <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" id="bulkattributes">
            <div style="float:left; margin-right:40px;">
                <h3>Select a product category to update:</h3>
                <ul>
                    <li><label><input type="checkbox" name="allproducts" value="1"> All Products</label></li>
                    <?php
                    $categories = get_terms('product_cat');
                    if(!empty($categories)){
                        foreach($categories as $category){
                            echo '<li><label><input type="checkbox" name="category['.$category->slug.']" value="1"> '.$category->name.'</label></li>';
                        }
                    }
                    ?>
                </ul>
                <input type="submit" value="Update Product Variations" class="button-primary">
            </div>
            <div style="float:left;">
                <h3>Select Attributes:</h3>
                <ul>
                <?php
            	if($attributes = $wpdb->get_results("SELECT attribute_name, attribute_label FROM  " . $wpdb->prefix . "woocommerce_attribute_taxonomies ORDER BY attribute_label")){
					foreach($attributes as $attribute){
						$options = get_terms('pa_'.$attribute->attribute_name, array('hide_empty' => false));
						if(!empty($options)){
							echo '<li><label><input type="checkbox" name="attribute_all" value="'.$attribute->attribute_name.'"> '.$attribute->attribute_label.'</label><ul style="margin-left:20px;">';
							foreach($options as $option){
								 echo '<li><label><input type="checkbox" name="attribute['.$attribute->attribute_name.']['.$option->term_id.']" value="1"> '.$option->name.'</label></li>';
							}
							echo '</ul></li>';
						}
					}
				}
				?>
                </ul>
            </div>
            </form>
            <br style="clear:both;">
        </div>
		<?php
	}
	
	function session(){
		if(!isset($_SESSION)) {
			 session_start();
		}
	}
	
	function setup(){
		unset($_SESSION['bulkatt']);
		$args = array('post_type' => 'product',
					  'fields' => 'ids',
					  'numberposts'   => -1);
		if($_POST['allproducts'] != 1 && !empty($_POST['category'])){
			$args['product_cat'] = implode(',',array_keys($_POST['category']));
		}
		$products = get_posts($args);
		$_SESSION['bulkatt']['products'] = $products;
		$_SESSION['bulkatt']['attributes'] = $_POST['attribute'];
		echo 'Done';
		exit();
	}
	
	function process(){
		$products = $_SESSION['bulkatt']['products'];
		$current = (int)$_SESSION['bulkatt']['current'];
		?>
        <style type="text/css">
		BODY {
			margin:20px;
			font-family:"Open Sans";
			font-size:16px;
		}
		</style>
        <?php
		if(!empty($products)){
			if(($current) == count($products)){
				?>
                <h1>Completed!</h1>
                <p><?php echo count($products);?> Products were updated</p>
            <?php
			}elseif(isset($products[$current])){
				
				$product_id = $products[$current];
				
				$product_type = wp_get_object_terms($product_id, 'product_type');
				$product_type = $product_type[0]->name;
				
				$this->deleteAll($product_id);
				
				if(empty($_SESSION['bulkatt']['attributes'])){
					if($product_type == 'variable'){
						wp_set_object_terms($product_id, 'simple', 'product_type');
					}
				}else{
					foreach($_SESSION['bulkatt']['attributes'] as $tax => $tmp){
						foreach($tmp as $attribute => $o){
							$slug = 'pa_'.$tax;
							$attrib_array[$slug] = array('name' => $slug,
													   'value' => '',
													   'position' => 0,
													   'is_visible' => 1,
													   'is_variation' => 1,
													   'is_taxonomy' => 1);
							wp_set_object_terms($product_id, $attribute, $slug, true);
						}
					}
					
					$variation_id = wp_insert_post(array(
					  'post_title'    => 'Product '.$product_id.' Variation',
					  'post_content'  => '',
					  'post_status'   => 'publish',
					  'post_type' => 'product_variation',
					  'post_author'   => 1,
					  'post_parent' => $product_id
					));
					
					
					$price = get_post_meta($product_id, '_max_variation_regular_price', true);
					if($price == ''){
						$price = get_post_meta($product_id, '_price', true);
					}
					
					$attribute_keys = array_keys($attrib_array);
					foreach($attribute_keys as $k){
						update_post_meta($variation_id, 'attribute_'.$k, '');
					}
					update_post_meta($variation_id, '_regular_price', $price);
					update_post_meta($variation_id, '_price', $price);
					update_post_meta($variation_id, '_thumbnail_id', 0);
					update_post_meta($variation_id, '_virtual', 'no');
					update_post_meta($variation_id, '_downloadable', 'no');
					
					
					update_post_meta($product_id, '_product_attributes', $attrib_array);
					update_post_meta($product_id, '_max_variation_regular_price',$price);
					update_post_meta($product_id, '_min_variation_regular_price', $price);
					update_post_meta($product_id, '_max_variation_price', $price);
					update_post_meta($product_id, '_min_variation_price', $price);
					
					
					wp_set_object_terms($product_id, 'variable', 'product_type');
				}
			?>
            <p>Updating product #<?php echo ($current+1);?> of <?php echo count($products);?>: <?php echo get_post_field('post_title',$product_id);?></p>
            <table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #000;">
            	<tr>
                	<td style="background:#006; font-weight:bold; text-align:center; height:50px; line-height:50px; color:#FFF;" width="<?php echo ceil((($current+1) / count($products))*100);?>%"><?php echo ceil((($current+1) / count($products))*100);?>%</td>
                    <td></td>
                </tr>
			</table>
            <script language="javascript" type="text/javascript">
			location.reload(); 
			</script>
        <?php
			$_SESSION['bulkatt']['current']++;
			}else{
				echo '<p>Error: Product not found</p>';
			}
		}else{
			echo '<p>Error: No Products were found</p>';
		}
		exit();
	}
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$wba = new woocommerceBulkAttributes;
}
?>