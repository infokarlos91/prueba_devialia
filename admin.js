jQuery(document).on('click',"span.save_default_shortcode",function(){
	jQuery(this).attr('disabled','true');
	
	 if(jQuery('#cboxhover').is(':checked')) {
		var en_hover = "true";
	}else{
		var en_hover = "false";
	}
	jQuery.ajax({
		type: 'POST',
		url: admin_url.ajax_url,
		data: {
		'action'        :   'actualizar_opciones_shortcode',
		'en_hover'        :   en_hover,
		} ,
		success: function (data) {
			jQuery(this).attr('disabled','false');
			jQuery(".save_default_shortcode").empty();
			jQuery(".save_default_shortcode").append("Actualizado");
		} ,
		error: function (errorThrown) {
			console.log("Error");
		} 
	});
});
jQuery(document).ready(function() {
    jQuery('#cboxhover').change(function() {
        if(this.checked) {
           jQuery(".shortcode_final").append(' hover="true"');
        }else{
			var str = jQuery(".shortcode_final").text();
			var res = str.replace(' hover="true"', '');
			jQuery(".shortcode_final").empty();
			jQuery(".shortcode_final").append(res);
			
		}   
    });
});
