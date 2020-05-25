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
  @endif