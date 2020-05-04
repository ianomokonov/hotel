<?php
    //обработка запросов
    include_once './utils/token.php';
    include_once './utils/database.php';
    include_once 'models.php';
    class OlympicsRepository{
        private $database;
        private $token;
        private $filesUpload;
        private $baseUrl = 'http://localhost/olympics';

        public function __construct()
        {
            $this->database = new DataBase();
            $this->token = new Token();
        }

        public function GetCourseDetails($courseId){
            if($courseId == null){
                return array("message" => "Введите id курса", "method" => "GetCourseDetails", "requestData" => $courseId);
            }

            $query = $this->database->db->prepare("SELECT * from course WHERE id = ?");
            $query->execute(array($courseId));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Course');
            
            $course = $query->fetch();
            $course->questions = $this->GetCourseQuestions($course->id);
            return $course;
        }

        private function GetCourseQuestions($courseId){
            if($courseId == null){
                return array("message" => "Введите id курса", "method" => "GetRoomDates", "requestData" => $courseId);
            }

            $str = "SELECT * from question WHERE courseId = ?";

            $query = $this->database->db->prepare($str);
            $query->execute(array($courseId));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Question');
            $questions = [];

            while ($question = $query->fetch()) {
                $question->answers = $this->GetQuestionAnswers($question->id);
                $questions[] = $question;
            }
            return $questions;
            
        }

        public function GetQuestionAnswers($questionId){
            $query = $this->database->db->prepare("SELECT * from answer WHERE questionId = ?");
            $query->execute(array($questionId));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Answer');
            return $query->fetchAll();            
        }

        public function GetCourses($userId){
            $text = "SELECT link, name, id, description, (select COUNT(*) from question WHERE question.courseId = course.id) as points, (SELECT COUNT(*) FROM `useranswer` u JOIN question q ON u.questionId = q.id JOIN answer a ON u.answerId = a.id WHERE q.courseId = course.id AND a.isRight = '1' AND u.userId = $userId) as correctPoints from course";
            $query = $this->database->db->query($text);
            $query->setFetchMode(PDO::FETCH_CLASS, 'Course');
            return $query->fetchAll();
            
        }
        public function AddQuestion($question){
            if($question == null){
                return array("message" => "Вопрос пуст", "method" => "AddQuestion", "requestData" => $question);
            }
            $answers = $question->answers;
            
            unset($question->answers);
            $insert = $this->database->genInsertQuery((array)$question, 'question');
            $query = $this->database->db->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            $id = $this->database->db->lastInsertId();
            foreach ($answers as $answer){
                
                $answer->questionId = $id;
                $this->AddAnswer($answer);
            }

            return $id;
            
        }

        private function AddAnswer($answer){
            $insert = $this->database->genInsertQuery((array)$answer, 'answer');
            $query = $this->database->db->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }
        }

        private function RemoveAnswers($questionId){
            $this->database->db->query("DELETE FROM answer WHERE questionId = $questionId");
        }

        public function UpdateQuestion($question){
            if(!$question || !isset($question->id) || !$question->id){
                return array("message" => "Укажите id вопроса", "method" => "UpdateOrder", "requestData" => $question);
            }

            $questionId = $question->id;
            unset($question->id);
            $answers = $question->answers;
            unset($question->answers);
            $a = $this->database->genUpdateQuery(array_keys((array)$question), array_values((array)$question), "question", $questionId);
            $query = $this->database->db->prepare($a[0]);
            $query->execute($a[1]);
            $this->RemoveAnswers($questionId);
            foreach ($answers as $answer){
                $answer->questionId = $questionId;
                $this->AddAnswer($answer);
            }
            return array('message' => 'Вопрос обновлен');
        }

        public function SaveAnswers($userId, $answers){
            if(!$answers){
                return array("message" => "Укажите ответы", "method" => "SaveAnswers", "requestData" => $answers);
            }

            $points = [];
            
            foreach ($answers as $answer){
                $answer->userId = $userId;
                $questionId = $answer->questionId;
                $answerId = $answer->answerId;
                $this->database->db->query("DELETE FROM useranswer WHERE questionId = $questionId");
                $this->AddUserAnswer($answer);
                $query = $this->database->db->query("SELECT * FROM answer WHERE id = $answerId");
                if($query->fetch()['isRight'] == '1'){
                    $points[] = $questionId;
                }
            }

            $this->AddUserPoints($userId);

            return $points;
        }

        private function AddUserAnswer($answer){
            $insert = $this->database->genInsertQuery((array)$answer, 'userAnswer');
            $query = $this->database->db->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }
        }

        private function AddUserPoints($userId){
            $curPoints = $this->database->db->query("SELECT COUNT(*) as points FROM useranswer u JOIN answer a ON u.answerId = a.id WHERE a.isRight = '1' AND u.userId = $userId")->fetch()['points'];
            
            $this->database->db->query("UPDATE user SET points = $curPoints WHERE id = $userId");
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
            $sth = $this->database->db->prepare("SELECT name, surname, middlename, email, isAdmin, points FROM user WHERE id = ? LIMIT 1");
            $sth->setFetchMode(PDO::FETCH_CLASS, 'User');
            $sth->execute(array($userId));
            $user = $sth->fetch();
            $user->isAdmin = $user->isAdmin == '1';
            return $user;
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

        public function AddCourse($course = null){
            if($course != null){
                try{
                    $insert = $this->database->genInsertQuery((array)$course, 'course');
                    $query = $this->database->db->prepare($insert[0]);
                    if ($insert[1][0]!=null) {
                        $query->execute($insert[1]);
                    }
                    return $this->database->db->lastInsertId();
                } catch(Exception $e) {
                    return array("message" => "Ошибка добавления курса", "error" => $e->getMessage());
                }
                
            } else {
                return array("message" => "Введите данные курса");
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

        public function UpdateCourse($course){
            if($course == null || !isset($course->id)){
                return array("message" => "Укажите id курса", "method" => "UpdateСourse", "requestData" => $course);
            }

            $courseId = $course->id;
            unset($course->id);
            $a = $this->database->genUpdateQuery(array_keys((array)$course), array_values((array)$course), "course", $courseId);
            $query = $this->database->db->prepare($a[0]);
            $query->execute($a[1]);
            return array('message' => 'Курс обновлен');
        }

    }
?>