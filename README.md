collaborative-documents
==========================

Herramienta para la elaboración de documentos colaborativos basados en propuestas y votación de la gente

Instalación:
==========================

La aplicación está lista para funcionar.

Hay que crear la base de datos con el script de la carpeta (programa_colaborativo.sql) y subir todo el resto de ficheros al servidor.

Hay que configurar la conexión con la base de datos y personalizar la aplicación como se comenta en el apartado de **Configuración**.

Configuración
==========================

En el fichero vistas/config.html hay que poner:
* entity: la información referida a la entidad que matiene la web: nombre, web, logo, ...
* metainfo: información que se pondrá en las etiquetas <meta de la cabecera HTML (<head>)
* presentationData: Información del título, introducción, etc. para la página principal en los idiomas que se quiera.
* sectors: Información sobre cada uno de los sectores (categorías) en los que quieren clasificar las propuestas. 

En el fichero static/css/main.css definir colores y tipos de letra

En el fichero colpr-config.php definir las variables de configuración del programa: Base de datos, usuario, contraseña, ...
se puede definir a partir de colpr-config.default.php 

En el directorio locale se encuentran los ficheros traducidos. Explicación xgettext i demás. FIXME

La carpeta account contiene un par de programas para dar de alta usuarios con una validación por correo. Solo deja dar de alta
usuarios si aparece su correo en la tabla users_can_participate. Hay que crear, si no están creados, tres enlaces (ln -s) a ficheros o carpetas en el directorio padre: lib, static y colpr-config.php

En lib/functions.php: Comentar o descomentar // ini_set('display_errors', '1'); para mostrar o no en la web los errores.

En vistas/config.html (legalAdvice) y vistas/aviso-legal.html, poner los avisos legales pertinentes y avisar del uso de cookies.

Sectores
------------------------

A cada propuesta se le puede asignar un sector (categoría), hay que editar vistas/config.html y poner cada uno de los sectores que se quiere tener. También hay que definir una imagen por cada sector.

Motor de plantillas Twig
==========================

La aplicación utiliza el motor de plantilla Twig. Por defecto lo busca en una carpeta llamada `twig` dentro de otra llamada `vendor` que ha de estar en la raiz de la carpeta de la aplicación.

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

Créditos
==========================

Elaborada a partir de la Herramienta para la elaboración del programa colaborativo **Castelló en Moviment** CSeMV

Elaborada a partir de la Herramienta para la elaboración del programa colaborativo de **Ganemos Zaragoza** desarrollado por  Guillermo Lázaro (https://guillermolazaro.com/)
