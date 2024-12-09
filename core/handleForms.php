<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';


if (isset($_POST['loginBtn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the login function
    $result = loginUser($pdo, $username, $password);

    // Check if login is successful
    if ($result['status'] == '200') {
        // Set session variables after successful login
        $_SESSION['user_id'] = $result['id'];

        // Check user type and set appropriate session variable for HR or applicant
        if ($result['user_type'] == 'HR') {
            $_SESSION['hr_id'] = $result['id'];  // Store HR session
            header("Location: ../index.php");  // Redirect to HR dashboard
            exit();
        } else {
            $_SESSION['applicant_id'] = $result['id'];  // Store applicant session
            header("Location: ../applicants.php");  // Redirect to applicant's dashboard
            exit();
        }
    } else {
        // Invalid login message
        $_SESSION['message'] = 'Invalid username or password';
        header("Location: ../login.php");
        exit();
    }
}

// Handle HR Registrationif (isset($_POST['registerHRBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $confirmPassword = trim($_POST['confirm_password']);

    if ($password !== $confirmPassword) {
        $_SESSION['message'] = 'Passwords do not match.';
        $_SESSION['status'] = '400';
        header("Location: ../registerhr.php"); 
        exit();
    }

    $registerResult = registerHR($pdo, $username, $password, $email);
    $_SESSION['message'] = $registerResult['message'];
    $_SESSION['status'] = $registerResult['status'];
    header("Location: ../login.php"); 
    exit();
}


if (isset($_POST['registerApplicantBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $confirmPassword = trim($_POST['confirm_password']);

    if ($password !== $confirmPassword) {
        $_SESSION['message'] = 'Passwords do not match.';
        $_SESSION['status'] = '400';
        header("Location: ../registerapplicant.php"); 
        exit();
    }

    $registerResult = registerApplicant($pdo, $username, $password, $email);
    $_SESSION['message'] = $registerResult['message'];
    $_SESSION['status'] = $registerResult['status'];
    header("Location: ../login.php"); 
    exit();
}



if (isset($_POST['postJobBtn'])) {
    if (isset($_POST['jobTitle']) && isset($_POST['jobDescription'])) {
        $jobTitle = trim($_POST['jobTitle']);
        $jobDescription = trim($_POST['jobDescription']);
        $company = trim($_POST['company']);
        $location = trim($_POST['location']);
        $salary = isset($_POST['salary']) ? trim($_POST['salary']) : null;

        echo "Job Title: $jobTitle, Job Description: $jobDescription, Company: $company, Location: $location, Salary: $salary";

        if (!isset($_SESSION['hr_id'])) {
            $_SESSION['message'] = 'You must be logged in as HR to post a job.';
            header("Location: ../index.php");
            exit();
        }

        $postedBy = $_SESSION['hr_id']; 

        try {
            $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, company, location, salary, posted_by) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$jobTitle, $jobDescription, $company, $location, $salary, $postedBy]);

            $_SESSION['message'] = 'Job posted successfully!';
            header("Location: ../index.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Error posting job: ' . $e->getMessage();
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['message'] = 'Please fill in all the required fields.';
        header("Location: ../index.php");
        exit();
    }
}

if (isset($_POST['viewApplicantsBtn'])) {
    if (!isset($_SESSION['hr_id'])) {
        header('Location: login.php');
        exit();
    }

    $jobId = $_POST['job_id'];

    header('Location: ../viewapplicants.php?job_id=' . $jobId);
    exit();
}


if (isset($_POST['applyJobBtn'])) {
    $jobId = $_POST['job_id'];
    $resume = $_FILES['resume'];

    if ($resume['type'] == 'application/pdf') {
        $uploadDir = '../uploads/resumes/';
        $resumeFile = $uploadDir . basename($resume['name']);

        if (move_uploaded_file($resume['tmp_name'], $resumeFile)) {

            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume) VALUES (?, ?, ?)");
            $stmt->execute([$jobId, $_SESSION['applicant_id'], $resumeFile]);
        } else {
            echo "Error uploading the resume.";
        }
    } else {
        echo "Only PDF files are allowed.";
    }
}

if (isset($_GET['download'])) {
    $filePath = urldecode($_GET['download']);
    
    if (file_exists($filePath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    } else {
        echo "File not found.";
    }
}


if (isset($_POST['uploadResumeBtn'])) {
    $user_id = $_SESSION['user_id'];
    $user_type = 'Applicant';
    $file_name = $_FILES['resume']['name'];
    $file_path = "uploads/" . basename($file_name);
    move_uploaded_file($_FILES['resume']['tmp_name'], $file_path);
    $description = trim($_POST['description']);

    $uploadResult = uploadResume($pdo, $user_id, $user_type, $file_name, $file_path, $description);
    $_SESSION['message'] = $uploadResult['message'];
    header("Location: applicants.php");
}

if (isset($_POST['deleteJobBtn']) && isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];

    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE id = :job_id AND posted_by = :posted_by");
    $stmt->bindParam(':job_id', $job_id);
    $stmt->bindParam(':posted_by', $_SESSION['hr_id']);
    $stmt->execute();

    header('Location: index.php');
    exit();
}

if (isset($_POST['sendMessageBtn'])) {
    $sender_id = $_SESSION['user_id'];
    $recipient_id = $_POST['recipient_id'];
    $message = trim($_POST['message']);

    $messageResult = sendMessage($pdo, $sender_id, $recipient_id, $message);
    $_SESSION['message'] = $messageResult['message'];
    header("Location: applicants.php");
}
?>
