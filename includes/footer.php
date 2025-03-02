    </div>
    <footer class="bg-light text-secondary py-4 mt-5 border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-dark">PHP Forum System</h5>
                    <p class="text-muted">A simple and secure forum system built with PHP, MySQL, and Bootstrap.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted">&copy; <?php echo date('Y'); ?> PHP Forum. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>
</html> 