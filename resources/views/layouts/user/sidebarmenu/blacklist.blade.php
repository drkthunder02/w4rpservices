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