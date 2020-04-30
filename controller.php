<?php
//прием запросов с клиента
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization");

include_once 'repository.php';
include_once './utils/token.php';

$repository = new BookingRepository();
$token = new Token();
// http://localhost/controller.php?key=get-cars&id=3&name=John
if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'check-admin':
            if($decodeToken = checkToken($token, true)){
                if($decodeToken){
                    echo json_encode($decodeToken->isAdmin == "1");
                } else {
                    echo json_encode($decodeToken);
                }
                
            }
            return;
        case 'get-rooms':
            echo json_encode($repository->GetRooms($_GET));
            return;
        case 'add-order':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->AddOrder($decodeToken->id, $data));
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
        case 'get-history':
            if($decodeToken = checkToken($token, true)){
                echo json_encode($repository->GetHistory($decodeToken->id, true));
                return;
            }
            if($decodeToken = checkToken($token)){
                echo json_encode($repository->GetHistory($decodeToken->id, false));
            }
            return;
        case 'get-user-info':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->GetUserInfo($decodeToken->id));
                return;
            }
            return;
        case 'add-room':
            if($decodeToken = checkToken($token, true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->AddRoom($data));
            }
            return;
        case 'update-room':
            if($decodeToken = checkToken($token, true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->UpdateRoom($data));
            }
            return;
        case 'update-user-info':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->UpdateUserInfo($decodeToken->id, $data));
            }
            return;
        case 'update-order':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->UpdateOrder($data));
            }
            return;
        case 'cancel-order':
            if($decodeToken = checkToken($token)){
                echo json_encode($repository->CancelOrder($_GET['orderId']));
            }
            return;
        case 'upload-room-img':
            if($decodeToken = checkToken($token, true)){
                echo json_encode($repository->UploadRoomImg($_FILES['RoomImage']));
            } else {
                echo json_encode(array("message" => "В доступе отказано"));
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