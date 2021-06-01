<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
        @hasSection('footer-right')  
        @yeild('footer-right')
        @endif
    </div>
    @hasSection('footer-center')
    @yeild('footer-center')
    @endif
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    @hasSection('footer-left')
    @yeild('footer-left')
    @endif
  </footer>