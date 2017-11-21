<aside class="main-sidebar">
<!-- sidebar: style can be found in sidebar.less -->
<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 343px;">
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">

        @if(Auth::user()->photo != '0')
          <img src="{{ url('photo_profile/user.png') }}" class="user-image" alt="User Image">
        @else
          <img src="{{ url('adminlte/dist/img/user.png') }}" class="user-image" alt="User Image">
        @endif

      </div>
      <div class="pull-left info">
        <p>{{ App\Models\manage_karyawan::whereKaryawanId(Auth::user()->karyawan_id)->first()['nama'] }}</p>
        <a><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">MENU</li>
      <?php $dash = Request::is('dashboard/home');?>
      <li class="{{$dash?"active":""}}">
        <a {{Request::is('dashboard/home')?"style=color:white":""}} href="{{ url('dashboard/home') }}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      
      <?php $menu_1 = DB::table('level_akses')->where('id_menu', '1')->where('id_level',auth::user()->level_id)->get(); ?>
      @if(sizeof($menu_1)==1)
      @if($menu_1[0]->r==1)
        <?php $open_1 = Request::is('hrd/*');?>
        <li class="treeview {{$open_1?"active":""}}">
          <a href="#">
            <i class="fa fa-folder"></i>
            <span>HRD</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu {{$open_1?"menu-open":""}}" {{$open_1?"style=display:block":""}}>
            <li><a {{Request::is('hrd/emp_home')?"style=color:white":""}} href="{{ url('hrd/emp_home') }}"><i class="fa fa-user"></i> Manage Employee</a></li>
            <li><a {{Request::is('hrd/sal_home')?"style=color:white":""}} href="{{ url('hrd/sal_home') }}"><i class="fa fa-money"></i> Manage Salary</a></li>
          </ul>
        </li>
      @endif
      @endif

      <?php $menu_2 = DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->get(); ?>
      @if(sizeof($menu_2)==1)
      @if($menu_2[0]->r==1)
        <?php $open_2 = Request::is('operational/*'); ?>
      <li class="treeview {{$open_2?"active":""}}">
        <a href="#">
          <i class="fa fa-folder"></i>
          <span>Operational</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu {{$open_2?"menu-open":""}}" {{$open_2?"style=display:block":""}}>
          <li><a {{Request::is('operational/items_type_home')?"style=color:white":""}} href="{{ url('operational/items_type_home') }}"><i class="fa fa-cubes"></i> Manage Type Items</a></li>
          <li><a {{Request::is('operational/items_home')?"style=color:white":""}} href="{{ url('operational/items_home') }}"><i class="fa fa-th"></i> Manage Items</a></li>
          <li><a {{Request::is('operational/service_home')?"style=color:white":""}} href="{{ url('operational/service_home') }}"><i class="fa fa-wrench"></i> Manage Service</a></li>
          <li><a {{Request::is('operational/supp_home')?"style=color:white":""}} href="{{ url('operational/supp_home') }}"><i class="fa fa-user-plus"></i> Manage Supplier</a></li>
          <li><a {{Request::is('operational/cust_home')?"style=color:white":""}} href="{{ url('operational/cust_home') }}"><i class="fa fa-users"></i> Manage Customer</a></li>
          <li><a {{Request::is('operational/car_home')?"style=color:white":""}} href="{{ url('operational/car_home') }}"><i class="fa fa-car"></i> Manage Car</a></li>
          <li><a {{Request::is('operational/quar_home')?"style=color:white":""}} href="{{ url('operational/quar_home') }}"><i class="fa fa-industry"></i> Manage Quarry</a></li>
          <li><a {{Request::is('operational/port_home')?"style=color:white":""}} href="{{ url('operational/port_home') }}"><i class="fa fa-anchor"></i> Manage Port</a></li>
          <li><a {{Request::is('operational/solar_type_home')?"style=color:white":""}} href="{{ url('operational/solar_type_home') }}"><i class="fa fa-houzz"></i> Manage Solar</a></li>
          <li><a {{Request::is('operational/solar_home')?"style=color:white":""}} href="{{ url('operational/solar_home') }}"><i class="fa fa-fire"></i> Solar Usage</a></li>
          <li><a {{Request::is('operational/route_home')?"style=color:white":""}} href="{{ url('operational/route_home') }}"><i class="fa fa-map-pin"></i> Manage Route</a></li>
          <li><a {{Request::is('operational/tonase_home')?"style=color:white":""}} href="{{ url('operational/tonase_home') }}"><i class="fa fa-sliders"></i> Manage Tonase</a></li>
          <li><a {{Request::is('operational/houling_home')?"style=color:white":""}} href="{{ url('operational/houling_home') }}"><i class="fa fa-truck"></i> Houling</a></li>
        </ul>
      </li>
      @endif
      @endif
      <?php $menu_3 = DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->get(); ?>
      @if(sizeof($menu_3)==1)
      @if($menu_3[0]->r==1)
      <?php $open_3 = Request::is('finance/*'); ?>
      <li class="treeview {{$open_3?"active":""}}">
        <a href="#">
          <i class="fa fa-folder"></i>
          <span>Finance</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu {{$open_3?"menu-open":""}}" {{$open_3?"style=display:block":""}}>
          <li><a {{Request::is('finance/buying_home')?"style=color:white":""}} href="{{ url('finance/buying_home') }}"><i class="fa fa-shopping-cart"></i> Manage Buying</a></li>
          <li><a {{Request::is('finance/debt_home')?"style=color:white":""}} href="{{ url('finance/debt_home') }}"><i class="fa fa-balance-scale"></i> Manage Debt</a></li>
          <li><a {{Request::is('finance/return_home')?"style=color:white":""}} href="{{ url('finance/return_home') }}"><i class="fa fa-reply"></i> Purchase return</a></li>
          <li><a {{Request::is('finance/selling_home')?"style=color:white":""}} href="{{ url('finance/selling_home') }}"><i class="fa fa-shopping-cart"></i> Manage Selling</a></li>
          <li><a {{Request::is('finance/credit_home')?"style=color:white":""}} href="{{ url('finance/credit_home') }}"><i class="fa fa-balance-scale"></i> Manage Credit</a></li>
          <li><a {{Request::is('finance/salreturn_home')?"style=color:white":""}} href="{{ url('finance/salreturn_home') }}"><i class="fa fa-reply"></i> Sales return</a></li>
          <li><a {{Request::is('finance/addtional_home')?"style=color:white":""}} href="{{ url('finance/addtional_home') }}"><i class="fa fa-list-alt"></i> Additional costs</a></li>
          <li><a {{Request::is('finance/loan_home')?"style=color:white":""}} href="{{ url('finance/loan_home') }}"><i class="fa fa-money"></i> Manage Loan</a></li>
        </ul>
      </li>
      @endif
      @endif
      <?php $menu_4 = DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->get(); ?>
      @if(sizeof($menu_4)==1)
      @if($menu_4[0]->r==1)
      <?php $open_4 = Request::is('usermanagement/*'); ?>
      <li class="treeview {{$open_4?"active":""}}">
        <a href="#">
          <i class="fa fa-folder"></i>
          <span>Management User</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu {{$open_4?"menu-open":""}}" {{$open_4?"style=display:block":""}}>
          <li><a {{Request::is('usermanagement/user_home')?"style=color:white":""}} href="{{ url('usermanagement/user_home') }}"><i class="fa fa-user"></i> User List</a></li>
          <li><a {{Request::is('usermanagement/user_pass_home')?"style=color:white":""}} href="{{ url('usermanagement/user_pass_home') }}"><i class="fa fa-key"></i> User Pass</a></li>
          <li><a {{Request::is('usermanagement/level_home')?"style=color:white":""}} href="{{ url('usermanagement/level_home') }}"><i class="fa fa-user-plus"></i> User Level</a></li>
          <li><a {{Request::is('usermanagement/dep_home')?"style=color:white":""}} href="{{ url('usermanagement/dep_home') }}"><i class="fa fa-group"></i> Department List</a></li>
          <li><a {{Request::is('usermanagement/pos_home')?"style=color:white":""}} href="{{ url('usermanagement/pos_home') }}"><i class="fa fa-get-pocket"></i> Position List</a></li>
        </ul>
      </li>
      @endif
      @endif
      <?php $menu_5 = DB::table('level_akses')->where('id_menu', '5')->where('id_level',auth::user()->level_id)->get(); ?>
      @if(sizeof($menu_5)==1)
      @if($menu_5[0]->r==1)
      <?php $open_5 = Request::is('developers/*'); ?>
      <li class="treeview {{$open_5?"active":""}}">
        <a href="#">
          <i class="fa fa-folder"></i>
          <span>Developer</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu {{$open_5?"menu-open":""}}" {{$open_5?"style=display:block":""}}>
          <li><a {{Request::is('developers/logs_home')?"style=color:white":""}} href="{{ url('developers/logs_home') }}"><i class="fa fa-user"></i> Logs List</a></li>
          <li><a {{Request::is('developers/destroy_home')?"style=color:white":""}} href="{{ url('developers/destroy_home') }}"><i class="fa fa-ban"></i> Destroy Data</a></li>
        </ul>
      </li>
      @endif
      @endif
    </ul>
  </section>
</div>
<!-- /.sidebar -->
</aside>
