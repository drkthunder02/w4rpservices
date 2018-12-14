<div class="container-fluid">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/dashboard">W4RP</a>
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moons</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    @can('isGuest')
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    @endcan
                    @can('isUser')
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    <a class="dropdown-item" href="/moons/display/worth">Moon Worth</a>
                    @endcan
                    @can('isAdmin')
                    <a class="dropdown-item" href="/moons/admin/display">Display Moons</a>
                    <a class="dropdown-item" href="/moons/display/worth">Moon Worth</a>
                    <a class="dropdown-item" href="/moons/admin/addmoon">Add Moon</a>
                    <a class="dropdown-item" href="/moons/admin/updatemoon">Update Moon</a>
                    <a class="dropdown-item" href="/moons/admin/journal">Journal</a>
                    @endcan
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wiki</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="https://www.w4rp.space">Wiki</a>
                    <a class="dropdown-item" href="/wiki/register">Registration</a>
                    <a class="dropdown-item" href="/wiki/changepassword">Change Password</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fleet</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/fleets/display">Display</a>
                    <a class="dropdown-item" href="/fleets/register">Register</a>
                </div>
            </li>
            @if(auth()->user()->hasPermission('logistics.minion') && 0)
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Logistics</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/logistics/contracts/available">Available Contracts</a>
                    <a class="dropdown-item" href="/logistics/contracts/completed">Completed Contracts</a>
                    <a class="dropdown-item" href="/logistics/insurance/request">Insurance Request</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('structure.operator'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Structures</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/structures/taxes/display">Current Taxes</a>
                    <a class="dropdown-item" href="/structures/register">Register Structure</a>
                    @can('isAdmin')
                    <a class="dropdown-item" href="/structures/admin/taxes/display">Corp Taxes</a>
                    @endcan
                </div>
            </li>
            @endif
        </ul>
        <ul class="navbar-nav m1-auto">
            <li class="nav-item">
                <a class="nav-link" href="/scopes/select">Add Esi Scopes</a>
            </li>
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/admin/dashboard">Admin</a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </nav>
</div>