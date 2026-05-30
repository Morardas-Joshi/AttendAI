# AttendAI

AI-powered Smart Attendance Management System built using Laravel + Python Face Recognition.

---

# GitHub Repository

Repository Link:

https://github.com/Morardas-Joshi/AttendAI

---

# Features

* AI Face Recognition Attendance
* Student Dashboard
* Faculty Dashboard
* Attendance Tracking
* Session Management
* Face Registration System
* Authentication & Authorization
* Real-Time Attendance Monitoring

---

# Tech Stack

## Backend

* Laravel
* PHP
* MySQL

## AI Service

* Python
* OpenCV
* face_recognition
* dlib

## Frontend

* Blade Templates
* Tailwind CSS
* Vite

---

# System Requirements

Before running the project install:

* PHP >= 8.x
* Composer
* Node.js & npm
* Python 3.10+
* XAMPP / MySQL

---

# Installation Guide

## 1. Clone Repository

```bash
git clone https://github.com/Morardas-Joshi/AttendAI.git
cd AttendAI
```

---

# Laravel Setup

## 2. Install PHP Dependencies

```bash
composer install
```

---

## 3. Install Node Modules

```bash
npm install
```

---

## 4. Create Environment File

```bash
copy .env.example .env
```

---

## 5. Generate Laravel Key

```bash
php artisan key:generate
```

---

# Database Setup

## 6. Create Database

Create a MySQL database named:

```text
attendai
```

---

## 7. Configure Database Credentials

Open `.env` file and update:

```env
DB_DATABASE=attendai
DB_USERNAME=root
DB_PASSWORD=
```

---

## 8. Run Migrations

```bash
php artisan migrate
```

---

# Python AI Service Setup

## 9. Open AI Service Folder

```bash
cd ai-service
```

---

## 10. Create Virtual Environment

```bash
python -m venv venv
```

---

## 11. Activate Virtual Environment

### Windows

```bash
.\venv\Scripts\activate
```

---

## 12. Install Python Dependencies

```bash
pip install -r requirements.txt
```

---

# AI Model Setup

Download the file:

```text
shape_predictor_68_face_landmarks.dat
```

Place it inside:

```text
ai-service/
```

---

# Running the Project

## Start Laravel Server

```bash
php artisan serve
```

---

## Start Vite Development Server

Open another terminal:

```bash
npm run dev
```

---

## Start Python AI Service

Open another terminal:

```bash
cd ai-service
python app.py
```

---

# Default URLs

Laravel Application:

```text
http://127.0.0.1:8000
```

Vite Frontend:

```text
http://127.0.0.1:5173
```

---

# Important Notes

* Do NOT upload `venv/`
* Do NOT upload `node_modules/`
* Do NOT upload `vendor/`
* AI model files are ignored due to GitHub file-size limitations

---

# Future Improvements

* Cloud Deployment
* Mobile Application
* Multi-Camera Support
* Real-Time Analytics
* Attendance Reports
* AI Performance Optimization

---

# Author

Morardas Joshi

GitHub:
https://github.com/Morardas-Joshi
