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
          User List
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>User List Home</li>
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

                <form class="form-horizontal" action="{{ url('/admin/user_home/do_edit/'.$users->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_karyawan_name" class="col-sm-2 control-label">Employee Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-karyawan-id form-control">
                          @if(isset($users->name_employee))
                          <option selected value="{{$users->karyawan_id}}">{{$users->karyawan_id .' - '. $users->name_employee}}</option>
                          @endif
                        </select>
                      @if($errors->has('karyawan_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_id') }}</p>
                      @endif
                      <input type="hidden" id="karyawan_id" name="karyawan_id" value="{{$users->karyawan_id}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_email" class="col-sm-2 control-label">Email <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{$users->email}}">
                      @if($errors->has('email'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('email') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_department" class="col-sm-2 control-label">Department <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="department_id" id="department_id">
                        <option value="">Choose department</option>
                          @foreach($ms_department as $item)
                            <option @if($item->id == $users->department_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                          @endforeach
                        </select>
                      @if($errors->has('department_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('department_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_position" class="col-sm-2 control-label">Position <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="position_id" id="position_id">
                        <option value="">Choose position</option>
                          @foreach($ms_jabatan as $item)
                            <option @if($item->id == $users->position_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                          @endforeach
                        </select>
                      @if($errors->has('position_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('position_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_level" class="col-sm-2 control-label">Level User <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="level_id" id="level_id">
                        <option value="">Choose Level</option>
                          @foreach($lt_user_type as $item)
                            <option @if($item->id == $users->level_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                          @endforeach
                        </select>
                      @if($errors->has('level_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('level_id') }}</p>
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
        window.location.replace("{{ url('/admin/user_home') }}");
      }
    }

		$(".js-karyawan-id").select2({
    ajax: {
      url: "{{ url('/admin/user_home/search_employee') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using custom formatting functions we do not need to
        // alter the remote JSON data
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });

    $(".js-karyawan-id").on("select2:select", function (e) {
      var obj = $(".js-karyawan-id").select2("data")
      $('#karyawan_id').val(obj[0].id);
    });

	</script>
</body>
@endsection