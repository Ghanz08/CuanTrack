/**
 * Main JavaScript file for CuanTrack
 */

document.addEventListener('DOMContentLoaded', function() {
    // Close alert messages
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 3000);
    });

    // If charts are present, initialize them
    if (document.getElementById('expense-chart')) {
        // This is just a placeholder. You would use a charting library like Chart.js
        console.log('Charts would be initialized here');
        // Example with Chart.js:
        // const ctx = document.getElementById('expense-chart').getContext('2d');
        // new Chart(ctx, { type: 'line', data: {...}, options: {...} });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Mohon isi semua field yang diperlukan');
            }
        });
    });
});
