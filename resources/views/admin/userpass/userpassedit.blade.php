@extends('template.app')

{{-- set title --}}
@section('title', 'User Edit')

{{-- set main content --}}
@section('content')

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <header class="main-header">
    <!-- Logo -->
    <a href="{{ url('dashboard/home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>MG</b>S</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Management</b> System</span>
    </a>

     <nav class="navbar navbar-static-top">
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			<span class="sr-only">Toggle navigation</span>
		</a>
          @include('template.menu')
      </nav>
    </header>

         @include('template.sidebar')

		<div class="content-wrapper">
			<!-- Main content -->
			<section class="content-header">
        <h1>
          User Pass
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>User Pass Home</li>
          <li class="active">Edit</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">

              {{-- alert --}}
              @if (session('status'))
                <div class="alert alert-success">
              {{ session('status') }}
                </div>
              @endif
              @if (session('error'))
                <div class="alert alert-danger">
              {{ session('error') }}
                </div>
              @endif
            
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/admin/user_pass_home/do_edit/'.$users->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_emp_name" class="col-sm-2 control-label">Employee ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="emp_name" class="form-control" name="emp_name" id="emp_name" placeholder="Employee ID" value="{{$users->karyawan_id}}" readonly>
                      @if($errors->has('emp_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_email" class="col-sm-2 control-label">Email <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{$users->email}}" readonly>
                      @if($errors->has('email'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('email') }}</p>
                      @endif
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="input_password" class="col-sm-2 control-label">Password <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" name="password_user" id="password_user" placeholder="Password">
                      @if($errors->has('password_user'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('password_user') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_re_password" class="col-sm-2 control-label">Re Password <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" name="re_password_user" id="re_password_user" placeholder="Re Password">
                      @if($errors->has('re_password_user'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('re_password_user') }}</p>
                      @endif
                      </div>
                    </div>

                    <input type="hidden" name="_method" id="_method" value="PUT">

                  </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to update this data ?')">Submit</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
      </div>
    </section>
        <!-- /.content -->

	</div>

	<script>

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/admin/user_pass_home') }}");
      }
    }

	</script>
</body>
@endsection