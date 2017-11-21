@extends('template.app')

{{-- set title --}}
@section('title', 'Supplier Edit')

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
          Supplier
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Supplier Home</li>
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

                <form class="form-horizontal" action="{{ url('/mainmenu/supp_home/do_edit/'.$ms_supplier->supplier_id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_supp_id" class="col-sm-2 control-label">Supplier ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="supp_id" id="supp_id" placeholder="Supplier ID" value="{{$ms_supplier->supplier_id}}" readonly>
                      @if($errors->has('supp_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('supp_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_supp_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="supp_name" id="supp_name" placeholder="Name" value="{{$ms_supplier->nama}}">
                      @if($errors->has('supp_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('supp_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_supp_address" class="col-sm-2 control-label">Address <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="supp_address" id="supp_address" placeholder="Address">{{$ms_supplier->alamat}}</textarea>
                      @if($errors->has('supp_address'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('supp_address') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_email" class="col-sm-2 control-label">Email <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{$ms_supplier->email}}">
                      @if($errors->has('email'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('email') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_no_telp" class="col-sm-2 control-label">Telephone <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="no_telp" id="no_telp" placeholder="Telephone" value="{{$ms_supplier->no_telp}}">
                      @if($errors->has('no_telp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('no_telp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_no_hp" class="col-sm-2 control-label">Handphone <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="no_hp" id="no_hp" placeholder="Handphone" value="{{$ms_supplier->no_hp}}">
                      @if($errors->has('no_hp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('no_hp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_fax" class="col-sm-2 control-label">Fax <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="fax" id="fax" placeholder="Fax" value="{{$ms_supplier->fax}}">
                      @if($errors->has('fax'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('fax') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_norek" class="col-sm-2 control-label">No Rekening <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="norek" id="norek" placeholder="No Rekening" value="{{$ms_supplier->no_rekening}}">
                      @if($errors->has('norek'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('norek') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_card_name" class="col-sm-2 control-label">Card Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="card_name" id="card_name" placeholder="Card Name" value="{{$ms_supplier->nama_rekening}}">
                      @if($errors->has('card_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('card_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_bank_name" class="col-sm-2 control-label">Bank Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" value="{{$ms_supplier->bank_nama}}">
                      @if($errors->has('bank_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('bank_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_npwp" class="col-sm-2 control-label">NPWP <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="npwp" id="npwp" placeholder="NPWP" value="{{$ms_supplier->npwp}}">
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
        window.location.replace("{{ url('/mainmenu/supp_home') }}");
      }
    }

  </script>
</body>
@endsection