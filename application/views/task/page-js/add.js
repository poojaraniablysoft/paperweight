/** Multiple users **/
/* $(function() {
	function split( val ) {
		//return val.split();
	}

	function extractLast( term ) {
		return split( term ).pop();
	}

	$('#invitees')
	//don't navigate away from the field on tab when selecting an item
	.bind( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB &&
		$( this ).data( "ui-autocomplete" ).menu.active ) {
			event.preventDefault();
		}
	})
	.autocomplete({
		source: function( request, response ) {
			$.getJSON(generateUrl('task', 'search_writers', '', '', 0), {
				term: extractLast( request.term )
			}, response );
		},
		search: function() {
			// custom minLength
			var term = extractLast( this.value );
			if ( term.length < 2 ) {
				return false;
			}
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function( event, ui ) {
			var terms	= split( this.value );
			var ids		= $('#invitee_ids').val();
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			
			ids += ui.item.id + ',';
			$('#invitee_ids').val(ids);
			return false;
		}
	});
}); */

/** Single user **/
$(function() {
	$( "#invitees" ).autocomplete({
		source: generateUrl('user', 'search_writers', '', '', 0),
		minLength: 2,
		select: function( event, ui ) {
			$('#invitee_ids').val(ui.item.id);
		},
		response: function(event, ui) {
			if (!ui.content.length) {
				var noResult = { value:"",label:"No results found" };
				ui.content.push(noResult);
			}
		}
	});
});
$(document).ready(function(){
	var currentDate = new Date();
	var day = currentDate.getDate();
	var month = currentDate.getMonth() + 1;
	var year = currentDate.getFullYear();
	var my_date = month+"-"+day+"-"+year;
	/* $("#task_due_date").datetimepicker({
		format: 'M d, Y H:i',
		minDate: my_date
	}); */
	/*invities delete manually*/
	$('#invitees').keyup(function(){	
		if( $(this).val() == "" ){			
			$("#invitee_ids").val( "" );
		}
	});
});

function confirmation() {
	return confirm('If you change and save the details of your order - all writers\' bids will become outdated. Do you want to continue?');
}

function add_new(ref){
	/* if($(".uploads-file tr").length > 5) {
		$('#common_error_msg').html('<div class="div_error"><ul><li>You are not allowed to upload files more than five.</li></ul></div>');
		return false;
	} */
	var row = '<tr><td><input id="task_file" type="file" title="" name="task_files[]"> <a onclick="return add_new(this);" href="javascript:void(0);" class="add-file"><i class="icon ion-plus-round"></i></a></td></tr>';
	if($(ref).hasClass('add-file')){
		$(ref).addClass('remove-file').removeClass('add-file').attr('onclick','return remove_new(this)').html('<i class="icon ion-minus-round">');
	}
	$(".uploads-file tr:last-child").before(row);
	return;
}

function remove_new(reff) {
	$(reff).parent().parent().remove();
	return;
}	

