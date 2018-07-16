<!DOCTYPE html>
<html>
<head>
<title><?php echo Utilities::getLabel( 'L_Facebook_Login_JavaScript_Example' ); ?></title>
<meta charset="UTF-8">
<script src="/public/js/jquery-1.10.2.js" type="text/javascript"></script>
</head>
<body>
<a onclick="return checkLoginState();" href="javascript:void(0);"><?php echo Utilities::getLabel( 'L_Facebook_Login' ); ?></a>

<div id="status"></div>
<script>
function checkLoginState() {
    FB.getLoginStatus(function(response) {
      
		console.log('statusChangeCallback');
		console.log(response);
		
		if (response.status === 'connected') {
		  // Logged into your app and Facebook.
			FB.api('/me', function(response_fb) {
				console.log(response_fb);
				document.getElementById('status').innerHTML =
				'Thanks for logging in, ' + response_fb.name + '!';
				data = 'fb_token='+response.authResponse.accessToken;
				$.ajax({
					url: 		'fb_user_login',
					data:		data,
					type:		'POST',
					dataType:	'JSON',
					success:	function(res){
						
						if(res['status'] && res['redirect']){
							location.href = res['redirect'];
						}
						
						if(res['status']){
							location.reload();
						}
						
						if( !res['status']){
							alert(res['msg']);
						}

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});
		} else if (response.status === 'not_authorized') {
		  // The person is logged into Facebook, but not your app.
		  document.getElementById('status').innerHTML = 'Please log ' +
			'into this app.';
		} else {
		  // The person is not logged into Facebook, so we're not sure if
		  // they are logged into this app or not.
		  document.getElementById('status').innerHTML = 'Please log ' +
			'into Facebook.';
		}
    });
  }

  window.fbAsyncInit = function() {
	  FB.init({
		appId      : '345433532310321',
		cookie     : true,  // enable cookies to allow the server to access 
							// the session
		xfbml      : true,  // parse social plugins on this page
		version    : 'v2.1' // use version 2.1
	  });
	};

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
</body>
</html>