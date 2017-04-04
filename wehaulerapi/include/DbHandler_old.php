<?php
 
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @oibrahim
 */
class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }



 
    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createAdmin($name, $email, $password) {
        require_once 'PassHash.php';
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isadminExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
 
            // Generating API key
            $api_key = $this->generateApiKey();
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO superadmin(name, email, password_hash, api_key, status) values(?, ?, ?, ?, 1)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);
 
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return Admin_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return Admin_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return Admin_ALREADY_EXISTED;
        }
 
        return $response;
    }
 
    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createUser($fullname, $email, $password, $company_name,$company_licence_no, $company_licence_expdate,$security_question, $security_answer) {
        require_once 'PassHash.php';
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
 
            // Generating API key
            $api_key = $this->generateApiKey();
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users(fullname, email, password_hash, api_key,company_name,company_licence_no,company_licence_expdate,status,security_question,security_answer) values(?,?,?,?,?,?,?,1,?,?)");
            $stmt->bind_param("sssssssis",$fullname, $email, $password_hash, $api_key, $company_name, $company_licence_no, $company_licence_expdate, $security_question , $security_answer);
 
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }
 
        return $response;
    }
 
    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE email = ?");
 
        $stmt->bind_param("s", $email);
 
        $stmt->execute();
 
        $stmt->bind_result($password_hash);
 
        $stmt->store_result();
 
        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password
 
            $stmt->fetch();
 
            $stmt->close();
 
            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();
 
            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Checking employee login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkemployeeLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password_hash FROM employee WHERE email = ?");
 
        $stmt->bind_param("s", $email);
 
        $stmt->execute();
 
        $stmt->bind_result($password_hash);
 
        $stmt->store_result();
 
        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password
 
            $stmt->fetch();
 
            $stmt->close();
 
            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();
 
            // user not existed with the email
            return FALSE;
        }
    }


    /**
     * Checking for duplicate adminuser by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isadminExists($email) {
        $stmt = $this->conn->prepare("SELECT id from superadmin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
 
    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    /**
     * Checking for duplicate employee by email address
     * @param String $email email to check in db
     * @return boolean
     */

    private function isemployeeExists($email) {
        $stmt = $this->conn->prepare("SELECT id from employee WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
 
    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT fullname, email, api_key, status, created_at, company_name, company_licence_no ,company_licence_expdate FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching employee by email
     * @param String $email employee email id
     */
    public function getemployeeByEmail($email) {
        $stmt = $this->conn->prepare("SELECT fullname, email, api_key, created_at, status,company_name,dot_medical_no,dot_medical_no_expdate,driver_licence_no,driver_licence_expdate,status FROM employee WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
 
    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }
 
    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }
 
    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
 
    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
        }
 
    /* ------------- `tasks` table method ------------------ */
 
    /**
     * Creating new task
     * @param String $user_id user id to whom task belongs to
     * @param String $task task text
     */
    public function createEmployee($fullname, $email, $password, $company_name,$driver_licence_no, $driver_licence_expdate,$dot_medical_card_no,$dot_medical_card_expdate) {        

        require_once 'PassHash.php';
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isemployeeExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
 
            // Generating API key
            $api_key = $this->generateApiKey();
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO employee(fullname, email, password_hash, api_key,company_name,dot_medical_no,dot_medical_no_expdate,driver_licence_no,driver_licence_expdate,status) values(?, ?, ?, ?,?,?,?,?,?,1)");
            $stmt->bind_param("sssssssss", $fullname, $email, $password_hash, $api_key, $company_name, $dot_medical_card_no, $dot_medical_card_expdate,$driver_licence_no,$driver_licence_expdate);
 
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return Employee_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return Employee_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return Employee_ALREADY_EXISTED;
        }
 
        return $response;

    }
 
    /**
     * Fetching single task
     * @param String $task_id id of the task
     */
    public function getemployee($email, $company_name) {
        $stmt = $this->conn->prepare("SELECT fullname, email, password_hash, api_key, status,company_name,dot_medical_no,dot_medical_no_expdate,driver_licence_no,driver_licence_expdate FROM employee WHERE email = ? AND company_name = ? ");
        $stmt->bind_param("ss", $email,$company_name);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Updating task
     * @param String $task_id id of the task
     * @param String $task task text
     * @param String $status task status
     */
    public function updateEmployeeDetails($fullname, $email, $password, $company_name,$driver_licence_no, $driver_licence_expdate,$dot_medical_card_no,$dot_medical_card_expdate) {
        $stmt = $this->conn->prepare("UPDATE employee set fullname = ? ,dot_medical_no = ?,dot_medical_no_expdate = ?,driver_licence_no = ?,driver_licence_expdate = ? WHERE email = ? AND company_name = ? ");
        $stmt->bind_param("sssssss", $fullname, $dot_medical_card_no, $dot_medical_no_expdate, $driver_licence_no, $driver_licence_expdate, $email, $company_name);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
 
    /**
     * Deleting a task
     * @param String $task_id id of the task to delete
     */
    public function deleteEmployee($email, $company_name) {
        $stmt = $this->conn->prepare("DELETE FROM employee WHERE employee.email = ? AND employee.company_name = ? ");
        $stmt->bind_param("ss", $email, $company_name);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    private function isupdateExists($topic) {
        $stmt = $this->conn->prepare("SELECT id from news_updates WHERE topic = ?");
        $stmt->bind_param("s", $topic);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    private function isTopicExists($topic) {
        $stmt = $this->conn->prepare("SELECT topic from meetup WHERE topic = ?");
        $stmt->bind_param("s", $topic);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    private function isMeetupIdExists($meeting_id) {
        $stmt = $this->conn->prepare("SELECT meeting_id from meetup WHERE meeting_id = ?");
        $stmt->bind_param("s", $meeting_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    /**
     * Creating new news update
     */
    public function createNewsUpdate($topic, $description, $posted_by, $target, $image_dir, $video_dir) {  

    $response = array();

    if (!$this->isupdateExists($topic)) {      
        $stmt = $this->conn->prepare("INSERT INTO news_updates(topic,description,posted_by,target,image_dir,video_dir) VALUES(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $topic,$description,$posted_by,$target,$image_dir,$video_dir);
        $result = $stmt->execute();
        $stmt->close();
 
        if ($result) {

                return News_CREATED_SUCCESSFULLY;

            } else {
                // Failed to create user
                return News_CREATION_FAILED;
            }
        }else{
             return News_ALREADY_EXISTED;
        }

        return $response;
    }

    public function createMeetup($topic, $description, $posted_by, $target) {  

        $response = array();
        $meeting_id = $this->generateApiKey();

        if (!$this->isTopicExists($topic)) {      
            $stmt = $this->conn->prepare("INSERT INTO meetup(topic,description,posted_by,target,meeting_id) VALUES(?,?,?,?,?)");
            $stmt->bind_param("sssss", $topic,$description,$posted_by,$target,$meeting_id);
            $result = $stmt->execute();
            $stmt->close();
     
            if ($result) {

                    return Meetup_CREATED_SUCCESSFULLY;

                } else {
                    // Failed to create user
                    return Meetup_CREATION_FAILED;
                }
            }else{
                 return Meetup_ALREADY_EXISTED;
            }

            return $response;
        }

    public function createAcceptMeetup($meetup_id, $driver_email, $driver_company) {  

        $response = array();

        if (!$this->isMeetupIdExists($meeting_id)) {      
            $stmt = $this->conn->prepare("INSERT INTO meetup_acceptance(meeting_id,driver_email,driver_company) VALUES(?,?,?)");
            $stmt->bind_param("iss", $meetup_id, $driver_email, $driver_company);
            $result = $stmt->execute();
            $stmt->close();
     
            if ($result) {

                    return Meetup_Accepted_SUCCESSFULLY;

                } else {
                    // Failed to create user
                    return Meetup_Acceptance_FAILED;
                }
            }else{
                 return Meetup_Doesnt_Exist;
            }

            return $response;
        }

    public function createVehicleMaintainance($email, $company_name, $lightning, $horn, $tire_tread_depth, $wheel_rims, $tractor_safty_loops, $fluid_leaks_oil, $fluid_leaks_brake, $fuel_system, $wind_sheild, $periodic_inspection, $rear_vision_mirror, $fire_extinguisher, $warning_triangle, $required_markings, $conspicuty_tape, $vehicle_no, $vehicle_type) {  

        $response = array();
      
            $stmt = $this->conn->prepare("INSERT INTO vehicle_maintanance(email, company_name, lightning, horn, tire_tread_depth, wheel_rims, tractor_safty_loops, fluid_leaks_oil, fluid_leaks_brake, fuel_system, wind_sheild, periodic_inspection, rear_vision_mirror, fire_extinguisher, warning_triangle, required_markings, conspicuty_tape,vehicle_no, vehicle_type) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssiiiiiiiiiiiiiiiis", $email, $company_name, $lightning, $horn, $tire_tread_depth, $wheel_rims, $tractor_safty_loops, $fluid_leaks_oil, $fluid_leaks_brake, $fuel_system, $wind_sheild, $periodic_inspection, $rear_vision_mirror, $fire_extinguisher, $warning_triangle, $required_markings, $conspicuty_tape, $vehicle_no, $vehicle_type);
            $result = $stmt->execute();
            $stmt->close();
     
            if ($result) {

                    return Maintainance_updated_SUCCESSFULLY;

                } else {
                    // Failed to create user
                    return Maintainance_FAILED;
                }
                return $response;
            }


    public function getAllMeetup($target) {
        $stmt = $this->conn->prepare("SELECT * FROM meetup WHERE target = ?");
        $stmt->bind_param("s", $target);
        $stmt->execute();
        $meetupupdate = $stmt->get_result();
        $stmt->close();
        return $meetupupdate;
    }

    public function getVehicleMaintainance() {
        $stmt = $this->conn->prepare("SELECT * FROM vehicle_maintanance");
        $stmt->execute();
        $vehicle = $stmt->get_result();
        $stmt->close();
        return $vehicle;
    }


    public function getAcceptedMeetup($meeting_id) {
        $stmt = $this->conn->prepare("SELECT * FROM meetup_acceptance WHERE meeting_id = ?");
        $stmt->bind_param("i", $meeting_id);
        $stmt->execute();
        $meetupaccept= $stmt->get_result();
        $stmt->close();
        return $meetupaccept;
    }



    public function getAllUpdates($target) {
        $stmt = $this->conn->prepare("SELECT * FROM news_updates WHERE target = ?");
        $stmt->bind_param("s", $target);
        $stmt->execute();
        $newupdate = $stmt->get_result();
        $stmt->close();
        return $newupdate;
    }
    
    public function gettotalemployee($company_name) {
        $var = "SELECT * FROM employee WHERE company_name='".$company_name."'";
        $stmt = $this->conn->prepare($var);
        //$stmt->bind_param("s", $company_name);
        $stmt->execute();
        $totalemployee = $stmt->get_result();
        $stmt->close();
        return $totalemployee;
    }



}
 
?>