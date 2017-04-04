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
 * User Registration
 * url - /register
 * method - POST
 * params - fullname, email, password, company_name, company_licence_no, company_licence_expdate
 */
$app->post('/register', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('fullname', 'email', 'password','company_name','company_licence_no','company_licence_expdate','security_question','security_answer'));
 
            $response = array();
 
            // reading post params
            $fullname = $app->request->post('fullname');
            $email = $app->request->post('email');
            $password = $app->request->post('password');
            $company_name = $app->request->post('company_name');
            $company_licence_no = $app->request->post('company_licence_no');
            $company_licence_expdate = $app->request->post('company_licence_expdate');
            $security_question = $app->request->post('security_question');
            $security_answer = $app->request->post('security_answer');
 
            // validating email address
            validateEmail($email);
 
            $db = new DbHandler();
            $res = $db->createUser($fullname, $email, $password, $company_name,$company_licence_no, $company_licence_expdate,$security_question,$security_answer);
 
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
 * User Registration
 * url - /registeremployee
 * method - POST
 * params - fullname', 'email', 'password','company_name','driver_licence_no','driver_licence_expdate','dot_medical_card_no','dot_medical_card_expdate'
 */
$app->post('/registeremployee', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('fullname', 'email', 'password','company_name','driver_licence_no','driver_licence_expdate','dot_medical_card_no','dot_medical_card_expdate'));
 
            $response = array();
 
            // reading post params
            $fullname = $app->request->post('fullname');
            $email = $app->request->post('email');
            $password = $app->request->post('password');
            $company_name = $app->request->post('company_name');
            $driver_licence_no = $app->request->post('driver_licence_no');
            $driver_licence_expdate = $app->request->post('driver_licence_expdate');
            $dot_medical_card_no = $app->request->post('dot_medical_card_no');
            $dot_medical_card_expdate = $app->request->post('dot_medical_card_expdate');
 
            // validating email address
            validateEmail($email);
 
            $db = new DbHandler();
            $res = $db->createEmployee($fullname, $email, $password, $company_name,$driver_licence_no, $driver_licence_expdate,$dot_medical_card_no,$dot_medical_card_expdate);
 
            if ($res == Employee_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == Employee_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(200, $response);
            } else if ($res == Employee_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(200, $response);
            }
        });

/**
 * Admin Registration
 * url - /registerAdmin
 * method - POST
 * params - 'name', 'email', 'password'
 */
$app->post('/registeradmin', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('name', 'email', 'password'));
 
            $response = array();
 
            // reading post params
            $name = $app->request->post('name');
            $email = $app->request->post('email');
            $password = $app->request->post('password');
 
            // validating email address
            validateEmail($email);
 
            $db = new DbHandler();
            $res = $db->createAdmin($name, $email, $password);
 
            if ($res == Admin_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == Admin_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(200, $response);
            } else if ($res == Admin_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(200, $response);
            }
        });


/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('email', 'password'));
 
            // reading post params
            $email = $app->request()->post('email');
            $password = $app->request()->post('password');
            $response = array();
 
            $db = new DbHandler();
            // check for correct email and password
            if ($db->checkLogin($email, $password)) {
                // get the user by email
                $user = $db->getUserByEmail($email);
 
                if ($user != NULL) {
                    $response["error"] = false;
                    $response['fullname'] = $user['fullname'];
                    $response['email'] = $user['email'];
                    $response['apiKey'] = $user['api_key'];
                    $response['createdAt'] = $user['created_at'];
                    $response['company_name'] = $user['company_name'];
                } else {
                    // unknown error occurred
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }
 
            echoRespnse(200, $response);
        });

/**
 * User Login
 * url - /loginemployee
 * method - POST
 * params - email, password
 */
$app->post('/loginemployee', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('email', 'password'));
 
            // reading post params
            $email = $app->request()->post('email');
            $password = $app->request()->post('password');
            $response = array();
 
            $db = new DbHandler();
            // check for correct email and password
            if ($db->checkemployeeLogin($email, $password)) {
                // get the user by email
                $user = $db->getemployeeByEmail($email);
 
                if ($user != NULL) {
                    $response["error"] = false;
                    $response['fullname'] = $user['fullname'];
                    $response['email'] = $user['email'];
                    $response['apiKey'] = $user['api_key'];
                    $response['created_at'] = $user['created_at'];
                    $response['company_name'] = $user['company_name'];
                    $response['dot_medical_no'] = $user['dot_medical_no'];
                    $response['driver_licence_no'] = $user['driver_licence_no'];
                    $response['driver_licence_expdate'] = $user['driver_licence_expdate'];
                } else {
                    // unknown error occurred
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }
 
            echoRespnse(200, $response);
        });



