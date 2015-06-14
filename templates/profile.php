<!DOCTYPE HTML>
<html>
	<head>
	<meta charset="UTF-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/hmac-sha256.js"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/components/enc-base64-min.js"></script>
	</head>
	<body>
		<script>
			$(document).ready(function(){


				$('#butt').click(function(){
					var request = 'GET';
					var uri = '/project/spons/users/sumit';
					var secret = "<?php echo $data['token'] ?>";
					var signature = CryptoJS.HmacSHA256(request + uri, secret).toString(CryptoJS.enc.Base64);
					//alert(signature);
					//var u = unescape(encodeURIComponent(request + '\n' + uri));
					//alert(u);
					//var signature = CryptoJS.HmacSHA256(u, secret).toString();
					//var hash = CryptoJS.HmacSHA256("a", "b").toString();
					//alert(hash);
					signature = 'sumit:'+signature;
					//alert(signature);
					$.ajax({
						url: 'http://localhost/project/spons/users/sumit',
						contentType: false,
						headers: {'Authorization': signature},
						method:'GET'
					}).done(function(data){
						//alert(data);
						/*var uri = '/project/spons/users/sumit';
						var request = 'GET';
						var str=request + uri;
						unescape(encodeURIComponent(str));
						alert(str==data);
						console.log(str+'\n'+data);*/
						document.write(data);
					});
				});
			});
		</script>
		<button id="butt">Send</button>
	</body>
</html>
