document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');

    bell.addEventListener('click', function() {
        // toggle dropdown
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';

        // mark notifications as read via AJAX
        fetch(window.notificationsMarkAsReadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
        }).then(res => res.json())
          .then(data => {
              if(data.success) {
                  // hide the unread badge
                  const badge = bell.querySelector('.badge');
                  if(badge) badge.style.display = 'none';
              }
          });
    });
});
