CREATE TABLE hr_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    resume VARCHAR(255),  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    company VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    salary DECIMAL(10, 2),
    posted_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES hr_users(id) ON DELETE SET NULL
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_id INT NOT NULL,
    cover_letter TEXT,
    status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
);

CREATE TABLE pdf_uploads (
    pdf_id INT AUTO_INCREMENT PRIMARY KEY,
    pdf_name VARCHAR(255) NOT NULL,  
    user_id INT NOT NULL,              
    user_type ENUM('Applicant', 'HR') NOT NULL,  
    description VARCHAR(255),          
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255) NOT NULL,   
    application_id INT,
    FOREIGN KEY (user_id) REFERENCES applicants(id) ON DELETE CASCADE
);


CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES applicants(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES hr_users(id) ON DELETE CASCADE
);
