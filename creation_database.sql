-- Pour incrémenter les mois à chaque cycle de 2 min : SET current_date = DATE_ADD(current_date, INTERVAL 1 MONTH);
-- Poir récupérer le mois sous forme de int : SELECT MONTH(date_inscription) FROM ...

CREATE TABLE global_date (
    id INT AUTO_INCREMENT PRIMARY KEY,
    global_date DATE  -- Date globale du jeu, dont on devra incrémenter le mois à chaque "tour"
 );
   
CREATE TABLE dividende (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pourcentage FLOAT NOT NULL,
    date_distribution INT -- val : 1 à 12. On comparera avec SELECT MONTH(...) FROM ... poue savoir si c'est le mois de versement ou non
);

CREATE TABLE action (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    symbole VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,    /* Prix actuel */
    original_price DECIMAL(10,2) NOT NULL,    /* Prix d'origine, à "t=0" */
    id_dividende INT,
    FOREIGN KEY (id_dividende) REFERENCES dividende(id)
 );
 
 CREATE TABLE utilisateur (
     id INT PRIMARY KEY AUTO_INCREMENT,
     pseudo VARCHAR(255) UNIQUE NOT NULL,
     email VARCHAR(255) UNIQUE NOT NULL,
     mdp VARCHAR(255) NOT NULL,
     solde FLOAT DEFAULT 10000.00,
     date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE solde_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    solde_value INT,
    registement_date DATE,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id)
);


CREATE TABLE global_wallet (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT,
    id_action INT,
    quantity INT,
    average_price DECIMAL(10,2),
    FOREIGN KEY (id_user) REFERENCES utilisateur(id),
    FOREIGN KEY (id_action) REFERENCES action(id)
);

CREATE TABLE action_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    action_id INT,
    date DATE,
    price_at_this_date DECIMAL(10,2),
    FOREIGN KEY (action_id) REFERENCES action(id)
);

CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    text TEXT,
    impacted_action_id INT,
    FOREIGN KEY (impacted_action_id) REFERENCES action(id)
);

CREATE TABLE follows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_follower INT,
    id_followed INT,
    FOREIGN KEY (id_follower) REFERENCES utilisateur(id),
    FOREIGN KEY (id_followed) REFERENCES utilisateur(id)
);

CREATE TABLE total_value_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT,
    total_value INT,
    registement_date DATE,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id)
);
