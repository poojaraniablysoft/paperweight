var tmr;
var timeout;

$(document).ready(function() {
	$('#loading').show();
	$('#queshow').load(test_question());
	
	var d				= new Date();
	var test_end_time	= d.getTime() + remaining_time;
	
	function updateTimer() {
		var d				= new Date();
		var available_sec	= Math.floor((test_end_time - d.getTime())/1000);
		var m				= Math.floor(available_sec/60);
		
		if (m < 10) m = '0' + m;
		
		var s = available_sec % 60;
		if (s < 10) s = '0' + s;
		
		if (available_sec > 0) {
			document.getElementById('timer').innerHTML = m + ' min ' + s + ' sec left';
		}
		else {
			document.getElementById('timer').innerHTML = 'Time out';
		}
		
		callAjax(generateUrl('test', 'update_remaining_time'), 'remaining_time=' + available_sec, function(){});
	}
	
	function stopUpdating() {
		return result(); 
	}
	
	tmr = setInterval(function() {updateTimer();}, 1000);
	
	setTimeout(stopUpdating, remaining_time);
});

function validate_ans() {
	if (!$('input[name=ans]:checked').val()) {
		$.facebox('<div class="popup_msg information">Please select one option or skip to next question.</div>');
		return;
	}
	
	updateAns();
}

function test_question() {
	callAjax(generateUrl('test', 'start_test'), 'quest=' + quenum, function(t){
		var ans = parseJsonData(t);
		var str = '';
		
		$('#loading').html("").hide();
		
		if (ans === false) {
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			str += '<form action="" method="post" id="frmQuiz" onsubmit="validate_ans(); return false;">';
			str += '<input type="hidden" name="question_id" value="' + ans.data['test_question_id'] + '">';
			str += '<div id="queshow">';
			str += '<table>';
			str += '<tr>';
			str += '<td align="center" colspan="2"><h5>Choose the word or phrase that best completes each sentence.</h5></td>';
			str += '</tr>';
			str += '<tr>';
			
			str += '<td align="right" width="30%"><strong>Question:</strong></td>';
			
			str += '<td>' + ans.data['quest_question'] + '</td></tr>';
			str += '<tr><td align="right"><strong>Answers:</strong></td>';
			
			str += '<td>';
			str += '<input name="ans" type="radio" value="1"> <strong>A</strong> - ' + ans.data['quest_opt1'] ;
			str += '<br /><input name="ans" type="radio" value="2"> <strong>B</strong> - ' + ans.data['quest_opt2'] ;
			str += '<br /><input name="ans" type="radio" value="3"> <strong>C</strong> - ' + ans.data['quest_opt3'];
			str += '<br /><input name="ans" type="radio" value="4"> <strong>D</strong> - ' + ans.data['quest_opt4'] ;
			str += '</td></tr>';
			
			str += '<tr><td align="right"><input type="button" onclick="return updateAns();return false;" class="greenBtn" value="Skip"></td><td><input name="btn_submit" type="submit" value="Save Answer" class="greenBtn"> </td></tr>'
			str += '</table>';
			str += '</div></form>';
		
			document.getElementById('queshow').innerHTML = str;
			
			quenum = ans.data['test_quest_order'];
			
			$('#num').html(quenum);
		}
		
		if (ans.status == 2) {
			$('#status').html('Grammar test is not available. Please contact administrator.');
		}
	});
}

function updateAns() {
	var ques_id	= $('input[name=question_id]').val();
	var ans		= $('input:radio[name=ans]:checked').val();
	
	callAjax(generateUrl('test', 'update_ans'), 'ques_id=' + ques_id + '&ans=' + ans, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) {
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			if (quenum == totalque) {
				return result();
			}
			else {
				test_question();
			}
		}
	});
}

function result() {
	callAjax(generateUrl('test', 'get_result'), '', function(t) {
		var ans = parseJsonData(t);
		var html = '';
		
		if (ans === false) {
			$.facebox('<div class="popup_msg failure">Oops ! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		clearInterval(tmr);
		clearTimeout(timeout);
		
		
		if (ans.status == 2) {
			html += '<div class="centerSection" id="status">';
			html += '<h3>Dear Applicant,</h3>';
			html += '<div class="congo-msg-div">Unfortunately, the number of mistakes have exceeded the allowed maximum, which means you have failed the test. Your registration is now closed.</div>';
			html +='</div>';
			
			document.getElementById('status').innerHTML = html;
			
			setTimeout(function() {
				document.location.href = generateUrl('user', 'signin');
			}, 8000);
			
			return;
		}
		
		if (ans.status == 1) {
			html += '<div class="centerSection sucess" id="status">';
			html +=	'<h3>Congratulations, you have passed the test!</h3>';
			html += '<div class="congo-msg-div">You have to submit a sample essay.<br>You will receive an email notification upon  verification within the next 2 business days.</div><a href="' + generateUrl('sample', 'default_action') + '" class="greenBtn">Go to Step 4</a>';
			html += '</div>';
			
			document.getElementById('status').innerHTML = html;
			return;
		}
	});
}
