<?php
// Archivo de compatibilidad MySQL para contenedor Docker PHP 5.6
// Este archivo proporciona funciones mysql_* obsoletas usando mysqli

// Variables globales para conexiones
if (!isset($GLOBALS['mysql_link'])) {
    $GLOBALS['mysql_link'] = null;
}

if (!isset($GLOBALS['mysql_last_connection'])) {
    $GLOBALS['mysql_last_connection'] = null;
}

if (!function_exists('mysql_connect')) {
    function mysql_connect($server, $username, $password) {
        // Forzar conexión TCP en lugar de socket para Docker
        if ($server === 'mysql_qaponline' || $server === 'localhost') {
            $server = 'mysql_qaponline:3306';
        }
        
        $link = mysqli_connect($server, $username, $password);
        if ($link) {
            $GLOBALS['mysql_link'] = $link;
            $GLOBALS['mysql_last_connection'] = $link;
        }
        return $link;
    }
}

if (!function_exists('mysql_select_db')) {
    function mysql_select_db($database, $connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return false;
        }
        return mysqli_select_db($connection, $database);
    }
}

if (!function_exists('mysql_query')) {
    function mysql_query($query, $connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return false;
        }
        return mysqli_query($connection, $query);
    }
}

if (!function_exists('mysql_error')) {
    function mysql_error($connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return "No MySQL connection available";
        }
        return mysqli_error($connection);
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result, $result_type = MYSQLI_BOTH) {
        if (!$result) {
            return false;
        }
        return mysqli_fetch_array($result, $result_type);
    }
}

if (!function_exists('mysql_num_rows')) {
    function mysql_num_rows($result) {
        if (!$result) {
            return 0;
        }
        return mysqli_num_rows($result);
    }
}

if (!function_exists('mysql_close')) {
    function mysql_close($connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return false;
        }
        $result = mysqli_close($connection);
        if ($connection === $GLOBALS['mysql_link']) {
            $GLOBALS['mysql_link'] = null;
        }
        return $result;
    }
}

if (!function_exists('mysql_set_charset')) {
    function mysql_set_charset($charset, $connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return false;
        }
        return mysqli_set_charset($connection, $charset);
    }
}

if (!function_exists('mysql_fetch_assoc')) {
    function mysql_fetch_assoc($result) {
        if (!$result) {
            return false;
        }
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('mysql_insert_id')) {
    function mysql_insert_id($connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return false;
        }
        return mysqli_insert_id($connection);
    }
}

if (!function_exists('mysql_affected_rows')) {
    function mysql_affected_rows($connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return false;
        }
        return mysqli_affected_rows($connection);
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($string, $connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            // Fallback básico si no hay conexión
            return addslashes($string);
        }
        return mysqli_real_escape_string($connection, $string);
    }
}

if (!function_exists('mysql_escape_string')) {
    function mysql_escape_string($string) {
        return mysql_real_escape_string($string);
    }
}

if (!function_exists('mysql_get_client_info')) {
    function mysql_get_client_info() {
        return "MySQL compatibility layer via mysqli";
    }
}

if (!function_exists('mysql_get_server_info')) {
    function mysql_get_server_info($connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return "Unknown";
        }
        return mysqli_get_server_info($connection);
    }
}

if (!function_exists('mysql_errno')) {
    function mysql_errno($connection = null) {
        if ($connection === null) {
            $connection = $GLOBALS['mysql_link'];
        }
        if ($connection === null || $connection === false) {
            return 0;
        }
        return mysqli_errno($connection);
    }
}
?>