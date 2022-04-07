<?php

class query {

    public static function pdoInsertQuery($query, $array_params, $con) {

        $stmt = $con -> prepare($query);
        
        foreach ($array_params as $key => $value) {
            $stmt -> bindValue($key, $value);
        }

        $stmt -> execute();
    
    }

    public static function pdoSelectQuery($query, $array_params, $con) {

        $stmt = $con -> prepare($query);
        
        foreach ($array_params as $key => $value) {
            $stmt -> bindValue($key, $value);
        }

        $stmt -> execute();
        
        return $stmt -> fetch();
    
    }
    
    public static function pdoSelectAllQuery($query, $array_params, $con) {

        $stmt = $con -> prepare($query);
        
        foreach ($array_params as $key => $value) {
            $stmt -> bindValue($key, $value);
        }

        return $stmt -> execute();
    
    }

    public static function msSqlInsertQuery($query, $con) {
        $stmt = $con -> prepare($query);

        $stmt -> execute();
        
    }

    public static function msSqlSelectQuery($query, $con) {
        $stmt = $con -> prepare($query);

        $stmt -> execute();
        
        return $stmt -> fetch();
    }


}