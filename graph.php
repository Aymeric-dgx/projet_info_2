<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique Boursier avec Zoom</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            color: white;
            text-align: center;
        }
        canvas {
            display: block;
            margin: 20px auto;
            background-color: #222;
        }
        h1 {
            color: #fff;
        }
        .controls {
            margin: 20px 0;
        }
        .controls button {
            margin: 5px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<h1>Graphique Boursier avec Options de Zoom</h1>
<div class="controls">
    <button onclick="viewTotal()">Voir Total</button>
    <button onclick="viewLastFiveYears()">Dernières 5 années</button>
    <button onclick="viewLastYear()">Dernière année</button>
</div>
<canvas id="stockChart" width="800" height="400"></canvas>

<script>
    // Génération des données : prix mensuels sur 10 ans
    const prices = [];
    let price = 100; // Prix initial
    const startYear = 2013; // Début des données
    const months = [];

    for (let i = 0; i < 120; i++) { // 120 mois = 10 ans
        const year = startYear + Math.floor(i / 12);
        const month = i % 12 + 1;
        const dateString = `${year}-${String(month).padStart(2, '0')}-01`;
        months.push(dateString);
        price += (Math.random() - 0.5) * 10; // Variation aléatoire
        prices.push({ date: new Date(dateString), price });
    }

    // Récupération du canvas et configuration du contexte
    const canvas = document.getElementById('stockChart');
    const ctx = canvas.getContext('2d');

    // Dimensions
    const width = canvas.width;
    const height = canvas.height;
    const margin = 50;

    // Fonction pour mapper les prix sur l'axe Y
    function mapPriceToY(value, minPrice, maxPrice) {
        return height - margin - ((value - minPrice) / (maxPrice - minPrice)) * (height - 2 * margin);
    }

    // Fonction pour dessiner les axes
    function drawAxes(minPrice, maxPrice) {
        ctx.strokeStyle = 'white';
        ctx.lineWidth = 1;

        // Axe Y
        ctx.beginPath();
        ctx.moveTo(margin, margin);
        ctx.lineTo(margin, height - margin);
        ctx.stroke();

        // Axe X
        ctx.beginPath();
        ctx.moveTo(margin, height - margin);
        ctx.lineTo(width - margin, height - margin);
        ctx.stroke();

        // Labels Y
        ctx.fillStyle = 'white';
        ctx.fillText(maxPrice.toFixed(2), 10, mapPriceToY(maxPrice, minPrice, maxPrice) + 5);
        ctx.fillText(minPrice.toFixed(2), 10, mapPriceToY(minPrice, minPrice, maxPrice) + 5);
    }

    // Fonction pour dessiner le graphique
    function drawGraph(data) {
        const minPrice = Math.min(...data.map(p => p.price));
        const maxPrice = Math.max(...data.map(p => p.price));

        ctx.clearRect(0, 0, width, height); // Effacer le canvas
        drawAxes(minPrice, maxPrice); // Redessiner les axes

        for (let i = 1; i < data.length; i++) {
            const x1 = margin + ((i - 1) * (width - 2 * margin)) / (data.length - 1);
            const y1 = mapPriceToY(data[i - 1].price, minPrice, maxPrice);
            const x2 = margin + (i * (width - 2 * margin)) / (data.length - 1);
            const y2 = mapPriceToY(data[i].price, minPrice, maxPrice);

            // Ligne verte si montée, rouge si descente
            ctx.strokeStyle = data[i].price > data[i - 1].price ? 'green' : 'red';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
        }
    }

    // Zoom : Voir toutes les données
    function viewTotal() {
        drawGraph(prices);
    }

    // Zoom : Voir les 5 dernières années
    function viewLastFiveYears() {
        const lastFiveYears = prices.filter(p => p.date >= new Date('2018-01-01'));
        drawGraph(lastFiveYears);
    }

    // Zoom : Voir la dernière année
    function viewLastYear() {
        const lastYear = prices.filter(p => p.date >= new Date('2022-01-01'));
        drawGraph(lastYear);
    }

    // Dessiner le graphique initial (Total)
    viewTotal();
</script>
</body>
</html>
