<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div id="dismiss">
            <i class="fas fa-arrow-left"></i>
        </div>
        <div class="sidebar-header">
            <h3>W4RP</h3>
        </div>
        <ul class="list-unstyled components">
            <p>Admin Dashboard</p>
            @if(auth()->user()->hasRole('Admin'))
            <li class="active">
                <a href="#adminSubmenu" data-toggle="collapse" aria-expanded="false">General</a>
                <ul class="collapse list-unstyled" id="adminSubmenu">
                    <li>
                        <a href="/admin/dashboard/users">Users</a>
                    </li>
                    <li>
                        <a href="/admin/dashboard/taxes">Taxes</a>
                    </li>
                    <li>
                        <a href="/admin/dashboard/logins">Allowed Logins</a>
                    </li>
                    <li>
                        <a href="/admin/dashboard/wiki">Wiki</a>
                    </li>
                    <li>
                        <a href="/admin/dashboard/journal">Wallet Journal</a>
                    </li>
                </ul>
            </li>
            <li class="active">
                <a href="#flexSubmenu" data-toggle="collapse" aria-expanded="false">Flex</a>
                <ul class="collapse list-unstyled" id="flexSubmenu">
                    <li>
                        <a href="/flex/display">Display</a>
                    </li>
                    <li>
                        <a href="/flex/display/add">Add</a>
                    </li>
                </ul>
            </li>
            @endif
            @if(auth()->user()->hasPermission('moon.admin'))
            <li class="active">
                <a href="#moonSubmenu" data-toggle="collapse" aria-expanded="false">Moons</a>
                <ul class="collapse list-unstyled" id="moonSubmenu">
                    <li>
                        <a href="/moons/admin/display/rentals">Display Moons</a>
                    </li>
                    <li>
                        <a href="/moons/admin/updatemoon">Update Moon</a>
                    </li>
                    <li>
                        <a href="/moons/admin/display/request">Moon Request</a>
                    </li>
                </ul>
            </li>
            @endif
            @if(auth()->user()->hasPermission('srp.admin'))
            <li class="active">
                <a href="#srpSubmenu" data-toggle="collapse" aria-expanded="false">SRP</a>
                <ul class="collapse list-unstyled" id="srpSubmenu">
                    <li>
                        <a href="/srp/admin/display">SRP Admin Dashboard</a>
                    </li>
                    <li>
                        <a href="/srp/admin/statistics">SRP Statistics</a>
                    </li>
                    <li>
                        <a href="/srp/admin/costcodes/display">SRP Admin Cost Codes</a>
                    </li>
                    <li>
                        <a href="/srp/admin/display/history">SRP History</a>
                    </li>
                </ul>
            </li>
            @endif
            @if(auth()->user()->hasPermission('contract.admin'))
            <li class="active">
                <a href="#contractSubmenu" data-toggle="collapse" aria-expanded="false">Contracts</a>
                <ul class="collapse list-unstyled" id="contractSubmenu">
                    <li>
                        <a href="/contracts/admin/display">Contract Admin</a>
                    </li>
                    <li>
                        <a href="/contracts/admin/new">New Contract</a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </nav>

    <!-- Page Content -->
</div>