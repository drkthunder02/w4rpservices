<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-140677389-1"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-140677389-1');
        </script>

        <title>{{  config('app.name', 'W4RP Services') }}</title>

        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="/bower_components/admin-lte/plugins/fontawesome-free/css/all.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="/bower_components/admin-lte/dist/css/adminlte.min.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    </head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/dashboard" class="nav-link">Dashboard</a>
      </li>
      @if(auth()->user()->hasPermission('srp.admin'))
      <li class="nav-item d-none d-sm-inline-block dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SRP Admin</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
            <a class="dropdown-item" href="/srp/admin/display">SRP Admin Dashboard</a>
            <a class="dropdown-item" href="/srp/admin/statistics">SRP Statistics</a>
            <a class="dropdown-item" href="/srp/admin/costcodes/display">SRP Admin Cost Codes</a>
            <a class="dropdown-item" href="/srp/admin/display/history">SRP History</a>
        </div>
      </li>
      @endif
      @if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('contract.admin') || auth()->user()->hasPermission('moon.admin'))
      <li class="nav-item d-non d-sm-inline-block">
        <a class="nav-link" href="/admin/dashboard">Admin Dashboard</a>
      </li>
      @endif
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" href="#">
          <i class="fas fa-fighter-jet"></i>
          <span class="badge badge-warning navbar-badge">{{ auth()->user()->srpOpen() }}</span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" href="#">
            <i class="fas fa-planet-slash"></i>
            <span class="badge badge-danger navbar-badge">{{ auth()->user()->srpDenied() }}</span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" href="#">
            <i class="fas fa-space-shuttle"></i>
            <span class="badge badge-success navbar-badge">{{ auth()->user()->srpApproved() }}</span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" href="/logout">Logout</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i
            class="fas fa-th-large"></i></a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <span class="brand-text font-weight-light">W4RP</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="/profile" class="d-block">{{ auth()->user()->getName() }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- General Items -->
          @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>General<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/profile" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Profile</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/scopes/select" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ESI Scopes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/logistics/fuel/structures" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Jump Gate Fuel</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://buyback.w4rp.space" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Buyback Program</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/wormholes/display" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Wormholes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/wormholes/form" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Wormhole Form</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End General Items -->  
          <!-- Moon Items -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-moon"></i>
              <p>Moons<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
              <li class="nav-item">
                <a href="/moons/display/all" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display All Moons</p>
                </a>
              </li>
              @endif
              <li class="nav-item">
                <a href="/moons/display/rentals" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Rental Moons</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/moons/display/form/worth" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Moon Worth</p>
                </a>
              </li>
              @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
              <li class="nav-item">
                <a href="/moons/display/request" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Moon Reservation</p>
                </a>
              </li>
              @endif
              @if(auth()->user()->hasPermission('corp.lead') && auth()->user()->hasEsiScope('esi-industry.read_corporation_mining.v1'))
              <li class="nav-item">
                <a href="/moons/ledger/display/moons" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Mining Ledger</p>
                </a>
              </li>
              @endif
              @if(auth()->user()->isMoonRenter() || auth()->user()->hasPermission('rentalmoon.viewer'))
              <li class="nav-item">
                <a href="/moons/ledger/display/rentals" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Moon Rental Ledger</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          <!-- End Moon Items -->
          <!-- SRP Items -->
          @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-money-bill-alt"></i>
              <p>SRP<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/srp/form/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SRP Form</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/srp/display/costcodes" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cost Codes</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- SRP Items -->
          <!-- Contracts -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-contract"></i>
              <p>Contracts<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/contracts/display/all" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display All Contracts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/contracts/display/public" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Public Contracts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/contracts/display/private" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Private Contracts</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- End Contracts -->
          <!-- Structures -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-building"></i>
              <p>
                Structures<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(auth()->user()->hasPermission('fc.team'))
              <li class="nav-item">
                <a href="/structures/display/requests" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Requests</p>
                </a>
              </li>
              @endif
              <li class="nav-item">
                <a href="/structures/display/form" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Request Form</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- End SRP Admin -->
          <!-- Blacklist -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon far fa-meh-blank"></i>
                <p>Blacklist<br>
                <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a href="/blacklist/display" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Display</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="/blacklist/display/search" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Search</p>
                  </a>
              </li>
              @if(auth()->user()->hasPermission('blacklist.admin'))
              <li class="nav-item">
                  <a href="/blacklist/display/add" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add To</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="/blacklist/display/remove" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Remove From</p>
                  </a>
              </li>
              @endif
            </ul>
          </li>
          @endif
          <!-- End Blacklist -->
          <!-- Start of Wiki -->
          @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('User') || auth()->user()->hasRole('Renter'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fab fa-wikipedia-w"></i>
                <p>Wiki<br>
                <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a href="https://www.w4rp.space" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Wiki</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="/wiki/register" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Registration</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="/wiki/changepassword" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Change Password</p>
                  </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End of Wiki Stuff -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Included Messages -->
    @include('inc.messages')
    <!-- Main content -->
    <div class="content">
        @yield('content')
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Help</h5>
      <p>
          Go to Discord with any issues and post in the channel called #w4rp-it-needs.  Someone will be along to help you when possible.
      </p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      
    </div>
    <!-- Default to the left -->
    <strong><a href="https://services.w4rp.space">Warped Intentions</a></strong>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="/bower_components/admin-lte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/bower_components/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/bower_components/admin-lte/dist/js/adminlte.min.js"></script>
</body>
</html>
