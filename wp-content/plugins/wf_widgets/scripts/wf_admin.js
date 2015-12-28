
/*
v6.74	3/1/15	Latest jQuery UI 1.11 Sortable Widget breaks the interface because it changes <tr class="vscroller">
				to <tr class="vscroller ui-sortable-handle"> - meaning that we can't extract the wigetType so easily.
				Oddly, the first tab never(?) adds "ui-sortable-handle" to the rows - even though they sort nicely.

v6.71	25/9/14	Added delay for validation in wf_admin.js when OK pressed.

v6.60	6/2/14	Added Checkbox for Cancel inheritance?

v6.50	6/10/13	Disabled param_delete is now disabled! Also adjusted z-index of tooltips.

v6.11	13/6/13	Replaced hardwired urls for glob.theme_dir and glob.plugins_dir with localize_script versions from admin.php.
				NB: now 'plugins_dir' not 'plugin_dir' - and trailing slashes added. Hardwired versions started 'http//' etc.

v5.15	8/5/13	json_validate.php now returns the validation function name as well as feedback - so we can do client-side checks

*/

var fn = (function ($, W) { // now have access to globals jQuery (as $) and window (as W)
var glob ={};
//glob.theme_dir = '/wingtheme/wp-content/themes/wingtheme2/';//'http://www.graphicdesignleeds.info/wingtheme/wp-content/themes/wingtheme/';
//glob.plugin_dir = '/wingtheme/wp-content/plugins/';//'http://www.graphicdesignleeds.info/wingtheme/wp-content/plugins/';
glob.theme_dir = wf_admin_params.theme_dir + '/'; // get_stylesheet_directory_uri() // v6.11
glob.plugins_dir = wf_admin_params.plugins_dir + '/'; // plugins_url() // v6.11
//alert(glob.theme_dir); // '/wingtheme/wp-content/themes/wingtheme2'
//alert(glob.plugins_dir); // '/wingtheme/wp-content/plugins'
glob.widgetType = false;

jQuery(document).ready( function($){
	
	// WF WIDGET INTERFACE v3.84 ///////////////////////////////////////////
	
	
	
	// HELPER FUNCTIONS ///////////////////////////////////////////////////////////////////////////////////////
	
	
	//stackoverflow.com/questions/8051975/access-object-child-properties-using-a-dot-notation-string
	// console.log(getProp(r, "b.b2"));
	function getProp(obj, desc) {
		var arr = desc.split(".");
		while(arr.length && (obj = obj[arr.shift()]));
		return obj;
	}
	
	// purge list of updates
	function remove_lines_starting_with(needle, haystack) { // eg: "right-"
		var hay_array = haystack.split("\n");
		var needlelen = needle.length;
		var len = hay_array.length;
		while (len--) {
			if(hay_array[len].substr(0,needlelen) == needle) { 
				hay_array.splice(len, 1);
			}
		}
		return hay_array.join("\n");
	}
	
	function flush_all(region) {
		if(glob.flushAll[region]) { // they may have already been flagged for deleting
			return;
		}
		glob.flushAll[region] = true;
		$('#list_of_deletes').val(function(i,val) { 
			return val + (val ? "\n" : '') + glob.originals[region].join("\n"); // sets up this textarea with all the original names
		});
	}
	
	function recreate_hidden_inputs(region) {
		// delete existing hidden textareas for this region and recreate new ones from glob.cfields[current_region]
		$("#cfields_to_update textarea[name^='"+region+"-']").remove(); // 'starts-with' selector			
		var listOfUpdates = remove_lines_starting_with(region+'-', $("#list_of_updates").val());
		
		$.each(glob.cfields[region], function(num, value) {
			var cfield_name = region+'-'+num+'-'+value.widget; // eg: 'right-0-post'
			listOfUpdates += (listOfUpdates ? "\n" : '') + cfield_name;
			var txtarea_val = '';
			$.each(value.params, function(param_num, nvpair) {
				txtarea_val +=  ((param_num==0) ? '' : "\n")+nvpair.pname+'='+nvpair.pvalue;
			});
			$('#cfields_to_update').append("<textarea  cols='30' rows='2' name='"+cfield_name+"'>"+txtarea_val+"</textarea>\n");
		});
		
		$("#list_of_updates").val(listOfUpdates);
	}
	
	
	
	
	function set_up_sortable(current_region) {
		var $this_widget_sort = $('.active_region .widget_sort');
		$this_widget_sort.sortable({ // <tbody>
			axis: 'y',
			containment: "parent",
			tolerance: 'pointer',
			stop: function(event, ui) { 
				$this_widget_sort.find('td.order').each(function(i) {
					var $this = $(this);
					glob.cfields[glob.current_region][($this.text())-1].order_num = i; // set order_num to new i
					$this.text(i+1); // update the visible order number for this table row
				});
				glob.cfields[current_region].sort(function(a, b) {return a.order_num - b.order_num}); // sort by order_num
				flush_all(current_region);
				recreate_hidden_inputs(current_region);
			},
			cancel: "tr:only-child",
			distance: 5,
			helper: "clone"
		}).disableSelection();
	}
	
	
	
	
	
	// On first visit to this region tab, set up globals cfields and originals
	// If widget order numbers don't run 0,1,2 etc, set up textareas to recreate them on save
	function visit_region(region_index) {
		var $activeRegionDiv = $('#region'+region_index);
		$('.region').removeClass('active_region');
		$activeRegionDiv.addClass('active_region');
		var current_region = $activeRegionDiv.find('h4').attr('class');
		glob.current_region = current_region;
		set_up_sortable(current_region);
		
		if (!(current_region in glob.cfields)) {
			glob.cfields[current_region] = [];			
			glob.originals[current_region] = []; // list of original customfield names so we can delete them if reqd
			// "#the-list" is WordPress's <tbody> displaying all the customfields
			
			// v6.60
			var $inheritance = $("#the-list input[value='"+current_region+"_cancel']");
			glob.inheritance[current_region] = '0';
			if ($inheritance.length) { // ie it exists
				glob.inheritance[current_region] = ''+$inheritance.parent().parent().find('textarea').val();
			}
			// set the checkbox to match the customfield
			$('#'+current_region+'_break_inherit').prop('checked', (glob.inheritance[current_region]=='1'));
			$('#cfields_to_update').append("<textarea  cols='30' rows='2' name='"+current_region+"_cancel'>"+glob.inheritance[current_region]+"</textarea>\n");
			var listOfUpdates = $("#list_of_updates").val();
			listOfUpdates += (listOfUpdates ? "\n" : '') + current_region +"_cancel";
			$("#list_of_updates").val(listOfUpdates);
			//
			
			// To keep things simple, we use left_cancel instead of left-cancel // v6.60
			$("#the-list input[value^='"+current_region+"-']").each(function(i) { 
				glob.originals[current_region].push($(this).val());
				var key_array = $(this).val().split("-");
				var num = key_array[1];
				var cf_row = $(this).parent().parent(); 
				//var cf_meta = cf_row.attr('id').substr(5); // not used
				var cf_value = cf_row.find('textarea').val();
				var cf_params = {};
				glob.cfields[current_region][i] ={
					widget: key_array[2],
					//meta_id: cf_meta,
					params: cf_params,
					order_num: num
				};
				var params_array = cf_value.split("\n");
				$.each(params_array, function(j,param) {
					var	nameValPair = param.split("=");
					glob.cfields[current_region][i]['params'][j] = {
						pname: $.trim(nameValPair[0]),
						pvalue: $.trim(nameValPair[1])
					};
				});
			});
			
			glob.cfields[current_region].sort(function(a, b) {return a.order_num - b.order_num});
			
			var flush = false; // delete all existing widget customfields for this post and recreate from scratch?
			glob.widgetCount[current_region] = glob.cfields[current_region].length;
			for (var i=0; i<glob.widgetCount[current_region]; i++) {
				if(glob.cfields[current_region][i].order_num != i) {
					flush = true; // dealing once and for all with legacy stuff that's not in order 0,1,2
					glob.cfields[current_region][i].order_num = i; // set order_num to correct value
				}
			}
			if(flush) {
				flush_all(current_region);
				recreate_hidden_inputs(current_region);
			}			
		}

	}
	
	
	glob.inheritance ={}; // v6.60	
	glob.cfields ={}; // array of customfield data
	glob.originals = {};
	glob.flushAll = {};
	glob.widgetCount = {};
	visit_region(0);
	
	 
	
    $("#admin_region_list").tabs({
		activate: function( event, ui ) {
			visit_region(ui.newTab.index());
		}
	});
	
	$("#update2").click(function () { // Copy of Update button at bottom of Wf widgets box
		$('#publish').trigger('click');
		return false;
	});
	
	
		
		
		/* cfields.right is an array (so we can sort it) and looks like this...
		
		[0]=> {
			widget: 'post',
			params: {
				0: {
					pname: 'ids',
					pvalue: '142'
				}
				1: {
					pname: 'comment',
					pvalue: 'Potentials bucket-head photo'
				}
			}
		},
		[1]=> {
			widget: 'random',
			params: {
				0: {
					pname: 'id',
					pvalue: '20'
				}
				1: {
					pname: 'style',
					pvalue: 'quiet'
				}
			}
		}
	
		*/
		
	glob.activePlugins = $('#active_plugin_dirs').text(); // v6.3 string, eg: syntaxhighlighter,wf_library,wf_widget_forms,wf_widgets
	$('#active_plugin_dirs').remove();
	
	glob.userRoles = $('#user_roles').text(); // v6.5 string, eg: administrator,editor,author,contributor,subscriber,civi_contact
	$('#user_roles').remove();
	
	
	// Load widget specs
	$.getJSON(glob.plugins_dir + 'wf_widgets/json_specs.php?callback=?', {activePlugins: glob.activePlugins}, function(specs_results){ // v6.11
		if(!specs_results) {
			alert("Can't find widget specs");
		}
		glob.specs = specs_results;
		
		/* glob.specs looks like this...
		
		"random":{
			"display_name":"Random text widget",
			"class_name":"Random_widget",
			"params":{
				"id":{
					"reqd":true,
					"validation":"id",
					"dfault":false
					},
				"comment":{
					"reqd":false,
					"validation":"string",
					"dfault":false
					},
				"style":{
					"reqd":false,
					"validation":"style",
					"dfault":false
				}
			}
		}
		*/
				
				
		glob.postTypePicker = $('#post_type_picker').html(); //v5.19 stash until needed by list widget v6.2
		$('#post_type_picker').remove(); // v6.2
		
				
		// Move available items to the last item in .ui-dialog - which fortunately by now seems to have been created!
		var availableItemsHtml = $('#available_items').html();
		$('#available_items').remove();
		$( "#widget_dialog_box" ).parent().append("<div id='available_items'>\n" + availableItemsHtml + "</div>\n");
			
		//add_widget_selector();
		$('.region').append(get_widget_picker()); // adds to every region - done in js because needs access to specs
		
		
		$("#widget_box").show();
		
		
		// Now create the arrays of available post IDs
		glob.post_ids = {};
		$('#available_items .post_type').each(function(i,ptype){
			var ptname = $(this).attr('name');
			glob.post_ids[ptname] = [];
			$(this).find('option').each(function(j,opt){
				var val = $(this).attr('value');
				if(val != '0') {
					glob.post_ids[ptname].push(val);
				}
			});
		});
		
		// Now create the arrays of available cat IDs
		glob.term_ids = {};
		$('#available_items .taxonomy').each(function(){
			var taxname = $(this).attr('name');
			var ptname = (taxname == 'category') ? 'post' : taxname.substr(0,taxname.length-5);
			glob.term_ids[ptname] = [];
			$(this).find('option').each(function(j,opt){
				var val = $(this).attr('value');
				if(val != '0') {
					glob.term_ids[ptname].push(val);
				}
			});
		});
		var dummy; // breakpoint only!
		
		// PARAMETER SELECTION /////////////////////////////////////////////////////////////////////////////
		
		
		// Remove any existing parameter selector, and append a new one to the end of #widget_editor 
		function add_param_selector(widgetType) {
			var this_widget_params = getProp(glob.specs, widgetType+".params");	
			var existingParams = [];
			$('#params').find('label').map(function(){
				existingParams.push($(this).attr('for'));
			});
			var html = "<select class='add_param' name='params'><option value='0' selected='selected'>Add parameter</option>";
			var c=0;
			$.each(this_widget_params, function(name, value) {
				if($.inArray(name,existingParams) == -1) {
					html += "<option value='"+ name +"' >"+ name +"</option>"
					c++;
				}
			});
			if(c == 0) { // there are no further params that can be added
				html = '';
			} else {
				html += "</select>";
			}
			var $widget_editor = $('#widget_dialog_box');
			$widget_editor.find('.add_param').remove();
			$widget_editor.append(html);
		}
		
		
		
		
		// "Add a parameter" dropdown has changed
		$(".add_param").live('change',function (e) {
			var pname = $(".add_param option:selected").text();
			var this_widget_params = getProp(glob.specs, glob.widgetType+".params");
			var html = make_param_row(pname,this_widget_params,'');
			$('#params tbody').append(html);
			$('#params tr:last input, #params tr:last select').each(function(){ // should return just one item
				validate_param($(this));											 
			});														 
			add_param_selector(glob.widgetType);
		});
		
		
		function make_param_row(pname,this_widget_params,val) {
			var validation = getProp(this_widget_params, pname).validation;
			var paramdesc = getProp(this_widget_params, pname).paramdesc;
			var reqd = getProp(this_widget_params, pname).reqd;
			var classReqd = (reqd) ? " reqd" : '';	
			var html = "<tr>";
			html += "<td class='pname" + classReqd + "'><label for='" + pname +"'>"+ pname +"</label></td>";
			if(validation.substr(0,1) == '=' || validation == 'bool' || validation == 'post_type') { //v5.19
				html += "<td>"+ get_dropdown(pname,validation,val) + "</td>"; // eg: 'style', '=green|blue', 'blue'
			} else {
				html += "<td><input class='widefat' type='text' name='"+ pname +"' id='"+ pname +"' value='"+val+"' size='30' /></td>";
			}
			html += "<td class='feedback'><i class='icon-large'></i></td>\n";
			html += "<td class='help'><div class='"+ pname +"'><i class='icon-large icon-info-sign' title='"+ paramdesc +"'></i><div></td>\n";
			html += "<td class='param_delete'>";
			if(reqd) {
				html += "<i class='disabled icon-large icon-remove-sign' title='Required parameter - can&rsquo;t delete'></i>";
			} else {
				html += "<i class='icon-large icon-remove-sign' title='Delete this parameter'></i>";
			}
			html += "</td>\n";
			html += "</tr>";
			return html;
		}
		
		
		
		// WIDGET SELECTION /////////////////////////////////////////////////////////////////////////////
		
		
		
		function get_widget_picker() {
			var html = "<select class='add_widget' name='widgets'><option value='0' selected='selected'>Add widget</option>";
			$.each(glob.specs, function(name, value) {
				var displayName = getProp(value, "display_name");
				html += "<option value='"+ name +"' >"+ displayName +"</option>"
			});
			html += "</select>";
			return html;
		}

		
		// "Add a widget" dropdown has changed
		$(".add_widget").live('change',function (e) {
			e.stopPropagation();
			glob.widgetType = $(this).find("option:selected").val(); // eg: random
			get_edit_dialog_table(false); // is_edit
		});
		

		$('.widget_table .delete i').live('click', function(e){
			e.stopPropagation();
			var $this = $(this);
			yesnodialog('Delete this widget', 'Cancel', $this);
		});



		// WIDGET EDITING /////////////////////////////////////////////////////////////////////////////

		
		$("#widget_box tr:not(.editing) .edit").live('click', function(e){
			e.stopPropagation();
			var $row = $(this).parent();
			$('#widget_box tr').removeClass('editing');
			//glob.widgetType = $row.attr('class');
			glob.widgetType = get_widgetType($row); // v6.74
			$row.addClass('editing');
			
			get_edit_dialog_table(true); // is_edit
		});
		
		function get_widgetType($row) { // v6.74
			var wType = $row.attr('class');
			wType = wType.replace('ui-sortable-handle','');
			return wType.replace(/\s/g,''); // trim
		}
		
				
		
		// Used to add a new widget or to edit an existing one
		function get_edit_dialog_table(is_edit) {
			glob.is_edit = is_edit;
			var dialogBox = $( "#widget_dialog_box" ); // wrapper round whole thing (inc available items)
			var dialogForm = $( "#dialog-form" );
			var this_widget_params = getProp(glob.specs, glob.widgetType+".params");
			var widget_display_name = getProp(glob.specs, glob.widgetType+".display_name");
			var widget_inserts = getProp(glob.specs, glob.widgetType+".inserts"); // true if this widget inserts posts
			var widget_description = getProp(glob.specs, glob.widgetType+".description");
			var val;
			
			if(is_edit) {
				var rownum = ($('.editing td.order').text())-1;
				var lookup = {};
				var pobject = glob.cfields[glob.current_region][rownum].params;
				for (var i in pobject) {
					lookup[pobject[i].pname] = pobject[i].pvalue;
				}
			}
			
			var html  = "<p class='desc'>" + widget_description + "</p>";
			
			html += "<table id='params' class='" + glob.widgetType + "' cellspacing='0'>\n";
			html += "<tbody>\n";
			$.each(this_widget_params, function(pname, pvalue) {
				val = '';
				if(is_edit) {
					val = (lookup.hasOwnProperty(pname)) ? lookup[pname] : '';
				}
				
				if(pvalue.reqd || val != '') {	
					html += make_param_row(pname,this_widget_params,val);
				}
			});
			html += "</tbody>\n";
			html += "</table>\n";
			
			dialogForm.html(html);
			add_param_selector(glob.widgetType);
			
			var boxTitle = (is_edit) ? 'Editing ' : 'Add a new ';
			dialogBox.dialog("option", "title", boxTitle + widget_display_name );
			
			if(widget_inserts) {
				$('#available_items').show();
			} else {
				$('#available_items').hide();
			}
			dialogBox.dialog( "open" );			
			
		}
		
	
		
		
		function get_dropdown(name,vstring,val) { // eg: 'style', '=green|blue', 'blue'
			var html = '';
			if(vstring == 'post_type') { // v5.19
				html = glob.postTypePicker;
				html = html.replace('value="'+ val +'"', 'value="'+ val +'" selected="selected"'); // for some reason, the quotes are this way round
				return html;
			}
			
			var seltext = '';
			html += "<select class='" + name + " widefat' id='"+ name +"' name='" + name + "'>";
			html += "<option value='0'>Select...</option>";
			
			var validation = (vstring == 'bool') ? 'true|false' : vstring.substr(1); // remove leading '='
			var	arr = validation.split('|');
			//var	i;
			for(var i in arr){
				seltext = (arr[i] == val) ? " selected='selected'" : '';
				html += "<option value='"+arr[i]+"' "+seltext+">"+arr[i]+"</option>";
			}
			html += "</select>";
			return html;
		}

		
		// CANCEL INHERITANCE HAS CHANGED ////////////////////////////////////////////////////////////////////////////////////
		
		// v6.60
		$(".break_inherit input").click(function (e) {
			e.stopPropagation();
			glob.inheritance[glob.current_region] = $(this).prop('checked') ? '1' : '0';
			var $textarea_cancel = $("#cfields_to_update textarea[name='"+glob.current_region+"_cancel']");
			$textarea_cancel.val(glob.inheritance[glob.current_region]);
			//alert ($textarea_cancel.val()); // NB doesn't visibly change in firebug
		});
		
		
		// CONFIRM DELETE MODAL DIALOG ///////////////////////////////////////////////////////////////////////////////////////
			
			
		// stackoverflow.com/questions/7919845/how-to-build-a-jquery-dialog-for-confirmation-yes-no-that-can-work-anywhere-in	
		function yesnodialog(button1, button2, element) {
			var btns = {};
			btns[button1] = function(){
				if(button1 == 'Delete this parameter') {
					element.parent().parent().remove();
					add_param_selector(glob.widgetType);
				}
				if(button1 == 'Delete this widget') {
					var $row = element.parent().parent();
					var rownum = ($row.find('td.order').text())-1;
					glob.cfields[glob.current_region].splice(rownum, 1); // removes that element
					glob.widgetCount[glob.current_region]--;
					flush_all(glob.current_region);
					recreate_hidden_inputs(glob.current_region); // because names may have changed
					
					if(glob.widgetCount[glob.current_region] == 0) { // last one
						alert("All deleted!");
						$('.active_region .current').remove();
						$('.active_region .inherited').show();
					} else {
						$row.remove();
						$('.active_region .widget_sort').find('td.order').each(function(i) {
							$(this).text(i+1); // update the visible order number for this table row
							glob.cfields[glob.current_region][i].order_num = i;
						});
					}
					
				}
				$(this).dialog("close");
			};
			btns[button2] = function(){
				$(this).dialog("close");
			};
			$("<div></div>").dialog({
				resizable: false,
				height:140,
				autoOpen: true,
				title: 'Confirm deletion',
				modal:true,
				buttons:btns,
				dialogClass: 'wp-dialog' // v6.45
			});
		}
		
		
		
		
		$('.param_delete i:not(.disabled)').live('click', function(e){ // v6.50 added :not(.disabled)
			e.stopPropagation();
			yesnodialog('Delete this parameter', 'Cancel', $(this));
		});
		
		
		
		function set_up_tooltips(element) {
			$(document).on('mouseenter', element, function(e) {			
				var offset = $(this).offset(); // coordinates relative to window (?)
				var titleText = $(this).attr('title');// "pname: " + pname;
				$(this).data('tipText', titleText)
				.removeAttr('title');
				$('<p class="tooltip wp-dialog"></p>')
					.text(titleText)
					.css('top', (offset.top - 16) + 'px')
					.css('left',(offset.left + 25)  + 'px')
					.appendTo('body') 
					.show();
			});
			$(document).on('mouseleave', element, function() {
				$(this).attr('title', $(this).data('tipText'));
				$('.tooltip').remove();
			});
			/* // v6.50
			if(element == '.help i') {
				$(document).on('click', element, function(e) {
					e.stopImmediatePropagation();
				});
			}
			*/
		}
		
		set_up_tooltips('.edit i');
		set_up_tooltips('.delete i');
		set_up_tooltips('.help i');
		set_up_tooltips('.param_delete i');
		set_up_tooltips('.feedback i');
		
	}); // Load widget specs $.getJSON ??
	
	
	
	
	
	// ADD-OR-EDIT-A-WIDGET MODAL DIALOG /////////////////////////////////////////////////////////////////////////////////////
	
	
	
	$( "#widget_dialog_box" ).dialog({
		autoOpen: false,
		height: 300,
		width: 400,
		modal: true,
		dialogClass: 'wp-dialog',
		buttons: {
			"OK": function() {
				var unvalidCount = $('#params tr:not(.ok) input, #params tr:not(.ok) select').length;
				if(unvalidCount != 0) { 
					// Bail out (ie: just sit and wait) if there are any parameters without class ok
					function secondAttempt() { // v6.71
						unvalidCount = $('#params tr:not(.ok) input, #params tr:not(.ok) select').length;
					}
					var timeoutID = setTimeout(secondAttempt,2000);
				}
				if(unvalidCount == 0) { // v6.71
				//} else { // all parameters are OK
					var widgetnum = (glob.is_edit) ? ($('.editing td.order').text())-1 : glob.cfields[glob.current_region].length;
					var params = {};
					var lookup = {}; // because it's a pain extracting pvalues from params
					var pval;
					//var $activeRegionDiv = $('.region_'+glob.current_region);
					$('#params tr').each(function(i) {
						var $select = $(this).find("select");
						if($select.length == 0) { // <input>
							pval = $(this).find('input').val();
						} else { // <select>
							pval = $select.find("option:selected").val();
						}
						params[i] = {
							pname: $(this).find('label').text(),
							pvalue: pval
						};
						lookup[params[i].pname] = params[i].pvalue;
					});
					// Update the internal model
					glob.cfields[glob.current_region][widgetnum] = {
						widget: glob.widgetType,
						params: params,
						order_num: widgetnum
					};
					// Update the displayed table of widgets
					var description = (lookup.hasOwnProperty('comment')) ? lookup['comment'] : '';
					var cfield_name = glob.current_region+'-'+widgetnum+'-'+glob.widgetType; // eg: 'right-0-post'
					var listOfUpdates = $("#list_of_updates").val();

					if(glob.is_edit) {
						$('.editing td.widget_instance_name').text(description); // only visible thing that can change
					} else { // it's a new widget
						var row_html = "<tr class='"+glob.widgetType+"'>\n";
						row_html += "<td class='order'>"+ (widgetnum + 1) +"</td>\n";
						row_html += "<td class='widget_type'>"+ glob.specs[glob.widgetType].display_name +"</td>\n";
						row_html += "<td class='widget_instance_name'>"+ description +"</td>\n";
						row_html += "<td class='edit'><i class='icon-large icon-edit' title='Edit this widget'></i></td>\n";
						row_html += "<td class='delete'><i class='icon-large icon-remove-sign' title='Delete this widget'></i></td>\n";
						row_html += "</tr>\n";
						if(widgetnum == 0) { // It's the first one, so needs the whole table creating
							var table_html = "<div class='current'>\n";
								table_html += "<p class='strapline'>Widgets on this page: &nbsp;(Drag to reorder)</p>\n";
								table_html += "<table cellspacing='0' class='widget_table current'>\n";
								table_html += "<tbody class='widget_sort'>\n";
								table_html += row_html;
								table_html += "</tbody>\n";
								table_html += "</table>\n";
								table_html += "</div>\n";
							$('.active_region .add_widget').before(table_html); // inserts table before (eg) 'region_right .add_widget'
							$('.active_region .inherited').hide();
							set_up_sortable(glob.current_region);
							// we also need to set region_cancel to 0  // v6.66
							$('#'+glob.current_region+'_break_inherit').prop('checked', false);
							glob.inheritance[glob.current_region] = '0';
							var $textarea_cancel = $("#cfields_to_update textarea[name='"+glob.current_region+"_cancel']");
							$textarea_cancel.val('0');
						} else { // the table already exists
							$('.active_region .widget_sort').append(row_html);
						}
						glob.widgetCount[glob.current_region] = widgetnum + 1;
					}
					
					// Update the textareas and list of updates
					var txtarea_val = '';
					$.each(params, function(param_num, nvpair) {
						txtarea_val +=  ((param_num==0) ? '' : "\n")+nvpair.pname+'='+nvpair.pvalue;
					});
					var $textarea = $("#cfields_to_update textarea[name='"+cfield_name+"']");
					
					if($textarea.length == 0) { // textarea doesn't exist yet (it's a new widget or we're editing one that so far hasn't needed updating) 
						$('#cfields_to_update').append("<textarea  cols='30' rows='2' name='"+cfield_name+"'>"+txtarea_val+"</textarea>\n");
						listOfUpdates += (listOfUpdates ? "\n" : '') + cfield_name;
						$("#list_of_updates").val(listOfUpdates);
															
					} else  { // we're editing one that's already there and on the list
						$textarea.val(txtarea_val);
					}
					
					$(this).dialog( "close" );
				}
				
			},
			Cancel: function() {
				$(this).dialog( "close" );
			}
		},
		close: function() {
			$('#widget_box tr').removeClass('editing');
			$(".add_widget").val('0');
		},
		open: function() {
			$('#params input, #params select').each(function() {
				validate_param($(this));											 
			});
		}
	});
		
	
	
	
	
	// VALIDATION /////////////////////////////////////////////////////////////////////////////////////

	
		
	
	$('#params input, #params select').live('change', function(event) {
		validate_param($(this));
		// and if it's the post_type parameter that's changed, recheck the cat parameter
		if($(this).attr('name') == 'post_type') {
			validate_param($('#dialog-form #cat'));
		}
	});
	
	
	
	// Check things like whether a post id exists
	function localChecks(field_params, validation) { // v5.14, v5.15
		var feedback;
	
		switch (validation) { // the validation function name - eg: validate_id
			
			
			case 'validate_comments_post_id': // v6.5
			
			case 'validate_id':
				if(validation == 'validate_comments_post_id'  && field_params.val == 'current') { // v6.5
					feedback = 'ok';
				} else {
					feedback = 'No post with this ID';
					$.each(glob.post_ids, function(index,id_array){
						if($.inArray(field_params.val,$(this)) !== -1 ) {
							feedback = 'ok';
							//alert(index);
						}
					});
				}
				break;
				
				
			case 'validate_ids':
				var test_array = field_params.val.split(',');
				var missing = []; // an array of all the specified ids that don't appear to exist
				//alert(test_array);
				for(var i in test_array){
					var found = false;
					var this_id = $.trim(test_array[i]);
					$.each(glob.post_ids, function(index,id_array){ // look for this id in each of the post_types
						if($.inArray(this_id,$(this)) !== -1 ) {
							found = true;
						}
					});
					if(!found) { // not found in any of the post_types
						missing.push(this_id);
					}
				}
				feedback = (missing.length == 0) ? 'ok' : 'Can\'t find: ' + missing.join(',');
				break;
				
			case 'validate_cat':
				var $post_type = $('#dialog-form #post_type');
				var ptype = ($post_type.length) ? $post_type.val() : 'post';
				
				var test_array = field_params.val.split(',');
				var missing = []; // an array of all the specified ids that don't appear to exist
				for(var i in test_array){
					var found = false;
					var this_id = $.trim(test_array[i]);
					if($.inArray(this_id,glob.term_ids[ptype]) === -1 ) {
						missing.push(this_id);
					}					
				}
				feedback = (missing.length == 0) ? 'ok' : 'Can\'t find categories: ' + missing.join(',') + ' for post_type: ' + ptype;
				break;
				
			default:
				feedback = 'ok';
		}
			
		return feedback;
		
	}
		
		

	
	function validate_param($this) {
		var $row = $this.parent().parent(); // the <tr>
		var	field_params = {
			activePlugins: glob.activePlugins, // v6.5
			val: $this.val(),
			name: $this.attr('id'),
			widget: glob.widgetType,
			userRoles: glob.userRoles // v6.5
		};
		$row.removeClass('ok'); // in case we've just changed it to something invalid
		$.getJSON(glob.plugins_dir + 'wf_widgets/json_validate.php?callback=?', field_params, function(res){ // v6.11
			//v5.15	Now returns the validation function name as well as feedback - so we can do client-side checks
			if(res.feedback === false ) {
				//
			} else {
				var feedback = res.feedback;
				if(feedback == 'ok') { // v5.15
					feedback = localChecks(field_params, res.validation);
					//alert(feedback);
					if(feedback == 'ok' ) {
						$row.removeClass('error').addClass('ok');
					}
				}
				if(feedback != 'ok') { // can't use 'else' because value may have changed!
					$row.removeClass('ok').addClass('error');
				}
				$row.find('.feedback i').attr('title', feedback); // // v5.14 moved here so can change res if local checks fail
			}
		});
	}
	
			
	
});// jQuery(document).ready( function($){
	

return glob;

}(jQuery, window));

