<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title') | Backsite</title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


  {{-- CSS and Plugins --}}

  {{-- datetimepicker --}}
  <link rel="stylesheet" href="{{ url('adminlte/extension/bootstrap-datetimepicker.min.css') }}">

  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ url('adminlte/bootstrap/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/ionicons/css/ionicons.min.css') }}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('adminlte/dist/css/AdminLTE.min.css') }}">
  <!-- Select2 style -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/select2/select2.min.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/iCheck/square/blue.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ url('adminlte/dist/css/skins/_all-skins.min.css') }}">
</head>
<body>
<div class="wrapper">

      <div class="error-page" style="padding-top:200px;">
        <h2 class="headline text-yellow"> 404</h2>
        <div class="error-content">
          <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
          <p>
            We could not find the page you were looking for.
            Meanwhile, you may return to dashboard.
          </p>

            <div class="input-group">
              <a href="{{url('dashboard/home')}}" class="btn btn-info pull-right">Back to dashboard</a>
            </div>
        </div>
      </div>
</div>
<!-- ./wrapper -->

<script src="../../dist/js/demo.js"></script>
</body>
</html>
