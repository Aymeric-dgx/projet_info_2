<script>
                // Récupération du canvas et configuration du contexte
                const canvas = document.getElementById('stockChart');
                const ctx = canvas.getContext('2d');

                // Fonction pour ajuster la taille du canvas
                function resizeCanvas() {
                    canvas.width = window.innerWidth * 0.7;
                    canvas.height = window.innerHeight * 0.6;
                }

                // Appeler resizeCanvas à chaque redimensionnement de la fenêtre
                window.addEventListener('resize', () => {
                    resizeCanvas();
                    viewTotal(); // Redessiner le graphique après le redimensionnement
                });

                // Initialiser la taille du canvas
                resizeCanvas();

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

                const margin = 50;

                // Fonction pour mapper les prix sur l'axe Y
                function mapPriceToY(value, minPrice, maxPrice) {
                    return canvas.height - margin - ((value - minPrice) / (maxPrice - minPrice)) * (canvas.height - 2 * margin);
                }

                // Fonction pour dessiner les axes
                function drawAxes(minPrice, maxPrice) {
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

                    // Labels Y
                    ctx.fillStyle = 'white';
                    ctx.fillText(maxPrice.toFixed(2), 10, mapPriceToY(maxPrice, minPrice, maxPrice) + 5);
                    ctx.fillText(minPrice.toFixed(2), 10, mapPriceToY(minPrice, minPrice, maxPrice) + 5);
                }

                // Fonction pour dessiner le graphique
                function drawGraph(data) {
                    const minPrice = Math.min(...data.map(p => p.price));
                    const maxPrice = Math.max(...data.map(p => p.price));

                    ctx.clearRect(0, 0, canvas.width, canvas.height); // Effacer le canvas
                    drawAxes(minPrice, maxPrice); // Redessiner les axes

                    for (let i = 1; i < data.length; i++) {
                        const x1 = margin + ((i - 1) * (canvas.width - 2 * margin)) / (data.length - 1);
                        const y1 = mapPriceToY(data[i - 1].price, minPrice, maxPrice);
                        const x2 = margin + (i * (canvas.width - 2 * margin)) / (data.length - 1);
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
