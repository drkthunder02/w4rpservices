<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    @hasSection('footer-right')
    @yield('footer-right')
    @endif
    <div class="float-right d-none d-sm-inline">
      
    </div>
    @hasSection('footer-center')
    @yield('footer-center')
    @endif
    <!-- Default to the left -->
    <strong><a href="https://services.w4rp.space">Warped Intentions</a></strong>
    @hasSection('footer-left')
    @yeild('footer-left')
    @endif
</footer>