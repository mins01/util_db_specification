<!doctype html>
<html lang="ko" >
<head>
	<title>DB specification</title>
	<meta charset="utf-8">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="shortcut icon" href="http://www.mins01.com/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<script src="/js/ForGoogle.js"></script>
	<!-- google analytics -->
	<script>ForGoogle.analytics()</script>


	<!-- jquery 관련 -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>


	<!-- 부트스트랩 4 : IE8지원안됨! -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" crossorigin="anonymous">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" crossorigin="anonymous"></script>
	<!-- vue.js -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> -->

	<!-- meta og -->

	<meta property="og:title" content="DB specification">
	<meta property="og:description" content="DB specification">
	<meta property="og:site_name" content="DB specification" />
	<meta property="og:type" content="website">

	<!-- //meta og -->


</head>
<body>
	<div class="container">
		<h1>DB SPEC to EXCEL(XLSX)</h1>
		<hr>
		<form method="post" action="web.php">
			<div >
				<div class="input-group mb-1">
					<div class="input-group-prepend">
						<span class="input-group-text" >HOST</span>
					</div>
					<input name="host" type="text" class="form-control" placeholder="127.0.0.1 or localhost or etc" aria-label="host" required>
				</div>
				<div class="input-group mb-1">
					<div class="input-group-prepend">
						<span class="input-group-text" >USER</span>
					</div>
					<input name="user" type="text" class="form-control" placeholder="DB user" aria-label="user" required>
				</div>
				<div class="input-group mb-1">
					<div class="input-group-prepend">
						<span class="input-group-text" >Password</span>
					</div>
					<input name="pw" type="password" class="form-control" placeholder="Password" aria-label="user" required>
				</div>
				<div class="input-group mb-1">
					<div class="input-group-prepend">
						<span class="input-group-text" >Database</span>
					</div>
					<input name="database" type="text" class="form-control" placeholder="DB name" aria-label="Database Name" required>
				</div>
				<div class="text-center">
					<button class="btn btn-warning" style="min-width:50%" type="submit" >Save Excel</button>
				</div>
			</div>
		</form>
	</div>
</body>
</html>
