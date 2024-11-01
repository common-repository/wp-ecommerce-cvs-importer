<?php
/*
Plugin Name: WP E-commerce CSV Importer
Plugin URI: http://ihayag.com/wpplugin
Description: WP E-commerce CSV Importer
Version: 1.0.4
Author: Jeremias Francisco
Author URI: http://ihayag.com/wpplugin


/*  Copyright 2010  Jeremias  (email : jeremias@ihayag.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

?>
<?php
function wp_ecommerce_cvs_import_page() {
	add_options_page('WP E-commerce CSV Importer', 'WP E-commerce CSV Importer', 'manage_options', 'wpecommerccvsimporter', 'wp_ecommerce_cvs_importer');
}
add_action('admin_menu', 'wp_ecommerce_cvs_import_page');


function wp_ecommerce_cvs_importer() {
	global $wpdb, $table_prefix;
	$wpsc_upload_dir = WP_CONTENT_DIR . "/uploads/"; ?>
	<link href="<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/cvs_import.css" rel="stylesheet" type="text/css" />
    <?php
	echo "<div id='plugin-div'>";
    echo "<form name='cart_options' enctype='multipart/form-data' id='cart_options' method='post' action=''>";
    echo "<h2>WP E-commerce CSV Importer</h2>";
	echo "<div>You can import your products from a comma delimited text file.</p><p>An example of a csv import file would look like this: </div><div>SKU, Product Name, Product Description, Additional Description, Price, Weight, Weight Unit, Quantity, Limited Quantity, Publish, Local Shipping, International Shipping, Thumbnail Image </div>";
	echo "<div id='top-input'><input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
		<input type='file' name='csv_file' />
		<input type='submit' value='Validate Header' class='button-primary'></div>";
		
	if ($_FILES['csv_file']['name'] != '') {

		$file = $_FILES['csv_file'];

		if(move_uploaded_file($file['tmp_name'], $wpsc_upload_dir.$file['name'])){
			$content = file_get_contents($wpsc_upload_dir.$file['name']);
			$handle = @fopen($wpsc_upload_dir.$file['name'], 'r');
			while (($csv_data = @fgetcsv($handle, filesize($handle), ",")) !== false) {
				$fields = count($csv_data);
				for ($i=0;$i<$fields;$i++) {
					if (!is_array($data1[$i])){
						$data1[$i] = array();
					}
					array_push($data1[$i], $csv_data[$i]);
				}
			}

			$_SESSION['cvs_data'] = $data1;
			$wpsc_product_categories = $table_prefix . "wpsc_product_categories";
			$categories_sql = "SELECT id, name FROM ".$wpsc_product_categories." WHERE active='1'";
			$categories = $wpdb->get_results($categories_sql, ARRAY_A);
			
			echo "<div class='metabox-holder' style='width:90%'>";
			
			$cvs_fields = array('SKU', 
								'Product Name', 
								'Product Description',
								'Additional Description', 
								'Price', 
								'Sale Price', 
								'Weight', 
								'Weight Unit', 
								'Local Shipping', 
								'International Shipping', 
								'Quantity', 
								'Limited Quantity', 
								'No Tax', 
								'Publish', 
								'Active', 
								'Donation', 
								'No Shipping Cost', 
								'Thumbnail Image', 
								'Image Link',
								'Category Id',
								'Product Id'
						);

			foreach ((array)$data1 as $key => $datum) {
				foreach ($datum as $column) {
					if(in_array($column, $cvs_fields)){
						//echo "[$column] is there inside the array <br>";
						$cvs_ok_field .= "<strong><em>$column</em></strong>" . "&nbsp;[ok], ";
					} else{ 
						//echo "[$column] is not there <br>";
						$cvs_error_field .= "<strong><em>$column</em></strong>" ."&nbsp;[is not correct Header Field, please see documentation]<br>";
					}	
					//echo "'$column', ";				
					break;
				} 
			} 
			if($cvs_error_field){
				echo "<br>$cvs_error_field <br>";
			} else {
				echo "<br><strong><em>No errors found! in : </em></strong>" .$_FILES['csv_file']['name']. " &nbsp;&nbsp;&nbsp;&nbsp; See Additional instructions below.<br><br>";
				echo "<label for='category'>Please select a category you would like to place all products from this CSV into:</label><br>";
				echo "OR if you include the <em>Category Id</em> in the CSV file, this will be ignored.</div>";
				echo "<div id='bottom-input'><select id='category' name='category'>";
				
				foreach($categories as $category){
					echo '<option value="'.$category['id'].'">'. $category['id'] .' = '. $category['name'].'</option>';
				}
				echo "</select>";
				echo "<input type='submit' name='cvs_action' value='Import Now' class='button-primary'></div>";
				
			} 
		} 
	} 
	
	// Handle the post action
	
	if($_POST['cvs_action'] == 'Import Now'){
		global $wpdb, $table_prefix, $record_count, $cvs_sku, $cvs_special_price, $cvs_category_id, $cvs_product_id, $cvs_thumbnail_image, $query_field, $query_update, $cvs_name, $get_id, $price_changed, $primary_key;
		$record_count = 0;
		$price_changed = 0;
		$primary_key = '';
		$cvs_data = $_SESSION['cvs_data'];
		$cvs_fields = array('SKU' => 'sku', 
							'Product Name' => 'name', 
							'Product Description' => 'description', 
							'Additional Description' => 'additional_description', 
							'Price' => 'price', 
							'Sale Price' => 'special_price', 
							'Weight' => 'weight', 
							'Weight Unit' => 'weight_unit', 
							'Local Shipping' => 'pnp', 
							'International Shipping' => 'international_pnp', 
							'Quantity' => 'quantity', 
							'Limited Quantity' => 'quantity_limited', 
							'No Tax' => 'notax', 
							'Publish' => 'publish', 
							'Active' => 'active', 
							'Donation' => 'donation', 
							'No Shipping Cost' => 'no_shipping', 
							'Thumbnail Image' => 'thumbnail_image', 
							'Image Link' => 'image',
							'Category Id' => 'category_id',
							'Product Id' => 'product_id'
					);

		$num = count($cvs_data);
		
		$rec_count = count($cvs_data['1']) -1;
		
		for($i =0; $i < $rec_count; $i++){
			$query = '';
			$query_field = '';
			$query_update ='';
			$cvs_sku =''; $cvs_special_price=''; $cvs_category_id=''; $cvs_product_id=''; $cvs_thumbnail_image=''; 
			for($j =0; $j < $num; $j++){
				$cvs_field_value = esc_attr($cvs_data[$j][$i+1]);
				
				/*if($j == ($num-1) && !empty($cvs_field_value)){
					$query .= "'$cvs_field_value'";
					$query_field .= $cvs_fields[$cvs_data[$j]['0']];
					$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value'" ;
				} else { */
					if(!empty($cvs_field_value)){
						if($cvs_data[$j]['0'] == 'SKU'){
							$cvs_sku = $cvs_field_value;
						} elseif($cvs_data[$j]['0'] == 'Sale Price') {
							$cvs_special_price = $cvs_field_value;
								$query .= "'$cvs_field_value', ";
								$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
								$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
						} elseif($cvs_data[$j]['0'] == 'Category Id') {
							$cvs_category_id = $cvs_field_value;
						} elseif($cvs_data[$j]['0'] == 'Product Id') {
							$cvs_product_id = $cvs_field_value;
						} elseif($cvs_data[$j]['0'] == 'Thumbnail Image') {
							$cvs_thumbnail_image = $cvs_field_value;
							$query .= "'$cvs_field_value', ";
							$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
							$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
						} elseif($cvs_data[$j]['0'] == 'Price') {
							$cvs_price = $cvs_field_value;
							$query .= "'$cvs_field_value', ";
							$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
							$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
						} elseif($cvs_data[$j]['0'] == 'Product Name') {
							$cvs_name = $cvs_field_value;
							$query .= "'$cvs_field_value', ";
							$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
							$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
						} else {
							$query .= "'$cvs_field_value', ";
							$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
							$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
						}
					} else{
						if($cvs_data[$j]['0'] == 'Publish') {
							if($cvs_field_value == '0'){
								$query .= "'$cvs_field_value', ";
								$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
								$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
							}
						}
						if($cvs_data[$j]['0'] == 'Active') {
							if($cvs_field_value == '0'){
								$query .= "'$cvs_field_value', ";
								$query_field .= $cvs_fields[$cvs_data[$j]['0']] . ", ";
								$query_update .= $cvs_fields[$cvs_data[$j]['0']] ." = '" . "$cvs_field_value', " ;
							}
						}
					}
				//}
			}
			if(substr($query_field, strlen($query_field)-2, 1) == ','){
				$query_field = substr($query_field, 0, strlen($query_field)-2);
			}
			$query_field = "($query_field)";
			
			if(substr($query, strlen($query)-2, 1) == ','){
				$query = substr($query, 0, strlen($query)-2);
			}
			$query = "($query)";
			
			if(substr($query_update, strlen($query_update)-2, 1) == ','){
				$query_update = substr($query_update, 0, strlen($query_update)-2);
			}
			
			if(!empty($cvs_product_id) && !empty($cvs_sku)){
				$get_id = $wpdb->get_var("SELECT id FROM {$table_prefix}wpsc_product_list WHERE id = $cvs_product_id LIMIT 1");
			} elseif(empty($cvs_product_id) && !empty($cvs_sku)){
				$get_id = $wpdb->get_var("SELECT product_id FROM {$table_prefix}wpsc_productmeta WHERE meta_value = '$cvs_sku' LIMIT 1");
			} elseif(!empty($cvs_product_id) && empty($cvs_sku)){
				$get_id = $wpdb->get_var("SELECT id FROM {$table_prefix}wpsc_product_list WHERE id = $cvs_product_id LIMIT 1");
			} else {
				$get_id = $cvs_product_id;
			}
			if(empty($get_id)){
				insert_query($query, $query_field, $cvs_thumbnail_image, $cvs_category_id, $cvs_sku, $cvs_name, $cvs_special_price, $cvs_price);
			} else {
				update_query($query_update, $get_id, $cvs_special_price, $cvs_price, $cvs_category_id, $cvs_sku, $cvs_thumbnail_image);
			}
		}

		if($record_count > 0){
			echo "<p>$record_count record(s) affected</p>";
		}
		if($price_changed > 0){
			echo "<p>$price_changed price changed</p>";
		}
	}
	
	echo "</form>";

	echo "</div>";
	echo "<div id='plugin-paypal'>";
		?>
        <div id="happy-text">"Make Me Happy"</div>
        <div id="make-me-happy">
        	<a href="http://ihayag.com/wpplugin"><img src="<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/make_me_happy.jpg" /></a>
		</div>
        
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="WUW8X6U9WCJLQ">
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
	
		<?php
	echo "<div class='multi-uploader'>";
		//image_uploader();
	echo "</div>";
	echo "</div>";
}


