window.addEventListener('alert', (event) => {

console.log(event)

    Swal.fire({
        allowOutsideClick: false,
        title: event.detail[0].title,
        text: event.detail[0].message,
        icon: event.detail[0].type,
    });
});
