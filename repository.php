<?php
    //обработка запросов
    include_once './utils/token.php';
    include_once './utils/database.php';
    include_once './utils/filesUpload.php';
    include_once 'models.php';
    class BookingRepository{
        private $database;
        private $token;
        private $filesUpload;
        private $baseUrl = 'http://localhost/hotel';

        public function __construct()
        {
            $this->database = new DataBase();
            $this->token = new Token();
            $this->filesUpload = new FilesUpload();
        }

        public function GetRooms($query){
            
            $queryText = "SELECT * FROM room WHERE ";
            if(isset($query['dateFrom']) && isset($query['dateTo']) && !!($dateFrom = $query['dateFrom']) && $dateTo = $query['dateTo']){
                $queryText = $queryText."0 = (SELECT COUNT(*) FROM roomOrder co WHERE co.roomId = room.id ) OR 0 = (SELECT COUNT(*) FROM roomOrder co WHERE co.dateFrom = '$dateFrom' OR co.dateTo = '$dateTo' OR co.dateFrom > '$dateFrom' AND co.dateTo < '$dateTo' OR co.dateFrom < '$dateFrom' AND co.dateTo > '$dateTo' OR co.dateFrom > '$dateFrom' AND co.dateFrom < '$dateTo' AND co.dateTo > '$dateTo' OR co.dateTo > '$dateFrom' AND co.dateTo < '$dateTo' AND co.dateFrom < '$dateFrom') AND";
            }
            if(!isset($query['dateTo']) && isset($query['dateFrom']) && $dateFrom = $query['dateFrom']){
                $queryText = $queryText."0 = (SELECT COUNT(*) FROM roomOrder co WHERE co.roomId = room.id ) OR 0 = (SELECT COUNT(*) FROM roomOrder co WHERE co.dateFrom = '$dateFrom' OR co.dateTo = '$dateFrom' OR co.dateFrom < '$dateFrom' AND co.dateTo > '$dateFrom') AND";
            }
            if(isset($query['count']) && $count = $query['count']){
                $queryText = $queryText." roomCount >= $count";
            }
            $queryText = rtrim($queryText,'AND');
            $queryText = rtrim($queryText,'WHERE ');
            // return $queryText;
            $query = $this->database->db->query($queryText);
            $query->setFetchMode(PDO::FETCH_CLASS, 'Room');
            
            return $query->fetchAll();
            
        }

        public function UploadRoomImg($file){
            $newFileName = $this->filesUpload->upload($file, 'Files', uniqid());
            return $this->baseUrl.'/Files'.'/'.$newFileName;
        }

        public function GetRoomDetails($roomId){
            if($roomId == null){
                return array("message" => "Введите id автомобиля", "method" => "GetRoomDetails", "requestData" => $roomId);
            }

            $query = $this->database->db->prepare("SELECT * from room WHERE id = ?");
            $query->execute(array($roomId));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Room');
            
            return $query->fetch();
            
        }

        public function GetRoomDates($roomId, $orderId = null){
            if($roomId == null){
                return array("message" => "Введите id автомобиля", "method" => "GetRoomDates", "requestData" => $roomId);
            }

            $str = "SELECT id, dateFrom, dateTo from roomOrder WHERE dateFrom > now() AND roomId = ? AND status IN (1,2)";
            if($orderId){
                $str = $str." AND id != $orderId";
            }

            $query = $this->database->db->prepare($str);
            $query->execute(array($roomId));
            $query->setFetchMode(PDO::FETCH_CLASS, 'DateRange');
            return $query->fetchAll();
            
        }

        public function GetHistory($userId, $isAdmin){
            if($userId == null){
                return array("message" => "Введите id пользователя", "method" => "GetHistory", "requestData" => $userId);
            }
            $text = "SELECT * from roomOrder WHERE userId = ? ORDER BY status ASC, dateFrom DESC";
            if($isAdmin){
                $text = "SELECT * from roomOrder ORDER BY status ASC, dateFrom DESC";
            }
            $query = $this->database->db->prepare($text);
            $query->execute(array($userId));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Order');
            $orders = [];
            while ($order = $query->fetch()) {
                $order->room = $this->GetRoomDetails($order->roomId);
                $order->room->dates = $this->GetRoomDates($order->roomId, $order->id);
                if($isAdmin){
                    $order->user = $this->getUserInfo($order->userId);
                }
                unset($order->userId);
                $orders[] = $order;
            }
            return $orders;
            
        }

        public function AddOrder($userId, $order){
            if($userId == null){
                return array("message" => "Вы не вошли", "method" => "AddOrder", "requestData" => array("userId" => $userId, "order" => $order));
            }
            if($order == null){
                return array("message" => "Заказ пуст", "method" => "AddOrder", "requestData" => array("userId" => $userId, "order" => $order));
            }
            $order->userId = $userId;
            $insert = $this->database->genInsertQuery((array)$order, 'roomOrder');
            $query = $this->database->db->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            return $this->database->db->lastInsertId();
            
        }

        public function UpdateOrder($order){
            if(!$order || !isset($order->id) || !$order->id){
                return array("message" => "Укажите id заказа", "method" => "UpdateOrder", "requestData" => $order);
            }

            $orderId = $order->id;
            unset($order->id);
            $a = $this->database->genUpdateQuery(array_keys((array)$order), array_values((array)$order), "roomOrder", $orderId);
            $query = $this->database->db->prepare($a[0]);
            $query->execute($a[1]);
            return array('message' => 'Заказ обновлен');
        }

        public function CancelOrder($orderId){
            if(!$orderId){
                return array("message" => "Укажите id заказа", "method" => "CancelOrder", "requestData" => $orderId);
            }
            $query = $this->database->db->prepare("UPDATE roomOrder SET status=3 WHERE id=?");
            $query->execute(array($orderId));
            return array('message' => 'Заказ отменен');
        }

        public function SignIn($user = null){
            if($user != null){
                $sth = $this->database->db->prepare("SELECT id, password, isAdmin FROM user WHERE email = ? LIMIT 1");
                $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
                $sth->execute(array($user->email));
                $fullUser = $sth->fetch();
                
                if($fullUser){
                    if(!password_verify($user->password, $fullUser->password)){
                        return false;
                    }
                    return $this->token->encode(array("id" => $fullUser->id, "isAdmin" => $fullUser->isAdmin));
                } else {
                    return false;
                }
                
            } else {
                return array("message" => "Введите данные для регистрации");
            }
        }

        public function getUserInfo($userId){
            $sth = $this->database->db->prepare("SELECT name, surname, middlename, phone, email FROM user WHERE id = ? LIMIT 1");
            $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
            $sth->execute(array($userId));
            return $sth->fetch();
        }

        public function UpdateUserInfo($userId, $user){
            if(!$userId){
                return array("message" => "Укажите id пользователя", "method" => "UpdateUserInfo", "requestData" => array($userId, $user));
            }
            if(!$user){
                return array("message" => "Укажите данные", "method" => "UpdateUserInfo", "requestData" => $user);
            }
            $a = $this->database->genUpdateQuery(array_keys((array)$user), array_values((array)$user), "user", $userId);
            $query = $this->database->db->prepare($a[0]);
            $query->execute($a[1]);
            return array('message' => 'Пользователь обновлен');
        }

        public function SignUp($user = null){
            if($user != null){
                try{
                    if($this->EmailExists($user->email)){
                        return false;
                    }
                    $user->password = password_hash($user->password, PASSWORD_BCRYPT);
                    $insert = $this->database->genInsertQuery((array)$user, 'user');
                    $query = $this->database->db->prepare($insert[0]);
                    if ($insert[1][0]!=null) {
                        $query->execute($insert[1]);
                    }
                    return $this->token->encode(array("id" => $this->database->db->lastInsertId()));
                } catch(Exception $e) {
                    return false;
                }
                
            } else {
                return false;
            }
        }

        public function AddRoom($room = null){
            if($room != null){
                try{
                    $insert = $this->database->genInsertQuery((array)$room, 'room');
                    $query = $this->database->db->prepare($insert[0]);
                    if ($insert[1][0]!=null) {
                        $query->execute($insert[1]);
                    }
                    return $this->database->db->lastInsertId();
                } catch(Exception $e) {
                    return array("message" => "Ошибка добавления автомобиля", "error" => $e->getMessage());
                }
                
            } else {
                return array("message" => "Введите данные автомобиля");
            }
        }

        private function EmailExists(string $email){
            $query = "SELECT id, email FROM user WHERE email = ?";
 
            // подготовка запроса 
            $stmt = $this->database->db->prepare( $query );
            // инъекция 
            $email=htmlspecialchars(strip_tags($email));
            // выполняем запрос 
            $stmt->execute(array($email));
        
            // получаем количество строк 
            $num = $stmt->rowCount();

            return $num > 0;
        }

        public function UpdateRoom($room){
            if($room == null || !isset($room->id)){
                return array("message" => "Укажите id автомобиля", "method" => "UpdateRoom", "requestData" => $room);
            }

            $roomId = $room->id;
            unset($room->id);
            if($room->oldImg && $room->img != $room->oldImg){
                $this->removeFile($room->oldImg);
            }
            unset($room->oldImg);
            $a = $this->database->genUpdateQuery(array_keys((array)$room), array_values((array)$room), "room", $roomId);
            $query = $this->database->db->prepare($a[0]);
            $query->execute($a[1]);
            return array('message' => 'Автомобиль обновлен');
        }

        private function removeFile($filelink){
            $path = explode($this->baseUrl.'/', $filelink);
            if($path[1] && file_exists($path[1])){
                unlink($path[1]);
            }
        }

    }
?>