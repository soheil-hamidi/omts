# About OMTS

This is a demo app for Online Movie Ticket Service (OMTS), which is an application for the advance purchase of movie tickets from any local theatre. Customers use the service to find out information about movies currently playing in their city and to order advance tickets for specific showings of the movies. Your task is to provide the database and related functionality for this application.

# User’s guide

### For developers:

- Download and run XAMPP
- Download/Clone the app from github
- Depending on your Operating System change XAMPP’s starting location ([guide](https://stackoverflow.com/questions/8847392/how-to-change-xampp-localhost-to-another-folder-outside-xampp-folder))
- Go to http://localhost/phpmyadmin/
- Go to SQL tab and copy - paste and run the content of the omts.ddl
- Open http://localhost/ and you should be able to see the app
- The code is commented properly and the variable names are logical so readability and understanding the logic should be easy.

### For Admins:
- With admin access you have all the privileges of a normal user and also access to admin dashboard.
- On admin dashboard you will be able to see some statistics for most popular movie and theatre.
- Managing members by deleting or checking their purchase history.
- Adding or updating theatre complexes.
- Adding movies.
- And managing shows.

### For Users:
- Users can sign up.
- Log in to the app.
- Under profile tab, they can update their info, also check or cancel their purchases.
- On the homepage they can browse movies, buy tickets and read or leave a review.

# List of assumptions:

1.  For screening table the weak entity relationship is defined as three unique key and screening_id as a primary key. This decision was made to easily access screening data.

2.  There are two roles: admin and user and it is defined in app-user table as role.

3.  Users can review a movie only once.

4.  Passwords are not hashed for demo purposes.

# Technologies and tools used in developing the application.

**Languages:** PHP, SQL, HTML, CSS, JavaScript

**Frameworks:** Bootstrap, Bootstrap-select

**Others:** XAMPP, Git
