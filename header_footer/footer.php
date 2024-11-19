</main>
<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; TARUMT Cyber Range <?php echo date('Y'); ?></div>
            <div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>
</div>
</div>

<style>
    #layoutSidenav {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
    }

    #layoutSidenav_content {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }

    footer {
        flex-shrink: 0;
    }

    #layoutSidenav .sb-sidenav {
        width: 240px;
        transition: width 0.3s;
    }

    #layoutSidenav .sb-sidenav.collapsed {
        width: 58px;
    }

    #layoutSidenav_content {
        margin-left: 240px;
        transition: margin-left 0.3s;
    }

    #layoutSidenav .sb-sidenav.collapsed+#layoutSidenav_content {
        margin-left: 58px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../js/scripts.js"></script>

<!-- JS files required by DataTables -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script src="../js/datatables-simple-demo.js"></script>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.querySelector('.sb-sidenav');
        sidebar.classList.toggle('collapsed');
    });
</script>
</body>

</html>