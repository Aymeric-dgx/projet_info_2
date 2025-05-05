console.log('script_search.js chargé');

document.getElementById('search_form').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche le rechargement de la page


    const formData = new FormData(this); // Récupère les données du formulaire

    // Envoie les données au serveur via fetch
    fetch('scripts/treatment_search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Traite la réponse comme du texte
    .then(data => {
        // Affiche les résultats dans le conteneur #result
        document.getElementById('result').innerHTML = data;
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
});

document.getElementById('best_players_button').addEventListener('click', function() {
    const searchInput = document.getElementById('search_input');
    searchInput.value = ''; // Vide la barre de recherche
    this.disabled = false; // Active le bouton si désactivé

    // Envoie une requête pour afficher les meilleurs joueurs
    fetch('scripts/get_best_players.php')
    .then(response => response.text()) // Traite la réponse comme du texte
    .then(data => {
        // Affiche les meilleurs joueurs dans le conteneur #result
        document.getElementById('result').innerHTML = data;
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
});



// Gestion concernant les follow

document.addEventListener('DOMContentLoaded', function () {
    // Ajoute un gestionnaire d'événement sur le document pour intercepter les formulaires dynamiques
    document.addEventListener('submit', function (event) {
        if (event.target && event.target.classList.contains('follow_user')) {
            event.preventDefault(); // Empêche le rechargement de la page

            const formData = new FormData(event.target); // Récupère les données du formulaire

            // Envoie les données au serveur via fetch
            fetch('scripts/add_follow.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Réponse du serveur :', data); // Affiche la réponse du serveur
                alert(data); // Affiche un message à l'utilisateur
            })
            .catch(error => {
                console.error('Erreur lors de la requête :', error);
            });
        }
    });
});