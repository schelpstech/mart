<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User
{
    private $model;
    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
        $this->model = new Model($db_conn);
    }

    /**
     * Check if email already exists
     */
    public function emailExists($email)
    {
        try {
            error_log("User::emailExists checking email={$email}");
            $run = $this->db->exists("users_mart", "email = '{$email}'");
            error_log("User::emailExists result=" . var_export($run, true));
            return $run;
        } catch (Exception $e) {
            error_log("User::emailExists ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Register user
     */
    public function register($email, $phone, $password)
    {
        try {
            error_log("User::register started for email={$email}");

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $verificationToken = bin2hex(random_bytes(32));

            $fields = [
                "email"              => $email,
                "phone"              => $phone,
                "password_hash"           => $hashedPassword,
                "verification_token" => $verificationToken,
                "verified"        => 0,
                "created_at"         => date("Y-m-d H:i:s"),
                "updated_at"         => date("Y-m-d H:i:s")
            ];

            $run = $this->db->insert("users_mart", $fields);
            error_log("User::register insert result=" . var_export($run, true));

            return $run;
        } catch (Exception $e) {
            error_log("User::register ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail($email)
    {
        try {
            error_log("User::sendVerificationEmail started for {$email}");

            $user = $this->db->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);

            if (!$user) {
                error_log("User::sendVerificationEmail failed: no user found for {$email}");
                return false;
            }

            $verifyLink = "http://localhost/mart/app/verify.php?token=" . $user["verification_token"];

            $mail = new PHPMailer(true);

            // SMTP Settings
            $mail->isSMTP();
            $mail->Host       = 'server163.web-hosting.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'noreply@queenzy.assoec.org';
            $mail->Password   = 'UNYOpat2017@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('noreply@queenzy.assoec.org', 'Queenzy Stores');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "Verify Your Email";
            $mail->Body    = "
                <h2>Welcome!</h2>
                <p>Click the link below to verify your email address:</p>
                <a href='{$verifyLink}'>Verify Email</a>
            ";

            $result = $mail->send();
            error_log("User::sendVerificationEmail send result=" . var_export($result, true));

            return $result;
        } catch (Exception $e) {
            error_log("User::sendVerificationEmail ERROR - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify account
     */
    public function verifyAccount($token)
    {
        try {
            error_log("User::verifyAccount started for token={$token}");

            $user = $this->db->getRows("users_mart", [
                "where" => ["verification_token" => $token],
                "return_type" => "single"
            ]);

            if ($user) {
                $this->db->update("users_mart", [
                    "verified" => 1,
                    "verification_token" => null
                ], ["user_id" => $user["user_id"]]);

                error_log("User::verifyAccount success for user_id=" . $user["id"]);
                return true;
            }

            error_log("User::verifyAccount failed: no user found for token={$token}");
            return false;
        } catch (Exception $e) {
            error_log("User::verifyAccount ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        try {
            error_log("User::login started for {$email}");

            $user = $this->model->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);

            switch (true) {
                case !$user:
                    error_log("User::login failed - email not found: {$email}");
                    return ["status" => false, "message" => "Invalid login credentials."];

                case !password_verify($password, $user["password_hash"]):
                    error_log("User::login failed - wrong password for {$email}");
                    return ["status" => false, "message" => "Invalid login credentials."];

                case !$user["verified"]:
                    error_log("User::login failed - email not verified for {$email}");
                    return [
                        "status" => false,
                        "message" => 'Please check your email for verification instructions. 
                  <a href="./resendverification.php"><b>Click to resend link</b></a>'
                    ];

                default:
                    // Successful login
                    $_SESSION["user_id"] = $user["user_id"];
                    $_SESSION["user_email"] = $user["email"];

                    // Update cart table to assign session items to logged-in user
                    $this->model->update(
                        "cart",
                        ["user_id" => $user["user_id"]],
                        ["session_id" => session_id()]
                    );

                    error_log("User::login success for user_id=" . $user["user_id"]);
                    return ["status" => true, "message" => "Login successful"];
            }
        } catch (Exception $e) {
            error_log("User::login ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    public function getByEmail($email)
    {
        try {
            error_log("User::getByEmail started for {$email}");
            $result = $row = $this->model->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);
            error_log("User::getByEmail result=" . var_export($result, true));
            return $result;
        } catch (Exception $e) {
            error_log("User::getByEmail ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    public function resendVerificationEmail($email)
    {
        try {
            error_log("User::resendVerificationEmail started for email={$email}");

            // Fetch user by email
            $row = $this->model->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);

            if ($row) {
                if ($row['is_verified'] == 1) {
                    error_log("User::resendVerificationEmail failed: email already verified");
                    return false;
                }

                // Generate new token
                $newToken = bin2hex(random_bytes(16));

                // Update with new token
                $this->model->update(
                    "users_mart",
                    ["verification_token" => $newToken],
                    ["id" => $row['id']]
                );

                // Send the verification email
                $this->sendVerificationEmail($email);

                error_log("User::resendVerificationEmail success for user_id=" . $row['id']);
                return true;
            }

            error_log("User::resendVerificationEmail failed: email not found");
            return false;
        } catch (Exception $e) {
            error_log("User::resendVerificationEmail ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    public function logout()
    {
        // Destroy all session variables
        $_SESSION = array();

        // If using cookies for login, clear them too
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        // Destroy the session
        session_destroy();
        return true;
    }
}
