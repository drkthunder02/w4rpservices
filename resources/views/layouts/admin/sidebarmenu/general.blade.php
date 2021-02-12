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
      <a href="/admin/dashboard/journal" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Wallet Journal</p>
      </a>
    </li>
  </ul>
</li>
@endif
<!-- End General Administrative Stuff -->