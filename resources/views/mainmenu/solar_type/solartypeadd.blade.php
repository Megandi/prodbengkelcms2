@extends('template.app')

{{-- set title --}}
@section('title', 'Solar Type Add')

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
          Solar Type
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Solar Type Home</li>
          <li class="active">Add</li>
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

                <form class="form-horizontal" action="{{ url('/mainmenu/solar_type_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_name" class="col-sm-2 control-label">Name Solar <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="name" id="name" placeholder="Name Solar" value="{{old('name')}}">
                      @if($errors->has('name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_price" class="col-sm-2 control-label">Price Liter <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="price" id="price" placeholder="Price Liter" value="{{old('price')}}">
                      @if($errors->has('price'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('price') }}</p>
                      @endif
                      </div>
                    </div>

                  </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to save this data ?')">Submit</button>
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
        window.location.replace("{{ url('/mainmenu/solar_type_home') }}");
      }
    }

	</script>
</body>
@endsection