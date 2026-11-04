/**
 * =========================================================================
 * S.I.G.S.M. - VALIDACIONES DE FORMULARIO CLIENTE
 * =========================================================================
 * Este script valida los datos ingresados en el cliente antes de enviar
 * los formularios de respuesta a encuestas o campos requeridos, remarcando
 * visualmente los inputs no válidos mediante clases nativas de Bootstrap.
 */

document.addEventListener("DOMContentLoaded", function () {

    // Captura del primer formulario disponible en la vista actual
    const formEncuesta = document.querySelector("form");

    if (formEncuesta) {
        formEncuesta.addEventListener("submit", function (e) {
            let esValido = true;

            // Limpia mensajes de error de ejecuciones previas
            removerAlertas();

            // Validación de elementos desplegables (<select>)
            const selects = formEncuesta.querySelectorAll("select");
            selects.forEach(select => {
                if (select.value.includes("Seleccione") || select.value === "") {
                    mostrarError(select, "Por favor, seleccione una opción válida.");
                    esValido = false;
                }
            });

            // Validación de áreas de texto (<textarea>)
            const textareas = formEncuesta.querySelectorAll("textarea");
            textareas.forEach(textarea => {
                if (textarea.value.trim() === "") {
                    mostrarError(textarea, "Este campo no puede quedar vacío.");
                    esValido = false;
                }
            });

            // Cancela el envío del formulario si no supera las validaciones
            if (!esValido) {
                e.preventDefault();
            }
        });
    }

    /**
     * Resalta el campo con error y genera la alerta visual debajo del elemento.
     * @param {HTMLElement} elemento - Control de formulario que falló la validación.
     * @param {string} mensaje - Texto descriptivo del error para el usuario.
     */
    function mostrarError(elemento, mensaje) {
        elemento.classList.add("is-invalid");
        const divError = document.createElement("div");
        divError.className = "invalid-feedback d-block fw-bold mt-1";
        divError.innerText = mensaje;
        elemento.parentNode.appendChild(divError);
    }

    /**
     * Remueve todas las clases y mensajes de error previos del DOM.
     */
    function removerAlertas() {
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
    }
});