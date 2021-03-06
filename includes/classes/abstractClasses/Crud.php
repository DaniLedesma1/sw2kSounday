<?php
namespace es\ucm\fdi\aw\classes\abstractClasses;
use es\ucm\fdi\aw\Application as Application;

     abstract class Crud {
        private $table;
        private $connection;
        private $wheres = "";
         private $join = "";
        private $sql = null;
        private $insert;
        private $update;

        public function __construct($table = null,$propertiesStatic) {
            $singleton = Application::getSingleton();
            $this->connection = $singleton->connect();
            $this->table = $table;
            if($propertiesStatic !== null) {
                $this->insert = $this->CRUDinsert($propertiesStatic);
                $this->update = $this->CRUDupdate($propertiesStatic);
            }
        }

        private function CRUDinsert($propertiesStatic){
            $arrayKeys = implode(", ", array_keys($propertiesStatic)); 
            $arrayValues = ":" . implode(", :", array_keys($propertiesStatic));
            return "INSERT INTO {$this->table} ({$arrayKeys}) VALUES ({$arrayValues})";
        }
        
        private function CRUDupdate($propertiesStatic){
           $Keys = "";
           foreach ($propertiesStatic as $key => $value) {
               $Keys .= "$key=:$key,"; 
           }
           $Keys = rtrim($Keys, ",");
           return "UPDATE {$this->table} SET {$Keys}";
       }

        public function get() {
            try {
                $this->sql = "SELECT {$this->table}.* FROM {$this->table} {$this->join} {$this->wheres}";
                $query = $this->runCRUD();
                return $query->fetchAll();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }


         public function insert($object) {
             try {

                 if($this->insert === null){
                    $this->insert = $this->CRUDinsert($object);
                 }
                 $this->sql = $this->insert;
                 $this->runCRUD($object);
                 return $this->connection->lastInsertId();
             } catch (Exception $exc) {
                 echo $exc->getTraceAsString();
             }
         }



         public function update($object) {
              try {
                if($this->update === null){
                    $this->update = $this->CRUDupdate($object);
                 }
                 $this->sql =  $this->update . "{$this->wheres}";
                 var_dump( $this->sql );
                 $this->runCRUD($object);
             } catch (Exception $exc) {
                 echo $exc->getTraceAsString();
             }
         }

        public function delete() {
            try {
                $this->sql = "DELETE FROM {$this->table} {$this->wheres}";
                $this->runCRUD();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        public function where($key, $condition, $value) {
            $this->wheres .= (strpos($this->wheres, "WHERE")) ? " AND " : " WHERE ";
            $this->wheres .= "$key $condition " . ((is_string($value)) ? "\"$value\"" : $value) . " ";
            return $this;
        }

         public function join($keyF, $Table, $keyS) {
            $this->join .=  " INNER JOIN $Table ON $keyF = $keyS ";
            return $this;
        }

         public function orWhere($key, $condition, $value) {
             $this->wheres .= (strpos($this->wheres, "WHERE")) ? " OR " : " WHERE ";
             $this->wheres .= "$key $condition " . ((is_string($value)) ? "\"$value\"" : $value) . " ";
             return $this;
         }

        private function runCRUD($object = null) {
            $query = $this->connection->prepare($this->sql);
            if ($object !== null) { 
                foreach ($object as $key => $value) {
                    $query->bindValue(":$key", $value);
                }
            }
            $query->execute();
            $this->wheres = "";
            $this->join = "";
            $this->sql = null;
            return $query;
        }

}