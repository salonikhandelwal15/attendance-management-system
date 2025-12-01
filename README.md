# 🎓 Attendance Management System

![PHP](https://img.shields.io/badge/PHP-7.4-blue?logo=php&logoColor=white) 
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql&logoColor=white) 
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple?logo=bootstrap&logoColor=white) 
![HTML5](https://img.shields.io/badge/HTML5-orange?logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-blue?logo=css3&logoColor=white) 
![JavaScript](https://img.shields.io/badge/JavaScript-yellow?logo=javascript&logoColor=black)

A *modern, fully responsive web-based Attendance Management System* built with *PHP, MySQL, and Bootstrap 5*.  
Designed for *Admin, Teacher, and Student* roles with *secure login, role-based access, and attendance tracking*.

---

## ✨ Features

### *Admin 🛠*
- Add and manage *teachers* and *students*
- Create *courses* and *assign subjects*
- Assign *teachers to subjects*
- Full control over system management

### *Teacher 👩‍🏫*
- Create *class sessions*
- Mark *student attendance* (Present/Absent)
- Track participation per subject

### *Student 👨‍🎓*
- View *attendance percentage*
- Rate and give feedback for each class session

---

## 🔐 Security & Authentication
- *Role-based access control* for Admin, Teacher, Student  
- *Secure password storage* using *bcrypt hashing*  
- Authorization ensures *data privacy and integrity*

---

## 🖥 Technology Stack

| Frontend | Backend | Database |
|----------|---------|---------|
| HTML5, CSS3, JavaScript, Bootstrap 5 | PHP | MySQL |

---

## 📊 Database Structure

The system includes:

- *courses* – Store course info  
- *users* – Admin, Teacher, Student data  
- *subjects* – Subject details with assigned teacher  
- *course_subjects* – Map subjects to courses  
- *enrollments* – Map students to subjects  
- *class_sessions* – Store session info  
- *attendance* – Record attendance status  
- *ratings* – Student feedback for sessions  

