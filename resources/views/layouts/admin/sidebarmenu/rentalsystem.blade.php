<!-- System Rentals -->
@if(auth()->user()->hasRole('Admin'))
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            System Rental<br>
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="/system/rental/dashboard" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Display</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/system/rental/add" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Add</p>
            </a>
        </li>
    </u>
</li>
@endif
<!-- End System Rentals -->