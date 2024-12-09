<?php
session_start();
require_once 'core/dbConfig.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyBtn'])) {
    $job_id = $_POST['job_id'];
    $cover_letter = $_POST['cover_letter'];
    $applicant_username = $_SESSION['user'];
    $stmt = $pdo->prepare("SELECT id FROM applicants WHERE username = ?");
    $stmt->execute([$applicant_username]);
    $applicant = $stmt->fetch();

    if (!$applicant) {
        echo "Applicant not found.";
        exit();
    }

    $applicant_id = $applicant['id'];

    // Handle file upload (resume)
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $resume_tmp = $_FILES['resume']['tmp_name'];
        $resume_name = $_FILES['resume']['name'];
        $resume_ext = pathinfo($resume_name, PATHINFO_EXTENSION);

        // Check if file is a PDF
        if ($resume_ext != 'pdf') {
            echo "Only PDF files are allowed.";
            exit();
        }

        // Save the resume to the server
        $resume_path = 'uploads/' . uniqid() . '.' . $resume_ext;
        if (move_uploaded_file($resume_tmp, $resume_path)) {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, cover_letter, status) 
                                   VALUES (:job_id, :applicant_id, :cover_letter, 'Pending')");
            $stmt->bindParam(':job_id', $job_id);
            $stmt->bindParam(':applicant_id', $applicant_id);
            $stmt->bindParam(':cover_letter', $cover_letter);

            if ($stmt->execute()) {
                $application_id = $pdo->lastInsertId(); 

                $pdf_name = basename($resume_name);  
                $user_type = 'Applicant';  
                $description = $cover_letter;  

                $stmt = $pdo->prepare("INSERT INTO pdf_uploads (application_id, file_path, pdf_name, user_id, user_type, description) 
                                       VALUES (:application_id, :file_path, :pdf_name, :user_id, :user_type, :description)");
                $stmt->bindParam(':application_id', $application_id);
                $stmt->bindParam(':file_path', $resume_path);
                $stmt->bindParam(':pdf_name', $pdf_name);
                $stmt->bindParam(':user_id', $applicant_id);
                $stmt->bindParam(':user_type', $user_type);
                $stmt->bindParam(':description', $description);
                $stmt->execute();

                echo "Application submitted successfully!";
            } else {
                echo "Error applying for the job.";
            }
        } else {
            echo "Error uploading the resume.";
        }
    } else {
        echo "Please upload a resume.";
    }
}
?>
