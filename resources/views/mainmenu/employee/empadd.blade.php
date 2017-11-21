@extends('template.app')

{{-- set title --}}
@section('title', 'Employee Add')

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
          Employee
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Employee Home</li>
          <li class="active">Add</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/mainmenu/emp_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_karyawan_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="karyawan_name" id="karyawan_name" placeholder="Name" value="{{old('karyawan_name')}}">
                      @if($errors->has('karyawan_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_address" class="col-sm-2 control-label">Address <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="karyawan_address" id="karyawan_address" placeholder="Address">{{old('karyawan_address')}}</textarea>
                      @if($errors->has('karyawan_address'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_address') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_telp" class="col-sm-2 control-label">Telephone <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="karyawan_telp" id="karyawan_telp" placeholder="Telephone" value="{{old('karyawan_telp')}}">
                      @if($errors->has('karyawan_telp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_telp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_hp" class="col-sm-2 control-label">Handphone <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="karyawan_hp" id="karyawan_hp" placeholder="Handphone" value="{{old('karyawan_hp')}}">
                      @if($errors->has('karyawan_hp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_hp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_address_2" class="col-sm-2 control-label">Address 2 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="karyawan_address_2" id="karyawan_address_2" placeholder="Address 2">{{old('karyawan_address_2')}}</textarea>
                      @if($errors->has('karyawan_address_2'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_address_2') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_position" class="col-sm-2 control-label">Position <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="position_id" id="position_id">
                        <option value="">Choose position</option>
                          @foreach($ms_jabatan as $item)
                            <option @if(old("position_id") == $item->id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                          @endforeach
                        </select>
                      @if($errors->has('position_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('position_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_dob" class="col-sm-2 control-label">Date of birth <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker'>
                          <input type="text" class="form-control for_date" name="karyawan_dob" id="karyawan_dob" value="{{old('karyawan_dob')}}">
                          @if($errors->has('karyawan_dob'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_dob') }}</p>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_bop" class="col-sm-2 control-label">Place of Birth <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="karyawan_bop" id="karyawan_bop" placeholder="Place of Birth" value="{{old('karyawan_bop')}}">
                      @if($errors->has('karyawan_bop'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_bop') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_status" class="col-sm-2 control-label">Status Employee <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="karyawan_status" id="karyawan_status">
                          <option value="">Choose Status</option>
                          <option @if(old("karyawan_status") == "Permanent") selected @endif value="Permanent">Permanent</option>
                          <option @if(old("karyawan_status") == "Kontrak") selected @endif value="Kontrak">Kontrak</option>
                          <option @if(old("karyawan_status") == "Magang") selected @endif value="Magang">Magang</option>
                        </select>
                      @if($errors->has('karyawan_status'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_status') }}</p>
                      @endif
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="input_norek" class="col-sm-2 control-label">No Rekening <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="norek" id="norek" placeholder="No Rekening" value="{{old('norek')}}">
                      @if($errors->has('norek'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('norek') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_card_name" class="col-sm-2 control-label">Card Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="card_name" id="card_name" placeholder="Card Name" value="{{old('card_name')}}">
                      @if($errors->has('card_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('card_name') }}</p>
                      @endif
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="input_bank_name" class="col-sm-2 control-label">Bank Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" value="{{old('bank_name')}}">
                      @if($errors->has('bank_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('bank_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emergency_name" class="col-sm-2 control-label">Emergency Name <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="emergency_name" id="emergency_name" placeholder="Emergency Name" value="{{old('emergency_name')}}">
                      @if($errors->has('emergency_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emergency_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emergency_address_2" class="col-sm-2 control-label">Emergency Address <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="emergency_address_2" id="emergency_address_2" placeholder="Address 2">{{old('emergency_address_2')}}</textarea>
                      @if($errors->has('emergency_address_2'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emergency_address_2') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emergency_contact" class="col-sm-2 control-label">Emergency Contact <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="emergency_contact" id="emergency_contact" placeholder="Emergency Contact" value="{{old('emergency_contact')}}">
                      @if($errors->has('emergency_contact'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emergency_contact') }}</p>
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
        window.location.replace("{{ url('/mainmenu/emp_home') }}");
      }
    }

    // for datetimepicker
    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      maxDate: new Date()
    });

	</script>
</body>
@endsection