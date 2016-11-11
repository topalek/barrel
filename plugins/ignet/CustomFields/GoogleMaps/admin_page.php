<?php 
/*
* Админка управления Gmap
*/
	global $map_options;

	if ( $_REQUEST['saved'] ) 
		echo '<div  class="wrap" style=""><h2 style="color:green;">Настройки сохранены.</h2></div>';
	 
	if ( $_REQUEST['reset'] ) 
		echo '<div  class="wrap" style=""><h2 style="color:green;">Настройки сброшены.</h2></div>';
	?>
	<style>
		.IGNET_G_MAP-table, .save_button_box{
			margin: auto;
			width: inherit;	
			font-size: 16px;			
		}
		.IGNET_G_MAP-table tr td{
			font-size: 17px;		
		}
		.IGNET_G_MAP-table tr td h3{
			margin: 0px;		
		}		
		.dicsr{
			font-size: 12px;
			color:#979393;
			line-height: 12px;		
		}
		.title td{
			border-top:1px solid #000;
		}
		.save_button {
		width: 200px;
		}
		
		.tarif_link_input{
		width: 350px;
		border-radius: 6px;
		}
	</style>
	<form method="post">
	<table class="IGNET_G_MAP-table form-table">
		<tr>
			<td>
				<h3>Адррес по умолчанию:</h3>
				<p class="dicsr">место на карте, если нет объектов<p>
			</td>
			<td>
				<input type="text" name="IGNET_G_MAP_default_center" value="<?php echo get_option('IGNET_G_MAP_default_center'); ?>" />
			</td>
		</tr>
		
		<tr class="title">
			<td>
				<h3>Иконка объекта на карте</h3>
			</td>
			<td>
			</td>
		</tr>
		
		<tr>
			<td>
				Создавать иконку из миниатюры?
			</td>
			<td>
				<input type="hidden" name="IGNET_G_MAP_icon_work" value="0" />
				<input <?php checked(get_option('IGNET_G_MAP_icon_work'), 1); ?> type="checkbox" name="IGNET_G_MAP_icon_work" value="1" />
			</td>
		</tr>
		<tr>
			<td>
				Сделать миниатюру обязательной?<br>
				<p class="dicsr">посты без миниатюры будут сохранятся <br>только в статусе черновиков<p>
			</td>
			<td>
				<input type="hidden" name="IGNET_G_MAP_icon_ever" value="0" />
				<input <?php checked(get_option('IGNET_G_MAP_icon_ever'), 1); ?> type="checkbox" name="IGNET_G_MAP_icon_ever" value="1" />
			</td>
		</tr>		

		<tr class="title">
			<td>
				<h3>Инфобокс объекта</h3>
			</td>
			<td>
			</td>
		</tr>
		
		<tr>
			<td>
				Создавать инфобоксы?
			</td>
			<td>
				<input type="hidden" name="IGNET_G_MAP_infobox_work" value="0" />
				<input <?php checked(get_option('IGNET_G_MAP_infobox_work'), 1); ?> type="checkbox" name="IGNET_G_MAP_infobox_work" value="1" />
			</td>
		</tr>
		<tr>
			<td>
				Выбрать фото по умолчанию?
			</td>
			<td>
				<label id="user_photo_label" for="IGNET_G_MAP_infobox_default_img">
					<?php 
						$default_img_url = get_option('IGNET_G_MAP_infobox_default_img');
						$default_img_url = empty($default_img_url)?IGNET_G_MAP_URL.'/image/default_user_photo.jpg':$default_img_url;
					?>
					<img class="user_photo_img" width="50" src="<?php echo $default_img_url;?>">
					<input id="upload_image_button" value="Заменить?" type="button">
				</label>
				<input id="upload_image" type="hidden" name="IGNET_G_MAP_infobox_default_img" value="<?php echo get_option('IGNET_G_MAP_infobox_default_img'); ?>" />
			</td>
		</tr>		
		
		<tr>
			<td>
				Какое поле использовать как цену?
			</td>
			<td>
				<select name="IGNET_G_MAP_infobox_price" >
					<?php IGNET_get_fields_to_infobox_price(); ?>
				</select>
			</td>
		</tr>		
		<tr>
			<td>
				Текст после цены
			</td>
			<td>
				<input type="text" name="IGNET_G_MAP_infobox_price_name" value="<?php echo get_option('IGNET_G_MAP_infobox_price_name'); ?>" />
			</td>
		</tr>		
		<tr class="title">
			<td></td>
			<td>
				<input name="map_save" type="submit" value="Сохранить изменения" class="save_button" />
				<input type="hidden" name="action" value="map_save" />
			</td>		
		</tr>	
	</table>
	</form>