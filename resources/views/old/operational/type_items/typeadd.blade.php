@extends('template.app')

{{-- set title --}}
@section('title', 'Type Items Add')

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
          Type Items List
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Type Items List Home</li>
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

                <form class="form-horizontal" action="{{ url('/operational/items_type_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_name" class="col-sm-2 control-label">Type Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="name" id="name" placeholder="Type Name" value="{{old('name')}}">
                      @if($errors->has('name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_type_date" class="col-sm-2 control-label">Age / Time <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="type_date" id="type_date">
                          <option value="">Choose</option>
                          <option @if(old("type_date") == "1") selected @endif value="1">Age</option>
                          <option @if(old("type_date") == "2") selected @endif value="2">Time</option>
                        </select>
                      @if($errors->has('type_date'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('type_date') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group" id="age_year" style="display: none;">
                      <label for="input_year" class="col-sm-2 control-label">Year <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" max="99" class="form-control" name="year" id="year" placeholder="year" value="{{old('year')}}">
                      @if($errors->has('year'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('year') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group" id="age_month" style="display: none;">
                      <label for="input_month" class="col-sm-2 control-label">Month <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" max="99" class="form-control" name="month" id="month" placeholder="Month" value="{{old('month')}}">
                      @if($errors->has('month'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('month') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group" id="age_day" style="display: none;">
                      <label for="input_day" class="col-sm-2 control-label">Day <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" max="99" class="form-control" name="day" id="day" placeholder="Day" value="{{old('day')}}">
                      @if($errors->has('day'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('day') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group" id="time" style="display: none;">
                      <label for="input_expiry" class="col-sm-2 control-label">Date Expiry <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker'>
                          <input type="text" class="form-control for_date" name="expiry" id="expiry" value="{{old('expiry')}}">
                          @if($errors->has('expiry'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('expiry') }}</p>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_inven" class="col-sm-2 control-label">Inventory <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="checkbox" name="inven" id="inven" placeholder="inven" value="1">
                      @if($errors->has('inven'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('inven') }}</p>
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
        window.location.replace("{{ url('/operational/items_type_home') }}");
      }
    }

    $("#type_date").on('change',function(e) {
      if(e.target.value==1){
        $("#age_year").show();
        $("#age_month").show();
        $("#age_day").show();
        $("#time").hide();
      }else{
        $("#age_year").hide();
        $("#age_month").hide();
        $("#age_day").hide();
        $("#time").show();
      }
    });

    // for datetimepicker
    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      minDate: new Date()
    });

	</script>
</body>
@endsection