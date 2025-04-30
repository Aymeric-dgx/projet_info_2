const updatePeriod = 15 * 1000; // 15 secondes (modifiable)


// Fonction pour appeler update.php
async function update_loop() {
    console.log("Appel de update.php...");
    try {
        const response = await fetch("update.php");
        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }
        const data = await response.text();
        console.log("Mise à jour réussie :", data);
    } catch (error) {
        console.error("Erreur lors de l'appel à update.php :", error);
    }
}

// Événement d'installation du service worker
self.addEventListener('install', (event) => {
    console.log("Service Worker installé.");
    self.skipWaiting(); // Force l'activation immédiate
});

// Événement d'activation du service worker
self.addEventListener('activate', (event) => {
    console.log("Service Worker activé.");
    event.waitUntil(clients.claim()); // Prend le contrôle des clients immédiatement

    // Démarre la boucle de mise à jour
    setInterval(update_loop, updatePeriod);
});
