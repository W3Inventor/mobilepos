document.addEventListener('keydown', function(event) {
    if (event.key === "F11") {
        event.preventDefault(); // Prevent the default F11 behavior
        toggleFullScreen();
    }
});

document.getElementById('fullscreen-toggle').addEventListener('click', function() {
    toggleFullScreen();
});

function toggleFullScreen() {
    if (!document.fullscreenElement) {
        // Enter full-screen mode
        document.documentElement.requestFullscreen().then(() => {
            // Show minimize icon, hide maximize icon
            document.querySelector('.feather-maximize').style.display = 'none';
            document.querySelector('.feather-minimize').style.display = 'inline-block';
        });
    } else {
        // Exit full-screen mode
        if (document.exitFullscreen) {
            document.exitFullscreen().then(() => {
                // Show maximize icon, hide minimize icon
                document.querySelector('.feather-maximize').style.display = 'inline-block';
                document.querySelector('.feather-minimize').style.display = 'none';
            });
        }
    }
}

// Add an event listener to detect when full-screen mode changes
document.addEventListener('fullscreenchange', function() {
    if (!document.fullscreenElement) {
        // If exiting full-screen, show maximize icon and hide minimize icon
        document.querySelector('.feather-maximize').style.display = 'inline-block';
        document.querySelector('.feather-minimize').style.display = 'none';
    }
});
