# 📱 My Contact List

A simple **Contact List web application** built with PHP and MySQL.
This project allows users to add, view, edit, and delete contacts through a clean Bootstrap interface.

## ✨ Features

* ➕ Add new contacts
* 📋 View all saved contacts
* ✏️ Edit contact information (WIP)
* 🗑️ Delete individual contacts
* 🧹 Delete all contacts
* 📄 Download the contact list as a PDF
* 🔢 Display the total number of contacts
* 💾 Store contact information in a MySQL database
* 📱 Responsive interface using Bootstrap

## 🛠️ Technologies Used
* **PHPStorm** – IDE Used for development
* **PHP** – Backend and server-side logic
* **MySQL** – Database for storing contacts
* **HTML5** – Page structure
* **CSS3** – Custom styling
* **Bootstrap 5** – Responsive UI components
* **JavaScript** – Client-side interactions and PDF generation
* **html2pdf.js / jsPDF** – PDF export

## 📂 Project Structure

```text
my-contact-list/
│
├── mylist.php          # Main contact list page
├── style.css           # Custom styles
├── index.php           # Optional landing page
│
└── README.md           # Project documentation
```

## 🗄️ Database

The application uses a MySQL database named:

```text
mycontacts
```

The main table is:

```text
contacts
```

### Contacts Table

| Column       | Description                   |
| ------------ | ----------------------------- |
| Number       | Contact phone number          |
| Name         | Contact's name                |
| Relationship | Relationship with the contact |
| Carrier      | Mobile network/carrier        |

## ⚙️ Setup

### 1. Install a Local Server

Install a local PHP development environment such as **Laragon/XAMPP**.

Make sure these services are running:

* Apache
* MySQL

### 2. Create the Database

Open **phpMyAdmin** and create a database named:

```text
mycontacts
```

Then create the `contacts` table with the required columns.

### 3. Configure the Database Connection

The application currently uses:

```php
$conn = mysqli_connect('localhost', 'root', '', 'mycontacts');
```

If your MySQL configuration is different, update the connection details in `mylist.php`.

### 4. Place the Project in the Server Directory

For XAMPP, place the project inside:

```text
C:\xampp\htdocs\
```

For example:

```text
C:\xampp\htdocs\my-contact-list\
```

### 5. Run the Application

Start Apache and MySQL in XAMPP.

Then open:

```text
http://localhost/my-contact-list/mylist.php
```

## 📄 PDF Download

The application includes a **Download List** feature that generates a PDF version of the contact list.

The action buttons such as **Edit** and **Delete** are excluded from the generated PDF.

## 🔐 Security

The application uses **prepared statements** for database operations involving user input to help prevent SQL injection.

Output displayed on the page is also escaped using:

```php
htmlspecialchars()
```

## 🎯 Purpose

This project was created as a simple PHP and MySQL CRUD application for practicing:

* PHP fundamentals
* MySQL database operations
* CRUD functionality
* Forms and POST requests
* Prepared statements
* Bootstrap
* JavaScript
* PDF generation

## 🚀 Future Improvements

Possible improvements include:

* Add a dedicated `id` primary key for contacts
* Add proper Edit functionality
* Add search and filtering
* Add contact validation
* Add confirmation modals for deletion
* Add pagination
* Add authentication/login
* Improve PDF formatting
* Add profile/contact pictures

## 👤 Author

**crimsoncrows/anjlalxn**
A simple academic/practice project focused on learning PHP, MySQL, and web application development.
Made the day before midterm exam as a practice.