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
    <li class="nav-item">
      <a href="/contracts/admin/past" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Past Contracts</p>
      </a>
    </li>
  </ul>
</li>
@endif
<!-- End Contract Admin -->