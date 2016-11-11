( function( $ ) {
	$(window).load(function() {
		window.send_to_editor = function(html) {
			//alert(html); //Это и есть полный обьект для вставки в пост
			imgurl = jQuery('img', html).attr('src');
			jQuery('#upload_image').val( imgurl );
			jQuery('.user_photo_img').attr({'src':imgurl});
			tb_remove();
		}
		
		jQuery('#upload_image_button').click(function(){ 
			formfield = $(this).next().attr('name');
			tb_show('', '/wp-admin/media-upload.php?type=image&amp;TB_iframe=true');
			return false;
		});	
	})
} )( jQuery );
