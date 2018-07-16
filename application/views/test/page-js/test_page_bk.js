$(document).ready(function() {
	$('#loading').show();
	$('#queshow').load(test_question());
	
	var d = new Date();
	var test_end_time = d.getTime() + remaining_time;

	function updateTimer() {
		var d = new Date();
		var available_sec = Math.floor((test_end_time - d.getTime())/1000);
		var m = Math.floor(available_sec / 60);
		
		if (m < 10) m = '0' + m;
		
		var s = available_sec % 60;
		if (s < 10) s = '0' + s;
		
		document.getElementById('timer').innerHTML = m + ' mins ' + s + ' sec left';
		callAjax(generateUrl('test', 'update_remaining_time'), 'remaining_time=' + available_sec, function(){});
	}

	var tmr = setInterval(function() {updateTimer();}, 1000);
	
	function stopUpdating() {
		clearInterval(tmr);
		$.facebox('TimeOut');
		return result(0); 
	}
	setTimeout(stopUpdating, remaining_time);
	
});

function validate_ans(number) {
	if(!$('input[name="ans"]:checked').val()) {
		$.facebox('Please select any option or skip to next question!');
	}else { 
		
		test_question(number);
		
	 }
	return (false); 
}

function test_question(question_num_id) {

	var number = quenum++;			//question no.
	alert(quenum);
	alert(number);
	var results 	= $('input[name="ans"]:checked').val();
	var question_id = $('input[name="question_id"]').val();
	if(results == null) {
		results = 0;
	}
	if(question_id == null) {
		question_id = question_num_id;
	}
	
	if(number <= totalque){
		document.getElementById('num').innerHTML = number;
	}
	
	
	
	callAjax(generateUrl('test', 'start_test'), 'quest='+ (number++) + '&answer=' + results , function(t){
	
		var ans = parseJsonData(t);
		var str = '';
		
		if (ans.status == 'no_que') {
			noQues(0);
			return;
		}
		
		updateAns(results,question_id);
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		} 
		if (ans.status === 'Timeout') {
			result(0);
			return;
		}
		if (ans.status === 'Fail') {
			result(0);
			return;
		}
		if (ans.status === 'loggedout') {
			result(0);
			window.location.href=generateUrl('user','signin');
			return;
		}
		if (ans.question.length == 0) {
			result(results);
			return;
		};
		$('#loading').html("").hide();
		
		$.each(ans.question, function(index,arr) {
			str += '<form action="" method="post" class="siteForm" onsubmit="validate_ans('+ number +'); return false;">';
			str += '<input type="hidden" name="question_id" value="'+ arr['test_question_id'] +'">';
			str += '<div class="qWrap" id="queshow"><h3>Choose the word or phrase that best completes each sentence.</h3>';
			str += '<table width="100%" border="0" class="qTable" cellpadding="0" cellspacing="0">';
			str += '<tr>';
			
			str += '<td align="right" width="30%"><strong>Question:</strong></td>';
			
			str += '<td>'+ arr['quest_question'] +'</td></tr>';
			str += '<tr><td align="right"><strong>Answers:</strong></td>';
			
			str += '<td><table width="100%" border="0">';
			str += '<tr><td><input name="ans" type="radio" value="1"> <strong>A</strong> - '+ arr['quest_opt1'] +'</td></tr>';
			str += '<tr><td><input name="ans" type="radio" value="2"> <strong>B</strong> - '+ arr['quest_opt2'] +'</td></tr>';
			str += '<tr><td><input name="ans" type="radio" value="3"> <strong>C</strong> - '+ arr['quest_opt3'] +'</td></tr>';
			str += '<tr><td><input name="ans" type="radio" value="4"> <strong>D</strong> - '+ arr['quest_opt4'] +'</td></tr>';
			str += '</table></td></tr>';
			
			str += '<tr><td>&nbsp;</td><td><input name="btn_submit" type="submit" value="Save Answer" > <a href="javascript:void(0);" onclick="return test_question('+ arr['test_question_id'] +');" class="buttoncommon new skip_btn">Skip</a></td></tr>'
			str += '</table>';
			str += '</div></form>';
			ans.question.length--;
			
		});
		document.getElementById('queshow').innerHTML = str;
		
	}); 
}

function updateAns(results,question_id) {
	
	callAjax(generateUrl('test', 'update_ans'), 'answer=' + results + '&ques_id=' + question_id, function(t){
		var ans = parseJsonData(t);
		
		if(ans === false) {
			return false;
		}
		return true;
	});
}

/* function timer() {
	var current_date = new Date(); 
	var sec = current_date.getSeconds();
	//alert(sec); return false;
	var end_time = document.getElementById('start_time').value;
	//alert(current_date.getTime());
	//var end_time = new Date(parseInt(start_time) + (5*60*1000));
	
	var available_time = Math.floor((end_time - current_date.getTime())/1000);
	var available_minutes = Math.floor(available_time/60);
	var available_seconds = Math.floor(available_time%60);
	//var hours=CurrentDate.getHours()
	//var minutes=CurrentDate.getMinutes()
	//var seconds=CurrentDate.getSeconds()
	var msg = document.getElementById('timer');
	msg.innerHTML = available_minutes + ':' +available_seconds ;
}
	 */
	

function result(results){
	callAjax(generateUrl('test', 'result'), 'answer=' + results, function(r) {
		var rs = parseJsonData(r);
		var html = '';
		
		if (rs === false) {
			$.facebox('Oops ! Internal error.');
			return;
		}
				
		if (rs.status == 'Fail') {
			
				html += '<div class="centerSection" id="status">';
				html += '<h3>Dear Applicant,</h3>';
				html += '<p>Unfortunately, the number of mistakes has exceeded the allowed maximum, which means your have failed the test. Your registration is now closed.</p>';
				html +='</div>';
				document.getElementById('status').innerHTML = html;
				setTimeout(function() {
				window.location.reload();
				}, 5000);
				return;
				
			
		}
		if (rs.status == 'Success') {
			html += '<div class="centerSection sucess" id="status">';
			html +=	'<h3>Congratulations, you have passed the test!</h3>';
			html += '<p>You have to submit a sample essay.<br>You will receive an email notification upon the verification with the next 2 business days.</p><a href="'+generateUrl('sample','')+'" class="buttonGreen">Go to Step 4</a>';
			html += '</div>';
			document.getElementById('status').innerHTML = html;
			return;
		}
	});
}

function noQues(results){
	callAjax(generateUrl('test', 'noque'), 'answer=' + results, function(r) {
		var rs = parseJsonData(r);
		var html = '';
		
		if (rs === false) {
			$.facebox('Oops ! Internal error.');
			return;
		}
		
		if(rs.status ==1) {	
			html += '<div class="centerSection" id="status">';
			html += '<h3>Dear Applicant,</h3>';
			html += '<p>Unfortunately, grammar test is not available this time.Please contact to Administrator.</p>';
			html +='</div>';
			document.getElementById('status').innerHTML = html;
		}
		return;
		
	});
}

