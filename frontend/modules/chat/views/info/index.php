<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;
use common\components\Functions;

$func= new Functions();
/* @var $this yii\web\View */
/* @var $searchModel common\models\message\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chats';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("/css/modcss/chat.css", [
], 'css-chat');
$this->registerJsFile("/js/chat.js", [
], 'css-chat');

?>
<div class="chat-index">
	
	<!-- MODAL HERE!! -->
	<button class="open-button" onclick="openForm()"><i class="fa fa-commenting-o"></i></button>
	<div class="chat-popup" id="myForm">
		<form class="form-container" enctype="multipart/form-data">
			<div class="chat-popup-header">
				<!--<span>OneLab Chat</span>-->
				<i class="fa fa-gear"></i>
				<label id="profilenamepop"> &nbsp;</label>
				<label id="chattype" hidden> &nbsp;</label> <!-- personal(1) or group(2) -->
				<label id="dataid" hidden> &nbsp;</label> <!-- contact_id or group_id -->
				<i class="fa fa-close" onclick="closeForm()"></i>

			</div>
			<div class="chat-popup-tab">
				<button type="button" class="btntab" id="btnuser"><i class="fa fa-user"></i></button>
			
				<button type="button" class="btntab" id="btngroup"><i class="fa fa-group"></i></button>
				
				
			</div>
			<div class="scroll-style1" id="popchatbody">
			
			</div>
			<input type="file" name="filetoupload">
			<div class="chat-popup-footer">
				<button type="button" class="btn" id="subm"><i class="fa fa-paperclip"></i></button>
				<textarea id="chatareapop" placeholder="Type message.." name="msg" required></textarea>
				<button type="button" class="btn" id="sendmes"><i class="fa fa-send-o"></i></button>
			</div>
		</form>
	</div>
	<!-- END MODAL HERE!! -->
	
</div>
<script type="text/javascript">

var x;

function mes(id,type) {
	$("#recipientid").text(id);
	var user_id= <?= Yii::$app->user->identity->profile->user_id?>;
	const token =<?php echo json_encode($_SESSION['usertoken'])?>;
	
		$.ajax({
		url: "http://www.eulims.local/api/message/getcontact", //API LINK FROM THE CENTRAL
		type: 'POST',
		dataType: "JSON",
		beforeSend: function (xhr) {
			xhr.setRequestHeader('Authorization', 'Bearer '+ token);
		}, 
		data: {
			userid: user_id,
			recipientid: id,
			type:type
		},
		success: function(response) {
			var y;
			x="";
			//console.log(response.chat.length);
			x = x+'<br>';
			for(y=0;y<response.chat.length;y++){
				//alert(response.chat[y].timestamp);
				var dt=new Date(response.chat[y].timestamp);
				dt=formatAMPM(dt);
				x = x+'<div>';
				if (response.chat[y].sender_userid != user_id){
					
					x = x+"<div class='message-blue'>";
					x = x+"<p class='message-content'>"+response.chat[y].chat_data+"</p>";
					x = x+"<div class='message-timestamp-left'>"+dt+"</div>";
					x = x+"</div>";
					x = x+'<br>';
				
				}else{
					x = x+"<div class='message-orange'>";
					x = x+"<p class='message-content'>"+response.chat[y].chat_data+"</p>";
					x = x+"<div class='message-timestamp-right'>"+dt+"</div>";
					x = x+"</div>";
					x = x+'<br>';
				}

				x = x+'</div>';
					
			}
			$('#dataid').html(response.id);
			$('#chattype').html(type);
			
			if(type == 1){
				$('#chathere').html(x); 
				$('#profilename').html(response.profile.fullname);
				
				$('#popchatbody').html(x); 
				
				$('#profilenamepop').html(response.profile.fullname);
			}
			if(type == 2){
				//console.log(response.profile);
				$('#profilenamepop').html(response.profile.group_name);
				$('#popchatbody').html(x); 
			}
		    //$('#chatbody').scrollTop($('#chatbody')[0].scrollHeight);
		},
		error: function(xhr, status, error) {
			alert(error);
		}
		});  
 }


function sendmessage() {
 // $("#myForm").modal({"backdrop": "static"});
 // $("#myForm").preventDefault();
   var txt=$('#chatareapop').val();
   var token=<?php echo json_encode($_SESSION['usertoken'])?>;
   var id=$('#dataid').text();
   //alert(id);
   var type = $('#chattype').text();
 //  var type=1;//personalmessage
   var sender_userid= <?= Yii::$app->user->identity->profile->user_id?>;
	$.ajax({
		url: "http://www.eulims.local/api/message/setmessage", //API LINK FROM THE CENTRAL
		type: 'POST',
		dataType: "JSON",
		beforeSend: function (xhr) {
			xhr.setRequestHeader('Authorization', 'Bearer '+ token);
		}, 
		data: {
			sender_userid: sender_userid,
			message: txt,
			type: type,
			id:id
		},
		success: function(response) {
			var dt=new Date();
			dt=formatAMPM(dt);
			x = x+'<br>';
			x = x+"<div class='message-orange'>";
			x = x+"<p class='message-content'>"+txt+"</p>";
			
			x = x+"<div class='message-timestamp-right'>"+dt+"</div>";
			x = x+"</div>";
			x = x+'<br>';
			$('#chatareapop').val(" ");
			$('#popchatbody').html(x); 
			//openForm();
		},
		error: function(xhr, status, error) {
			alert(error);
		}
	}); 
} 

function formatAMPM(date) {
 const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date);
 const mo = new Intl.DateTimeFormat('en', { month: 'short' }).format(date);
 const da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);

  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = da+"-"+mo+"-"+ye + " " + hours + ':' + minutes + ' ' + ampm;
  return strTime;
}

$("#file").click(function(){
	$("#filedialog").trigger("click");
 });
 
 function sendfile() {
	 var file = document.getElementById('fileid').file;
	 alert("Hello");
 }

//window.setInterval(function(){
  /// call your function here
  //var id= $("#recipientid").text();
   // mes(id);
//}, 3000);

function openForm() {
	document.getElementById("myForm").style.display = "block";
}

function closeForm() {
	document.getElementById("myForm").style.display = "none";
}

$("#btnuser").click(function(){
	$('#profilenamepop').html("");
	contacts();
});
$("#btngroup").click(function(){
	$('#profilenamepop').html("");
	groupcontacts();
});


function contacts(){ //Personnal Messages
	var y;
	 y = "<h4>List of Contacts </h4>";
	<?php foreach ($contacts as $data)
	 { ?>
	y=y + "<a class='thismessage' onclick='mes(<?=$data['user_id']?>,1)'>";
	y= y + "<div class='first'><b>"+ '<?= $data['fullname']?>' +"</div>";
	y= y + "</a>";
	<?php } ?>
	//GROUPS
	y= y + "<br> <div> <h4>Groups Contacts </h4></div>";
	<?php foreach ($group as $data)
	 { ?>
	y=y + "<a class='thismessage1' onclick='mes(<?=$data['chat_group_id']?>,2)'>";
	y= y + "<div class='first'><b>"+ '<?= $data['chatGroup']['group_name']?>' +"</div>";
	y= y + "</a>";
	<?php } ?>
	
	
	$('#popchatbody').html(y); 
}
function groupcontacts(){ //Group Messages
	var y;
	//document.getElementById('popuser').style.display='block';
	 y = "<h4>List of Contacts </h4>";
	<?php foreach ($group as $data)
	 { ?>
	y=y + "<a class='thismessage' onclick='mes(<?=$data['chat_group_id']?>,2)'>";
	y= y + "<div class='first'><b>"+ '<?= $data['chatGroup']['group_name']?>' +"</div>";
	y= y + "</a>";
	<?php } ?>
	
	$('#popchatbody').html(y); 
}
$("#sendmes").click(function(){
	sendmessage();
});

$('#subm').click(function(e){
	var token=<?php echo json_encode($_SESSION['usertoken'])?>;
    var formData = new FormData($('form')[0]);
	//var formData = new FormData();
	//formData.append('image', $('input[type=file]')[0].files[0]);
	//formData.append('key1', 'value1');
	//formData.append('key2', 'value2');
	/*for (var pair of formData.entries()) {
    console.log(pair[0]+ ', ' + pair[1]); 
	} */
    //console.log(formData);
	
	$.ajax({
        url: "http://www.eulims.local/api/message/savefile", //API LINK FROM THE CENTRAL
		type: 'POST',
		dataType: "JSON",
		beforeSend: function (xhr) {
			xhr.setRequestHeader('Authorization', 'Bearer '+ token);
		}, 
		data: formData,
		success: function(response) {
			alert(response.message);
			
			
		},
		error: function(xhr, status, error) {
			alert(error);
		},
        cache: false,
        contentType: false,
        processData: false
    });  
	
});

 </script>