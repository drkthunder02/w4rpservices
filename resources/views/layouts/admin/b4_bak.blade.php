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
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      @if(auth()->user()->hasPermission('srp.admin'))
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <!-- Link to the SRP Page with notifications based on how many open SRP requests there are -->
        <a class="nav-link" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
      </li>
      @endif
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
      <span class="brand-text font-weight-light">W4RP Admin Dashboard</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">{{ auth()->user()->getName() }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- General Administrative Stuff -->
          @if(auth()->user()->hasRole('Admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                General<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin/dashboard/users" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Users</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/dashboard/taxes" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Taxes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/dashboard/logins" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Allowed Logins</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/dashboard/wiki" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Wiki</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/dashboard/journal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Wallet Journal</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End General Administrative Stuff -->
          <!-- Flex Structure -->
          @if(auth()->user()->hasRole('Admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Flex Structures<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/flex/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/flex/display/add" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End Flex Structure -->
          <!-- Moon Admin -->
          @if(auth()->user()->hasPermission('moon.admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Moon Admin<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/moons/admin/display/rentals" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Moons</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/moons/admin/updatemoon" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Moon</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/moons/admin/display/request" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Moon Request</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End Moon Admin -->
          <!-- SRP Admin -->
          @if(auth()->user()->hasPermission('srp.admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                SRP Admin<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/srp/admin/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SRP Admin Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/srp/admin/statistics" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SRP Statistics</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/srp/admin/costcodes/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SRP Admin Cost Codes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/srp/admin/display/history" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SRP History</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End SRP Admin -->
          <!-- Contract Admin -->
          @if(auth()->user()->hasPermission('contract.admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Contract Admin<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/contracts/admin/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Admin Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/contracts/admin/new" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>New Contract</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End Contract Admin -->
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
      <h5>Admin Dashboard</h5>
      <p>
          The admin dashboard holds all the functionality for the administrators of the site.  This tab is currently not utilized.
      </p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
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
