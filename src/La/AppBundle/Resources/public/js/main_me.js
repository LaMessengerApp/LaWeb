$( document ).ready(function(){
	$(".btn-friends").click(function(){
		$(".friends-section").toggle(300);
	});
	$(".message").hide();
	$(".btn-messages").click(function(){
		$(this).parent().find('.message').toggle(100);
	});
	$('.message').click(function(){
		changeCenter($(this).data('lat'), $(this).data('long'));
	});

	$('#btn-searchFriend').click(function (e) {
		tmpName = $('#newfriendusername').val().replace(/[^a-z0-9_\-]+/,'');
		if(tmpName != ""){
			$("#newFriendResponse").html('<i class="fa fa-circle-o-notch fa-spin fa-5x"></i>');
			tmpNewFriend = searchFriend(tmpName);
		}
	    e.preventDefault();
	});

	init();

	//home menu
	$('.btn-menu').click(function (e){
		$('#menu-section').slideToggle();
	});
	$('#btn-friends').click(function (e){
		$('#homepanel').slideUp();
		$('#friendspanel').slideDown(0);
	});
	$('#btn-conversations').click(function (e){
		$('#homepanel').slideUp();
		$('#conversationspanel').slideDown(0);
	});
	$('.btn-back').click(function (e){
		$('.second').slideUp();
		$('#homepanel').slideDown();
	});
	$('#btn-addfriend').click(function (e){
		$('.second').slideUp();
		$('#addfriendpanel').slideDown();
	});
	$('.btn-newmessage').click(function (e) {
		$('.second').slideUp();
		$('#homepanel').slideUp();
		$('#newmessagepanel').slideDown();
	});
	$('#btn-setlocation').click(function (e){
		$('#locationcursor').remove();
		$('body').append('<div id="locationcursor"><i class="fa fa-crosshairs fa-3x"></div>');
		$('body').mousemove(function (e){
			$('#locationcursor').css({left:e.pageX+30, top:e.pageY+30});
		});
		map.on('click', function(e) {
			$('#locationcursor').remove();
			$('.right-section').off();
			$('#newmessagelat').val(e.latlng.lat);
			$('#newmessagelong').val(e.latlng.lng);
		});
	});

	//changepicture
	$('.profilImg').click(function (e){
		$('.chosepicture').fadeToggle(200);
	});
	$('.cancel-upload-profil').click(function (e){
		e.preventDefault();
		$('.chosepicture').fadeToggle(200);
	});
	$('.ok-upload-profil').click(function (e){
		e.preventDefault();
		uploadProfilPicture();
	});

	$('#sentmessage').click(function (e){
		e.preventDefault();
		miss =0;
		if($('#newmessageuser').val() == null){
			$('#newmessageuser').css({'background-color':'#FF5349'});
			miss = 1;
		}else{
			$('#newmessageuser').css({'background-color':'#FFF'});
		} 
		if($('#newmessagetext').val().replace(/[^a-z0-9_\-]+/,'') == ""){
			$('#newmessagetext').css({'background-color':'#FF5349'});
			miss = 1;
		}else{
			$('#newmessagetext').css({'background-color':'#FFF'});
		} 
		if($('#newmessagelat').val().replace(/[^a-z0-9_\-]+/,'') == ""){
			$('#newmessagelat').css({'background-color':'#FF5349'});
			miss = 1;
		}else{
			$('#newmessagelat').css({'background-color':'#FFF'});
		} 
		if($('#newmessagelong').val().replace(/[^a-z0-9_\-]+/,'') == ""){
			$('#newmessagelong').css({'background-color':'#FF5349'});
			miss = 1;
		}else{
			$('#newmessagelong').css({'background-color':'#FFF'});
		}
		if(miss == 0){	
			newMessage($('#newmessageuser').val(), $('#newmessagetext').val(), $('#newmessagelat').val(), $('#newmessagelong').val());
		}
		e.preventDefault();
	});
	$('.notopen').click(function (e){
		tmpIdMessage = $(this).parent().data('id');
		$(this).remove();
		openMessage(tmpIdMessage);
	});
	$('.acceptinvitation').click(function (e){
		console.log("accept invitation");
		tmpName = $(this).data("name");
		valideFriendship(tmpName, $(this));
	});

});

function init(){
	$('.second').slideUp(0);
	$('#menu-section').slideUp(0);
}


