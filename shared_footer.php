<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight current tab based on page
        const currentPage = window.location.pathname.split('/').pop();
        if (currentPage === 'manageclass.php') {
            document.getElementById('tab1').checked = true;
        } else if (currentPage === 'addclass.php') {
            document.getElementById('tab2').checked = true;
        } else if (currentPage === 'deleteclass.php') {
            document.getElementById('tab3').checked = true;
        } else if (currentPage === 'managelist.php' || currentPage === 'addstudentpt2.php' || currentPage === 'deletestudentpt2.php') {
            document.getElementById('tab4').checked = true;
        }

        // Tab navigation with loading
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('change', function() {
                if (this.id === 'tab1' && this.checked) {
                    window.location.href = 'manageclass.php';
                } else if (this.id === 'tab2' && this.checked) {
                    window.location.href = 'addclass.php';
                } else if (this.id === 'tab3' && this.checked) {
                    window.location.href = 'deleteclass.php';
                } else if (this.id === 'tab4' && this.checked) {
                    window.location.href = 'managelist.php';
                }
            });
        });
        
        // Add loading page for all navigation from home
        const homeLinks = document.querySelectorAll('a[href*="loading.php"]');
        homeLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.href;
            });
        });

        // Handle back to home navigation
        const homeIcon = document.querySelector('a[href="home.php"]');
        if (homeIcon) {
            homeIcon.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'loading.php?redirect=home.php';
            });
        }
    });
</script>
</body>
</html>