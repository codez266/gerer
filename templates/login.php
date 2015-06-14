<html>
	<head>
		<title>Login</title>
		<link rel="stylesheet" href="loginstyle.css" />
	</head>
	<body>
		<header>
	<div class="err">
			<?php
			if( isset( $_SESSION['err'] ) ) {
			echo $_SESSION['err'];
			}
			?></div>
		</header>
	<h2 class="heading">Login</h2>
		<div class="login">
			<form action="profile" method="post">
				<ul class="list">
					<li>Username:<input class="inp" type="text" name="name" value="<?php if(isset($_SESSION['username'])){echo $_SESSION['username'];}?>" required="required" placeholder="Username"/></li>
					<li>Password:<input class="inp" type="password" name="password" required="required" placeholder="Password"/></li>
					<li><input class="inp" type="submit" name="submit" value="Submit"/></li>
				</ul>
				<!--<button class="button" value="Not a member"><a href="register.php">Not a member?</a></button>
		<input class="button" type="submit" value="Login"/>
		<input type="hidden" name = "CSRF" value="<?php $_SESSION['csrf'] = generate_csrf(); echo $_SESSION['csrf']; ?>">-->
			</form>
		</div>
	</body>
</html>
