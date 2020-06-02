CREATE DATABASE IF NOT EXISTS api_rest_symfony;
USE api_rest_symfony;


CREATE TABLE users(
    id          int(255) auto_increment not null,
    nombre      varchar(50) not null,
    email       varchar(255) not null,
    password    varchar(255) not null,
    role        varchar(20),
    created_at  datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT  pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE subastas(
    id              int(255) auto_increment not null,
    user_id         int(255) not null,
    titulo          varchar(50) not null,
    precio          int(10) not null,
    descripcion     text,
    fecha_inicio    datetime DEFAULT CURRENT_TIMESTAMP,
    fecha_fin       datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_subastas PRIMARY KEY(id),
    CONSTRAINT fk_subasta_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;

CREATE TABLE lotes(
    id              int(255) auto_increment not null,
    subasta_id      int(255) not null,
    titulo          varchar(50) not null,
    descripcion     text,
    
    CONSTRAINT pk_lotes PRIMARY KEY(id),
    CONSTRAINT fk_lotes_subastas FOREIGN KEY(subasta_id) REFERENCES subastas(id)
)ENGINE=InnoDb;

CREATE TABLE articulos(
    id              int(255) auto_increment not null,
    lote_id         int(255) not null,
    titulo          varchar(50) not null,
    descripcion     text,
    
    CONSTRAINT pk_articulos PRIMARY KEY(id),
    CONSTRAINT fk_articulos_lotes FOREIGN KEY(lote_id) REFERENCES lotes(id)
)ENGINE=InnoDb;

CREATE TABLE pujas(
    id              int(255) auto_increment not null,
    user_id         int(255) not null,
    subasta_id      int(255) not null,
    puja            int(10) not null,
    created_at      datetime DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_pujas PRIMARY KEY(id),
    CONSTRAINT fk_pujas_user FOREIGN KEY(user_id) REFERENCES users(id),
    CONSTRAINT fk_pujas_subasta FOREIGN KEY(subasta_id) REFERENCES subastas(id)

)ENGINE=InnoDb;