document.addEventListener('DOMContentLoaded', function() {
    const footer = document.getElementById('footer');

    function checkFooterVisibility() {
        const rect = footer.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        // Check if the footer is within the viewport
        if (rect.top <= windowHeight && rect.bottom >= 0) {
            footer.classList.add('visible');
        } else {
            footer.classList.remove('visible'); // Optional: hide if out of view
        }
    }

    // Check visibility on scroll
    window.addEventListener('scroll', checkFooterVisibility);
    
    // Initial check in case footer is in view on page load
    checkFooterVisibility();
});
