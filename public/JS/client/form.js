document.addEventListener("DOMContentLoaded", function () {
    const userForm = document.getElementById('userFields');
    const checkbox = document.getElementById('CreateUser');
    const prenomField = document.getElementById('prenom');
    const nomField = document.getElementById('nom');
    const emailField = document.getElementById('email');
    const loginField = document.getElementById('login');
    const passwordField = document.getElementById('password');
    const fileKeyField = document.getElementById('fileKey');
    const form = document.getElementById('clientForm');
    const btnSave = document.getElementById('btn-Save');

    // Validation de formulaire
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append("CreateUser", checkbox.checked);

        if (checkbox.checked) {
            // Ajoutez les champs de l'utilisateur si le checkbox est coché
            formData.append("prenom", prenomField.value);
            formData.append("nom", nomField.value);
            formData.append("email", emailField.value);
            formData.append("login", loginField.value);
            formData.append("password", passwordField.value);
            if (fileKeyField.files.length > 0) {
                formData.append("fileKey", fileKeyField.files[0]);
            }
        }

        try {
            const response = await fetch("/api/clients/store", {
                method: "POST",
                body: formData,
            });

            if (response.ok) {
                alert("Client ajouté avec succès !");
                window.location.href = "http://127.0.0.1:8000/clients";
                form.reset();
                userForm.style.display = 'none';
            } else {
                const errorText = await response.text();
                console.error("Erreur serveur :", errorText);
                alert("Erreur lors de l'ajout du client: " + errorText);
            }
        } catch (error) {
            console.error("Erreur :", error);
            alert("Une erreur s'est produite.");
        }
    });

    function toggleUser(checkbox) {
        if (checkbox.checked) {
            userForm.style.display = 'block'; // Affichez le formulaire utilisateur
            emailField.setAttribute('required', 'required');
            loginField.setAttribute('required', 'required');
            passwordField.setAttribute('required', 'required');
        } else {
            userForm.style.display = 'none'; // Cachez le formulaire utilisateur
            emailField.removeAttribute('required');
            loginField.removeAttribute('required');
            passwordField.removeAttribute('required');
        }
    }

    checkbox.addEventListener('change', () => {
        toggleUser(checkbox);
    });

    toggleUser(checkbox);
});
