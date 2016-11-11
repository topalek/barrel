( function( $ ) {
	$(window).load(function() {
		$('.upload_image_button').click(function(e){
			e.preventDefault();
			
			var button_obj = $(this);
			var multiple = button_obj.attr('multi');

			if(multiple == '0'){
				multiple = false;
			}
			else {
				multiple = true; 
			}
			var mime_type = button_obj.attr('mime_type');

			var custom_uploader = wp.media({
				className: 'media-frame ignet-media-frame',
				title: 'Загрузка медаифайлов',
				library: {
					type: mime_type
				},
				button: {
					text: 'Добавить'
				},
				multiple: multiple
			})
			.on('select', function() {
				
				add_attachments( custom_uploader.state().get('selection'), button_obj, mime_type );	
			})
			.open();   
		});
		
		$('.image_box').hover(
			function(){
				$(this).find("span").css({'opacity': '0.3'});
				$(this).find("a").css({'opacity': '1'});
			},
			function(){
				$(this).find("span").css({'opacity': '1'});
				$(this).find("a").css({'opacity': '0.5'});
			}
		);
		
		$('.remove_image_button').click(function(){remove_image( this )});
		
		/*
		* удаляем вложения из скрытого поля и из отображения в блоке
		*/		
		function remove_image( obj ){
			var div = $(obj).parent();
			var upload_input = $(obj).parent().parent().find("input.file_hidden_input");
			var att_id = $(obj).parent().attr("att_id");
			
			$(obj).parent().parent().parent()
					.find(".upload_image_button_div")
					.css({'display': 'block'});
					
			if( upload_input.val() != ''){
				var upload_input_val = upload_input.val().split(',');
			}
			else{
				var upload_input_val = [];
			}
			div.remove();
			upload_input_val.splice(upload_input_val.indexOf(att_id), 1)
			upload_input.val(upload_input_val.join(','));
		}
		
		/*
		* Добавляем картинки или архивы в скрытое поле и для отображения в блок
		*
		*/
		function add_attachments( attachments, button_obj ){
				var upload_input = button_obj.parent().parent().find("input.file_hidden_input");
				var multiple = button_obj.attr('multi');

				if( upload_input.val() != ''){
					var upload_input_val = upload_input.val().split(',');
				}
				else{
					var upload_input_val = [];
				}
				
				attachments.each(function( attach ){
					att_props = attach.toJSON();
					if( jQuery.inArray(jQuery.trim(att_props.id), upload_input_val) == -1 ){
						if(att_props.type == 'image'){
							if(att_props.sizes.thumbnail){
								var thumb_url = att_props.sizes.thumbnail.url;
							} else {
								var thumb_url = att_props.sizes.full.url;
							}
						}
						else{
							var thumb_url = att_props.icon;	
						}

						var div = $('<div/>', {
							"class": "image_box",
							"att_id": att_props.id,
							"style": "background-image: url("+thumb_url+")"
						});					
						
						var span = $('<span/>', {
							"text": att_props.title
						});					
						
						var button = $('<a/>', {
							"text": "×",
							"class": "remove_image_button button",
							"title": " Удалить? ",
							click: function(){remove_image( this )},
							mouseover: function(){$(this).prev().css({'opacity': '0.3'});},
							mouseout: function(){$(this).prev().css({'opacity': '1'});}
						});
						div.append( span );
						div.append( button );

						button_obj.parent().parent().find("div.tumb_results")
						.append( 
							div
							//JSON.stringify(att_props)
						);
						
						upload_input_val.push( att_props.id );
					}
				});
				
				if(multiple == '0'){
					$(button_obj).parent().parent().parent()
						.find(".upload_image_button_div")
						.css({'display': 'none'});
				}
				
				upload_input.val(upload_input_val.join(','));			
		}
	
	
	})
} )( jQuery );