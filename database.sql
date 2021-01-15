CREATE DATABASE IF NOT EXISTS api_blog;

USE api_blog;

CREATE TABLE users(
    id INT(255) AUTO_INCREMENT NOT NULL ,
    name VARCHAR(60) NOT NULL,
    surname VARCHAR(60) NOT NULL,
    role VARCHAR(20),
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    remember_token VARCHAR(255),
    CONSTRAINT pk_users PRIMARY KEY (id) 
)ENGINE=InnoDb;

CREATE TABLE categories(
    id INT(255) AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    CONSTRAINT pk_categories PRIMARY KEY (id)
)ENGINE=InnoDb;

CREATE TABLE posts(
    id INT(255) AUTO_INCREMENT NOT NULL,
    user_id INT(255) NOT NULL,
    category_id INT(255) NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    CONSTRAINT pk_posts PRIMARY KEY (id),
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) 
    REFERENCES users(id),
    CONSTRAINT fk_posts_categories FOREIGN KEY (category_id) 
    REFERENCES categories(id)
)ENGINE=InnoDb;


INSERT INTO `users` (`id`, `name`, `surname`, `role`, `email`, `password`, `description`, `image`, `created_at`, `updated_at`, `deleted_at`, `remember_token`) 
VALUES (NULL, 'Jonathan', 'Garcia', 'role_admin', 'admin@admin.com', 'admin', 'Soy el admin', NULL, '2021-01-05 16:07:44', NULL, NULL, NULL);

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`)
VALUES (NULL, 'Informatica', '2021-01-05 16:10:48', NULL, NULL), (NULL, 'Moviles', '2021-01-05 16:10:48', NULL, NULL);

INSERT INTO `posts` (`id`, `user_id`, `category_id`, `titulo`, `content`, `image`, `created_at`, `updated_at`, `deleted_at`) 
VALUES (NULL, '1', '1', 'Nuevos Ryzen 5000', 'Mejor performance en todo momento', NULL, '2021-01-05 16:11:56', NULL, NULL), (NULL, '1', '2', 'Galaxy Fold un fracaso?', 'Todo parece que estos moviles son una caca', NULL, '2021-01-05 16:11:56', NULL, NULL);