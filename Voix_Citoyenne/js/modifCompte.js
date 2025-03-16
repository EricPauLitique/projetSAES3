function togglePasswordSection() {
    var passwordSection = document.getElementById('password-section');
    var changePasswordField = document.getElementById('change_password');
    if (passwordSection.style.display === 'none') {
        passwordSection.style.display = 'block';
        changePasswordField.value = '1';
    } else {
        passwordSection.style.display = 'none';
        changePasswordField.value = '0';
    }
}

function togglePasswordVisibility(id) {
    var passwordField = document.getElementById(id);
    var eyeIcon = document.getElementById(id + '-eye');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.src = '../images/eye-open.png'; // Chemin vers l'icône d'œil ouvert
    } else {
        passwordField.type = 'password';
        eyeIcon.src = '../images/eye-closed.png'; // Chemin vers l'icône d'œil fermé
    }
}

async function handleSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    console.log("Données envoyées : ", data); // Ajoutez ce message de débogage

    const response = await fetch('../api.php?endpoint=modifcompte', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    if (result.status === 'success') {
        alert(result.message);
        window.location.href = 'accueil.php';
    } else {
        document.getElementById('error-message').innerText = result.message;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('modify-account-form').addEventListener('submit', handleSubmit);
});