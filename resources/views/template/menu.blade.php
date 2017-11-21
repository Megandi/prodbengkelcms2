<!-- Navbar Right Menu -->
<div class="navbar-custom-menu">
  <ul class="nav navbar-nav">
    <li class="dropdown user user-menu">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">

      <?php if(Auth::user()->photo != '0'){?>
        <img src="{{ url('photo_profile/user.png') }}" class="user-image" alt="User Image">
      <?php }else{?>
        <img src="{{ url('adminlte/dist/img/user.png') }}" class="user-image" alt="User Image">
      <?php }?>

        <span class="hidden-xs">{{ App\Models\manage_karyawan::whereKaryawanId(Auth::user()->karyawan_id)->first()['nama'] }}</span>
      </a>
      <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">

      <?php if(Auth::user()->photo != '0'){?>
        <img src="{{ url('photo_profile/user.png') }}" class="img-circle" alt="User Image">
      <?php }else{?>
        <img src="{{ url('adminlte/dist/img/user.png') }}" class="img-circle" alt="User Image">
      <?php }?>

          <p>
            {{ App\Models\manage_karyawan::whereKaryawanId(Auth::user()->karyawan_id)->first()['nama'] }}
            <small class="font-size:20px;">{{ App\Models\manage_department::whereid(Auth::user()->department_id)->first()['name'] }}</small>
            <small>Joined since {{ date_format(Auth::user()->created_at, "M Y") }}</small>
          </p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
          <div class="pull-left">
            <a href="{{ url('usermanagement/user_profile/'.Auth::user()->id) }}" class="btn btn-default btn-flat">Profile</a>
          </div>
          <div class="pull-right">

            <a class="btn btn-default btn-flat" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>