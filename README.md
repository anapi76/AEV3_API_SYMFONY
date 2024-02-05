# DWES → AEV3 - API REST CON SYMFONY.

## Descripción:

Esta actividad evaluable consiste en crear una API que sirva para que una aplicación de cliente pueda conectarse al servidor del BAR JUAN 2.0, pero en este caso con el framework de symfony. 
El objetivo es que en el Bar JUAN puedan usar la aplicación 2.0 tanto para las comandas como para la generación de tickets, así como y gestión de pedidos a los proveedores. 

## Recursos generales:

Presentaciones y videos 
#### • Temas 1, 2, 3, 4, 5, 6 , 7 y 8
Material de apoyo:
#### • Todo lo visto en el curso hasta ahora.
## Recursos adicionales:
#### • No se puede usar el script creación BB.DD. barjuan.sql en formato SQL que se ha entregado junto con los recursos de la actividad. Es únicamente como guía a la hora de aplicar el modelado

## Actividades:
Para ello se pide:
### 1. Generar dos contenedores de docker de symfony desde los cuales debemos trabajar. 
#### 1.1. Un contenedor tendrá PHP 8.1 o superior con un servidor apache, que evidentemente tendrá que contener el symfony-cli. 
#### 1.2. El otro contenedor tendrá una base de datos de mysql o mariadb que será la BB.DD. que utilizaremos para almacenar todos los datos. La estructura de la BB.DD. debe crearse mediante migraciones con symfony.     
### 2. En esta versión toda la aplicación está concebida para trabajar con el cliente que tienen disponible ya en sus tabletas. Por lo que, no es necesario implementar en nuestro servidor sistema de renderizado de los datos salvo el necesario para generar los JSON. 
### 3. Para esta versión 2.0 del servidor del Bar Juan, nos piden que creemos una API que controle todas las acciones siguientes: 
#### 3.1. Crear una ruta a partir de la cual podamos crear un nuevo pedido.
###### 3.1.1. La ruta será: /pedido 
###### 3.1.2. Únicamente podrá usarse el método POST para su gestión.
###### 3.1.3. Recibiremos junto a la ruta un JSON con todos los datos del pedido y las líneas de pedido que contenga el mismo.  
###### 3.1.4. Tendremos que devolver un mensaje en JSON indicando si se ha insertado o no.
#### 3.2. Crear una ruta a partir de la cual podamos gestionar íntegramente un proveedor dependiendo del método de envío realizado.  
###### 3.2.1. La ruta será: /proveedor 
###### 3.2.2. GET -> Nos permitirá visualizar los datos de un proveedor a partir de su id. Si no se entrega id debe devolver todos los proveedores. 
###### 3.2.3. POST -> Nos permite dar de alta un nuevo proveedor a partir de los datos recibidos por JSON.
###### 3.2.4. PUT -> Actualizará los datos de un proveedor a partir de los datos recibidos por JSON. No se recibe ninguna ID, deberemos hacerlo por su nombre.  
###### 3.2.5. PATCH -> Actualizará los datos de un proveedor a partir de los datos recibidos por JSON, en este caso se recibe además la id del proveedor. 
###### 3.2.6. DELETE -> Eliminará los datos de un proveedor, si el proveedor tuviera pedidos activos, en vez de hacerlo nos devolverá un mensaje indicando que el proveedor no puede ser borrado porque ya posee pedidos activos. 
#### 3.3. Crear una ruta para la gestión de los productos.
###### 3.3.1. La ruta será: /productos.
###### 3.3.2. Tendremos un método para mostrar todos los productos, que serán devueltos en formato JSON.
###### 3.3.3. Mediante otro método devolveremos el producto a partir de su nombre o id.
###### 3.3.4. Y finalmente poseeremos otro método que nos permitirá insertar un nuevo producto o actualizarlo si este ya existe en el sistema. 
#### 3.4. Crear una ruta para la gestión del stock.
###### 3.4.1. La ruta será: /stock. 
###### 3.4.2. En este caso tendremos un método que nos devolverá todo el histórico de movimientos en stock de un producto. 
###### 3.4.3. Tendremos otro método que nos devolverá el stock a una fecha y hora dada. Que nos han de pasar por JSON. (De igual forma que lo hacíamos en la AEV2) 
###### 3.4.4. Y tendremos otra ruta para hacer inventarios: /inventarios. En esta ruta nos tiene que devolver un json con todos los datos del producto y el stock en el instante que se solicita.
### 4.	Respecto a las comandas, no ha variado lo que ya existía en la versión inicial y por lo tanto se pedirá hacer lo mismo: 
#### 4.1. La gestión de comandas a cocina será a partir de la creación de las comandas por parte de los camareros, que se grabarán en la tabla comandas y lineascomandas. Será el cliente quien cree un formulario para la inserción de la comanda y por lo tanto, nosotros recibiremos mediante una petición POST con los siguientes datos remitidos en una estructura JSON asociada. 
#### 4.2. De igual forma, es posible que alguna mesa decida modificar la comanda, por lo que recibiremos por PUT a la misma ruta que teníamos en el punto anterior, de nuevo la estructura JSON asociada y con el mismo formato, de forma que tendremos que actualizar la comanda actual. 
#### 4.3. En ambas opciones deberemos devolver la id de la comanda y en la respuesta el código de estado correspondiente: 201 si se ha creado o actualizado correctamente ,400 si algún dato no se puede procesar adecuadamente y 500 si no se ha podido procesar la petición por algún error del servidor.  
### 5. Cada vez que desde cocina saquen una línea de la comanda, su terminal de cliente se encarga de enviar una actualización al servidor, con la línea de la comanda y la petición PATCH a una ruta exclusiva para este tipo de actualización, por ejemplo: /entregadaLineaComanda, 
#### 5.1. Al mismo tiempo que actualizamos la línea de la comanda, deberemos hacer una nueva entrada en la tabla de stock, en la que deberemos buscar cual es el último stock de ese producto y restarle la cantidad de la línea de la comanda. 
#### 5.2. Si al entregar la línea de la comanda, estuvieran todas las líneas de la comanda como entregadas, actualizaremos el estado de la comanda también. 
#### 5.3.	Deberemos devolver una respuesta con el código de estado correspondiente: 201 si se ha creado o actualizado correctamente ,400 si algún dato no se puede procesar adecuadamente y 500 si no se ha podido procesar la petición por algún error del servidor.
### 6. Cada camarero tiene en su terminar de cliente la opción de generar ticket. Para ello, deberá siempre de estar la comanda en estado de cerrada. 
#### 6.1. Para poder generar el ticket, lo primero que tendremos que hacer es comprobar el estado de la comanda, de forma que, si la comanda aún esta activa, no se podrá generar. 
#### 6.2. Una vez confirmado que se puede hacer el ticket, mediante la id de la comanda deberemos generar un ticket. En nuestro servidor generar un ticket equivale únicamente a insertar en la tabla tickets un nuevo registro con los datos de la comanda. Es importante, deberemos calcular el importe total del ticket, que será igual al precio por la cantidad de cada producto de las líneas de la comanda. 
#### 6.3. Una vez generado el ticket, devolveremos al cliente una estructura JSON que tendrá que contener todos los datos necesarios para que el cliente pueda sacar impreso el ticket. Se entrega un ejemplo de formato de esa estructura y en la respuesta el código de estado correspondiente: 201 si se ha creado o actualizado correctamente ,400 si algún dato no se puede procesar adecuadamente y 500 si no se ha podido procesar la petición por algún error del servidor. 


## License

This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.

<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a>


## Credits

Authors: Ana Piqueras Jiménez([](https://github.com/)) 
