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