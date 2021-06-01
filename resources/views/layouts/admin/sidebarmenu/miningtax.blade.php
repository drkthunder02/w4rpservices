@if(auth()->user()->hasPermission('mining.officer'))
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cubes"></i>
        <p>Mining Taxes</p>
        <i class="right fas fa-angle-left"></i>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="/miningtax/admin/display/unpaid" class="nav-link">
                <i class="far fa-money-bill-alt nav-icon"></i>
                <p>Unpaid Invoices</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/miningtax/admin/display/paid" class="nav-link">
                <i class="far fa-money-bill-alt nav-icon"></i>
                <p>Paid Invoices</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/miningtax/admin/display/form/operations" class="nav-link">
                <i class="far fa-money-bill-alt nav-icon"></i>
                <p>Mining Operation Form</p>
            </a>
        </li>
    </ul>
</li>
@endif