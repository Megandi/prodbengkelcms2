@extends('template.app')

{{-- set title --}}
@section('title', 'Dashboard')

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
          Dashboard
        </h1>
        <ol class="breadcrumb">
          <li class="active">Dashboard</li>
        </ol>
      </section>

			<!-- Main content -->
			<section class="content">
        <?php $menu_10 = DB::table('level_akses')->where('id_menu', '10')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_10)==1)
          @if($menu_10[0]->r==1)
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

                  {{-- temporary buying & selling --}}
                  <?php $menu_3 = DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_3)==1)
                    @if($menu_3[0]->r==1)
                  <div class="box box-success collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Temporary Items <b>BUYING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>
                    <div class="box-body table-responsive">
                      <table id="table-temp-buying" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Created Date</th>
                            <th>Qty</th>
                            <th>Buying Price</th>
                            <th>Sell Price</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($barang_temp as $item)
                            @if($item['harga_beli']=="")
                            @else
                              <tr>
                                <td>{{$item->nama}}</td>
                                <td>{{date("d/m/Y",strtotime($item->created_date))}}</td>
                                <td>{{$item->qty}}</td>
                                <td>{{$item->harga_beli}}</td>
                                <td>{{$item->harga_jual}}</td>
                                @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                                <td><a style="width:90px;" type="button" onclick="barang({{$item->id}})" class="btn btn-primary">FIX</a></td>
                                @endif
                              </tr>
                            @endif
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Name</th>
                            <th>Created Date</th>
                            <th>Qty</th>
                            <th>Buying Price</th>
                            <th>Sell Price</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                    @endif
                  @endif

                  <?php $menu_4 = DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_4)==1)
                    @if($menu_4[0]->r==1)
                  <div class="box box-success collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Temporary Items <b>SELLING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>
                    <div class="box-body table-responsive">
                      <table id="table-temp-selling" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Created Date</th>
                            <th>Qty</th>
                            <th>Buying Price</th>
                            <th>Sell Price</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($barang_temp as $item)
                            @if($item['harga_jual']=="")
                            @else
                              <tr>
                                <td>{{$item->nama}}</td>
                                <td>{{date("d/m/Y",strtotime($item->created_date))}}</td>
                                <td>{{$item->qty}}</td>
                                <td>{{$item->harga_beli}}</td>
                                <td>{{$item->harga_jual}}</td>
                                @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                                <td><a style="width:90px;" type="button" onclick="barang({{$item->id}})" class="btn btn-primary">FIX</a></td>
                                @endif
                              </tr>
                            @endif
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Name</th>
                            <th>Created Date</th>
                            <th>Qty</th>
                            <th>Buying Price</th>
                            <th>Sell Price</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                    @endif
                  @endif
                  {{-- temporary buying & selling --}}

                  {{-- RUGI LABA
                  <div class="row">
                    <div class="col-md-6">
                      <div class="box box-success collapsed-box">
                        <div class="box-header">
                          <h3 class="box-title"> Income <b>SUMMARY</b></h3>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                          </div>
                        </div>
                        <div class="box-body">
                          <div class="box">
                            <div class="box-body">
                              <select clas>

                              </select>
                            </div>
                            <div class="box-footer">
                              Gross Profit (Total Selling) :
                              <h3 class="pull-right">Rp {{number_format($gross)}} </h3><br><br><br>
                              Total Buying :
                              <h3 class="pull-right">Rp {{number_format($totalbuying)}}</h3><br><br><br>
                              <hr>
                              Net Income :
                              <h3 class="pull-right"> Rp Rp {{number_format($net)}} </h3><br><br><br>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                  RUGI LABA --}}


                  {{-- debt to supplier --}}
                  <?php $menu_3 = DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_3)==1)
                    @if($menu_3[0]->r==1)
                  <div class="box box-primary collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Debt to Supplier | Today : {{ date('d/m/Y') }} | <b>ON GOING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-debt" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Debt ID</th>
                            <th>Buy ID</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_hutang as $item)

                          <?php
                            $now  = date("Y-m-d");
                            $date = date("Y-m-d", strtotime($item->duedate));
                          ?>

                          @if($now == $date)
                            <tr style="background-color: yellow; color: black;">
                          @else
                            <tr>
                          @endif
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->debtid}}</td>
                            <td>{{$item->buyid}}</td>
                            <td>{{$item->suppid}}</td>
                            <td>{{$item->total}}</td>
                            <td>{{$item->nota}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/buying/debt_home/edit/'.$item->id_debt) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Debt ID</th>
                            <th>Buy ID</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                    @endif
                  @endif


                  <?php $menu_3 = DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_3)==1)
                    @if($menu_3[0]->r==1)
                  <div class="box box-warning collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Debt to Supplier | Today : {{ date('d/m/Y') }} | <b>PAST DUE</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-debt-to" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Debt ID</th>
                            <th>Buy ID</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_hutang_to as $item)

                          <tr style="background-color: red; color: white;">
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->debtid}}</td>
                            <td>{{$item->buyid}}</td>
                            <td>{{$item->suppid}}</td>
                            <td>{{$item->total}}</td>
                            <td>{{$item->nota}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/buying/debt_home/edit/'.$item->id_debt) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Debt ID</th>
                            <th>Buy ID</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  {{-- debt to supplier --}}
                    @endif
                  @endif

                  {{-- debt to customer --}}
                  <?php $menu_4 = DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_4)==1)
                    @if($menu_4[0]->r==1)
                  <div class="box box-primary collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Credit to Customer | Today : {{ date('d/m/Y') }} | <b>ON GOING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-credit" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Credit ID</th>
                            <th>Sell ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_piutang as $item)

                          <?php
                            $now  = date("Y-m-d");
                            $date = date("Y-m-d", strtotime($item->duedate));
                          ?>

                          @if($now == $date)
                            <tr style="background-color: yellow; color: black;">
                          @else
                            <tr>
                          @endif
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->creditid}}</td>
                            <td>{{$item->sellid}}</td>
                            <td>{{$item->custid}}</td>
                            <td>{{$item->total}}</td>
                            <td>{{$item->nota}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/selling/credit_home/edit/'.$item->id_credit) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Credit ID</th>
                            <th>Sell ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                    @endif
                  @endif

                  <?php $menu_4 = DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_4)==1)
                    @if($menu_4[0]->r==1)
                  <div class="box box-warning collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Credit to Customer | Today : {{ date('d/m/Y') }} | <b>PAST DUE</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-credit-to" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Credit ID</th>
                            <th>Sell ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_piutang_to as $item)

                          <tr style="background-color: red; color: white;">
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->creditid}}</td>
                            <td>{{$item->sellid}}</td>
                            <td>{{$item->custid}}</td>
                            <td>{{$item->total}}</td>
                            <td>{{$item->nota}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/selling/credit_home/edit/'.$item->id_credit) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Credit ID</th>
                            <th>Sell ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Nota</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                    @endif
                  @endif
                  {{-- debt to customer --}}

                  {{-- 10 debt and credit --}}
                  <div class="row">
                  <?php $menu_3 = DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_3)==1)
                    @if($menu_3[0]->r==1)
                    <div class="col-md-6">
                      <div class="box box-solid box-primary collapsed-box">
                        <div class="box-header">
                          <h3 class="box-title">10 TOTAL DEBT SUPPLIER</b></h3>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                          </div>
                        </div>

                        <div class="box-body table-responsive">
                          <table id="table-10-debt" class="table table-bordered table-hover">
                            <thead>
                              <tr>
                                <th>No. </th>
                                <th>Supplier ID</th>
                                <th>Name</th>
                                <th>Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0;?>
                              @foreach($tr_deposit_supp as $item)
                            <?php $i++;?>
                              <tr>
                                <td>{{$i}}</td>
                                <td>{{$item->suppid}}</td>
                                <td>{{$item->suppname}}</td>
                                <td>{{$item->total}}</td>
                              </tr>
                              @endforeach

                            </tbody>
                            <tfoot>
                              <tr>
                                <th>No. </th>
                                <th>Supplier ID</th>
                                <th>Name</th>
                                <th>Total</th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>
                    @endif
                  @endif

                  <?php $menu_4 = DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_4)==1)
                    @if($menu_4[0]->r==1)
                    <div class="col-md-6">
                      <div class="box box-solid box-info collapsed-box">
                        <div class="box-header">
                          <h3 class="box-title">10 TOTAL CREDIT CUSTOMER</b></h3>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                          </div>
                        </div>

                        <div class="box-body table-responsive">
                          <table id="table-credit-10" class="table table-bordered table-hover">
                            <thead>
                              <tr>
                                <th>No. </th>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0;?>
                              @foreach($tr_deposit_cust as $item)
                            <?php $i++;?>
                              <tr>
                                <td>{{$i}}</td>
                                <td>{{$item->custid}}</td>
                                <td>{{$item->custname}}</td>
                                <td>{{$item->total}}</td>
                              </tr>
                              @endforeach

                            </tbody>
                            <tfoot>
                              <tr>
                                <th>No. </th>
                                <th>Supplier ID</th>
                                <th>Name</th>
                                <th>Total</th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>
                    @endif
                  @endif
                  </div>
                  {{-- 10 debt and credit --}}


                  <?php $menu_7 = DB::table('level_akses')->where('id_menu', '7')->where('id_level',auth::user()->level_id)->get(); ?>
                  @if(sizeof($menu_7)==1)
                    @if($menu_7[0]->r==1)
                  {{-- loan from employee --}}
                  <div class="box box-default collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Loan to Employee | Today : {{ date('d/m/Y') }} | <b>ON GOING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-loan-emp" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_loan_emp as $item)

                          <?php
                            $now  = date("Y-m-d");
                            $date = date("Y-m-d", strtotime($item->duedate));
                          ?>

                          @if($now == $date)
                            <tr style="background-color: yellow; color: black;">
                          @else
                            <tr>
                          @endif
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->loanid}}</td>
                            <td>{{$item->userid}} - {{$item->name}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id_loan_emp) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                  <div class="box box-warning collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Loan to Employee | Today : {{ date('d/m/Y') }} | <b>PAST DUE</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-loan-emp-to" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_loan_emp_to as $item)

                          <tr style="background-color: red; color: white;">
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->loanid}}</td>
                            <td>{{$item->userid}} - {{$item->name}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id_loan_emp) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  {{-- loan from employee --}}

                  {{-- loan from supplier --}}
                  <div class="box box-default collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Loan to Supplier | Today : {{ date('d/m/Y') }} | <b>ON GOING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-loan-supp" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_loan_supp as $item)

                          <?php
                            $now  = date("Y-m-d");
                            $date = date("Y-m-d", strtotime($item->duedate));
                          ?>

                          @if($now == $date)
                            <tr style="background-color: yellow; color: black;">
                          @else
                            <tr>
                          @endif
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->loanid}}</td>
                            <td>{{$item->userid}} - {{$item->name}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id_loan_supp) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                  <div class="box box-warning collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Loan to Supplier | Today : {{ date('d/m/Y') }} | <b>PAST DUE</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-loan-supp-to" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_loan_supp_to as $item)

                          <tr style="background-color: red; color: white;">
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->loanid}}</td>
                            <td>{{$item->userid}} - {{$item->name}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id_loan_supp) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  {{-- loan from supplier --}}

                  {{-- loan from customer --}}
                  <div class="box box-default collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Loan to Customer | Today : {{ date('d/m/Y') }} | <b>ON GOING</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-loan-cust" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_loan_cust as $item)

                          <?php
                            $now  = date("Y-m-d");
                            $date = date("Y-m-d", strtotime($item->duedate));
                          ?>

                          @if($now == $date)
                            <tr style="background-color: yellow; color: black;">
                          @else
                            <tr>
                          @endif
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->loanid}}</td>
                            <td>{{$item->userid}} - {{$item->name}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id_loan_cust) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                  <div class="box box-warning collapsed-box">
                    <div class="box-header">
                      <h3 class="box-title">Data Loan to Customer | Today : {{ date('d/m/Y') }} | <b>PAST DUE</b></h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>

                    <div class="box-body table-responsive">
                      <table id="table-loan-cust-to" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </thead>
                        <tbody>

                          @foreach($lt_loan_cust_to as $item)

                          <tr style="background-color: red; color: white;">
                            <td>{{date("d/m/Y H:i:s",strtotime($item->createddate))}}</td>
                            <td>{{$item->loanid}}</td>
                            <td>{{$item->userid}} - {{$item->name}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{date("d/m/Y",strtotime($item->duedate))}}</td>
                            <td>{{date("d/m/Y",strtotime($item->modifydate))}}</td>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id_loan_cust) }}" type="button" class="btn btn-success">Pay</a></td>
                            @endif
                          </tr>
                          @endforeach

                        </tbody>
                        <tfoot>
                          <tr>
                            <th>Created Date</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Total</th>
                            <th>Due Date</th>
                            <th>Last Modify Date</th>
                            @if(DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->first()->u==1)
                            <th>Action</th>
                            @endif
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  {{-- loan from customer --}}
                    @endif
                  @endif

              </div>
            </div>
          @endif
        @endif
			</section>
		</div>

    <!-- /.content-wrapper -->
      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; 2017 <a href="#">Devcolin</a>.</strong> All rights
        reserved.
      </footer>
    <!-- ./wrapper -->

	</div>

  <div class="modal fade" id="rejectModalbarang" role="dialog" aria-labelledby="rejectModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#2980b9">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color:white">ITEM TEMPORARY</h4>
            </div>
          <form class="form-horizontal" action="{{ url('temp/fix') }}" method="post">
          <div class="modal-body">
              <div class="modal-body" style="color:black">
                  {{ csrf_field() }}
                  <input type="hidden" id="items_id" name="items_id">
                  <input type="hidden" id="idbarangtemp" name="idbarangtemp">
                  <div class="form-group">
                    <label for="input_return_id" class="col-sm-4 control-label">TEMPORARY</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" name="return_id" id="name_itemp" placeholder="ID Purchase Return" readonly="true">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_return_id" class="col-sm-4 control-label">QTY</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" name="return_id" id="qty_itemp" placeholder="ID Purchase Return" readonly="true">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_return_id" class="col-sm-4 control-label">FIX</label>
                    <div class="col-sm-5">
                      <select style="text-transform:uppercase; width:333px;" required class="js-items-id form-control">
                          <option selected>{{old('items_id')}}  {{old('name')}}</option>
                      </select>
                    </div>
                  </div>
              </div>
            <div class="modal-footer">
              <button style="width:90px;" type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to fix this data ?')">FIX</button>
            </div>
            <input type="hidden" id="type" name="type"/>
          </form>
          </div>
        </div>
    </div>
  </div>

