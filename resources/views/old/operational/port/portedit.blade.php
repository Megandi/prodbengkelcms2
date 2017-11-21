@extends('template.app')

{{-- set title --}}
@section('title', 'Port Edit')

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
          Port
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Port Home</li>
          <li class="active">Edit</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/operational/port_home/do_edit/'.$ms_pelabuhan->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_port_id" class="col-sm-2 control-label">Port ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="port_id" id="port_id" placeholder="Port ID" value="{{$ms_pelabuhan->pelabuhan_id}}" readonly>
                      @if($errors->has('port_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('port_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_port_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="port_name" id="port_name" placeholder="Name" value="{{$ms_pelabuhan->nama}}">
                      @if($errors->has('port_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('port_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_port_address" class="col-sm-2 control-label">Address <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="port_address" id="port_address" placeholder="Address">{{$ms_pelabuhan->alamat}}</textarea>
                      @if($errors->has('port_address'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('port_address') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_port_office" class="col-sm-2 control-label">Office Address <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="port_office" id="port_office" placeholder="Office Address">{{$ms_pelabuhan->alamat_perusahaan_pelabuhan}}</textarea>
                      @if($errors->has('port_office'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('port_office') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_no_telp" class="col-sm-2 control-label">Contact Port <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="no_telp" id="no_telp" placeholder="Contact Port" value="{{$ms_pelabuhan->no_telp_perusahaan_pelabuhan}}">
                      @if($errors->has('no_telp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('no_telp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_npwp" class="col-sm-2 control-label">NPWP <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="npwp" id="npwp" placeholder="NPWP" value="{{$ms_pelabuhan->npwp}}">
                      @if($errors->has('npwp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('npwp') }}</p>
                      @endif
                      </div>
                    </div>
                    <input type="hidden" name="_method" id="_method" value="PUT">

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
        window.location.replace("{{ url('/operational/port_home') }}");
      }
    }

  </script>
</body>
@endsection