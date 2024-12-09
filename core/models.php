<?php
include 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
    $response = array();

    $sql = "SELECT * FROM hr_users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$username])) {
        $userInfoArray = $stmt->fetch();
        if ($stmt->rowCount() > 0) {
            $response = array(
                "result" => true,
                "status" => "200",
                "userInfoArray" => $userInfoArray,
                "user_type" => 'HR'  
            );
        } else {
            $sql = "SELECT * FROM applicants WHERE username = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$username])) {
                $userInfoArray = $stmt->fetch();
                if ($stmt->rowCount() > 0) {
                    $response = array(
                        "result" => true,
                        "status" => "200",
                        "userInfoArray" => $userInfoArray,
                        "user_type" => 'Applicant'  
                    );
                } else {
                    $response = array(
                        "result" => false,
                        "status" => "400",
                        "message" => "User doesn't exist in either table"
                    );
                }
            }
        }
    }

    return $response;
}

function getUserByID($pdo, $user_id, $user_type) {
    $sql = "";
    if ($user_type == 'HR') {
        $sql = "SELECT * FROM hr_users WHERE id = ?";
    } else if ($user_type == 'Applicant') {
        $sql = "SELECT * FROM applicants WHERE id = ?";
    }

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$user_id])) {
        return $stmt->fetch();
    }

    return null;  
}


function loginUser($pdo, $username, $password) {
    $checkUser = checkIfUserExists($pdo, $username);
    
    if ($checkUser['result']) {
        $user = $checkUser['userInfoArray'];
        $user_type = $checkUser['user_type'];

        if (password_verify($password, $user['password'])) {
            return [
                'status' => '200',
                'id' => $user['id'],
                'user_type' => $user_type
            ];
        } else {
            return ['status' => '400', 'message' => 'Incorrect password'];
        }
    } else {
        return $checkUser; 
    }
}


function registerHR($pdo, $username, $password, $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM hr_users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return ['status' => '400', 'message' => 'Username already taken for HR'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO hr_users (username, password, email, created_at) VALUES (:username, :password, :email, NOW())");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            return ['status' => '200', 'message' => 'HR Registration successful'];
        } else {
            return ['status' => '500', 'message' => 'Error registering HR user'];
        }
    } catch (PDOException $e) {
        return ['status' => '500', 'message' => 'Database error: ' . $e->getMessage()];
    }
}


function registerApplicant($pdo, $username, $password, $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM applicants WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return ['status' => '400', 'message' => 'Username already taken for Applicant'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO applicants (username, password, email, created_at) VALUES (:username, :password, :email, NOW())");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            return ['status' => '200', 'message' => 'Applicant Registration successful'];
        } else {
            return ['status' => '500', 'message' => 'Error registering applicant'];
        }
    } catch (PDOException $e) {
        return ['status' => '500', 'message' => 'Database error: ' . $e->getMessage()];
    }
}



function createJobPost($pdo, $title, $description, $company, $location, $salary, $posted_by) {
    $sql = "INSERT INTO job_posts (title, description, company, location, salary, posted_by) 
            VALUES (:title, :description, :company, :location, :salary, :posted_by)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':company' => $company,
        ':location' => $location,
        ':salary' => $salary,
        ':posted_by' => $posted_by
    ]);
    return ['status' => '200', 'message' => 'Job post created successfully!'];
}

function getApplicantsForJob($pdo, $jobId) {
    try {
        $stmt = $pdo->prepare("SELECT a.username, a.email, a.resume, a.created_at, app.cover_letter, app.status, app.id as application_id 
                               FROM applications app 
                               JOIN applicants a ON app.applicant_id = a.id 
                               WHERE app.job_id = :job_id");
        $stmt->bindParam(':job_id', $jobId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['status' => '500', 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function applyToJob($pdo, $job_id, $applicant_id, $cover_letter) {
    $sql = "INSERT INTO applications (job_id, applicant_id, cover_letter) VALUES (:job_id, :applicant_id, :cover_letter)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':job_id' => $job_id,
        ':applicant_id' => $applicant_id,
        ':cover_letter' => $cover_letter
    ]);
    return ['status' => '200', 'message' => 'Application submitted successfully!'];
}

function uploadResume($pdo, $user_id, $user_type, $file_name, $file_path, $description) {
    $sql = "INSERT INTO pdf_uploads (user_id, user_type, pdf_name, file_path, description) 
            VALUES (:user_id, :user_type, :pdf_name, :file_path, :description)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':user_type' => $user_type,
        ':pdf_name' => $file_name,
        ':file_path' => $file_path,
        ':description' => $description
    ]);
    return ['status' => '200', 'message' => 'Resume uploaded successfully!'];
}

function sendMessage($pdo, $sender_id, $recipient_id, $message) {
    $sql = "INSERT INTO messages (sender_id, recipient_id, message) 
            VALUES (:sender_id, :recipient_id, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':recipient_id' => $recipient_id,
        ':message' => $message
    ]);
    return ['status' => '200', 'message' => 'Message sent successfully!'];
}

function getJobPosts($pdo, $hr_id) {
    $sql = "SELECT * FROM job_posts WHERE posted_by = :hr_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hr_id' => $hr_id]);
    return $stmt->fetchAll();
}


function getJobPostById($pdo, $job_id) {
    $stmt = $pdo->prepare("SELECT * FROM job_posts WHERE id = :job_id");
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC); 
}


function getAllJobPosts($pdo, $hr_id) {
    $sql = "SELECT * FROM job_posts WHERE posted_by = :hr_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hr_id' => $hr_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

function getApplications($pdo, $job_id) {
    $sql = "SELECT * FROM applications WHERE job_id = :job_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':job_id' => $job_id]);
    return $stmt->fetchAll();
}
?>
