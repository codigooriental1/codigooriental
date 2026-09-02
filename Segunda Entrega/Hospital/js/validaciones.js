document.addEventListener("DOMContentLoaded", function () {

    const formEncuesta = document.querySelector("form");

    if (formEncuesta) {
        formEncuesta.addEventListener("submit", function (e) {
            let esValido = true;

            removerAlertas();

            const selects = formEncuesta.querySelectorAll("select");
            selects.forEach(select => {
                if (select.value.includes("Seleccione") || select.value === "") {
                    mostrarError(select, "Por favor, seleccione una opción válida.");
                    esValido = false;
                }
            });

            const textareas = formEncuesta.querySelectorAll("textarea");
            textareas.forEach(textarea => {
                if (textarea.value.trim() === "") {
                    mostrarError(textarea, "Este campo no puede quedar vacío.");
                    esValido = false;
                }
            });

            if (!esValido) {
                e.preventDefault();
            }
        });
    }

    function mostrarError(elemento, mensaje) {
        elemento.classList.add("is-invalid");
        const divError = document.createElement("div");
        divError.className = "invalid-feedback d-block fw-bold mt-1";
        divError.innerText = mensaje;
        elemento.parentNode.appendChild(divError);
    }

    function removerAlertas() {
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
    }
});