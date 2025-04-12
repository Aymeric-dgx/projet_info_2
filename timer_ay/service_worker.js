let timer;

const updatePeriod = 2 * 60 * 1000; // 2 minutes


async function update_loop() {
  fetch("update.php");
}
  
  

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(clients.claim());
  setInterval(update_loop, updatePeriod); // Démarre la boucle au moment de l’activation
});
