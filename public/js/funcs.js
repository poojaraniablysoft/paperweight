// JavaScript Document
// -------------------------------------------------------------
function toggleLayer(div)
{
	var x;
	if(!(x=document[div])&&document.all){x=document.all[div];}
	if(!x && document.getElementById){x=document.getElementById(div);}
	if(!x){return;}	
	if(x.style.display=="none"){x.style.display="block"}
	else{x.style.display="none"}
}

function showChat(div)
{
	var x; var y = document.getElementById('base');
	if(!(x=document[div])&&document.all){x=document.all[div];}
	if(!x && document.getElementById){x=document.getElementById(div);}
	if(!x){return;}	
	if(x.style.display=="none"){x.style.display="block";y.innerHTML="<strong>Hide</strong>"}
	else{x.style.display="none";y.innerHTML="<strong>Send Admin a Message</strong>"}
}

function showLayer(div)
{
	var x;
	if(!(x=document[div])&&document.all){x=document.all[div];}
	if(!x && document.getElementById){x=document.getElementById(div);}
	if(!x){return;}	
	if(x.style.display=="none"){x.style.display="block"}
}

function hideLayer(div)
{
	var x;
	if(!(x=document[div])&&document.all){x=document.all[div];}
	if(!x && document.getElementById){x=document.getElementById(div);}
	if(!x){return;}	
	if(x.style.display=="block"){x.style.display="none"}
}

function divUpdate(sdiv, durl, dat) {
    $.ajax({
		method: 'post',
		url : durl,
		cache: false,
		dataType : 'html',
		data: dat,
		success: function (text) { $(sdiv).html(text); }
    });
}

function checkThird() {
	var room = document.getElementById('Room_Type').value;
	var third = document.getElementById('third');
	if (room == 'Triple') { showLayer('third'); } else { hideLayer('third'); }
}

function addKeyPerson() {
	var row_str = '<div id="dupli"> <div id="minp">Title: <br /><input type="text" name="otitle[]" id="otitle[]" /></div><div id="minp">First Name: <br /><input type="text" name="ofname[]" id="ofname[]" /></div><div id="minp">Last Name: <br /><input type="text" name="olname[]" id="olname[]" /></div><div id="minp">Email: <br /><input type="text" name="oemail[]" id="oemail[]" /></div><div id="cint">Background, Education, Experience ... etc: <br /><textarea name="background[]" id="background[]" rows="1"></textarea></div></div>';
	$("#dupli").append(row_str);
}

function addKeyInnovator() {
	var row_str = '<div id="dupli"><div id="minp">Company:<br /><input type="text" name="company[]" id="otitle" /></div><div id="minp">URL: <br /><input type="text" name="url[]" id="ofname" /></div></div>';
	$("#dupli").append(row_str);
}

function addPhoto() {
	var row_str = '<div id="photli"><div id="minp">Choose Photo: <br /><input type="file" name="photo[]" id="photo[]" /></div></div>';
	$("#photli").append(row_str);
}

function addVideoLink() {
	var row_str = '<div id="vili"><div id="minp">Video URL (<em>Youtube, Vimeo ... etc</em>): <br /><input type="text" name="vurl[]" id="videourl" /></div></div>';
	$("#vili").append(row_str);
}

function Agree(section, id, cid, durl, ag) {
    $.ajax({
		method: 'post',
		url : durl+'.php',
		cache: false,
		data: "section="+section+"&id="+id+"&cid="+cid+"&ag="+ag
    });
	// ---------------------------------------------------
	$("#agd").load("agrdis.php", { rid:id } );
}

function expressOpinion() {
	
	var opinion = $('#opinion').attr('value');
	var opin = $('#opin').attr('value');
	var id = $('#id').attr('value');
	// ---------------------------------------------------
	$.ajax({
		type: "POST",
		url: "exo.php",
		data:"opinion="+opinion+"&opin="+opin+"&id="+id
	});
	// ---------------------------------------------------
	$("#commentform").load("cform.php", { rid:id } );
	divUpdate('#commentwall','cwall.php');
	return false;
		
}

function Action(act, id) {
	// ---------------------------------------------------
	$.ajax({
		type: "POST",
		url: "agd.php",
		data:"ac="+ act + "&id=" + id,
	});
	// ---------------------------------------------------
	return false;
}

function subSection() {
	// ---------------------------------------------------
	var psec = $('#report_section').attr('value');
	$.ajax({
		type: "POST",
		url: "sel.php",
		data:"prod_sec="+ psec,
		success: function (text) {  
			$('#xlss').html(text); 
			$('#askconsumers').removeAttr("disabled"); 
			$('#askconsumers').attr("class","subtn"); 
		}
	});
	// ---------------------------------------------------
	return false;
}

function askCons() {
	// ---------------------------------------------------
	var rs = $('#report_section').attr('value');
	var rt = $('#report_type').attr('value');
	var qn = $('#question').attr('value');
	// ---------------------------------------------------
	$.ajax({
		type: "POST",
		url: "pw.php",
		data:"rsec="+ rs + "&rtype=" + rt + "&question=" + qn,
		success: function () { 
			$('#askfm').load("askform.php"); 
			$('#askconsumers').attr("disabled",""); 
			$('#askconsumers').attr("class","subtnd"); 
		}
	});
	// ---------------------------------------------------
	return false;
}

function btnCap() {
	var btn = document.getElementById('save_info');
	var sel = document.getElementById('Registration_Type').value;
	if (sel == 1) {
		btn.value = 'Save and go to Accommodation';
	} else {
		btn.value = 'Save and go to Payment Options';
	}
}