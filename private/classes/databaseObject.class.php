<?php
    class DatabaseObject{
        static protected $database;
        static protected $table_name = "";
        static protected $database_columns = [];
        public $errors = [];
        
        public static function set_database($database){
            self::$database = $database;
        }

        public static function find_by_sql($sql){
            $result = self::$database->query($sql);
            if(!$result){
            exit('Database query failed.');
            }

            $array_objects = [];

            while($record = $result->fetch_assoc()){
            $array_objects[] = static::instentiate($record);
            }

            $result->free();

            return $array_objects;
        }

        public static function find_all(){
            $sql = "Select * from " . static::$table_name . " ";
            return static::find_by_sql($sql);
        }

        public static function find_by_id($id){
            $sql = "SELECT * from " . static::$table_name . " ";
            $sql .= "WHERE id='" . self::$database->escape_string($id) . "'";
            $array_objects = self::find_by_sql($sql);
            if(!empty($array_objects)){
            return array_shift($array_objects);
            }else{
            return false;
            }
        }

        protected static function instentiate($record){
            $bike = new static;
            foreach($record as $property => $value){
            if(property_exists($bike, $property)){
                $bike->$property = $value;
            }
            }
            return $bike;
        }

        protected function validate(){
            $this->errors = [];
            if(is_blank($this->brand)){
            $this->errors[] = "Brand can not be empty.";
            }
            if(is_blank($this->model)){
            $this->errors[] = "Model can not be empty.";
            }
            return $this->errors;
        }

        protected function create(){
            $this->validate();
            if(!empty($this->errors)){ return false;}

            $sql = "INSERT INTO bicycles (";
            $sql .= join(', ', array_keys(static::sanitized_attributes()));
            $sql .= ") values ('";
            $sql .= join("', '", array_values(static::sanitized_attributes()));
            $sql .= "');";
            $result = self::$database->query($sql);
            if($result){
            $this->id = self::$database->insert_id;
            }
            return $result;
        }

        protected function update(){
            $this->validate();
            if(!empty($this->errors)){ return false;}

            $attributes = $this->sanitized_attributes();
            $attribute_pairs = [];
            foreach($attributes as $key => $value){
            $attribute_pairs[] = "{$key}='{$value}'";
            }

            $sql = "UPDATE bicycles SET ";
            $sql .= join(", ", $attribute_pairs);
            $sql .= " WHERE id='". self::$database->escape_string($this->id) ."' ";
            $sql .= "LIMIT 1";
            $result = self::$database->query($sql);
            return $result;
        }

        public function save(){
            if(isset($this->id )){
            return $this->update();
            }else{
            return $this->create();
            }
        }

        public function delete(){
            $sql = "DELETE from bicycles ";

            $sql .= " where id=" . self::$database->escape_string($this->id) . " ";

            $sql .= "LIMIT 1";

            $result = self::$database->query($sql);
            return $result;
        }

        public function merge_attributes($args){
            foreach($args as $key => $value){
            if(property_exists($this, $key) && !is_null($value)){
                $this->$key = $value;
            }
            }
        }

        public function attributes(){
            $attributes = [];
            foreach(static::$database_columns as $column){
            if($column == 'id'){ continue; }
            $attributes[$column] = $this->$column;
            }
            return $attributes;
        }

        public function sanitized_attributes(){
            $sanitized = [];
            foreach($this->attributes() as $key => $value){
            $sanitized[$key] = self::$database->escape_string($value);
            }
            return $sanitized;
        }
    }

?>