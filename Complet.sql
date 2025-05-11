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
('Apple annonce de nouveaux produits révolutionnaires', 'Apple a dévoilé aujourd''hui une nouvelle gamme de produits qui pourraient changer l''industrie technologique.', 1),
('Microsoft acquiert une startup d''IA prometteuse', 'Microsoft renforce ses capacités en intelligence artificielle avec cette acquisition stratégique.', 2),
('Amazon étend ses services cloud en Europe', 'Amazon Web Services ouvre trois nouveaux centres de données en Europe pour répondre à la demande croissante.', 3),
('Tesla dépasse les attentes avec ses derniers résultats', 'Tesla a annoncé des résultats trimestriels bien supérieurs aux prévisions des analystes.', 4),
('Alphabet lance un nouveau moteur de recherche alimenté par IA', 'Google teste une version révolutionnaire de son moteur de recherche intégrant des capacités avancées d''IA.', 5),
('Meta investit massivement dans le métavers', 'Meta Platforms alloue 10 milliards de dollars supplémentaires au développement de son écosystème métavers.', 6),
('NVIDIA présente ses nouvelles puces pour l''IA', 'NVIDIA a dévoilé une nouvelle génération de processeurs spécialisés dans les calculs d''intelligence artificielle.', 7),
('Netflix augmente ses tarifs dans plusieurs pays', 'Netflix ajuste ses prix pour refléter ses investissements accrus dans le contenu original.', 8),
('PayPal introduit une nouvelle fonctionnalité de paiement cryptographique', 'PayPal permet désormais les paiements en crypto-monnaies pour les marchands partenaires.', 9),
('Adobe révolutionne la création graphique avec son nouvel outil', 'Adobe a lancé un nouvel outil de design alimenté par l''IA qui automatise de nombreuses tâches créatives.', 10),
('Intel fait une percée dans la technologie des semi-conducteurs', 'Intel annonce une avancée majeure dans la miniaturisation des processeurs.', 11),
('Salesforce prévoit une croissance record pour le prochain trimestre', 'Le leader du CRM cloud prévoit une croissance de 20% grâce à ses nouvelles solutions sectorielles.', 12),
('AMD gagne des parts de marché face à Intel', 'AMD continue de progresser sur le marché des processeurs avec ses dernières générations de puces.', 13),
('Coca-Cola lance une nouvelle gamme de boissons saines', 'Coca-Cola diversifie son offre avec des produits à faible teneur en sucre et des ingrédients naturels.', 14),
('Pfizer obtient l''approbation pour un nouveau traitement innovant', 'Pfizer a reçu l''autorisation de mise sur le marché pour son nouveau médicament contre les maladies cardiovasculaires.', 15),
('Apple fait face à des problèmes de chaîne d''approvisionnement', 'Des retards dans la production des nouveaux iPhone pourraient affecter les résultats du prochain trimestre.', 1),
('Microsoft Azure connaît une panne majeure', 'Une interruption de service affecte plusieurs régions, impactant de nombreuses entreprises.', 2),
('Amazon annonce un partenariat stratégique avec un grand détaillant', 'Cet accord pourrait renforcer la position dominante d''Amazon dans le commerce électronique.', 3);
