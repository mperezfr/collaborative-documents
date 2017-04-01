presupestos-participativos
==========================

Herramienta para la elaboración de un programa colaborativo basado en propuestas y votación de la gente

Elaborada a partir de la Herramienta para la elaboración del programa colaborativo **Castelló en Moviment** CSeMV

Elaborada a partir de la Herramienta para la elaboración del programa colaborativo de **Ganemos Zaragoza** desarrollado por  Guillermo Lázaro (https://guillermolazaro.com/)


Instalación:
==========================

La aplicación está lista para funcionar.
Crea la base de datos con el script de la carpeta DB y sube todo el resto al servidor. 
Cambia todas las conexiones de la base de datos por las tuyas y personaliza las imágenes y textos.

Cambios adaptación CSeMV
==========================

Adaptado para ser multilingüe
Corregidos algunos bugs
Otros cambios menores (o no tanto).
Algunas mejoras

Configuración
==========================

En el fichero vistas/config.html hay que poner:
* entity: la información referida a la entidad que matiene la web: nombre, web, logo, ...
* metainfo: información que se pondrá en las etiquetas <meta de la cabecera HTML (<head>)

En el fichero static/css/main.css definir colores y tipos de letra

En el fichero colpr-config.php definir las variables de configuración del programa: Base de datos, usuario, contraseña, ...
se puede definir a partir de colpr-config.default.php 

En el directorio locale se encuentran los ficheros traducidos. Explicación xgettext i demás. FIXME

La carpeta account contiene un par de programas para dar de alta usuarios con una validación por correo. Solo deja dar de alta
usuarios si aparece su correo en la tabla users_can_participate. Hay que crear tres enlace (ln -s) a ficheros o carpetas en el directorio
padre: lib, static y colpr-config.php

En lib/functions.php: Comentar o descomentar // ini_set('display_errors', '1'); para mostrar o no en la web los errores.

En vistas/aviso-legal.html, poner los avisos legales pertinentes y avisa del uso de cookies.

Sectores
------------------------

A cada propuesta se le puede asignar un sector (categoría), hay que editar vistas/nueva-propuesta.html y poner cada uno de los sectores que se quiere tener. 
En index.html, también hay que definir una imagen y un enlace por cada sector

FIXME: He intentado poner en un vector los sectores y mediante un bucle ponerlos todos en vistas/nueva-propuesta.html e index.html
pero el problema es que twig no deja poner una variable ({{variable}}) dentro de {% trans %}: {% trans %}{{variable}}{% endtrans %}

Base de datos
==========================

Crear la base de datos en mysql

Entra en mysql (mysql -u root -p):

CREATE DATABASE basededatos;
use basededatos;

Crear el usuario y darle permisos:

CREATE USER 'username'@'localhost' IDENTIFIED BY 'newpass';
GRANT ALL PRIVILEGES ON database.* TO 'username'@'localhost';

Crear las tablas, las órdenes están en el fichero programa_colaborativo.sql. En mysql ejecutar:

\. programa_colaborativo.sql

Tablas
----------------------------

* prog_propuestas: Propuestas
* prog_comentarios: Comentarios de las enmiendas
* prog_enmiendas: Comentarios de primer nivel de las propuestas
* prog_likes_comentarios: Almacena likes y dislikes de los comentarios
* prog_likes_enmiendas: Almacena likes y dislikes de las enmiendas
* prog_likes_propuesta: Almacena votos a favor y en contra de las propuestas
* users: Almacena los usuarios que tienen acceso a la aplicación
* users_can_participate: Almacena los usuarios que se pueden registrar en la web para hacer propuestas y votarlas
