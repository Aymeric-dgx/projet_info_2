/*Pour mettre dans dividende*/
INSERT INTO dividende (pourcentage, date_distribution) VALUES
(2.5, '2025-06-15'),
(3.0, '2025-07-20'),
(1.8, '2025-08-10'),
(4.2, '2025-09-05'),
(3.5, '2025-10-12');

/*Pour mettre dans action*/
INSERT INTO action (nom, symbole, description, price, original_price, id_dividende) VALUES
('Apple Inc.', 'AAPL', 'Entreprise technologique spécialisée dans les produits électroniques, les logiciels et les services en ligne.', 175.50, 150.00, 2),
('Microsoft Corporation', 'MSFT', 'Leader mondial des logiciels, services et solutions technologiques.', 310.20, 280.75, 1),
('Amazon.com Inc.', 'AMZN', 'Commerce électronique, cloud computing, streaming et intelligence artificielle.', 120.75, 110.25, 3),
('Tesla Inc.', 'TSLA', 'Constructeur automobile spécialisé dans les véhicules électriques et énergies propres.', 650.30, 700.50, 4),
('Alphabet Inc.', 'GOOGL', 'Société mère de Google, spécialisée dans les services internet et la publicité en ligne.', 135.40, 125.80, 5),
('Meta Platforms Inc.', 'META', 'Réseaux sociaux et technologies de réalité virtuelle (Facebook, Instagram, WhatsApp).', 180.25, 160.00, 2),
('NVIDIA Corporation', 'NVDA', 'Fabricant de processeurs graphiques et solutions d''IA.', 220.60, 200.30, 1),
('Netflix Inc.', 'NFLX', 'Service de streaming de films et séries en ligne.', 300.45, 280.90, 3),
('PayPal Holdings Inc.', 'PYPL', 'Plateforme de paiement en ligne et transfert d''argent.', 95.80, 85.50, 4),
('Adobe Inc.', 'ADBE', 'Logiciels créatifs et solutions marketing digitales.', 510.20, 490.75, 5),
('Intel Corporation', 'INTC', 'Fabricant de semi-conducteurs et processeurs informatiques.', 42.30, 45.00, 2),
('Salesforce.com Inc.', 'CRM', 'Logiciels de gestion de la relation client (CRM) en cloud.', 160.75, 155.25, 1),
('Advanced Micro Devices', 'AMD', 'Fabricant de microprocesseurs et cartes graphiques.', 85.90, 80.40, 3),
('Coca-Cola Company', 'KO', 'Fabricant et distributeur de boissons non-alcoolisées.', 55.60, 50.25, 4),
('Pfizer Inc.', 'PFE', 'Entreprise pharmaceutique mondiale.', 40.35, 38.80, 5);
