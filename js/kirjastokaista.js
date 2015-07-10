(function($) {

    tinymce.PluginManager.add('kirjastokaista_tc_button', function( editor, url ) {
        editor.addButton( 'kirjastokaista_tc_button', {
            text: 'Kirjastokaista',
            icon: false,
            onclick: function() {

				 var embed_url= prompt('URL', ' ');

				 if ( (embed_url!=' ') && (embed_url!=null) ) 
				 { 
				 	$.ajax({
					  url: "/wp-admin/tools.php?single_embed=" + embed_url,
					  cache: false
					})
					.done(function( result ) {
						editor.insertContent(result);
					});				
				 }
                
            }
        });
    });

})(jQuery);