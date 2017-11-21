@extends('template.app')

{{-- set title --}}
@section('title', 'Profile Edit')

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
          Profile
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Profile Edit</li>
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

                <form class="form-horizontal" action="{{ url('/usermanagement/user_profile/do_edit/'.Auth::user()->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_id" class="col-sm-2 control-label">ID <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="karyawan_id" id="karyawan_id" placeholder="karyawan_id" value="{{ App\Models\manage_karyawan::whereKaryawanId(Auth::user()->karyawan_id)->first()['karyawan_id'] }}" readonly>
                      @if($errors->has('karyawan_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_name" class="col-sm-2 control-label">Name <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" id="name" placeholder="name" value="{{ App\Models\manage_karyawan::whereKaryawanId(Auth::user()->karyawan_id)->first()['nama'] }}" readonly>
                      @if($errors->has('name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_password" class="col-sm-2 control-label">Password <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" name="password_user" id="password_user" placeholder="Password" pattern="[A-Za-z0-9]+">
                      @if($errors->has('password_user'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('password_user') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_re_password" class="col-sm-2 control-label">Re Password <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" name="re_password_user" id="re_password_user" placeholder="Re Password" pattern="[A-Za-z0-9]+">
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
        window.location.replace("{{ url('dashboard/home') }}");
      }
    }

	</script>
</body>
@endsection