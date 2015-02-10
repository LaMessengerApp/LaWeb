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
	var request = $.ajax({
	  url: "http://localhost:8888/LaWeb/web/app_dev.php/api/userbyusernames/"+name,
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
	var request = $.ajax({
	  url: "http://localhost:8888/LaWeb/web/app_dev.php/api/friendrequests/"+id,
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
	});
	 
	request.fail(function( jqXHR, textStatus ) {

	});

}

function valideFriendship(name, link){
	var request = $.ajax({
	  url: "http://localhost:8888/LaWeb/web/app_dev.php/api/userbyusernames/"+name,
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

	var requestUser = $.ajax({
	  url: "http://localhost:8888/LaWeb/web/app_dev.php/api/userbyusernames/"+username,
	  type: "GET",
	  dataType: "json",
	});
	 
	requestUser.done(function( response ) {
		console.log(response['user']['id']);
		var messageData = { users : response['user']['id'], text : text, lat : lat, long: long };
		var request = $.ajax({
		  url: "http://localhost:8888/LaWeb/web/app_dev.php/api/messages",
		  type: "POST",
		  data:  messageData,
		  dataType: "json",
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
	console.log("openMessage");
	var messageData = {status : 2};
	var request = $.ajax({
	  url: "http://localhost:8888/LaWeb/web/app_dev.php/api/messages/"+messageId,
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