<?php
    class Admin extends DatabaseObject{
        static protected $table_name = "admins";
        static protected $database_columns = ['id','first_name', 'last_name', 'email', 'username', 'hashed_password'];

        public $id;
        public $first_name;
        public $last_name;
        public $email;
        public $username;
        protected $hashed_password;
        public $password;
        public $confirm_password;
        protected $password_required = true;

        public function __construct($args = []){
            $this->first_name = $args['first_name'] ?? NULL;
            $this->last_name = $args['last_name'] ?? NULL;
            $this->email = $args['email'] ?? NULL;
            $this->username = $args['username'] ?? NULL;
            $this->password = $args['password'] ?? NULL;
            $this->confirm_password = $args['confirm_password'] ?? NULL;
        }

        public function name(){
            return $this->first_name . ' ' . $this->last_name;
        }

        protected function set_hashed_password(){
            return $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
        }
    
        public function varify_password($password){
            return password_verify($password, $this->hashed_password);
        }

        protected function create(){
            $this->set_hashed_password();
            return parent::create();
        }

        protected function update(){
            if($this->password != ''){
                // validate password
            }else{
                $this->password_required = false;
            }
            $this->set_hashed_password();
            return parent::update();
        }

        protected function validate() {
            $this->errors = [];
            
            if(is_blank($this->first_name)) {
                $this->errors[] = "First name cannot be blank.";
            } elseif (!has_length($this->first_name, array('min' => 2, 'max' => 255))) {
                $this->errors[] = "First name must be between 2 and 255 characters.";
            }
            
            if(is_blank($this->last_name)) {
                $this->errors[] = "Last name cannot be blank.";
            } elseif (!has_length($this->last_name, array('min' => 2, 'max' => 255))) {
                $this->errors[] = "Last name must be between 2 and 255 characters.";
            }
            
            if(is_blank($this->email)) {
                $this->errors[] = "Email cannot be blank.";
            } elseif (!has_length($this->email, array('max' => 255))) {
                $this->errors[] = "Last name must be less than 255 characters.";
            } elseif (!has_valid_email_format($this->email)) {
                $this->errors[] = "Email must be a valid format.";
            }
            
            if(is_blank($this->username)) {
                $this->errors[] = "Username cannot be blank.";
            } elseif (!has_length($this->username, array('min' => 8, 'max' => 255))) {
                $this->errors[] = "Username must be between 8 and 255 characters.";
            }elseif(!has_unique_username($this->username, $this->id ?? 0)){
                $this->errors[] = "Username is not allowed. Try another";
            }

            if($this->password_required){
            
                if(is_blank($this->password)) {
                    $this->errors[] = "Password cannot be blank.";
                } elseif (!has_length($this->password, array('min' => 12))) {
                    $this->errors[] = "Password must contain 12 or more characters";
                } elseif (!preg_match('/[A-Z]/', $this->password)) {
                    $this->errors[] = "Password must contain at least 1 uppercase letter";
                } elseif (!preg_match('/[a-z]/', $this->password)) {
                    $this->errors[] = "Password must contain at least 1 lowercase letter";
                } elseif (!preg_match('/[0-9]/', $this->password)) {
                    $this->errors[] = "Password must contain at least 1 number";
                } elseif (!preg_match('/[^A-Za-z0-9\s]/', $this->password)) {
                    $this->errors[] = "Password must contain at least 1 symbol";
                }
                
                if(is_blank($this->confirm_password)) {
                    $this->errors[] = "Confirm password cannot be blank.";
                } elseif ($this->password !== $this->confirm_password) {
                    $this->errors[] = "Password and confirm password must match.";
                }
            }
            return $this->errors;
        }

        public static function find_by_username($username){
            $sql = "SELECT * from " . static::$table_name . " ";
            $sql .= "WHERE username='" . self::$database->escape_string($username) . "'";
            $array_objects = self::find_by_sql($sql);
            if(!empty($array_objects)){
            return array_shift($array_objects);
            }else{
            return false;
            }
        }
    }
?>