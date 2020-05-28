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