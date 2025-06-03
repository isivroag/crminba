<?php
    class conn{
        
        function connect(){
        /*
            define('servidor','bosquedelasanimas.com.mx');
            define('bd_nombre','bosquede_crminba');
            define('usuario','bosquede_sistema');
            define('password','Gpoferche2022#');
*/
             define('servidor','tecniem.com');
            define('bd_nombre','tecniemc_crminba');
            define('usuario','tecniemc_ivan');
            define('password','Obispo66.colima');

            $opciones=array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

            try{
                $conexion=new PDO("mysql:host=".servidor.";dbname=".bd_nombre, usuario,password, $opciones);
                return $conexion;
            }catch(Exception $e){
                return null;
            }
        }
    }
