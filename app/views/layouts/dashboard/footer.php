<script>
    // Toggle Sidebar (code is in reverse)
    document.addEventListener('DOMContentLoaded', function() {
        var sidebar = document.getElementById('sidebar');
        var content = document.getElementById('mainContent');
        
        // Ensure the sidebar is collapsed by default on page load
        sidebar.classList.add('collapsed');
        content.classList.add('collapsed');
        
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            // Toggle the sidebar's collapsed state
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });
    });
</script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>