/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();
 
        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user = $db->getUserId($api_key);
            if ($user != NULL)
                $user_id = $user["email"];
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Creating new news update in db
 * method POST
 * params - name
 * url - /newsupdate/
 */
$app->post('/newsupdate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('topic', 'description', 'posted_by', 'target', 'image_dir', 'video_dir'));
 
            $response = array();
            $topic = $app->request()->post('topic');
            $description = $app->request()->post('description');
            $posted_by = $app->request()->post('posted_by');
            $target = $app->request()->post('target');
            $image_dir = $app->request()->post('image_dir');
            $video_dir = $app->request()->post('video_dir');
 
            validateEmail($posted_by);
 
            $db = new DbHandler();
            $res = $db->createNewsUpdate($topic, $description, $posted_by, $target, $image_dir, $video_dir);
 
            if ($res == News_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if($res == News_CREATION_FAILED){
                $response["error"] = true;
                $response["message"] = "Failed to create update. Please try again";
                echoRespnse(200, $response);
            } else if ($res == News_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this News already existed";
                echoRespnse(200, $response);
            }
        });


$app->post('/Createmeetup', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('topic', 'description', 'posted_by', 'target'));
 
            $response = array();
            $topic = $app->request()->post('topic');
            $description = $app->request()->post('description');
            $posted_by = $app->request()->post('posted_by');
            $target = $app->request()->post('target');
 
            validateEmail($posted_by);
 
            $db = new DbHandler();
            $res = $db->createMeetup($topic, $description, $posted_by, $target);
 
            if ($res == Meetup_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You have created a meetup successfully";
                echoRespnse(201, $response);
            } else if($res == Meetup_CREATION_FAILED){
                $response["error"] = true;
                $response["message"] = "Failed to create meetup. Please try again";
                echoRespnse(200, $response);
            } else if ($res == Meetup_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this meetup already existed";
                echoRespnse(200, $response);
            }
        });

$app->post('/createVehicleMaintainance', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('email', 'company_name', 'lightning', 'horn', 'tire_tread_depth', 'wheel_rims', 'tractor_safty_loops', 'fluid_leaks_oil', 'fluid_leaks_brake', 'fuel_system', 'wind_sheild', 'periodic_inspection', 'rear_vision_mirror', 'fire_extinguisher', 'warning_triangle', 'required_markings', 'conspicuty_tape', 'vehicle_no', 'vehicle_type'));
 
            $response = array();
            $email = $app->request ()->post('email');
            $company_name = $app->request ()->post('company_name');
            $lightning = $app->request()->post('lightning');
            $horn = $app->request->post('horn');
            $tire_tread_depth = $app->request->post('tire_tread_depth');
            $wheel_rims = $app->request->post('wheel_rims');
            $tractor_safty_loops = $app->request->post('tractor_safty_loops');
            $fluid_leaks_oil = $app->request->post('fluid_leaks_oil');  
            $fluid_leaks_brake = $app->request->post('fluid_leaks_brake');
            $fuel_system = $app->request->post('fuel_system');
            $wind_sheild = $app->request->post('wind_sheild');
            $periodic_inspection = $app->request->post('periodic_inspection');
            $rear_vision_mirror = $app->request->post('rear_vision_mirror');
            $fire_extinguisher = $app->request->post('fire_extinguisher');
            $warning_triangle = $app->request->post('warning_triangle');
            $required_markings = $app->request->post('required_markings'); 
            $conspicuty_tape = $app->request->post('conspicuty_tape');
            $vehicle_no = $app->request->post('vehicle_no');
            $vehicle_type = $app->request->post('vehicle_type');   
            $posted_by = $app->request->post('posted_by');    
 
            validateEmail($email);
 
            $db = new DbHandler();
            $res = $db->createVehicleMaintainance($email, $company_name, $lightning, $horn, $tire_tread_depth, $wheel_rims, $tractor_safty_loops, $fluid_leaks_oil, $fluid_leaks_brake, $fuel_system, $wind_sheild, $periodic_inspection, $rear_vision_mirror, $fire_extinguisher, $warning_triangle, $required_markings, $conspicuty_tape, $vehicle_no, $vehicle_type);
 
            if ($res == Maintainance_updated_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You have updated Vehicle status successfully";
                echoRespnse(201, $response);
            } else if($res == Maintainance_FAILED){
                $response["error"] = true;
                $response["message"] = "Failed to update vehicle status. Please try again";
                echoRespnse(200, $response);
            }       
        });



