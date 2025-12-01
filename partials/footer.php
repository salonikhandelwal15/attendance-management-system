<style>
    /* Sticky footer */
    footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background: #6c40406c;
    }
    body {
        padding-bottom: 70px; /* Prevent content overlap */
    }
</style>

<footer class="border py-3">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">

        <span class=" text-white small">
            © <?php echo date('Y'); ?> Attendance Management System. All Rights Reserved.
        </span>

        <div class="mt-2 mt-md-0">
            <a href="#" class="text-white small text-decoration-none me-3">Privacy Policy</a>
            <a href="#" class="text-white small text-decoration-none">Terms</a>
        </div>

    </div>
</footer>

<!-- Bootstrap JS -->
<script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Rating Script -->
<script>
function rate(sessionId, stars) {
  const form = document.createElement('form');
  form.method = 'post';
  form.action = '/attendance-management-system/student/rate.php';
  form.innerHTML =
      '<input type="hidden" name="session_id" value="' + sessionId + '">' +
      '<input type="hidden" name="stars" value="' + stars + '">';
  document.body.appendChild(form);
  form.submit();
}
</script>

</body>
</html>
