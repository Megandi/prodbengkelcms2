@extends('template.app')

{{-- set title --}}
@section('title', 'Employee Edit')

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
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Employee Home</li>
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

                <form class="form-horizontal" action="{{ url('/mainmenu/emp_home/do_edit/'.$ms_karyawan->karyawan_id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_karyawan_id" class="col-sm-2 control-label">Employee ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="karyawan_id" id="karyawan_id" placeholder="Employee ID" value="{{$ms_karyawan->id_employee}}" readonly>
                      @if($errors->has('karyawan_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="karyawan_name" id="karyawan_name" placeholder="Name" value="{{$ms_karyawan->nama}}">
                      @if($errors->has('karyawan_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_address" class="col-sm-2 control-label">Address <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="karyawan_address" id="karyawan_address" placeholder="Address">{{$ms_karyawan->alamat}}</textarea>
                      @if($errors->has('karyawan_address'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_address') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_telp" class="col-sm-2 control-label">Telephone <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="karyawan_telp" id="karyawan_telp" placeholder="Telephone" value="{{$ms_karyawan->no_telp}}">
                      @if($errors->has('karyawan_telp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_telp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_hp" class="col-sm-2 control-label">Handphone <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="karyawan_hp" id="karyawan_hp" placeholder="Handphone" value="{{$ms_karyawan->no_hp}}">
                      @if($errors->has('karyawan_hp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_hp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_address_2" class="col-sm-2 control-label">Address 2 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="karyawan_address_2" id="karyawan_address_2" placeholder="Address 2">{{$ms_karyawan->alamat_asal}}</textarea>
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
                            @if($ms_karyawan->jabatan == $item->id)
                              <option selected value="{{$ms_karyawan->jabatan}}">{{ $ms_karyawan->position_name }}</option>
                            @else
                              <option value="{{$item->id}}">{{$item->name}}</option>
                            @endif
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
                          <input type="text" class="form-control for_date" name="karyawan_dob" id="karyawan_dob" value="{{$ms_karyawan->tanggal_lahir}}">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_bop" class="col-sm-2 control-label">Place of Birth <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="karyawan_bop" id="karyawan_bop" placeholder="Place of Birth" value="{{$ms_karyawan->tempat_lahir}}">
                      @if($errors->has('karyawan_bop'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_bop') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_karyawan_status" class="col-sm-2 control-label">Status Employee <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="karyawan_status" id="karyawan_status">
                          <option value="">Choose Status</option>
                          <option @if($ms_karyawan->status_karyawan == "Permanent") selected @endif value="Permanent">Permanent</option>
                          <option @if($ms_karyawan->status_karyawan == "Kontrak") selected @endif value="Kontrak">Kontrak</option>
                          <option @if($ms_karyawan->status_karyawan == "Magang") selected @endif value="Magang">Magang</option>
                        </select>
                      @if($errors->has('karyawan_status'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_status') }}</p>
                      @endif
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="input_norek" class="col-sm-2 control-label">No Rekening <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="norek" id="norek" placeholder="No Rekening" value="{{$ms_karyawan->no_rekening}}">
                      @if($errors->has('norek'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('norek') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_card_name" class="col-sm-2 control-label">Card Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="card_name" id="card_name" placeholder="Card Name" value="{{$ms_karyawan->nama_rekening}}">
                      @if($errors->has('card_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('card_name') }}</p>
                      @endif
                      </div>
                    </div>

                     <div class="form-group">
                      <label for="input_bank_name" class="col-sm-2 control-label">Bank Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" value="{{$ms_karyawan->bank_nama}}">
                      @if($errors->has('bank_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('bank_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emergency_name" class="col-sm-2 control-label">Emergency Name <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="emergency_name" id="emergency_name" placeholder="Emergency Name" value="{{$ms_karyawan->nama_emergency_karyawan}}">
                      @if($errors->has('emergency_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emergency_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emergency_address_2" class="col-sm-2 control-label">Emergency Address <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="emergency_address_2" id="emergency_address_2" placeholder="Address 2">{{$ms_karyawan->alamat_emergency_karyawan}}</textarea>
                      @if($errors->has('emergency_address_2'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emergency_address_2') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emergency_contact" class="col-sm-2 control-label">Emergency Contact <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="emergency_contact" id="emergency_contact" placeholder="Emergency Contact" value="{{$ms_karyawan->no_kontak_emergency_karyawan}}">
                      @if($errors->has('emergency_contact'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emergency_contact') }}</p>
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
        window.location.replace("{{ url('/mainmenu/emp_home') }}");
      }
    }

    // for datetimepicker
    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD' 
    });

	</script>
</body>
@endsection