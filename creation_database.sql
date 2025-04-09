CREATE TABLE dividende (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pourcentage FLOAT NOT NULL,
    date_distribution DATE
)

CREATE TABLE action (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    symbole VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
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

CREATE TABLE global_wallet (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user VARCHAR(255),
    id_action VARCHAR(255),
    quantity INT,
    average_price DECIMAL(10,2),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_action) REFERENCES action(id)
);

CREATE TABLE action_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    action_name VARCHAR(255),
    date DATE,
    price_at_this_date FLOAT,
    FOREIGN KEY (action_name) REFERENCES action(id)
);

CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    text TEXT,
    impacted_action_id VARCHAR(255),
    FOREIGN KEY (impacted_action_id) REFERENCES action(id)
);

CREATE TABLE follows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_follower VARCHAR(255),
    id_followed VARCHAR(255),
    FOREIGN KEY (follower) REFERENCES users(email),
    FOREIGN KEY (followed) REFERENCES users(email)
);
