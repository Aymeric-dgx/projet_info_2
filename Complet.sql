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

/* Dans news*/
INSERT INTO news (title, text, impacted_action_id) VALUES
('Apple annonce de nouveaux produits innovants', 'Apple prévoit de lancer une nouvelle gamme de produits technologiques révolutionnaires d''ici la fin du trimestre, ce qui pourrait booster ses ventes.', 16),
('Microsoft acquiert une startup IA', 'Microsoft a racheté une startup spécialisée en intelligence artificielle pour 2 milliards de dollars, renforçant sa position sur le marché du cloud.', 17),
('Amazon étend ses services cloud en Europe', 'Amazon Web Services ouvrira trois nouveaux data centers en Europe pour répondre à la demande croissante.', 18),
('Tesla dépasse les attentes de production', 'Tesla a produit 30% de véhicules électriques en plus que prévu ce trimestre, malgré les pénuries de composants.', 19),
('Alphabet lance un nouveau moteur de recherche', 'Google teste une version avancée de son moteur de recherche intégrant l''IA générative.', 20),
('Meta investit dans le métavers', 'Meta annonce un investissement de 10 milliards de dollars supplémentaires dans le développement du métavers.', 21),
('NVIDIA révèle de nouvelles puces graphiques', 'Les nouvelles cartes graphiques NVIDIA promettent des performances 2x supérieures aux modèles précédents.', 22),
('Netflix augmente ses tarifs', 'Netflix annonce une augmentation de 15% de ses abonnements pour financer de nouveaux contenus originaux.', 23),
('PayPal introduit le paiement crypto', 'PayPal permettra désormais d''effectuer des paiements en cryptomonnaies chez ses marchands partenaires.', 24),
('Adobe révolutionne la création graphique', 'La nouvelle version d''Adobe Photoshop intègre des outils d''IA pour la création automatique d''images.', 25),
('Intel reprend des parts de marché', 'Intel regagne des parts de marché face à AMD grâce à ses nouveaux processeurs plus performants.', 26),
('Salesforce prévoit une croissance record', 'Salesforce annonce des prévisions de croissance supérieures aux attentes pour le prochain trimestre.', 27),
('AMD lance de nouveaux processeurs', 'AMD dévoile sa nouvelle gamme de processeurs avec une efficacité énergétique améliorée de 40%.', 28),
('Coca-Cola innove avec des boissons santé', 'Coca-Cola lance une nouvelle gamme de boissons à faible teneur en sucre avec des ingrédients naturels.', 29),
('Pfizer développe un nouveau vaccin', 'Pfizer annonce des essais cliniques prometteurs pour un nouveau vaccin contre les infections respiratoires.', 30);
