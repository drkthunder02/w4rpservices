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
    @hasSection('navbar-upper-left')
    @yeild('navbar-upper-left')
    @endif

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
    @hasSection('navbar-upper-right')
    @yeild('navbar-upper-right')
    @endif
  </nav>
  <!-- /.navbar -->