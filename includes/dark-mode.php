<!-- Dark Theme CSS -->
<link rel="stylesheet" href="assets/css/dark-theme.css">

<!-- Theme Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply the saved theme
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }
});
</script>