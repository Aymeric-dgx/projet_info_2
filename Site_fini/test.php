<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="website icon" href="photo/logo.png">
    <title>Portefeuille</title>
    <script>
        if('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service_worker.js')
        }
    </script>
    <link rel="stylesheet" href="profil_css.css">
    <?php session_start();
    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        // S'il n'est pas connecté, on le redirige vers la page de login
        header("Location: index.php");
        exit();
    }
    ?>
</head>
<body>

    <script>
        function graph(prices, stockchart) {
            // Vérification des données
            if (!prices || prices.length === 0) {
                console.error("Pas de données pour", stockchart);
                return;
            }

            const canvas = document.getElementById(stockchart);
            if (!canvas) {
                console.error("Canvas non trouvé:", stockchart);
                return;
            }

            const ctx = canvas.getContext('2d');
            const margin = 50;

            // Trier par date
            prices.sort((a, b) => new Date(a.registement_date) - new Date(b.registement_date));

            // Extraire les valeurs et dates
            const values = prices.map(p => parseFloat(p.value));
            const dates = prices.map(p => p.registement_date);

            const minValue = Math.min(...values);
            const maxValue = Math.max(...values);

            // Effacer le canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Dessiner les axes
            ctx.strokeStyle = 'white';
            ctx.lineWidth = 1;

            // Axe Y
            ctx.beginPath();
            ctx.moveTo(margin, margin);
            ctx.lineTo(margin, canvas.height - margin);
            ctx.stroke();

            // Axe X
            ctx.beginPath();
            ctx.moveTo(margin, canvas.height - margin);
            ctx.lineTo(canvas.width - margin, canvas.height - margin);
            ctx.stroke();

            // Dessiner la courbe
            ctx.beginPath();
            ctx.strokeStyle = '#4CAF50';
            ctx.lineWidth = 2;

            for (let i = 0; i < values.length; i++) {
                const x = margin + (i * (canvas.width - 2 * margin)) / (values.length - 1);
                const y = canvas.height - margin - ((values[i] - minValue) / (maxValue - minValue)) * (canvas.height - 2 * margin);

                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            }

            ctx.stroke();

            // Ajouter des labels
            ctx.fillStyle = 'white';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';

            // Labels axe X (dates)
            for (let i = 0; i < dates.length; i++) {
                const x = margin + (i * (canvas.width - 2 * margin)) / (dates.length - 1);
                const date = new Date(dates[i]);
                const label = `${date.getMonth()+1}/${date.getFullYear().toString().slice(2)}`;
                ctx.fillText(label, x, canvas.height - margin + 20);
            }

            // Labels axe Y (valeurs)
            ctx.textAlign = 'right';
            ctx.fillText(maxValue.toFixed(2), margin - 5, margin + 10);
            ctx.fillText(minValue.toFixed(2), margin - 5, canvas.height - margin);
        }
    </script>
    <div class="bottom">
        <div class="frame">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $userId = 1;

            try {
                $bdd = new PDO("mysql:host=$servername;dbname=bourse;charset=utf8", $username, $password);
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT 
                                    solde_value AS value, 
                                    registement_date 
                                  FROM solde_history 
                                  WHERE id_user = :user_id 
                                  ORDER BY registement_date DESC 
                                  LIMIT 12";
                $stmt = $bdd->prepare($query);
                $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<script>const prices_solde = " . json_encode($results) . ";</script>";
            } catch (PDOException $e) {
                echo "<p>Erreur : " . $e->getMessage() . "</p>";
            }
            ?>
            <canvas id="stockChartSolde" width="800" height="400"></canvas>
            <script>
                if (typeof prices_solde !== 'undefined') {
                    graph(prices_solde, "stockChartSolde");
                } else {
                    console.error("Données solde non chargées");
                }
            </script>
            <h3>Historique du solde</h3>
        </div>
        <div class="frame">
            <?php
            try {
                $query = "SELECT 
                                    total_value AS value, 
                                    registement_date 
                                  FROM total_value_history 
                                  WHERE id_user = :user_id 
                                  ORDER BY registement_date DESC 
                                  LIMIT 12";
                $stmt = $bdd->prepare($query);
                $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<script>const prices_total_value = " . json_encode($results) . ";</script>";
            } catch (PDOException $e) {
                echo "<p>Erreur : " . $e->getMessage() . "</p>";
            }
            ?>
            <canvas id="stockChartTotalvalue" width="800" height="400"></canvas>
            <script>
                if (typeof prices_total_value !== 'undefined') {
                    graph(prices_total_value, "stockChartTotalvalue");
                } else {
                    console.error("Données valeur totale non chargées");
                }
            </script>
            <h3>Historique de la valeur totale</h3>

</body>
</html>
