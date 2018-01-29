<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="Admin, Dashboard, Bootstrap" />
	<link rel="shortcut icon" sizes="196x196" href="{{asset('theme/assets/images/logo.png')}}">
	<title>Login</title>
	
	<link rel="stylesheet" href="{{asset('theme/libs/bower/font-awesome/css/font-awesome.min.css')}}">
	<link rel="stylesheet" href="{{asset('theme/libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.css')}}">
	<!-- build:css {{asset('theme/assets/css/app.min.css')}} -->
	<link rel="stylesheet" href="{{asset('theme/libs/bower/animate.css/animate.min.css')}}">
	<link rel="stylesheet" href="{{asset('theme/libs/bower/fullcalendar/dist/fullcalendar.min.css')}}">
	<link rel="stylesheet" href="{{asset('theme/libs/bower/perfect-scrollbar/css/perfect-scrollbar.css')}}">
	<link rel="stylesheet" href="{{asset('theme/assets/css/bootstrap.css')}}">
	<link rel="stylesheet" href="{{asset('theme/assets/css/core.css')}}">
  <link rel="stylesheet" href="{{asset('theme/assets/css/misc-pages.css')}}">
	<link rel="stylesheet" href="{{asset('theme/assets/css/app.css')}}">
	<!-- endbuild -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
	<script src="{{asset('theme/libs/bower/breakpoints.js/dist/breakpoints.min.js')}}"></script>
	<script>
		Breakpoints();
	</script>
</head>

<body class="simple-page">

 <div id="back-to-home">
		<a href="{{url('/')}}" class="btn btn-outline btn-default"><i class="fa fa-home animated zoomIn"></i></a>
	</div>
	<div class="simple-page-wrap">
		<div class="simple-page-logo animated swing">
			<a href="index.html">
				<span><i class="fa fa-gg"></i></span>
				<span>Infinity</span>
			</a>
		</div><!-- logo -->
		<div class="simple-page-form animated flipInY" id="login-form">
	<h4 class="form-title m-b-xl text-center">Sign In With Your Infinity Account</h4>
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong><br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
	<form method="POST" action="/login">
	  <input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="form-group">
			<input id="sign-in-email" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
		</div>

		<div class="form-group">
			<input id="sign-in-password" type="password" class="form-control" name="password" placeholder="Password">
		</div>

		<div class="form-group m-b-xl">
			<div class="checkbox checkbox-primary">
				<input type="checkbox" name="remember" id="keep_me_logged_in"/>
				<label for="keep_me_logged_in">Keep me signed in</label>
			</div>
		</div>
		<input type="submit" class="btn btn-primary" value="SIGN IN">
	</form>
</div><!-- #login-form -->

<div class="simple-page-footer hide">
	<p><a href="/password/email">FORGOT YOUR PASSWORD ?</a></p>
	<p >
		<small>Don't have an account ?</small>
		<a href="signup.html">CREATE AN ACCOUNT</a>
	</p>
</div><!-- .simple-page-footer -->


	</div><!-- .simple-page-wrap -->  

</body>
</html>