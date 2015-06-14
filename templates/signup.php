<!DOCTYPE HTML>
<html>
	<head>

	</head>
	<body>
	<header>
	<div class="err">
			<?php
			if( isset( $_POST['err'] ) ) {
			echo $_POST['err'];
			unset( $_POST['err'] );
			}
			?></div>
		</header>
	<h1>Registrations</h1>
		<div class = "signup-form">
			<form action = "signup" method="POST">
				<ul class="list">
					<li>Username:<input class="inp" type="text" name="username" value="<?php if(isset($_POST['username'])){echo $_POST['username'];}?>"></li>
					<li>Password: <input class="inp" type="password" name ="password" value="password"/></li>
					<li>Name: <input class="inp" type="text" name ="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];}?>"/></li>
					<li>Email: <input class="inp" type="email" name="email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];}?>"/></li>
					<li>Number: <input class="inp" type="number" name="number" value="<?php if(isset($_POST['name'])){echo $_POST['number'];}?>"/></li>
					<li>Year: <select class="inp" name="year">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
					</select></li>
					<li><input type="submit" value="submit"/></li>
				</ul>
			</form>
		</div>
	</body>
</html>
