@extends('template.app')

{{-- set title --}}
@section('title', 'Manage Solar Usage')

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
          Solar Usage
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Solar Usage Home</li>
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
            <form class="form" action="{{ url('/houling/solar_home/getrange') }}" method="post">
              {{-- set token --}}
              {{ csrf_field() }}

              <div class="box">
                <div class="box-body">

                  <div class="col-md-6">
                    <div class="col-md-6">
                      <div class="row">
                        <div class='date' id='datetimepicker1'>
                          <label class="control-label">Start Date</label>
                          <input type="text" id="dateStart" name="dateStart" class="form-control for_date" @if(isset($arraydate[0])) value="{{$arraydate[0]}}" @endif>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="row">
                        <div class='date' id='datetimepicker1'>
                          <label class="control-label">End Date</label>
                          <input type="text" id="dateEnd" name="dateEnd" class="form-control for_date" @if(isset($arraydate[1])) value="{{$arraydate[1]}}" @endif>
                        </div>
                      </div>
                    </div>

                    <label class="control-label">Car ID</label>
                    <input class="form-control" type="text" id="carid" name="carid" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydate[2])) value="{{$arraydate[2]}}" @endif>

                    <div class="form-group" id="getposition_selectbox">
                      <label class="control-label">Solar Type</label>
                      <select class="form-control" id="type" name="type">
                        <option value="">Choose</option>
                        <?php
                          foreach($lt_solar as $item){
                          if(isset($arraydate[3])){
                            $val_id = $arraydate[3];
                          }else{
                            $val_id = "";
                          }
                        ?>

                        <?php
                            if($item->id == $val_id){
                              echo '<option selected value="'.$item->id.'">'.$item->name.'</option>';
                            }else{
                              echo '<option value="'.$item->id.'">'.$item->name.'</option>';
                            }

                        ?>

                        <?php
                          }
                        ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <label class="control-label">ID</label>
                    <input class="form-control" type="text" id="solarid" name="solarid" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydate[4])) value="{{$arraydate[4]}}" @endif>

                    <label class="control-label">Solar Charger</label>
                    <input class="form-control name" type="text" id="name" name="name" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydate[5])) value="{{$arraydate[5]}}" @endif>

                    <label class="control-label">Price</label>
                    <input class="form-control" type="number" id="price" name="price" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydate[6])) value="{{$arraydate[6]}}" @endif>

                  </div>

                </div>
                <div class="box-footer">
                  <div class="pull-right">
                    <a style="width : 90px;" href="{{url('houling/solar_home')}}" type="button" onclick="return confirm('Are you sure want to reset?')" class="btn btn-warning">Reset</a>
                    <button style="width : 90px;" type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </div>
            </form>

            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Solar</h3>
                </div>

                <div class="box-body table-responsive">

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->c==1)
                  <a href="{{ url('/houling/solar_home/add') }}" type="button" class="btn btn-primary">Add New Solar</a><br><br>
                  @endif
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>ID</th>
                        <th>Car ID</th>
                        <th>Solar Charger</th>
                        <th>Date Usage</th>
                        <th>Nota</th>
                        <th>Type Solar</th>
                        <th>Price Liter</th>
                        <th>Used (Liter)</th>
                        <th>Total Price</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($lt_pemakaiansolar as $item)
                      <?php $total = $item->price * $item->liter_pemakaian_solar ?>
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                        <td>{{ $item->pemakaian_solar_id }}</td>
                        <td>{{ $item->mobil_id }}</td>
                        <td>{{ $item->nama_pengisi }}</td>
                        <td><?= date("d/m/Y",strtotime($item->tanggal_pemakaian));?></td>
                        <td>{{ $item->no_nota }}</td>
                        <td>{{ $item->name_solar }}</td>
                        <td>{{ number_format($item->price) }}</td>
                        <td>{{ $item->liter_pemakaian_solar }}</td>
                        <td>{{ number_format($total) }}</td>
                        <td>{{ $item->modify_user_id }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->last_modify_date));?></td>
                        <td>@if($item->status == 'A') {{ 'Active' }} @elseif($item->status == 'D') {{ 'Deactive' }} @elseif($item->status == 'T') {{ 'Temporary' }} @endif</td>

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <td><a style="width:90px;" href="{{ url('/houling/solar_home/edit/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                        @endif

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <td><a style="width:90px;" href="{{ url('/houling/solar_home/delete/'.$item->id) }}" type="button" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data ?')">Delete</a></td>
                        @endif
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>ID</th>
                        <th>Car ID</th>
                        <th>Solar Charger</th>
                        <th>Date Usage</th>
                        <th>Nota</th>
                        <th>Type Solar</th>
                        <th>Price Liter</th>
                        <th>Used (Liter)</th>
                        <th>Total Price</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </tfoot>
                  </table>
                </div>

                @if(DB::table('level_akses')->where('id_menu', '1')->where('id_level',auth::user()->level_id)->first()->e==1)
                  <div class="box-footer">
                     <form class="form" action="{{ url('/houling/solar_home/export') }}" method="POST">

                        {{ csrf_field() }}

                        <input type="hidden" id="dateStart" name="dateStart" value="<?php if(isset($arraydate[0])){ echo $arraydate[0]; }?>">
                        <input type="hidden" id="dateEnd" name="dateEnd" value="<?php if(isset($arraydate[1])){ echo $arraydate[1]; }?>">
                        <input type="hidden" id="carid" name="carid" value="<?php if(isset($arraydate[2])){ echo $arraydate[2]; }?>">
                        <input type="hidden" id="type" name="type" value="<?php if(isset($arraydate[3])){ echo $arraydate[3]; }?>">
                        <input type="hidden" id="solarid" name="solarid" value="<?php if(isset($arraydate[4])){ echo $arraydate[4]; }?>">
                        <input type="hidden" id="name" name="name" value="<?php if(isset($arraydate[5])){ echo $arraydate[5]; }?>">
                        <input type="hidden" id="price" name="price" value="<?php if(isset($arraydate[6])){ echo $arraydate[6]; }?>">

                        <button type="submit" class="btn btn-success pull-right">Export Excel</button>
                    </form>
                  </div>
                @endif

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
          // dom: 'lrtipB',
          // buttons: [
          //         'copy', 'csv', 'excel', 'pdf', 'print'
          // ]
          dom: 'Bfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  title: 'TUNAS ABADI 8 | Data Solar Usage',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Solar Usage',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Data Solar Usage',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Solar Usage',
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
