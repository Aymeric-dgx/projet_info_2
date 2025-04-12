<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service_worker.js')
                .then(() => console.log("Service worker enregistré !"))
                .catch(e => console.error("Erreur service worker :", e));
}
    </script>
</head>
<body>
    <a href="2eme_page.html">Deuxieme page</a>
</body>
</html>
