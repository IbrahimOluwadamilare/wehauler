<?php 
require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}
 
/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

 
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}

/**
 * User Registration(Customer)
 * url - /register
 * method - POST
 * params - first_name,last_name, business_name, phone_no, password_hash, email, api_key,status,security_question, security_answer.
 */

$app->post('/register', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('first_name','last_name', 'business_name', 'phone_no', 'password', 'email','security_question', 'security_answer'));
 
            $response = array();
 
            // reading post params
            $first_name = $app->request->post('first_name');
            $last_name = $app->request->post('last_name');
 			$business_name = $app->request->post('business_name');
 			$phone_no = $app->request->post('phone_no');
 			$password = $app->request->post('password');
 			$email = $app->request->post('email');
 			$security_question = $app->request->post('security_question');
 			$security_answer = $app->request->post('security_answer');

            // validating email address
            validateEmail($email);
 
            $db = new DbHandler();
            $res = $db->createUsercustomer($first_name, $last_name, $business_name, $phone_no, $password, $email, $security_question, $security_answer);
 
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(200, $response);
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(200, $response);
            }
        });


/**
 * User Registration(Vendors)
 * url - /registervendor
 * method - POST
 * params - first_name,last_name, business_name, phone_no, password_hash, email,security_question, security_answer.
 */

$app->post('/registervendor', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('first_name','last_name', 'business_name', 'phone_no', 'password', 'email','security_question', 'security_answer'));
 
            $response = array();
 
            // reading post params
            $first_name = $app->request->post('first_name');
            $last_name = $app->request->post('last_name');
 			$business_name = $app->request->post('business_name');
 			$phone_no = $app->request->post('phone_no');
 			$password = $app->request->post('password');
 			$email = $app->request->post('email');
 			$security_question = $app->request->post('security_question');
 			$security_answer = $app->request->post('security_answer');

            // validating email address
            validateEmail($email);
 
            $db = new DbHandler();
            $res = $db->createUservendors($first_name, $last_name, $business_name, $phone_no, $password, $email, $security_question, $security_answer);
 
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(200, $response);
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(200, $response);
            }
        });

$app->run();

?>