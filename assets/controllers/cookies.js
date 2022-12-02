'use strict'
import Swal from 'sweetalert2'

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


if(getCookie("cookies")){
    console.log('You accepted the cookies!')
} else {
    console.log('Are you new? Accept the cookies please ... :c')
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-start',
        showConfirmButton: true,
        showCloseButton: true,
    })

    Toast.fire({
        imageUrl: '/images/divers/cookies.png',
        imageWidth: 100,
        imageHeight: 100,
        imageAlt: 'Cookies',
        title: 'To enter properly in this website, you need to accept our cookies.'
    }).then((result) => {
            if(result.isConfirmed){
                document.cookie = "cookies=accepted";
                location.reload();
            }
    })
}