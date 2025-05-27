<?php
class Bitacora {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function registrar($modulo, $accion, $registro_afectado = null, $descripcion = '') {
        try {
            // Verificar si la sesión no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Obtener información del usuario
            $id_usuario = $_SESSION['s_id_usuario'] ?? 0;
            $nombre_usuario = $_SESSION['s_nombre'] ?? 'Sistema';
            $rol_usuario = $_SESSION['s_rol'] ?? 'Desconocido';
            $ip = $_SERVER['REMOTE_ADDR'];
            
            // Validar campos obligatorios
            if (empty($modulo) || empty($accion)) {
                throw new Exception("Módulo y acción son requeridos");
            }
            
            // Preparar consulta SQL
            $sql = "INSERT INTO bitacora (
                id_usuario, nombre_usuario, rol_usuario,
                modulo, accion, registro_afectado,
                descripcion, ip_origen, fecha_registro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                $id_usuario,
                $nombre_usuario,
                $rol_usuario,
                $modulo,
                $accion,
                $registro_afectado,
                $descripcion,
                $ip
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Error PDO en Bitácora: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error en Bitácora: " . $e->getMessage());
            return false;
        }
    }
}
?>