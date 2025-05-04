<?php
    class conn{
        
        function connect(){
        
            define('servidor','tecniem.com');
            define('bd_nombre','tecniemc_srmcheca');
            define('usuario','tecniemc_srmcheca');
            define('password','SrmCheca.2024');

            $opciones=array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

            try{
                $conexion=new PDO("mysql:host=".servidor.";dbname=".bd_nombre, usuario,password, $opciones);
                return $conexion;
            }catch(Exception $e){
                return null;
            }
        }
    }
