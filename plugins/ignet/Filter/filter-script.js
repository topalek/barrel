jQuery(function(){
	// фильтрация ввода в поля
	jQuery('.cost-input').keypress(function(event){
		var key, keyChar;
		if(!event) var event = window.event;
		
		if (event.keyCode) key = event.keyCode;
		else if(event.which) key = event.which;
	
		if(key==null || key==0 || key==8 || key==13 || key==9 || key==46 || key==37 || key==39 ) return true;
		keyChar=String.fromCharCode(key);
		
		if(!/\d/.test(keyChar))	return false;
	
	});
});

function slider_init( prfx, min, max){
	var prefix = '_'+prfx;
	jQuery("#price_slider"+prefix).slider({
		min: min,
		max: max,
		values: [min, max],
		range: true,
		stop: function(event, ui) {
			jQuery("input#minCost"+prefix).val(jQuery("#price_slider"+prefix).slider("values",0));
			jQuery("input#maxCost"+prefix).val(jQuery("#price_slider"+prefix).slider("values",1));
			
		},
		slide: function(event, ui){
			jQuery("input#minCost"+prefix).val(jQuery("#price_slider"+prefix).slider("values",0));
			jQuery("input#maxCost"+prefix).val(jQuery("#price_slider"+prefix).slider("values",1));
		}
	});	
	
	if(jQuery("#minCost"+prefix).val() == ''){
		jQuery("#minCost"+prefix).val('0');
	}
	
	if(jQuery("#maxCost"+prefix).val() == ''){
		jQuery("#maxCost"+prefix).val('10000');
	}	
	
	jQuery("#price_slider"+prefix).slider("values",0, jQuery("#minCost"+prefix).val());	
	jQuery("#price_slider"+prefix).slider("values",1, jQuery("#maxCost"+prefix).val());	
}

function change_min_cost(prfx){
	var prefix = '_'+prfx;
	var value1=jQuery("input#minCost"+prefix).val();
	var value2=jQuery("input#maxCost"+prefix).val();

	if(parseInt(value1) > parseInt(value2)){
		value1 = value2;
		jQuery("input#minCost"+prefix).val(value1);
	}
	jQuery("#price_slider"+prefix).slider("values",0,value1);
}

function change_max_cost(prfx){
	var prefix = '_'+prfx;
	var value1=jQuery("input#minCost"+prefix).val();
	var value2=jQuery("input#maxCost"+prefix).val();
		
	if(parseInt(value1) > parseInt(value2)){
		value2 = value1;
		jQuery("input#maxCost"+prefix).val(value2);
	}
	jQuery("#price_slider"+prefix).slider("values",1,value2);
}