@extends('template.app')

{{-- set title --}}
@section('title', 'Manage Credit')

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
          Credit
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Credit Home</li>
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

            {{-- search --}}
            <!-- Search FIX -->
              <form class="form" action="{{ url('/finance/credit_home/getrange') }}" method="post">
                {{csrf_field()}}
                <div class="box">
                  <div class="box-body">
                    <div class="col-md-6">
                      <div class="col-md-6">
                        <div class="row">
                          <div class='date' id='datetimepicker1'>
                            <label class="control-label">Start Date</label>
                            <input type="text" id="dateStart" name="dateStart" class="form-control for_date" @if(isset($arraydata[0])) value="{{$arraydata[0]}}" @endif>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class='date' id='datetimepicker1'>
                            <label class="control-label">End Date</label>
                            <input type="text" id="dateEnd" name="dateEnd" class="form-control for_date" @if(isset($arraydata[1])) value="{{$arraydata[1]}}" @endif>
                          </div>
                        </div>
                      </div>

                      <label class="control-label">Credit ID</label>
                      <input class="form-control" type="text" id="idcredit" name="idcredit" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>
                    </div>

                    <div class="col-md-6">
                      <label class="control-label">Selling ID</label>
                      <input class="form-control" type="text" id="idselling" name="idselling" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>

                      <label class="control-label">Status</label>
                      <select style="text-transform:uppercase" name="status" class="form-control">
                        <option value="all" @if(isset($arraydata[4])) @if($arraydata[4]=="all") selected="true"  @endif @endif>ALL</option>
                        <option value="paid" @if(isset($arraydata[4])) @if($arraydata[4]=="paid") selected="true"  @endif @endif>Paid</option>
                        <option value="unpaid" @if(isset($arraydata[4])) @if($arraydata[4]=="unpaid") selected="true"  @endif @endif>Unpaid</option>
                      </select>
                    </div>

                  </div>
                  <div class="box-footer">
                    <div class="pull-right">
                      <a style="width : 90px;" href="{{url('finance/credit_home')}}" type="button" onclick="return confirm('Are you sure want to reset?')" class="btn btn-warning">Reset</a>
                      <button style="width : 90px;" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            <!-- Search FIX -->
            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Credit</h3>
                </div>

                <div class="box-body table-responsive">
                  {{-- <a href="{{ url('/finance/credit/add') }}" type="button" class="btn btn-primary">Add New Houling</a><br><br> --}}
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>Credit ID</th>
                        <th>Selling ID</th>
                        <th>Date Payment</th>
                        <th>Grand Total</th>
                        <th>Credit already paid</th>
                        <th>Total Credit payable</th>
                        <th>Status</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($lt_piutang as $item)
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                        <td>{{ $item->piutang_id }}</td>
                        <td>{{ $item->b_penjualan_id }}</td>
                        <td><?= date("d/m/Y",strtotime($item->jatuhtempo));?></td>
                        <td>{{ number_format($item->total) }}</td>
                        <td>{{ number_format($item->bayar) }}</td>
                        <td>{{ number_format($item->total-$item->bayar) }}</td>
                        <td>@if($item->status_piutang > 0) {{ 'UNPAID' }} @else {{ 'PAID' }} @endif</td>
                        <td>{{ $item->modify_user_id }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->last_modify_date));?></td>
                        @if($item->status_piutang > 0)
                          <td><a style="width:90px;" href="{{ url('/finance/credit_home/edit/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                        @else
                          <td><a style="width:90px;" href="" type="button" class="btn btn-default disabled">Edit</a></td>
                        @endif
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>Credit ID</th>
                        <th>Selling ID</th>
                        <th>Date Payment</th>
                        <th>Grand Total</th>
                        <th>Credit already paid</th>
                        <th>Total Credit payable</th>
                        <th>Status</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                  </div>

                  <div class="box-footer">
                    <form class="form" action="{{ url('/finance/credit_home/export') }}" method="post">
                      {{ csrf_field() }}
                      <input type="hidden" name="dateStart" value="<?php if(isset($arraydata[0])){ echo $arraydata[0]; }?>">
                      <input type="hidden" name="dateEnd" value="<?php if(isset($arraydata[1])){ echo $arraydata[1]; }?>">
                      <input class="form-control" type="hidden" id="idcredit" name="idcredit" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>
                      <input class="form-control" type="hidden" id="status" name="status" pattern=".{3,}" title="3 characters minimum"  @if(isset($arraydata[4])) @if($arraydata[4]=="all") value="all"  @elseif($arraydata[4]=="paid") value="paid" @else value="unpaid" @endif @endif>
                      <input class="form-control" type="hidden" id="idselling" name="idselling" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
                      <button style="width:90px;" type="submit" class="btn btn-success pull-right">Export</button>
                    </form>
                  </div>

              </div>
            </div>
          </div>
      </section>

    </div>
  </div>

  <script>
      $(function () {
        $('#table-home tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
        });

        var table = $('#table-home').DataTable({
          responsive: true,
          stateSave: true,
          "paging": true,
          "lengthChange": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
          "order": [[ 0, "desc" ]],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
          dom: 'Bfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  title: 'TUNAS ABADI 8 | Data Credit',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Credit',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Data Credit',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Credit',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed four-column',
                columnText: function ( dt, idx, title ) {
                    return (idx+1)+': '+title;
                }
              },
          ],
            columnDefs: [ {
                targets: -1,
                visible: false
            }]
        });
        // Apply the search
        table.columns().every( function () {
          var that = this;
          $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          });
        });

        // for datetimepicker
        $('.for_date').datetimepicker({
          format: 'YYYY/MM/DD'
        });

      });
    </script>
<body>
@endsection
