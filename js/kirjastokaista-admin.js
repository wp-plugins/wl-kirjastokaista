jQuery(function($) {

	

	var kirjastokaista_options = {
  		get_ajax_categories:  true,
  		kirjastokaistaAPICategories: objectL10n.kirjastokaista_plugin_path + "/kirjastokaista-get-api.php?kirjastokaista_url=" + objectL10n.kirjastokaista_url + "&categories",
  		kirjastokaistaResult: "tools.php?just_result"
	}


	/* CHOOSE CATEGORIES */
	
    var $choose_categories = $("#modal_choose_categories");
    $choose_categories.dialog({                   
        'dialogClass'   : 'wp-dialog',
        'position': { 
				'my': 'top', 
				'at': 'top' 
			},         
        'width'			: 800,           
        'modal'         : true,
        'autoOpen'      : false, 
        'closeOnEscape' : true,      
        'buttons'       : [
			{
			'text' : objectL10n.kirjastokaista_txt_close,
			'click' : function() {
			kirjastokaista_updateSelectedCategories();
			$(this).dialog('close');
			}
			}
			]
    	});
    
    
    
    /* Open modal Choose Categories */
    $("#open_modal_choose_categories").click(function(event) {
        event.preventDefault();
        $choose_categories.dialog('open');
        
        /* Check flag */
        if (kirjastokaista_options.get_ajax_categories) {
        	$( "#modal_available_categories" ).html( objectL10n.kirjastokaista_txt_fetching );
	        /* Start fetching Kirjastokaista's api */
			var categoriesContentHTML = '<table border="0"><tr><td valign="top">';
			var categoriesLibraryContentHTML = '', categoriesRadioContentHTML = '', catsInArray = [], uncheckedCatsInArray = [];
	
			var selectedUI = $('#kirjastokaista_tools_ui').val();

			$.getJSON(kirjastokaista_options.kirjastokaistaAPICategories, function (json) {
				inArray = false;
				$.each( json.categories, function( key, value ) {
					
					if (value.is_radio != true && value.is_library != true) {	
						$.each( value.translations, function( tkey, tvalue ) {
							if (tkey == selectedUI) {
								categoriesContentHTML += '<label><input type="checkbox" class="category_value" value="' + tvalue.id + '" title="' + tvalue.name + '" />' + tvalue.name + '</label>';
								categoriesContentHTML += '<br />';
								catsInArray.push(tvalue.id);
							}
							
						});
					} else if (value.is_library == true) {
						$.each( value.translations, function( tkey, tvalue ) {
							if (tkey == selectedUI) {
								categoriesLibraryContentHTML += '<label><input type="checkbox" class="category_value" value="' + tvalue.id + '" title="' + tvalue.name + '" />' + tvalue.name + '</label>';
								categoriesLibraryContentHTML += '<br />';
								catsInArray.push(tvalue.id);
							}
							
						});

					} else if (value.is_radio == true) {
						$.each( value.translations, function( tkey, tvalue ) {
							if (tkey == selectedUI) {
								categoriesRadioContentHTML += '<label><input type="checkbox" class="category_value" value="' + tvalue.id + '" title="' + tvalue.name + '" />' + tvalue.name + '</label>';
								categoriesRadioContentHTML += '<br />';
								catsInArray.push(tvalue.id);
							}
							
						});						

					}
					
					
				});
				
				categoriesContentHTML += '</td><td valign="top" style="padding-left:50px;">';
				
				if (categoriesLibraryContentHTML != "") {
					categoriesContentHTML += '<h4 style="margin-top:0;">' + objectL10n.kirjastokaista_txt_libraries_own + '</h4>';
					categoriesContentHTML += categoriesLibraryContentHTML + '<br /><br />';
				}
				
				if (categoriesRadioContentHTML != "") {
					categoriesContentHTML += '<h4 style="margin-top:0;">' + objectL10n.kirjastokaista_txt_audio_categories + '</h4>';
					categoriesContentHTML += categoriesRadioContentHTML;
				}
				categoriesContentHTML += '</td></tr></table>';
				
				/* Show available categories on content div */
				$( "#modal_available_categories" ).html( categoriesContentHTML );
				
			});
		/* Don't re-run get categories  */
		kirjastokaista_options.get_ajax_categories = false;
		}
    });
    
    

     /* VIEW SHORTCODE RESULTS */
    
    var $view_results = $("#modal_view_results");
    $view_results.dialog({                   
        'dialogClass'   : 'wp-dialog',
        'width'			: 600,           
        'modal'         : true,
        'autoOpen'      : false, 
        'closeOnEscape' : true,      
        'buttons'       : [
			{
			'text' : objectL10n.kirjastokaista_txt_close,
			'click' : function() {
			$(this).dialog('close');
			}
			}
			]
    });
    
    /* Open modal View Shortcode Results */
    $("#open_modal_view_results").click(function(event) {
        event.preventDefault();
        $view_results.dialog('open');
        $( "#modal_shortcode_results" ).html( objectL10n.kirjastokaista_txt_fetching );
        $.getJSON(kirjastokaista_options.kirjastokaistaResult + shortcode_atts(true), function (json) {
        	var resultContent = '<p>', resultCount = 0;
        	$.each( json.posts, function( key, value ) {
				resultContent += '<a href="' + value.url + '" target="_blank">' + value.title + '</a><br />';
				resultCount++;
			});
			resultContent += '</p>';
        	resultContent = '<p>' + objectL10n.kirjastokaista_txt_hits + ': ' + resultCount + '</p>' + resultContent;
        	var isContainsCategory = shortcode_atts(true).indexOf('categories') > -1;
        	if (!isContainsCategory) {
	        	resultsMessage = '<p>&nbsp;</p><p><strong>' + objectL10n.kirjastokaista_txt_notice_choose_some_categories + '</strong></p>'
    	    	resultContent += resultsMessage;
        	}
			$( "#modal_shortcode_results" ).html( resultContent );
		});
    });

    
    /* GENERATE SHORTCODE */
    
    var $generate_shortcode = $("#modal_generate_shortcode");
    $generate_shortcode.dialog({                   
        'dialogClass'   : 'wp-dialog',
        'width'			: 600,        
        'position': { 
				'my': 'top', 
				'at': 'top' 
			},       
        'modal'         : true,
        'autoOpen'      : false, 
        'closeOnEscape' : true,      
        'buttons'       : [
			{
			'text' : objectL10n.kirjastokaista_txt_saveas,
			'click' : function() {
				$('#modal_save_shortcode_as').toggle();
			}
			},
			{
			'text' : objectL10n.kirjastokaista_txt_close,
			'click' : function() {
				$(this).dialog('close');
			}
			}
			]

    });


	/* Save Shortcode button press */
    $("#save_shortcode").click(function(event) {
        event.preventDefault();
        if ($( "#shortcode_name" ).val()) {
        	var generatedShortcode = '[kirjastokaista' + shortcode_atts(false) + ']';
        	var isCached = 0;
        	if ($('#shortcode_cachedresults').prop('checked')) {
        		isCached = 1;
        	}
        	$.post( "tools.php", { save_shortcode: generatedShortcode, name: $( "#shortcode_name" ).val(), description: $( "#shortcode_description" ).val(), cachedresult: isCached })
			.done(function( data ) {
				
				$( "#modal_generated_shortcode" ).append( '<p></p><strong>Shortcode saved as [kirjastokaista id="' + data + '"]</strong>' );
			   	$( "#shortcode_name" ).val("");
			    $( "#shortcode_description" ).val("");
			    $( "#shortcode_cachedresults" ).prop('checked', false);
				$( "#modal_save_shortcode_as" ).hide();
			});

    	} else {
    		alert("Please type a name for shortcode");
    	}
    });
    
    $(".removekey").click(function(event) {
        event.preventDefault();
  		if (confirm(objectL10n.kirjastokaista_txt_are_you_sure_delete + $(this).attr('data-id'))) {
			$(location).attr('href','tools.php?page=kirjastokaista&tab=shortcodes&remove=' + $(this).attr('data-id'));
		}
        
    });

    /* Open modal Generate shortcode */
    $("#open_modal_generate_shortcode").click(function(event) {
        event.preventDefault();
        $generate_shortcode.dialog('open');
 
		var shortcodeContentHTML = '[kirjastokaista' + shortcode_atts(false) + ']';

		/* Show available categories on content div */
		$( "#modal_generated_shortcode" ).html( shortcodeContentHTML );
			

    });


    function shortcode_atts(is_result) {

        var categoriesChecked = '', shortcode_categories = '', typesChecked = '', shortcode_types = '', languagesChecked = '', shortcode_languages = '', open_new_window = '', enable_caption = '', enable_fromtxt = '';
        
        if (is_result) {
         	var generated_shortcode = {
	  			ui: '&ui=' + $('#kirjastokaista_tools_ui').val(),
	  			layout: '&layout=' + $("input[type='radio'].kirjastokaista_tools_layout:checked").val(),
	  			order: '&order=' + $('#kirjastokaista_tools_orderby').val(),
	  			categories: '',
	  			types: '',
	  			languages: '',
	  			results: '&results=1'
			}
    	} else {
	        var generated_shortcode = {
	  			ui: ' ui="' + $('#kirjastokaista_tools_ui').val() + '"',
	  			layout: ' layout="' + $("input[type='radio'].kirjastokaista_tools_layout:checked").val() + '"',
	  			order: ' order="' + $('#kirjastokaista_tools_orderby').val() + '"',
	  			categories: '',
	  			types: '',
	  			languages: '',
	  			results: ' results="1"'
			}
    	}

        $('input[type=checkbox].category_value').each(function () {
        	if (this.checked) {
				categoriesChecked += $(this).val() + ',';
			}
		});
		if (categoriesChecked != '') {
			categoriesChecked = categoriesChecked.slice(0,-1);
			if (is_result) {
				generated_shortcode.categories = '&categories=' + categoriesChecked;
			} else {
				generated_shortcode.categories = ' categories="' + categoriesChecked + '"';
			}
		}
        $('input[type=checkbox].kirjastokaista_mediatypes').each(function () {
        	if (this.checked) {
				typesChecked += $(this).val() + ',';
			}
		});
		if (typesChecked != '') {
			typesChecked = typesChecked.slice(0,-1);
			if (is_result) {
				generated_shortcode.types = '&types=' + typesChecked;
			} else {
				generated_shortcode.types = ' types="' + typesChecked + '"';
			}
		}
        $('input[type=checkbox].kirjastokaista_medialanguages').each(function () {
        	if (this.checked) {
				languagesChecked += $(this).val() + ',';
			}
		});
		if ($('.kirjastokaista_medialanguages').is(':disabled') == false) { 
				if (languagesChecked != '') {
				languagesChecked = languagesChecked.slice(0,-1);
				if (is_result) {
					generated_shortcode.languages = '&languages=' + languagesChecked;
				} else {
					generated_shortcode.languages = ' languages="' + languagesChecked + '"';
				}
			}
		}
		if( $('#kirjastokaista_tools_results').val() ) {
			if (is_result) {
				generated_shortcode.results = '&results=' + $('#kirjastokaista_tools_results').val();
			} else {
				generated_shortcode.results = ' results="' + $('#kirjastokaista_tools_results').val() + '"';
			}
		}

		if (!is_result) {
	        if ($('#kirjastokaista_layout_newwindow').is(':checked')) {
				open_new_window += ' new_window="1"';
			}
			if ($('input[name=kirjastokaista_tools_layout]:checked').val() == "slideshow") {

		        if ($('#kirjastokaista_slideshow_enable_caption').is(':checked')) {
					enable_caption += ' enable_caption="1"';
				}
		        if ($('#kirjastokaista_slideshow_enable_kirjastokaistatext').is(':checked')) {
					enable_fromtxt += ' enable_fromtxt="1"';
				}
			}

		}

		return generated_shortcode.ui + generated_shortcode.categories + generated_shortcode.types + generated_shortcode.languages + generated_shortcode.layout + generated_shortcode.order + generated_shortcode.results + open_new_window + enable_caption + enable_fromtxt;

	}
    

    /* If Order by Most popular is selected, hide language optins due to Kirjastokaista API limitation */

    $("#kirjastokaista_tools_orderby").change(function(){
        var selectedValue = $(this).val();
        switch (selectedValue) {
        	case "newest": $(".kirjastokaista_medialanguages").prop('disabled', false); break;
        	case "mostpopular": $(".kirjastokaista_medialanguages").prop('disabled', true); break;
        	case "random": $(".kirjastokaista_medialanguages").prop('disabled', true); break;
    	}
    });
    
    $("#kirjastokaista_tools_ui").change(function(){
    	/* Change Categories flag so that it reloads again */
    	kirjastokaista_options.get_ajax_categories = true;
    });
    

    function kirjastokaista_updateSelectedCategories() {
    	var allVals = [];
	    $('#modal_available_categories table tbody tr td label input:checked').each(function () {
	    	allVals.push($(this).attr('title') + '<br />');
    	});
    	$('#chosen_categories').html(allVals);
	}

	$('input[type=radio][name=kirjastokaista_tools_layout]').change(function() {
		if ($('input[name=kirjastokaista_tools_layout]:checked').val() == "slideshow") {
			$('#slideshow_options').show();
		} else {
			$('#slideshow_options').hide();
		}
	});


});    