function insert_query($query='', $query_field='', $cvs_thumbnail_image ='', $cvs_category_id='', $cvs_sku='', $cvs_name='', $cvs_special_price=0, $cvs_price=0){
	global $wpdb, $table_prefix, $record_count, $cvs_sku, $cvs_category_id, $cvs_product_id, $cvs_thumbnail_image, $query_field, $query_update, $cvs_name, $get_id, $price_changed, $cvs_special_price;
	
	if($wpdb->query("INSERT INTO {$table_prefix}wpsc_product_list {$query_field} VALUES {$query}")){
		$record_count++;
	}
	$query = '';
	
	$id = $wpdb->get_var("SELECT LAST_INSERT_ID() as id FROM {$table_prefix}wpsc_product_list");
	
	$meta_query = "INSERT INTO {$table_prefix}wpsc_productmeta VALUES ('', '{$id}', 'sku', '{$cvs_sku}', '0')";
	$wpdb->query($meta_query);
	
	if(!empty($cvs_category_id)){
		$category_query = "INSERT INTO {$table_prefix}wpsc_item_category_assoc VALUES ('','{$id}','" .$cvs_category_id. "')";
		$wpdb->query($category_query);
	} else {
		$category_query = "INSERT INTO {$table_prefix}wpsc_item_category_assoc VALUES ('','{$id}','" .$wpdb->escape($_POST['category']). "')";
		$wpdb->query($category_query);
	}
	
	$existing_name = get_product_meta($id, 'url_name');
	$tidied_name = strtolower(trim(stripslashes($cvs_name)));
	$url_name =  sanitize_title($tidied_name);
	$similar_names = (array)$wpdb->get_col("SELECT `meta_value` FROM `".WPSC_TABLE_PRODUCTMETA."` WHERE `product_id` NOT IN('{$id}}') AND `meta_key` IN ('url_name') AND `meta_value` REGEXP '^(".$wpdb->escape(preg_quote($url_name))."){1}[[:digit:]]*$' ");
	
	// Check desired name is not taken
	if(array_search($url_name, $similar_names) !== false) {
	  // If it is, try to add a number to the end, if that is taken, try the next highest number...
		$k = 0;
		do {
			$k++;
		} while(array_search(($url_name.$k), $similar_names) !== false);
		// Concatenate the first number found that wasn't taken
		$url_name .= $k;
	}
  // If our URL name is the same as the existing name, do othing more.
	if($existing_name != $url_name) {
		update_product_meta($id, 'url_name', $url_name);
	}

	$image_query = "INSERT INTO {$table_prefix}wpsc_product_images VALUES ('', '{$id}','{$cvs_thumbnail_image}', '150', '150', '0', 'null')";
	$wpdb->query($image_query);
	$image_id = $wpdb->get_var("SELECT LAST_INSERT_ID() as id FROM {$table_prefix}wpsc_product_images");
	$wpdb->query("UPDATE {$table_prefix}wpsc_product_list SET image = '{$image_id}' WHERE id = {$id}");
	
	if(!empty($cvs_special_price) || $cvs_special_price > 0){
		$sale_price = strval($cvs_price) - strval($cvs_special_price);
		$wpdb->query("UPDATE {$table_prefix}wpsc_product_list SET special_price = '{$sale_price}' WHERE id = {$id}");
	} 
	
}

