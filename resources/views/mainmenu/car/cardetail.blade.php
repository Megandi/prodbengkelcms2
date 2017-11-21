@extends('template.app')

{{-- set title --}}
@section('title', 'Manage Employee')

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
          Car
          <small>Detail</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Car Detail</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-body">
                  <div class="col-md-6">
                    <label class="control-label">ID</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->mobil_id}}">
                    <label class="control-label">Name</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->type_car}}">
                    <label class="control-label">Customer ID</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->customer_id}}">
                    <label class="control-label">Police Number Car</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->no_polisi_mobil}}">
                    <label class="control-label">Merk</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->merek_mobil}}">
                    <label class="control-label">Owner Address</label>
                    <textarea class="form-control" readonly="true">{{$ms_mobil->alamat_pemilik}}</textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="control-label">Type</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->tipe_mobil}}">
                    <label class="control-label">Kind</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->jenis_mobil}}">
                    <label class="control-label">Model</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->model}}">
                    <label class="control-label">Car Color</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->warna_mobil}}">
                    <label class="control-label">Year Production</label>
                    <input class="form-control" type="text" readonly="true" value="{{$ms_mobil->tahun_pembuatan_mobil}}">
                  </div>
                </div>
                <div class="box-footer">
                </div>
              </div>
            </div>
          </div>
      </section>
    </div>
  </div>
<body>
@endsection
