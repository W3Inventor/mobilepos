function showNotification(type, message) {
    Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: function(e){
            e.addEventListener("mouseenter", Swal.stopTimer);
            e.addEventListener("mouseleave", Swal.resumeTimer);
        }
    }).fire({
        icon: type, // "success", "error", "warning", "info"
        title: message
    });
}