<script>
  // items select2
  $(".js-items-id").select2({
  ajax: {
    url: "{{ url('/selling/selling_home/search_items_selling') }}",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        type: $("#type").val() // search term
      };
    },
    processResults: function (data) {
      // parse the results into the format expected by Select2.
      // since we are using carom formatting functions we do not need to
      // alter the remote JSON data
        return {
          results: data
        };
      },
      cache: true
    },
    minimumInputLength: 2
  });

  $(".js-items-id").on("select2:select", function (e) {
    var obj = $(".js-items-id").select2("data")
    $('#items_id').val(obj[0].id);
  });
  // items select2

  function barang(id){
    $('#rejectModalbarang').modal();
    $.ajax({
          url: "{{url('temp/fix/get')}}" + "/" + id,
          data: {},
          dataType: "json",
          type: "get",
          success:function(data)
          {
            var name_itemp = data[0]["name_itemp"];
            var qty_itemp = data[0]["qty_itemp"];
            var type = data[0]["type"];

                  $('#name_itemp').val(name_itemp);
                  $('#qty_itemp').val(qty_itemp);
                  $('#idbarangtemp').val(id);
                  $('#type').val(type);
          }

      });
  }

  // --------------------------------------------------------------

  $(function () {

    // table debt-------------------------------------------------

    $('#table-debt tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-debt').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Debt | ON GOING',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Debt | ON GOING',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Debt | ON GOING',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Debt | ON GOING',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table debt-------------------------------------------------

    // table debt-------------------------------------------------

    $('#table-debt-to tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-debt-to').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Debt | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Debt | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Debt | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Debt | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table debt-------------------------------------------------

    // table credit-------------------------------------------------

    $('#table-credit tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-credit').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Credit | ON GOING',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Credit | ON GOING',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Credit | ON GOING',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Credit | ON GOING',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table credit-------------------------------------------------

    // table credit-------------------------------------------------

    $('#table-credit-to tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-credit-to').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table credit-------------------------------------------------

    // table temp buying-------------------------------------------------

    $('#table-temp-buying tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-temp-buying').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
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

    // table temp buying-------------------------------------------------

    // table temp selling-------------------------------------------------

    $('#table-temp-selling tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-temp-selling').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Credit | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed two-column',
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

    // table temp selling-------------------------------------------------

    // table loan---------------------------------------------------------

    $('#table-loan-emp tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-loan-emp').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Loan Employee | ON GOING',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Loan Employee | ON GOING',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Loan Employee | ON GOING',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Loan Employee | ON GOING',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table loan---------------------------------------------------------

    // table loan---------------------------------------------------------

    $('#table-loan-emp-to tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-loan-emp-to').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Loan Employee | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Loan Employee | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Loan Employee | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Loan Employee | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table loan---------------------------------------------------------

    // table loan---------------------------------------------------------

    $('#table-loan-supp tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-loan-supp').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Loan Supplier | ON GOING',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Loan Supplier | ON GOING',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Loan Supplier | ON GOING',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Loan Supplier | ON GOING',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table loan---------------------------------------------------------

    // table loan---------------------------------------------------------

    $('#table-loan-supp-to tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-loan-supp-to').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Loan Supplier | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Loan Supplier | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Loan Supplier | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Loan Supplier | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table loan---------------------------------------------------------

    // table loan---------------------------------------------------------

    $('#table-loan-cust tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-loan-cust').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Loan Customer | ON GOING',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Loan Customer | ON GOING',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Loan Customer | ON GOING',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Loan Customer | ON GOING',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table loan---------------------------------------------------------

    // table loan---------------------------------------------------------

    $('#table-loan-cust-to tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#table-loan-cust-to').DataTable({
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
              title: 'TUNAS ABADI 8 | Data Loan Customer | PAST DUE',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              pageSize: 'A4',
              title: 'TUNAS ABADI 8 | Data Loan Customer | PAST DUE',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              pageSize: 'A4',
              orientation: 'landscape',
              title: 'TUNAS ABADI 8 | Data Loan Customer | PAST DUE',
              exportOptions: {
                  columns: ':visible',
              }
          },
          {
            extend: 'print',
            pageSize: 'A4',
            title: 'TUNAS ABADI 8 | Data Loan Customer | PAST DUE',
            exportOptions: {
                columns: ':visible'
            }
          },
          {
            extend: 'colvis',
            collectionLayout: 'fixed three-column',
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

    // table loan---------------------------------------------------------

  });

</script>

@endsection
