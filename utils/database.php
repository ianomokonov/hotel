<?php
    //класс базы данных
    class DataBase {
        private $dbname = "olympics";
        private $login = "root";
        private $password = "";
        public $db;
        public function __construct()
        {
            $this->db = new PDO("mysql:host=localhost;dbname=".$this->dbname.";charset=UTF8", $this->login, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }

        public function genInsertQuery($ins, $t){
            $res = array('INSERT INTO '.$t.' (',array());
            $q = '';
            for ($i = 0; $i < count(array_keys((array)$ins)); $i++) {
                $res[0] = $res[0].array_keys((array)$ins)[$i].',';
                $res[1][]=$ins[array_keys((array)$ins)[$i]];
                $q=$q.'?,';
                
            }
            $res[0]=rtrim($res[0],',');
            $res[0]=$res[0].') VALUES ('.rtrim($q,',').');';
            
            return $res;
            
        }
        
        public function genUpdateQuery($keys, $values, $t, $id){
            $res = array('UPDATE '.$t.' SET ',array());
            for ($i = 0; $i < count($keys); $i++) {
                $res[0] = $res[0].$keys[$i].'=?, ';
                $res[1][]=$values[$i];
                
                
            }
            $res[0]=rtrim($res[0],', ');
            $res[0]=$res[0].' WHERE Id = '.$id;
            
            return $res;
            
        }
    }
?>