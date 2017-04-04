<?php
 
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @oibrahim
 */
class DbHandler {
 
    private $conn;
 
    function __construct() 
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }


    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
        }


    /**
     * Checking for duplicate customeruser by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isCustomerExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users_customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    /**
     * Checking for duplicate vendoruser by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isVendorExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users_vendors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }    


    /**
     * Creating new user
     * @param String first_name, last_name, business_name, phone_no, password_hash,  email, api_key, security_answer 
     * @param Int security_question
     */
    public function createUsercustomer($first_name, $last_name, $business_name, $phone_no, $password, $email, $security_question, $security_answer) {
        require_once 'PassHash.php';
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isCustomerExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
 
            // Generating API key
            $api_key = $this->generateApiKey();
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users_customers(first_name, last_name, business_name, phone_no, password_hash, email, api_key, security_question, security_answer) values(?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssis",$first_name, $last_name, $business_name, $phone_no, $password_hash, $email, $api_key, $security_question, $security_answer);
 
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
     * Creating new user
     * @param String first_name,last_name, business_name, phone_no, password, email,security_answer, 
     * @param Int security_question
     */
    public function createUservendors($first_name, $last_name, $business_name, $phone_no, $password, $email, $security_question, $security_answer) {
        require_once 'PassHash.php';
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isVendorExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
 
            // Generating API key
            $api_key = $this->generateApiKey();
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users_vendors(first_name, last_name, business_name, phone_no, password_hash, email, api_key, security_question, security_answer) values(?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssis",$first_name, $last_name, $business_name, $phone_no, $password_hash, $email, $api_key, $security_question, $security_answer);
 
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


}
?>