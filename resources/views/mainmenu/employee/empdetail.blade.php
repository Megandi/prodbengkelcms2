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
          Employee
          <small>Detail</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Employee Detail</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-body">
                  <div class="col-md-6">
                    <label class="control-label">ID</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->karyawan_id}}">
                    <label class="control-label">Name</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->nama}}">
                    <label class="control-label">No Telp</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->no_telp}}">
                    <label class="control-label">No Telp (Mobile)</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->no_hp}}">
                    <label class="control-label">Position</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->position_name}}">
                    <label class="control-label">Address</label>
                    <textarea class="form-control" readonly="true">{{$emp->alamat}}</textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="control-label">Address from</label>
                    <textarea class="form-control" readonly="true">{{$emp->alamat_asal}}</textarea>
                    <label class="control-label">Status</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->status_karyawan}}">
                    <label class="control-label">No Rekening</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->no_rekening}}">
                    <label class="control-label">Rekening Name</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->nama_rekening}}">
                    <label class="control-label">Bank Name</label>
                    <input class="form-control" type="text" readonly="true" value="{{$emp->bank_nama}}">
                  </div>
                </div>
                <div class="box-footer">
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <!-- Custom Tabs -->
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#tab_1" data-toggle="tab">Transaksi Penjualan</a></li>
                  <li><a href="#tab_2" data-toggle="tab">Pinjaman</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                    <table id="table-home" class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Created Date</th>
                          <th>ID Selling</th>
                          <th>No Nota</th>
                          <th>Date Buy</th>
                          <th>Total Price</th>
                          <th>Status</th>
                          <th>Customer</th>
                          <th>Modify by</th>
                          <th>Last Modified</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $status = ""; ?>
                        @foreach($sellings as $selling)
                          @if($selling->id_karyawan_kerja1==$emp->karyawan_id || $selling->id_karyawan_kerja2==$emp->karyawan_id || $selling->id_karyawan_kerja3==$emp->karyawan_id || $selling->id_karyawan_kerja4==$emp->karyawan_id || $selling->id_karyawan_kerja5==$emp->karyawan_id)
                            @if($status!=$selling->penjualan_id)
                            <?php $status = $selling->penjualan_id; ?>
                            <tr>
                              <td><?= date("d/m/Y H:i:s",strtotime($selling->created_date));?></td>
                              <td>{{ $selling->penjualan_id }}</td>
                              <td>{{ $selling->no_nota }}</td>
                              <td><?= date("d/m/Y H:i:s",strtotime($selling->tanggal));?></td>
                              <td>{{ number_format($selling->penjualan_total)}}</td>
                              @if($selling->status_piutang)
                                <td>@if($selling->status_piutang==1) UNPAID @else PAID @endif</td>
                              @else
                                <td>PAID</td>
                              @endif
                              @if($selling->no_nota =="")
                                <td></td>
                              @else
                                <td>{{ $selling->custid }} - {{ $selling->namacustomer }} </td>
                              @endif
                              <td>{{ $selling->modify_user_id }}</td>
                              <td><?= date("d/m/Y H:i:s",strtotime($selling->last_modify_date));?></td>
                            </tr>
                            @endif
                          @endif
                        @endforeach

                      </tbody>
                      <tfoot>
                        <tr>
                          <th>Created Date</th>
                          <th>ID Selling</th>
                          <th>No Nota</th>
                          <th>Date Buy</th>
                          <th>Total Price</th>
                          <th>Status</th>
                          <th>Customer</th>
                          <th>Modify by</th>
                          <th>Last Modified</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div><!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_2">
                    <table id="table-home" class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Created Date</th>
                          <th>Loan ID</th>
                          <th>Loan user</th>
                          <th>Grand Total</th>
                          <th>Loan Already Paid</th>
                          <th>Total Loan payable</th>
                          <th>Status Loan</th>
                          <th>Due Date</th>
                          <th>Modify by</th>
                          <th>Last Modified</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>

                        @foreach($lt_loan as $item)
                        <?php $last_total =  $item->total - $item->bayar;?>
                        <tr>
                          <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                          <td>@if($item->loan_type != 1){{ $item->loan_id .' - '. $item->user_id  }}@else {{ $item->loan_id }} @endif</td>
                          <td>@if(substr($item->user_id,0,1) == 'E'){{ $item->employee_name .' - '. $item->user_id }}
                          @elseif(substr($item->user_id,0,1) == 'C'){{ $item->customer_name .' - '. $item->user_id }}
                          @elseif(substr($item->user_id,0,1) == 'S'){{ $item->supplier_name .' - '. $item->user_id }}
                          @elseif(substr($item->user_id,0,1) == 'A'){{ $item->user_id }}@endif</td>
                          <td>{{number_format($item->total) }}</td>
                          <td>{{number_format($item->bayar) }}</td>
                          <td>{{number_format($last_total) }}</td>
                          <td>@if($item->status_loan == 1){{ 'UNPAID' }}@else{{ 'PAID' }}@endif</td>
                          <td><?= date("d/m/Y",strtotime($item->tanggal_jatuh_tempo));?></td>
                          <td>{{ $item->modify_user_id }}</td>
                          <td><?= date("d/m/Y H:i:s",strtotime($item->last_modify_date));?></td>
                          <td>@if($item->status == 'A') {{ 'Active' }} @elseif($item->status == 'D') {{ 'Deactive' }} @elseif($item->status == 'T') {{ 'Temporary' }} @endif</td>
                        </tr>
                        @endforeach

                      </tbody>
                      <tfoot>
                        <tr>
                          <th>Created Date</th>
                          <th>Loan ID</th>
                          <th>Load user</th>
                          <th>Grand Total</th>
                          <th>Loan Already Paid</th>
                          <th>Total Loan payable</th>
                          <th>Status Loan</th>
                          <th>Due Date</th>
                          <th>Modify by</th>
                          <th>Last Modified</th>
                          <th>Status</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
              </div><!-- nav-tabs-custom -->
            </div><!-- /.col -->
          </div> <!-- /.row -->
      </section>
    </div>
  </div>
<body>
@endsection
