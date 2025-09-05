<?php
if(session_status() == PHP_SESSION_NONE): session_start(); endif;
header('Content-Type: application/json');
require_once('../dbConnect.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['uname'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    if (empty($username) || empty($password) || empty($captcha)) {
        echo json_encode(['status' => 'error', 'msg' => "All fields are required"]);
        exit();
    }

    if($captcha != $_SESSION['captcha_code']){
        echo json_encode(['status' => 'error', 'msg' => "Invalid Captcha", 'captcha' => $_SESSION['captcha_code'], 'user_captcha' => $captcha]);
        exit();
    }
    unset($_SESSION['captcha_code']); 
    
    try{
        $stmt = $pdo->prepare("SELECT kvum.rid, kvum.userid, kvum.pwd, sc.Centre_Name FROM kkhsou_verification_user_master kvum LEFT JOIN studycentre sc ON kvum.userid = sc.Centre_Code WHERE kvum.userid = ? ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        
        if(!$user || $password !== $user['pwd']){
            echo json_encode(['status' => 'error', 'msg' => "Invalid username or password"]);
            exit();
        }
        
        // If we reach here, login is successful.
        session_regenerate_id(true); // Prevent session fixation attacks
        $_SESSION['user_id'] = $user['rid'];
        $_SESSION['userid'] = $user['userid'];
        $_SESSION['center_name'] = $user['Centre_Name'];
        
        echo json_encode(['status' => 'success', 'msg' => "Login Successfully", 'user'=>$user]);
        exit();
    }
    catch(Exception $e){
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
    
}

?>