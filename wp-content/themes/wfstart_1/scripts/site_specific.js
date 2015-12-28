// JavaScript Document
;(function ($, window, document, undefined) { // now have access to globals jQuery (as $) and window (as W)

/*
if(init_params.dataset.length == 0) {
	init_params.dataset = {}; // for some reason, wp_localize_script brings it in as an empty array!
}
*/
// brings in params:{ajax_url,group_array:{}} 



	$(function() {	
			   
		/*	   
		function get_user_meta(meta_key,callback) {
			//var self = this;			
			var ajax_args = {
				type: 'POST',
				url: window.params.ajax_url, // originally passed in by wp_localize_script()
				data: {
					action: 'l4c_get_user_meta' // eg: 'l4c_group_dialog'
					,meta_key: meta_key // 'skillset'
				},
				dataType: 'JSON', // essential if we want to get an array back
				success: function(data, textStatus, XMLHttpRequest){
					console.log(data.set,'data.set from get_user_meta');
					//callback.call(self,data.set);
					callback(data.set);
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			};
			console.log(ajax_args, 'get_user_meta: ajax_args');
			$.ajax(ajax_args);
			return;
		}
		*/
		
		var get_single_user_meta =function(user_id, meta_key,callback) {
			var ajax_args = {
				type: 'POST',
				url: window.map_params.ajax_url, // originally passed in by wp_localize_script()
				data: {
					action: 'l4c_get_single_user_meta', 
					user_id: user_id, // not necessarily the logged-in user
					meta_key: meta_key // eg: 'skillset'
				},
				dataType: 'JSON', // essential if we want to get an array back
				success: function(data, textStatus, XMLHttpRequest){
					console.log('data.result: ' + data.result);
					callback(data.result);
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			};
			console.log(ajax_args, 'get_single_user_meta: ajax_args');
			$.ajax(ajax_args);
			
			return;
		}
		
		
		function get_post_content(ele,post_id,callback) {
			//var post_id = ele.attr('id').substr(5); // .; // eg: btn1_1848
			if(window.params.current_user == '0') {
				return;
			}
			var ajax_args = {
				type: 'POST',
				url: window.params.ajax_url, // originally passed in by wp_localize_script()
				data: {
					action: 'l4c_get_post_content', 
					post_id: post_id // eg: 1848
				},
				dataType: 'JSON', // essential if we want to get an array back
				success: function(data, textStatus, XMLHttpRequest){
					if(data.result != 'dismissed') {
						callback(ele,data.result);
					}
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			};
			console.log(ajax_args, 'get_post_content: ajax_args');
			$.ajax(ajax_args);
			return;
		}

		
		
		
		$('#navmain ul ul')
			.addClass('dropdown-menu')
			.find('li:has(ul)')
			.addClass('dropdown-submenu');
		$('#navmain .nav > li:has(ul)')
			// all the rest added v2.28 for bootstrap 3
			.addClass('dropdown')
			.find('> a')
			.addClass('dropdown-toggle')
			.attr("data-toggle","dropdown")
			.attr("aria-expanded","false")
			.attr("role","button")
			.append(" <span class='caret'></span>");
		//$('#navmain .dropdown-menu li:has(ul)').addClass('dropdown-submenu');
		
		
		$('#wp-admin-bar-toggle_debug').click(function() {
			var debug_box = $('.debug_widget');
			if(debug_box.is(':visible')) {
				debug_box.hide();
			} else {
				debug_box.show();
			}
			return;										   
		});
		
		
		$('#beta').mouseover(function() {
			$('#disclaimer').show();
		});
		$('#beta').mouseleave(function() {
			$('#disclaimer').hide();
		});
		
		/*
		$('#users_display').on('load', function(e) {
			$(this).append("<div class='beta_popup'></div>");
		});
		*/
		
		//$('#test_box').addClass('bp').append("<div class='beta_popup' title='Beta Release'><h4>Site note</h4><p>When we complete the work on the messaging system and privacy options, you'll be able to use this map to contact other users.</p><p>Until then, it&rsquo;s just a statistical tool.</p><span>&nbsp;</span><a class='close' href='#'>Dismiss<i class='icon-large icon-remove-sign'></i></a></div>");
		
		
		var got_post_content = function(ele,content) {
			//alert(content);
			ele.addClass('bp').append("<div class='beta_popup' title='Beta Release'><h4>Site note</h4>"+content+"<span>&nbsp;</span><a class='close' href='#'>Dismiss<i class='icon-large icon-remove-sign'></i></a></div>");
		}
		
		$('[class*="bp_"]').each(function(index) { // eg: bp white_box bp_3017
			var post_id = $(this).attr('class').match(/bp_(\d+)/)[1];
			//alert(ele_class);
			var ele = $(this);
			get_post_content(ele, post_id, got_post_content);
		});
		
		
		
		$('body').on('click','.beta_popup .close', function(e) {
			e.preventDefault();	
			var beta_popup = $(this).parent();
			beta_popup.hide();
			var post_id = beta_popup.parent().attr('class').match(/bp_(\d+)/)[1];
			bp_dismissed(post_id);
		});
		
		function bp_dismissed(post_id) {
			//alert(post_id);
			var ajax_args = {
				type: 'POST',
				url: window.params.ajax_url, // originally passed in by wp_localize_script()
				data: {
					action: 'l4c_bp_dismissed', 
					post_id: post_id // eg: 1848
				},
				dataType: 'JSON', // essential if we want to get an array back
				success: function(data, textStatus, XMLHttpRequest){
					// nothing
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			};
			console.log(ajax_args, 'bp_dismissed: ajax_args');
			$.ajax(ajax_args);
			return;
		}
		
		
		
		
		
		//$('bp_1').dialog();
		
		// POPUP WIDGET - will probably interfere if widget version is used
		
		var start_height = 30; // wild guess
		
		$(".popup_widget").mouseover(function() {
			$(this).find('.sliding_bit').animate({"top": 0}, 300, 'swing');
		});
		$(".popup_widget").mouseleave(function() {
			$(this).find('.sliding_bit').animate({"top": start_height}, 300, 'swing'); // 130
		});
		
		
		// FAIR TEN CHALLENGE QUESTIONS PAGE //////////////////////////////////////////////////////////
		
		$(".the-2015-fair-ten-challenge-questions .reveal_head").click(function(e){
			var parent = $(this).parent(); // eg: div.reveal h3.reveal_head							
			//e.stopPropagation();
			var is_closed = parent.hasClass('closed');
			console.log('is_closed: ' + is_closed); 
			var opens = $(".reveal:not(.closed)");
			console.log('opens: ' + opens.length);
			opens.addClass('closed').find('.reveal_tail').slideUp(function(){
					//parent.addClass('closed');
				});
			//var tail = parent.find('.reveal_tail');
		});
		
		
		
		// PRIVACY & SHARING SETTINGS PAGE //////////////////////////////////////////////////////////
				
		
		if(window.map_params && window.map_params.page_slug == 'privacy-and-sharing-settings') {
			
			console.log('page_slug: ' + window.map_params.page_slug); //privacy-and-sharing-settings
			console.log('selected_user: ' + window.map_params.selected_user); 
			
			
			
			
			function profile_section_visibility(el) {
				var inner = el.parent().parent().find('.inner');
				if(el.is(":checked")) {
					el.parent().addClass('open');
					inner.show();
				} else {
					el.parent().removeClass('open');
					inner.hide();
				}
				//save_setting(); 
				//save_sharing(sharing_item, value);
				return;
			}
			
			
			
			//var sharing_data_single_user;
			
			var got_single_user_sharing = function(sharing_data) {
				console.log("sharing_data['pc_district']: " + sharing_data['pc_district']); 
				console.log("sharing_data['interests']: " + sharing_data['interests']);
				console.log("sharing_data['groups']: " + sharing_data['groups']); 
				console.log("sharing_data['skills']: " + sharing_data['skills']); 
				console.log("sharing_data['search']: " + sharing_data['search']); 
				
				
				//sharing_data_single_user = sharing_data;
				$.each(sharing_data, function(sharing_item,value) {
					var $el = $('#share_' + sharing_item); // eg: #share_pc_district
					if(sharing_item == 'search' && value > 0) { //v2.23 prob better to give an indication that no button has been specified yet
						/*
						var default_value = 2; // map marker only
						value = (value == 0) ? default_value : value;
						*/
						$('#map_sharing'+value).prop('checked',true); // eg: '#map_sharing4'	
					} else {
						$el.prop('checked',value); 
						profile_section_visibility($el);
					}
											  
				});
			};
			
			/* //JSON seems to change ints to strings!
			So prob best only to pass individual values by ajax and not whole (un)serialized arrays
				
			function save_sharing(sharing_item, value) {
				var new_meta_value = sharing_data_single_user;
				new_meta_value[sharing_item] = value;
				var ajax_args = {
					type: 'POST',
					url: window.map_params.ajax_url, // originally passed in by wp_localize_script()
					data: {
						action: 'l4c_save_sharing', 
						user_id: window.map_params.selected_user,
						meta_key: 'sharing',
						value: new_meta_value // not the individual value param above
					},
					dataType: 'JSON', // essential if we want to get an array back
					success: function(data, textStatus, XMLHttpRequest){
						// nothing
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				};
				console.log(ajax_args, 'save_sharing: ajax_args');
				$.ajax(ajax_args);
				return;
			}
			*/
			
			function save_sharing(sharing_item, value) {
				var ajax_args = {
					type: 'POST',
					url: window.map_params.ajax_url, // originally passed in by wp_localize_script()
					data: {
						action: 'l4c_save_sharing', 
						user_id: window.map_params.selected_user,
						sharing_item: sharing_item,
						value: value // the individual value param above
					},
					dataType: 'JSON', // essential if we want to get an array back
					success: function(data, textStatus, XMLHttpRequest){
						// nothing
						settings_saved();
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					}
				};
				console.log(ajax_args, 'save_sharing: ajax_args');
				$.ajax(ajax_args);
				return;
			}
			
			function settings_saved() {
				//alert("Settings saved");
				$("<div class='alert_message'>Your settings have been saved</div>").insertBefore('#rightcol').delay(1500).fadeOut(1000,function() {
				   $(this).remove(); 
				});
			}
			
			function map_sharing_visibility(value) { // v2.22
				return;
			}
		
			// initiate the prepopulation process		
			get_single_user_meta.call(null,window.map_params.selected_user,'sharing',got_single_user_sharing);
			
			// change visibility of sections when checkbox changes
			$(".profile_section input").change(function() {
				profile_section_visibility($(this));
				var value = ($(this).is(":checked")) ? 1 : 0;
				var sharing_item = $(this).attr('id').substr(6); // eg: share_pc_district --> pc_district - strips first 6 chars
				//alert('sharing_item: ' + sharing_item + '  value: ' + value);
				save_sharing(sharing_item, value);
			});
			
			$("#map_sharing input").click(function() {
				var value = $(this).val();
				map_sharing_visibility(value); // v2.22
				var sharing_item = 'search'; 
				//alert('sharing_item: ' + sharing_item + '  value: ' + value);
				save_sharing(sharing_item, value);
			});
			
			
			/*
			$("#profile_interests .l4c_edit_link").click(function(e) {
				e.preventDefault();	
				//alert('Edit interests: a work in progress');
			});
			*/
		
		}
		
		
		
		//$("#profile_wrapper, #map_wrapper").find() // only on privacy-and-sharing-settings page
		
		
		
		
		/*
		// set up initial visibility of sections  
		$(".profile_section input").each(function() {
			//this._get_user_meta.call(this,'sharing',this._sharing_settings); //v2.17
			profile_section_visibility($(this));
			//alert('yo');
		});
		*/
		
		
		
		
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		/*
		
		// Admin area
		function got_ed_details(results) {
			console.log(results, 'got_ed_details: results');
			
			var html = "<table cellspacing='0'>\n";
			html += "<tr><td class='heading'>Contact name:</td><td>"+results.ed_contact_name+"</td></tr>\n";
			html += "<tr><td class='heading'>Contact phone:</td><td>"+results.ed_contact_phone+"</td></tr>\n";
			html += "<tr><td class='heading'>Role:</td><td>"+results.role0+"</td></tr>\n";
			html += "<tr><td class='heading'>Main editor for:</td><td></td></tr>\n";
			html += "<tr><td class='heading'>Sub editor for:</td><td></td></tr>\n";
			html += "<tr><td class='heading'>Blogging:</td><td></td></tr>\n";
			html += "</table>\n";
			
			
			$('#ed_details').html(html);
			$('#ed_details').parent().show();
			return;
		}
		
		function get_ed_details(ed_id,callback) {
			var ajax_args = {
				type: 'POST',
				url: window.params.ajax_url, // originally passed in by wp_localize_script()
				data: {
					action: 'l4c_get_ed_details',
					ed_id: ed_id
				},
				dataType: 'JSON', // essential if we want to get an array back
				success: function(data, textStatus, XMLHttpRequest){
					if(data.user_array != 'dismissed') {
						callback(data.user_array);
					}
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					alert(errorThrown);
				}
			};
			//console.log(ajax_args, 'get_ed_details: ajax_args');
			$.ajax(ajax_args);
			return;
		}
		
		
		
		
		$(".main_editor").mouseover(function() {
			$(this).find('.cell_helper').hide().html("<div id='ed_details'></div>"); 
			var ed_id = $(this).find('a').attr('id').substr(11); // eg: main_ed_id_177 --> 177
			//alert(ed_id); // eg: main_ed_id_177
			get_ed_details(ed_id,got_ed_details);
		});
		$(".main_editor").mouseleave(function() {
			$('#ed_details').parent().hide();
			$('#ed_details').remove(); 
		});
		
		*/
		
		
		
		
		
		
		$('#filtered_search_submit').button();
		
		
			
		var group_list = [];
		$.each(params.title_array, function(id,title) {
			group_list.push({'label': title, 'value': id});
		});
		
		var temp;
		
		//console.log(group_list+' group_list');
		//console.log(params.slug_array+' params.slug_array');
		$( "#group_search" ).val('').autocomplete({
			//source: group_list // [ "c++", "java", "php", "coldfusion", "javascript", "asp", "ruby" ]
			source: function( request, response ) {
				var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
				response( $.grep(group_list, function( item ){
					return matcher.test( item.label );
				}) );
			},
			
			select: function(event,ui) {
				//alert(ui.item.value);
				$(this).val(ui.item.label);
				window.location.href = "http://www.leedsforchange.org.uk/l4c_group/"+params.slug_array[ui.item.value]+"/";
				//$(this).val(''); 
				return false;
			},
		})
		
		.blur(function() {
			//temp = jQuery.extend(true, {}, $(this).val());
			temp = $(this).val();
			//alert(temp);
		});
		/*									  
		.keydown(function(e){ // this bit doesn't appear to work
			var self = this;
			if (e.keyCode === 13){
				//e.preventDefault();
				window.location.href = "http://www.leedsforchange.org.uk/l4c_group/"+self.val()+"/";
			}
		});
		*/
		$( "#group_search_go" ).click(function(e) {
			e.preventDefault();
			var lookfor = temp;
			var flag = false
			for(var i=0; i< params.title_array.length; i++) {
				if(lookfor == params.title_array[i].label) {
					flag = true
				}
			}
			if(!flag) { 
				alert('Group not found');
			}
		}).button();
		
		$('[class*="wf_help_"]').each(function(index) { // 
			var post_id = $(this).attr('class').match(/wf_help_(\d+)/)[1];
			//$(this).find('.description').addClass('wf_help_link wfhl_' + post_id);
			//$(this).find('.wf_lining').append("<i class='icon-large icon-info-sign wf_help_link wfhl_" + post_id + "' >&nbsp;</i>");//fa fa-envelope
			if($(this).prop("tagName") == 'H2' || $(this).prop("tagName") == 'H3') {
				$(this).append("<i class='fa fa-lg fa-info-circle wf_help_link wfhl_" + post_id + "' >&nbsp;</i>");
			} else {
				$(this).find('.wf_lining').append("<i class='fa fa-lg fa-info-circle wf_help_link wfhl_" + post_id + "' >&nbsp;</i>");
			}
		});

		/* NB: This stared as a duplicate of the one in site_specific.js */
	function get_post_content(ele,post_id,callback) {
		if(window.params.current_user == '0') {
			return;
		}
		var ajax_args = {
			type: 'POST',
			url: window.params.ajax_url, // originally passed in by wp_localize_script()
			data: {
				action: 'l4c_get_post_content', 
				post_id: post_id // eg: 1848
			},
			dataType: 'JSON', // essential if we want to get an array back
			success: function(data, textStatus, XMLHttpRequest){
				if(data.result != 'dismissed') {
					callback(ele,post_id,data.result);
				}
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		};
		console.log(ajax_args, 'get_post_content: ajax_args');
		$.ajax(ajax_args);
		return;
	}
	
	
	// 
	var got_help_content = function(ele,post_id,content) {
		//alert(content);
		
			ele.append("<div id='help_popup_"+ post_id +"' class='help_popup' ><h4>Help note</h4>"+content+"<span>&nbsp;</span><a class='close' title='Close' href='#'><i class='icon-large icon-remove-sign'></i></a></div>");
		
	};
	
	$('body').on('click','.wf_help_link', function(e) {
		e.preventDefault();	
		var post_id = $(this).attr('class').match(/wfhl_(\d+)/)[1];
		var ele = $(this);
		var help_popup = $("#help_popup_"+ post_id);
		if(help_popup.length == 0) {
			get_post_content(ele, post_id, got_help_content);
		} else {
			help_popup.remove();
		}
	});
	
	$('body').on('click','.help_popup', function(e) {
			//e.preventDefault();	
			e.stopPropagation();	
		});
	
	$('body').on('click','.help_popup .close', function(e) {
			e.preventDefault();	
			e.stopPropagation();	
			var help_popup = $(this).parent();
			help_popup.remove();
		});
	
	/****************************************************************/
	
	
	//stackoverflow.com/a/22353173
	/*
	$('.dropdown-toggle').mouseover(function() {
        $(this).parent().find('.dropdown-menu').show();
    });
	
	$('.dropdown-toggle').mouseout(function() {
        $(this).parent().find('.dropdown-menu').hide();
    });
	*/

   /*
	$('.dropdown-toggle').mouseout(function() {
        t = setTimeout(function() {
            $('.dropdown-menu').hide();
        }, 100);

        $('.dropdown-menu').on('mouseenter', function() {
            $('.dropdown-menu').show();
            clearTimeout(t);
        }).on('mouseleave', function() {
            $('.dropdown-menu').hide();
        })
    });
	*/	
  
	});


})(jQuery, window, document);