function searchFriend(name){
	var url = document.URL.substring(0,document.URL.length-3);
	var request = $.ajax({
	  url: url+"/api/userbyusernames/"+name,
	  type: "GET",
	  dataType: "json",
	});
	 
	request.done(function( response ) {
		$("#newFriendResponse").html('<h3>'+response['user']['username']+'<button data-userid="'+response['user']['id']+'" type="button" id="btn-addFriend" class="btn btn-success btn-sm">Add</button></h3>');
		$('#btn-addFriend').click(function (e) {
			tmpId = $(this).data('userid');
			addFriend(tmpId);
			$("#newFriendResponse").hide();
		    e.preventDefault();
		});
	});
	 
	request.fail(function( jqXHR, textStatus ) {
	  $("#newFriendResponse").html("No Result");
	});
}

function addFriend(id){
	var url = document.URL.substring(0,document.URL.length-3);
	var request = $.ajax({
	  url: url+"/api/friendrequests/"+id,
	  type: "POST",
	  dataType: "json",
	});
	 
	request.done(function( response ) {
		switch(response['code']) {
			case 1:
			    //friendship request
			    alert('Friend request sent');
			    break;
			case 2:
			    //validation
			    alert('Friend added');
			    break;
			case 3:
			    //already friend
			    alert("Already friend");
			    break;
			}
		if(response['code'] == 3){
			//deja ami
		}
		location.reload();
	});
	 
	request.fail(function( jqXHR, textStatus ) {

	});

}

function valideFriendship(name, link){
	var url = document.URL.substring(0,document.URL.length-3);
	var request = $.ajax({
	  url: url+"/api/userbyusernames/"+name,
	  type: "GET",
	  dataType: "json",
	});
	 
	request.done(function( response ) {
		console.log(response['user']['id']);
		addFriend(response['user']['id']);
		link.remove();
	});
}


function newMessage(username, text, lat, long){
	var url = document.URL.substring(0,document.URL.length-3);
	$("#spinner").show();
	var requestUser = $.ajax({
	  url: url+"/api/userbyusernames/"+username,
	  type: "GET",
	  dataType: "json",
	});

	requestUser.done(function( response ) {
		console.log(response['user']['id']);
		var file = $('.upload-message-image').prop('files');
		data = new FormData();
	    data.append("img", file[0]);
		var messageData = { users : response['user']['id'], text : text, lat : lat, long: long};
		data.append("users", response['user']['id']);
		data.append("text", text);
		data.append("lat", lat);
		data.append("long", long);
		console.log(data);
		var request = $.ajax({
		  url: url+"/api/messages",
		  type: "POST",
		  data:  data,
		  dataType: "json",
		  cache: false,
		  processData: false,
		  contentType: false,
		});
		 
		request.done(function( response ) {
			console.log(response);
			location.reload();
		});
		 
		request.fail(function( jqXHR, textStatus ) {
			console.log("fail");
			console.log(textStatus);
		});
	});
	
}

function openMessage(messageId){
	var url = document.URL.substring(0,document.URL.length-3);
	console.log("openMessage");
	var messageData = {status : 2};
	var request = $.ajax({
	  url: url+"/api/messages/"+messageId,
	  type: "PUT",
	  data:  messageData,
	  dataType: "json",
	});
	 
	request.done(function( response ) {
		console.log(response);
	});
	 
	request.fail(function( jqXHR, textStatus ) {
		console.log("fail");
		console.log(textStatus);
	});
}

function uploadProfilPicture(){
	$("#spinner").show();
	var url = document.URL.substring(0,document.URL.length-3);
	var file = $('.chosepicture .upload').prop('files');
	data = new FormData();
    data.append("image", file[0]);
	var request = $.ajax({
	  url: url+"/api/userpicture",
	  type: "POST",
	  data:  data,
	  dataType: "json",
	  cache: false,
	  processData: false,
	  contentType: false,
	});
	 
	request.done(function( response ) {
		console.log(response);
		$('.chosepicture').fadeToggle(200);
		$("#spinner").hide();
		$('.profilImg').css('background-image','url(/LaWeb/web/userimages/'+response['file']+')');
	});
	 
	request.fail(function( jqXHR, textStatus ) {
		console.log("fail");
		console.log(textStatus);
		$("#spinner").hide();
	});
}


