'use strict'

const formsConfirm = document.querySelectorAll(".needConfirm");

formsConfirm.forEach(form => {
    form.addEventListener('submit', validateDelete);
})

function validateDelete(event) {
    event.preventDefault();
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    return swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            swalWithBootstrapButtons.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            ).then(() => {
                //Event = évènement -> target = form -> submit = envoyer
                event.target.submit();
            })
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Your ressources is okay!',
                'error'
            )
        }
    })
}


$(document).ready(function () {

    var hash = location.hash.replace(/^#/, '');
    if (hash) {
        $('.nav-tabs a[href="#' + hash + '"]').tab('show');
    }


    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    })
})

//Permet au bouton "modify" qui se situe dans les informations utilisateur de faire en sorte d'avoir l'obligation de faire un clique droit pour y accéder.
const modify = document.getElementById('modify');

modify.addEventListener('click', () => {
    const x = document.getElementById("informations");
    const y = document.getElementById("informations-text");

    x.classList.toggle('d-none');
    y.classList.toggle('d-none');
})



