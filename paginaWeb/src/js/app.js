// Estos valores de los pasos se deben modificar
let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
})

function iniciarApp() {
    // Aqui se agregan todas las funciones que se van a utilizar al iniciar la app
    paginaAnterior();
    paginaSiguiente();

    mostrarSeccion() 
    botonesPaginador();

}

// Esta función se va a encargar de mostrar los formularios de los productos 
// cuando le des click al botón de ANTERIOR o SIGUIENTE, te mostrará ese formulario que solicitaste
function mostrarSeccion() {
    // Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    // Mostrar la sección con el paso
    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');
}

// Esta función se encarga de mostrar o no los botones de ANTERIOR y SIGUIENTE
// dependiendo la página en la que te encuentres
// Por ejemplo, si estas en la primera página, no te va a aparecer el botón de ANTERIOR
// o si estás en la ultima página, no te va a aparecer el botón de SIGUIENTE
// Sirve para eso básicamente
function botonesPaginador () {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === pasoInicial){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === pasoFinal) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

// Registra el click en el botón de ANTERIOR
// y manda a llamar a botonesPaginador para que muestre los botones correctos y el formulario ANTERIOR
function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', () => {
        if(paso <= pasoInicial) return;
        
        paso--;
        botonesPaginador();
    });
}

// Registra el click en el botón de SIGUIENTE
// y manda a llamar a botonesPaginador para que muestre los botones correctos y el formulario SIGUIENTE
function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', () => {
        if(paso >= pasoFinal) return;
        
        paso++;
        botonesPaginador();
    });
}