$app->post('/Acceptmeetup', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('meetup_id', 'driver_email', 'driver_company'));
 
            $response = array();
            $meetup_id = $app->request->post('meetup_id');
            $driver_email = $app->request->post('driver_email');
            $driver_company = $app->request->post('driver_company');
 
            validateEmail($posted_by);
 
            $db = new DbHandler();
            $res = $db->createAcceptMeetup($meetup_id, $driver_email, $driver_company);
 
            if ($res == Meetup_Accepted_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You have accepted a meetup successfully";
                echoRespnse(201, $response);
            } else if($res == Meetup_Acceptance_FAILED){
                $response["error"] = true;
                $response["message"] = "Failed to accept meetup. Please try again";
                echoRespnse(200, $response);
            } else if ($res == Meetup_Doesnt_Exist) {
                $response["error"] = true;
                $response["message"] = "Sorry, this meetup doesnt existed";
                echoRespnse(200, $response);
            }
        });

$app->get('/newsupdate', function() {
            $response = array();
            $db = new DbHandler();
 
            // fetching all user newsupdates
            $result = $db->getAllUpdates("general");
 
            $response["error"] = false;
            $response["newupdate"] = array();
 
            // looping through result and preparing news updates array
            while ($newupdate = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $newupdate["id"];
                $tmp["topic"] = $newupdate["topic"];
                $tmp["description"] = $newupdate["description"];
                $tmp["image_dir"] = $newupdate["image_dir"];
                $tmp['target'] = $newupdate["target"];
                $tmp["video_dir"] = $newupdate["video_dir"];                
                $tmp["createdAt"] = $newupdate["created_at"];
                array_push($response["newupdate"], $tmp);
            }
 
            echoRespnse(200, $response);
        });


$app->get('/getmeetupgeneral', function() use ($app) {

            verifyRequiredParams(array('target'));
 
            
            $target = $app->request()->post('target');
            $response = array();
            $db = new DbHandler();
 
            // fetching all user newsupdates
            $result = $db->getAllMeetup("general");
 
            $response["error"] = false;
            $response["meetupupdate"] = array();
 
            // looping through result and preparing news updates array
            while ($meetupupdate = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $meetupupdate["id"];
                $tmp["topic"] = $meetupupdate["topic"];
                $tmp["description"] = $meetupupdate["description"];
                $tmp["posted_by"] = $meetupupdate["posted_by"];
                $tmp['target'] = $meetupupdate["target"];
                $tmp["meeting_id"] = $meetupupdate["meeting_id"];                
                $tmp["created_at"] = $meetupupdate["created_at"];
                array_push($response["meetupupdate"], $tmp);
            }
 
            echoRespnse(200, $response);
        });

$app->get('/getmeetupdrivers', function() {
            $response = array();
            $db = new DbHandler();
 
            // fetching all user newsupdates
            $result = $db->getAllMeetup("drivers");
 
            $response["error"] = false;
            $response["meetupupdate"] = array();
 
            // looping through result and preparing news updates array
            while ($meetupupdate = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $meetupupdate["id"];
                $tmp["topic"] = $meetupupdate["topic"];
                $tmp["description"] = $meetupupdate["description"];
                $tmp["posted_by"] = $meetupupdate["posted_by"];
                $tmp['target'] = $meetupupdate["target"];
                $tmp["meeting_id"] = $meetupupdate["meeting_id"];                
                $tmp["created_at"] = $meetupupdate["created_at"];
                array_push($response["meetupupdate"], $tmp);
            }
 
            echoRespnse(200, $response);
        });

$app->get('/getacceptedmeetup', function() use ($app) {

            verifyRequiredParams(array('meeting_id'));
 
            
            $meeting_id = $app->request->post('meeting_id');
            $response = array();
            $db = new DbHandler();
 
            // fetching all user newsupdates
            $result = $db->getAcceptedMeetup($meeting_id);
 
            $response["error"] = false;
            $response["meetupaccept"] = array();
 
            // looping through result and preparing news updates array
            while ($meetupaccept = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $meetupaccept["id"];
                $tmp["topic"] = $meetupaccept["topic"];
                $tmp["description"] = $meetupaccept["description"];
                $tmp["posted_by"] = $meetupaccept["posted_by"];
                $tmp['target'] = $meetupaccept["target"];
                $tmp["meeting_id"] = $meetupaccept["meeting_id"];                
                $tmp["created_at"] = $meetupaccept["created_at"];
                array_push($response["meetupaccept"], $tmp);
            }
 
            echoRespnse(200, $response);
        });

