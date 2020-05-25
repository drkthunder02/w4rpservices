@if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
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
  @endif