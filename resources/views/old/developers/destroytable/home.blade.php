@extends('template.app')

{{-- set title --}}
@section('title', 'Destroy All Table')

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
          Destroy Home
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Destroy Home</li>
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

              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Destroy Information</h3>
                </div>
                <div class="box-body">
                  <div class="box box-solid box-danger">
                  <div class="box-body">
                    <a type="button" onclick="modal()" class="btn btn-danger">Destroy Data</a><br><br>
                    <li>All data will be deleted except the main table</li>
                    <li>Please make sure again before doing data destruction</li>
                  </div>
                </div>
                </div>

              </div>
            </div>
          </div>
      </section>
		</div>
	</div>

  <div class="modal fade" id="modal_verification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#e74c3c">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color:white">Verification User</h4>
            </div>
	          <div class="modal-body">
              <form class="form-horizontal" action="{{ url('/developers/destroy_home/login') }}" method="post">
                {{-- set token --}}
                {{ csrf_field() }}

                <div class="form-group">
                  <label for="input_notes" class="col-sm-2 control-label">Password <span style="color:red;">*</span></label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" name="pass" id="pass" placeholder="Password" value="{{old('Password')}}" required>
                  @if($errors->has('Password'))
                      <p style="font-style: bold; color: red;">{{ $errors->first('Password') }}</p>
                  @endif
                  </div>
                </div>

              <div class="modal-footer">
                  <button style="width:90px;" type="submit" onclick="return confirm('Are you sure you want to destroy all data ?')" class="btn btn-info pull-right">Submit</button>
              </div>
              </form>
  	        </div>
        </div>
    </div>
</div>

  <script>
  function modal(){
    $('#modal_verification').modal();
  }


  </script>
<body>
@endsection
