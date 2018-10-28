<div class="container-fluid">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/dashboard">W4RP</a>
        <ul class="navbar-nav mr-auto">
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/finances">Finances</a>
            </li>
            @endcan
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moons</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    @can('isAdmin')
                    <a class="dropdown-item" href="/moons/addmoon">Add Moon</a>
                    <a class="dropdown-item" href="/moons/updatemoon">Update Moon</a>
                    @endcan
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wiki</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/wiki/register">Registration</a>
                    <a class="dropdown-item" href="/wiki/changepassword">Change Password</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </nav>
</div>

<div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
          <div class="container">
             <a href="#" class="brand">BRAND</a>
            <ul class="nav pull-right">
              <li><a href="#">Fixed Link</a></li>
            </ul>
            <div class="nav-collapse">
              <ul class="nav">
                <li><a href="#">L1</a></li>
                <li><a href=#">L2</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>