<?php
    // требуется для декодирования JWT 
    include "JWT/JWT.php";
    use \Firebase\JWT\JWT;

    class Token{
    
        // переменные, используемые для JWT 
        private $key = "5ea727a301bc6";
        private $iss = "http://any-site.org";
        private $aud = "http://any-site.com";
        private $iat = 1356999524;
        private $nbf = 1357000000;

        public function __construct()
        {
            // установить часовой пояс по умолчанию 
            date_default_timezone_set('Europe/Moscow');

        }

        public function decode($jwt){
            // если JWT не пуст 
            if($jwt) {
            
                // если декодирование выполнено успешно, показать данные пользователя 
                try {
                    // декодирование jwt 
                    $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            
                    return $decoded;
            
                }
            
                // если декодирование не удалось, это означает, что JWT является недействительным 
                catch (Exception $e){
                
                    // сообщить пользователю отказано в доступе и показать сообщение об ошибке 
                    return array(
                        "message" => "Доступ закрыт.",
                        "error" => $e->getMessage()
                    );
                }
            }
            
            // показать сообщение об ошибке, если jwt пуст 
            else{
            
                // сообщить пользователю что доступ запрещен 
                echo json_encode(array("message" => "Доступ запрещён."));
            }
        }

        public function encode($data){
            $token = array(
                "iss" => $this->iss,
                "aud" => $this->aud,
                "iat" => $this->iat,
                "nbf" => $this->nbf,
                "data" => $data
             );
            
            try {
                // декодирование jwt 
                $token = JWT::encode($data, $this->key);
        
                return $token;
        
            }
        
            // если декодирование не удалось, это означает, что JWT является недействительным 
            catch (Exception $e){
            
                // сообщить пользователю отказано в доступе и показать сообщение об ошибке 
                return json_encode(array(
                    "message" => "Ошибка создания токена.",
                    "error" => $e->getMessage()
                ));
            }
        }
    }

    
?>