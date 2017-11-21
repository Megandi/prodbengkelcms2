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

      {{-- DASHBOARD --}}

        <li class="{{$dash?"active":""}}">
          <a {{Request::is('dashboard/home')?"style=color:white":""}} href="{{ url('dashboard/home') }}">
            <i class="fa fa-dashboard"></i> <span>DASHBOARD</span>
          </a>
        </li>

      {{-- DASHBOARD END --}}
      
      {{-- MAIN MENU --}}

        <?php $menu_1 = DB::table('level_akses')->where('id_menu', '1')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_1)==1)
        @if($menu_1[0]->r==1)
          <?php $open_1 = Request::is('mainmenu/*');?>
          <li class="treeview {{$open_1?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>MAIN MENU</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_1?"menu-open":""}}" {{$open_1?"style=display:block":""}}>

              <li><a {{Request::is('mainmenu/items_type_home')?"style=color:white":""}} href="{{ url('mainmenu/items_type_home') }}"><i class="fa fa-cubes"></i> Manage Type Items</a></li>
              <li><a {{Request::is('mainmenu/items_home')?"style=color:white":""}} href="{{ url('mainmenu/items_home') }}"><i class="fa fa-th"></i> Manage Items</a></li>
              <li><a {{Request::is('mainmenu/service_home')?"style=color:white":""}} href="{{ url('mainmenu/service_home') }}"><i class="fa fa-wrench"></i> Manage Service</a></li>
              <li><a {{Request::is('mainmenu/supp_home')?"style=color:white":""}} href="{{ url('mainmenu/supp_home') }}"><i class="fa fa-user-plus"></i> Manage Supplier</a></li>
              <li><a {{Request::is('mainmenu/cust_home')?"style=color:white":""}} href="{{ url('mainmenu/cust_home') }}"><i class="fa fa-users"></i> Manage Customer</a></li>
              <li><a {{Request::is('mainmenu/dep_home')?"style=color:white":""}} href="{{ url('mainmenu/dep_home') }}"><i class="fa fa-sitemap"></i> Department List</a></li>
              <li><a {{Request::is('mainmenu/pos_home')?"style=color:white":""}} href="{{ url('mainmenu/pos_home') }}"><i class="fa fa-get-pocket"></i> Position List</a></li>
              <li><a {{Request::is('mainmenu/emp_home')?"style=color:white":""}} href="{{ url('mainmenu/emp_home') }}"><i class="fa fa-user"></i> Manage Employee</a></li>
              <li><a {{Request::is('mainmenu/car_home')?"style=color:white":""}} href="{{ url('mainmenu/car_home') }}"><i class="fa fa-car"></i> Manage Car</a></li>
              <li><a {{Request::is('mainmenu/quar_home')?"style=color:white":""}} href="{{ url('mainmenu/quar_home') }}"><i class="fa fa-industry"></i> Manage Quarry</a></li>
              <li><a {{Request::is('mainmenu/port_home')?"style=color:white":""}} href="{{ url('mainmenu/port_home') }}"><i class="fa fa-anchor"></i> Manage Port</a></li>
              <li><a {{Request::is('mainmenu/solar_type_home')?"style=color:white":""}} href="{{ url('mainmenu/solar_type_home') }}"><i class="fa fa-houzz"></i> Manage Solar</a></li>
              <li><a {{Request::is('mainmenu/route_home')?"style=color:white":""}} href="{{ url('mainmenu/route_home') }}"><i class="fa fa-map-pin"></i> Manage Route</a></li>
              <li><a {{Request::is('mainmenu/tonase_home')?"style=color:white":""}} href="{{ url('mainmenu/tonase_home') }}"><i class="fa fa-sliders"></i> Manage Tonase</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- MAIN MENU END --}}

      {{-- HRD MENU --}}

        <?php $menu_2 = DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_2)==1)
        @if($menu_2[0]->r==1)
          <?php $open_2 = Request::is('hrd/*');?>
          <li class="treeview {{$open_2?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>HRD</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_2?"menu-open":""}}" {{$open_2?"style=display:block":""}}>

              <li><a {{Request::is('hrd/sal_home')?"style=color:white":""}} href="{{ url('hrd/sal_home') }}"><i class="fa fa-money"></i> Manage Salary</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- HRD MENU END --}}

      {{-- BUYING MENU --}}

        <?php $menu_3 = DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_3)==1)
        @if($menu_3[0]->r==1)
          <?php $open_3 = Request::is('buying/*');?>
          <li class="treeview {{$open_3?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>BUYING</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_3?"menu-open":""}}" {{$open_3?"style=display:block":""}}>

              <li><a {{Request::is('buying/buying_home')?"style=color:white":""}} href="{{ url('buying/buying_home') }}"><i class="fa fa-shopping-cart"></i> Manage Buying</a></li>
              <li><a {{Request::is('buying/debt_home')?"style=color:white":""}} href="{{ url('buying/debt_home') }}"><i class="fa fa-balance-scale"></i> Manage Debt</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- BUYING MENU END --}}

      {{-- SELLING MENU --}}

        <?php $menu_4 = DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_4)==1)
        @if($menu_4[0]->r==1)
          <?php $open_4 = Request::is('selling/*');?>
          <li class="treeview {{$open_4?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>SELLING</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_4?"menu-open":""}}" {{$open_4?"style=display:block":""}}>

              <li><a {{Request::is('selling/selling_home')?"style=color:white":""}} href="{{ url('selling/selling_home') }}"><i class="fa fa-shopping-cart"></i> Manage Selling</a></li>
              <li><a {{Request::is('selling/credit_home')?"style=color:white":""}} href="{{ url('selling/credit_home') }}"><i class="fa fa-balance-scale"></i> Manage Credit</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- SELLING MENU END --}}

      {{-- PURCHASE RETURN MENU --}}

        <?php $menu_5 = DB::table('level_akses')->where('id_menu', '5')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_5)==1)
        @if($menu_5[0]->r==1)
          <?php $open_5 = Request::is('purchase/*');?>
          <li class="treeview {{$open_5?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>PURCHASE RETURN</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_5?"menu-open":""}}" {{$open_5?"style=display:block":""}}>

              <li><a {{Request::is('purchase/return_home')?"style=color:white":""}} href="{{ url('purchase/return_home') }}"><i class="fa fa-reply"></i> Return Buying</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- PURCHASE RETURN MENU END --}}

      {{-- SALES RETURN MENU --}}

        <?php $menu_6 = DB::table('level_akses')->where('id_menu', '6')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_6)==1)
        @if($menu_6[0]->r==1)
          <?php $open_6 = Request::is('sales/*');?>
          <li class="treeview {{$open_6?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>SALES RETURN</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_6?"menu-open":""}}" {{$open_6?"style=display:block":""}}>

              <li><a {{Request::is('sales/salreturn_home')?"style=color:white":""}} href="{{ url('sales/salreturn_home') }}"><i class="fa fa-reply"></i> Return Selling</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- SALES RETURN MENU END --}}

      {{-- ADDTIONAL --}}

        <?php $menu_7 = DB::table('level_akses')->where('id_menu', '7')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_7)==1)
        @if($menu_7[0]->r==1)
          <?php $open_7 = Request::is('addtional/*');?>
          <li class="treeview {{$open_7?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>ADDTIONAL</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_7?"menu-open":""}}" {{$open_7?"style=display:block":""}}>

              <li><a {{Request::is('addtional/addtional_home')?"style=color:white":""}} href="{{ url('addtional/addtional_home') }}"><i class="fa fa-list-alt"></i> Additional costs</a></li>
              <li><a {{Request::is('addtional/loan_home')?"style=color:white":""}} href="{{ url('addtional/loan_home') }}"><i class="fa fa-money"></i> Manage Loan</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- ADDTIONAL END --}}

      {{-- HOULING --}}

        <?php $menu_8 = DB::table('level_akses')->where('id_menu', '8')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_8)==1)
        @if($menu_8[0]->r==1)
          <?php $open_8 = Request::is('houling/*');?>
          <li class="treeview {{$open_8?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>HOULING</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_8?"menu-open":""}}" {{$open_8?"style=display:block":""}}>

              <li><a {{Request::is('houling/houling_home')?"style=color:white":""}} href="{{ url('houling/houling_home') }}"><i class="fa fa-truck"></i> Manage Houling</a></li>
              <li><a {{Request::is('houling/solar_home')?"style=color:white":""}} href="{{ url('houling/solar_home') }}"><i class="fa fa-fire"></i> Solar Usage</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- HOULING END --}}

      {{-- ADMIN --}}

        <?php $menu_9 = DB::table('level_akses')->where('id_menu', '9')->where('id_level',auth::user()->level_id)->get(); ?>
        @if(sizeof($menu_9)==1)
        @if($menu_9[0]->r==1)
          <?php $open_9 = Request::is('admin/*');?>
          <li class="treeview {{$open_9?"active":""}}">
            <a href="#">
              <i class="fa fa-folder"></i>
              <span>ADMIN</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu {{$open_9?"menu-open":""}}" {{$open_9?"style=display:block":""}}>

              <li><a {{Request::is('admin/user_home')?"style=color:white":""}} href="{{ url('admin/user_home') }}"><i class="fa fa-user"></i> User List</a></li>
              <li><a {{Request::is('admin/user_pass_home')?"style=color:white":""}} href="{{ url('admin/user_pass_home') }}"><i class="fa fa-key"></i> User Pass</a></li>
              <li><a {{Request::is('admin/level_home')?"style=color:white":""}} href="{{ url('admin/level_home') }}"><i class="fa fa-user-plus"></i> User Level</a></li>
              <li><a {{Request::is('admin/logs_home')?"style=color:white":""}} href="{{ url('admin/logs_home') }}"><i class="fa fa-user"></i> Logs List</a></li>
              <li><a {{Request::is('admin/destroy_home')?"style=color:white":""}} href="{{ url('admin/destroy_home') }}"><i class="fa fa-ban"></i> Destroy Data</a></li>

            </ul>
          </li>
        @endif
        @endif

      {{-- ADMIN END --}}

    </ul>
  </section>
</div>
<!-- /.sidebar -->
</aside>