function update_query($query_update='', $get_id='', $cvs_special_price=0, $cvs_price=0, $cvs_thumbnail_image='', $cvs_sku=''){
	global $wpdb, $table_prefix, $record_count, $cvs_sku, $cvs_special_price, $cvs_category_id, $cvs_product_id, $cvs_thumbnail_image, $query_field, $query_update, $cvs_name, $get_id, $price_changed, $primary_key;
	
	if($wpdb->query("UPDATE {$table_prefix}wpsc_product_list SET $query_update WHERE id = '{$get_id}'")){
		$record_count++;
	}
	//echo "<p>$query_update</p>";
	$query = '';
	
	$wpdb->query("UPDATE {$table_prefix}wpsc_product_images SET image = '{$cvs_thumbnail_image}' WHERE product_id = {$get_id}");

	if(!empty($cvs_category_id)){
		$wpdb->query("UPDATE {$table_prefix}wpsc_item_category_assoc SET category_id = '" .$cvs_category_id. "' WHERE product_id = {$get_id}");
	}

	if(!empty($cvs_special_price) || $cvs_special_price > 0){
		$sale_price = strval($cvs_price) - strval($cvs_special_price);
		$wpdb->query("UPDATE {$table_prefix}wpsc_product_list SET special_price = '{$sale_price}' WHERE id = {$get_id}");
		$price_changed++;
	}

	$wpdb->query("UPDATE {$table_prefix}wpsc_product_images SET image = '{$cvs_thumbnail_image}' WHERE id = {$get_id}");
	
	if(!empty($cvs_sku)){
		$wpdb->query("UPDATE {$table_prefix}wpsc_productmeta SET meta_value = '{$cvs_sku}' WHERE product_id = {$get_id} AND meta_key = 'sku'");
	}
}

