<?php
//прием запросов с клиента
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization");

include_once 'repository.php';
include_once './utils/token.php';

$repository = new OlympicsRepository();
$token = new Token();
// http://localhost/controller.php?key=get-cars&id=3&name=John
if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'check-admin':
            if($decodeToken = checkToken($token, true)){
                if($decodeToken){
                    echo json_encode($decodeToken->isAdmin == "1");
                }
            }
            echo json_encode(false);
            return;
        case 'get-course':
            echo json_encode($repository->GetCourseDetails($_GET['courseId']));
            return;
        case 'add-question':
            if($decodeToken = checkToken($token, true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->AddQuestion($data));
            }
            return;
        case 'update-question':
            if($decodeToken = checkToken($token, true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->UpdateQuestion($data));
            }
            return;
        case 'sign-in':
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->SignIn($data));
            return;
        case 'sign-up':
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->SignUp($data));
            return;
        case 'get-courses':
            if($decodeToken = checkToken($token)){
                echo json_encode($repository->GetCourses($decodeToken->id));
                return;
            }
            return;
        case 'get-user-info':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->GetUserInfo($decodeToken->id));
                return;
            }
            return;
        case 'add-course':
            if($decodeToken = checkToken($token, true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->AddCourse($data));
            }
            return;
        case 'update-course':
            if($decodeToken = checkToken($token, true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->UpdateCourse($data));
            }
            return;
        case 'save-answers':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->SaveAnswers($decodeToken->id, $data));
            }
            return;
        default: 
            echo json_encode(array("message" => "Ключ запроса не найден"));
            return;
    }

} else {
    http_response_code(500);
    echo json_encode(array("message" => "Отсутствует ключ запроса."));
}

function checkToken($token, $checkAdmin = false) {
    try{
        if(!isset($_GET['token'])){
            return false;
        }
        $data = $token->decode($_GET['token']);
        if($checkAdmin && (!isset($data->isAdmin) || !$data->isAdmin)){
            return false;
        }
        return $data;
        
    } catch(Exception $e) {
        return false;
    }
}
?>