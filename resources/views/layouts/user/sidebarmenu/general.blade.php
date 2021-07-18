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
        <a href="/jumpbridges/fuel" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Jump Bridge Fuel</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="https://buyback.w4rp.space" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Buyback Program</p>
        </a>
      </li>
    </ul>
</li>
@endif