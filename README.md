# JDTER Management System

JDTER (Journal Digital Technology Expert Review) is a web-based journal management system developed to manage article submission, reviewer evaluation, and publication processes efficiently.

This project was developed as a Final Year Project (FYP) for Universiti Utara Malaysia (UUM).

---

## Features

### Author Module
- User registration and login
- Upload journal articles
- View submission status

### Editor Module
- Review article format
- Assign reviewers
- Manage publication decisions

### Reviewer Module
- Accept/reject review tasks
- Download assigned articles
- Submit evaluations and comments

### Admin Module
- Manage users and journal records
- Monitor submission activities

---

## Technologies Used

- PHP
- MySQL
- Bootstrap
- JavaScript
- HTML/CSS
- PHPMailer

---

## System Modules

- Authentication System
- Article Submission
- Reviewer Assignment
- Article Evaluation
- Publication Decision

---

## Installation

1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/JDTER.git
```

2. Move project into htdocs

Example for XAMPP:

```bash
C:\xampp\htdocs\JDTER
```

3. Import database

- Open phpMyAdmin
- Create database
- Import the SQL file

4. Configure database connection

Edit:

```bash
config.php
```

5. Run project

```bash
http://localhost/JDTER
```

---

## Project Structure

```bash
JDTER/
│
├── admin/
├── author/
├── reviewer/
├── editor/
├── vendor/
├── assets/
├── database/
└── index.php
```

---

## Future Improvements

- Email notification system
- AI-based article recommendation
- Plagiarism checking
- Better responsive UI

---

## Developer

Developed by Harisyah Adzami  
Bsc.Information Technology  
Universiti Utara Malaysia (UUM)

---

## License

This project is for educational purposes only.