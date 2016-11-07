Aplicación WEB-SPA
========================

Sistema de agendas/sesiones para un centro de cuidado estético. 
Proyecto de ingeniería de software

[Ver demo en heroku](https://web-spa.herokuapp.com/)       

[Ver doxygen en heroku](http://web-spa-doxygen.herokuapp.com/)

[Especificaciones detalladas del proyecto.pdf](https://github.com/Ricardo96r/Web-SPA/blob/master/especificaciones.pdf).


Requerimientos
------------

  * PHP 5.5.9 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements](http://symfony.com/doc/current/reference/requirements.html).

If unsure about meeting these requirements, download the demo application and
browse the `http://localhost:8000/config.php` script to get more detailed
information.

Instalación
------------
Para instalar Web-SPA sigue estos comandos.

```bash
$ git clone https://github.com/Ricardo96r/Web-SPA.git
$ cd Web-SPA/
$ composer install

  # Genera datos aleatorios con lógica para la base de datos:
$ php bin/console doctrine:fixtures:load
```

En cuanto a la base de datos se utiliza SQL-Lite, por lo cual no requiere ninguna instalación o problemas de configuración

Usar
-----

No es necesario tener un servidor web como apache/ngix para poder funcionar la aplicación. 
Solo necesita de PHP

```bash
$ cd Web-SPA/
$ php bin/console server:run
```

This command will start a web server for the Symfony application. Now you can
access the application in your browser at <http://localhost:8000>. You can
stop the built-in web server by pressing `Ctrl + C` while you're in the
terminal.

> **NOTE**
>
> If you want to use a fully-featured web server (like Nginx or Apache) to run
> Symfony Demo application, configure it to point at the `web/` directory of the project.
> For more details, see:
> http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html

Otro
---------------
Se puede crear la primera cuenta con el comando.

```bash
$ cd Web-SPA/
$ php bin/console app:add-user
```

> **NOTA**                                                                             
>                                                                                      
> Si usaste el generador de datos para la base de datos. 
> Por default la cuenta administrador es:
> usuario: admin,
> contraseña: password