function image_uploader(){
	$wpsc_dir = content_url() . '/uploads/wpsc/product_images/';
	?>
	<script type="text/javascript" src="<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/js/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/js/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/js/jquery.swfupload.js"></script>
	<script type="text/javascript">
	
	$(function(){
		
		$('#swfupload-control').swfupload({
			upload_url: "<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/upload-file.php",
			file_post_name: 'uploadfile',
			file_size_limit : "1024",
			file_types : "*.jpg;*.png;*.gif",
			file_types_description : "Image files",
			file_upload_limit : 0,
			flash_url : "<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/js/swfupload/swfupload.swf",
			button_image_url : '<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/js/swfupload/wdp_buttons_upload_114x29.png',
			button_width : 114,
			button_height : 29,
			button_placeholder : $('#button')[0],
			debug: false
		})
			.bind('fileQueued', function(event, file){
				var listitem='<li id="'+file.id+'" >'+
					'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
					'<div class="progressbar" ><div class="progress" ></div></div>'+
					'<p class="status" >Pending</p>'+
					'<span class="cancel" >&nbsp;</span>'+
					'</li>';
				$('#log').append(listitem);
				$('li#'+file.id+' .cancel').bind('click', function(){
					var swfu = $.swfupload.getInstance('#swfupload-control');
					swfu.cancelUpload(file.id);
					$('li#'+file.id).slideUp('fast');
				});
				// start the upload since it's queued
				$(this).swfupload('startUpload');
			})
			.bind('fileQueueError', function(event, file, errorCode, message){
				alert('Size of the file '+file.name+' is greater than limit');
			})
			.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
				$('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
			})
			.bind('uploadStart', function(event, file){
				$('#log li#'+file.id).find('p.status').text('Uploading...');
				$('#log li#'+file.id).find('span.progressvalue').text('0%');
				$('#log li#'+file.id).find('span.cancel').hide();
			})
			.bind('uploadProgress', function(event, file, bytesLoaded){
				//Show Progress
				var percentage=Math.round((bytesLoaded/file.size)*100);
				$('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
				$('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
			})
			.bind('uploadSuccess', function(event, file, serverData){
				var item=$('#log li#'+file.id);
				item.find('div.progress').css('width', '100%');
				item.find('span.progressvalue').text('100%');
				var pathtofile='<a href="<?php echo $wpsc_dir; ?>'+file.name+'" target="_blank" >view &raquo;</a>';
				item.addClass('success').find('p.status').append('Done!!! | '+pathtofile);
			})
			.bind('uploadComplete', function(event, file){
				// upload has completed, try the next one in the queue
				$(this).swfupload('startUpload');
			})
		
	});	
	
	</script>
	<style type="text/css" >
	#swfupload-control p{ margin:10px 5px; font-size:0.9em; }
	#log{ margin:0; padding:0; width:100%;}
	#log li{ list-style-position:inside; margin:2px; border:1px solid #ccc; padding:10px; font-size:12px; 
		font-family:Arial, Helvetica, sans-serif; color:#333; background:#fff; position:relative;}
	#log li .progressbar{ border:1px solid #333; height:5px; background:#fff; }
	#log li .progress{ background:#999; width:0%; height:5px; }
	#log li p{ margin:0; line-height:18px; }
	#log li.success{ border:1px solid #339933; background:#ccf9b9; }
	#log li span.cancel{ position:absolute; top:5px; right:5px; width:20px; height:20px; 
		background:url('<?php echo plugins_url(); ?>/wp-ecommerce-cvs-importer/upload/js/swfupload/cancel.png') no-repeat; cursor:pointer; }
	</style>
		<h3>Upload All your Image File</h3>
		
	<div id="swfupload-control">
		<p>After you import your CSV file, you may use this to upload all your images.<br /><br />
        	You must need javascript and flash to use this.
		</p>
		<input type="button" id="button" />
		<p id="queuestatus" ></p>
		<ol id="log"></ol>
	</div>
	
	
	<?php

}

?>
