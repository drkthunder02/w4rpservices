@if((auth()->user()->hasRole('User') && auth()->user()->hasPermission('ceo')) || 
    auth()->user()->hasRole('Admin'))
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-contract"></i>
        <p>Finances
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="/finances/card" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Cards</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/finances" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Outlook</p>
            </a>
        </li>
    </ul>
</li>
@endif