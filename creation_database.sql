CREATE TABLE action (
    name VARCHAR(255) PRIMARY KEY,
    dividende_part FLOAT,
    dividend_payment_date DATE
 );
 
 CREATE TABLE users (
     email VARCHAR(255) PRIMARY KEY,
     pseudo VARCHAR(255),
     mdp VARCHAR(255),
     solde INT
);

CREATE TABLE global_wallet (
    email_user VARCHAR(255),
    action_name VARCHAR(255),
    quantity INT,
    average_price INT,
    PRIMARY KEY(email_user, action_name),
    FOREIGN KEY (email_user) REFERENCES users(email),
    FOREIGN KEY (action_name) REFERENCES action(name)
);

CREATE TABLE action_history (
    action_name VARCHAR(255),
    date DATE,
    price_on_this_date INT,
    PRIMARY KEY(action_name, date),
    FOREIGN KEY (action_name) REFERENCES action(name)
);

CREATE TABLE news (
    title VARCHAR(255) PRIMARY KEY,
    text TEXT,
    impacted_action_name VARCHAR(255),
    action_variation FLOAT,
    FOREIGN KEY (impacted_action_name) REFERENCES action(name)
);

CREATE TABLE follows (
    follower VARCHAR(255),
    followed VARCHAR(255),
    PRIMARY KEY(follower, followed),
    FOREIGN KEY (follower) REFERENCES users(email),
    FOREIGN KEY (followed) REFERENCES users(email)
);