$app->get('/newsupdatedrivers', function() {
            $response = array();
            $db = new DbHandler();
 
            // fetching all user newsupdates
            $result = $db->getAllUpdates("drivers");
 
            $response["error"] = false;
            $response["newupdate"] = array();
 
            // looping through result and preparing news updates array
            while ($newupdate = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $newupdate["id"];
                $tmp["topic"] = $newupdate["topic"];
                $tmp['target'] = $newupdate["target"];
                $tmp["description"] = $newupdate["description"];
                $tmp["image_dir"] = $newupdate["image_dir"];
                $tmp["video_dir"] = $newupdate["video_dir"];                
                $tmp["createdAt"] = $newupdate["created_at"];
                array_push($response["newupdate"], $tmp);
            }
 
            echoRespnse(200, $response);
        });

$app->get('/getVehicleMaintainance', function() {
            $response = array();
            $db = new DbHandler();
 
            // fetching all user newsupdates
            $result = $db->getVehicleMaintainance();
 
            $response["error"] = false;
            $response["vehicle"] = array();
 
            // looping through result and preparing news updates array
            while ($vehicle = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["email"] = $vehicle["email"];
                $tmp["company_name"] = $vehicle["company_name"];
                $tmp['lightning'] = $vehicle["lightning"];
                $tmp["horn"] = $vehicle["horn"];
                $tmp["tire_tread_depth"] = $vehicle["tire_tread_depth"];
                $tmp["wheel_rims"] = $vehicle["wheel_rims"];                
                $tmp["tractor_safty_loops"] = $vehicle["tractor_safty_loops"];
                $tmp["fluid_leaks_oil"] = $vehicle["fluid_leaks_oil"];
                $tmp["fluid_leaks_brake"] = $vehicle["fluid_leaks_brake"];
                $tmp['fuel_system'] = $vehicle["fuel_system"];
                $tmp["wind_sheild"] = $vehicle["wind_sheild"];
                $tmp["periodic_inspection"] = $vehicle["periodic_inspection"];
                $tmp["rear_vision_mirror"] = $vehicle["rear_vision_mirror"];     
                $tmp["fire_extinguisher"] = $vehicle["fire_extinguisher"];
                $tmp["warning_triangle"] = $vehicle["warning_triangle"];
                $tmp["required_markings"] = $vehicle["required_markings"];
                $tmp['conspicuty_tape'] = $vehicle["conspicuty_tape"];
                $tmp["created_at"] = $vehicle["created_at"];
                $tmp["vehicle_no"] = $vehicle["vehicle_no"];
                $tmp["vehicle_type"] = $vehicle["vehicle_type"];           
                array_push($response["vehicle"], $tmp);
            }
            echoRespnse(200, $response);
        });
        

$app->get('/getemployee', function() use ($app) {
        
        verifyRequiredParams(array('company_name'));
 
        $response = array();
            $company_name = $app->request->get('company_name');
            
            $db = new DbHandler();
 
            // fetching all employees of a company
            $result = $db->gettotalemployee($company_name);
 
            $response["error"] = false;
            $response["totalemployee"] = array();
 
            // looping through result and preparing news updates array
            while ($totalemployee = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $totalemployee["id"];
                $tmp["fullname"] = $totalemployee["fullname"];
                $tmp["email"] = $totalemployee["email"];
                $tmp["company_name"] = $totalemployee["company_name"];
                $tmp["dot_medical_no"] = $totalemployee["dot_medical_no"];
                $tmp["dot_medical_no_expdate"] = $totalemployee["dot_medical_no_expdate"];  
                $tmp["driver_licence_no"] = $totalemployee["driver_licence_no"];
                $tmp["driver_licence_expdate"] = $totalemployee["driver_licence_expdate"];                               
                $tmp["createdAt"] = $totalemployee["created_at"];
                array_push($response["totalemployee"], $tmp);
            }
 
            echoRespnse(200, $response);
        });        

h


 
$app->run();



?>
