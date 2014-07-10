# COMP 3753 - Project Report

## Overview

The goal of the project was to create an online university bookstore system, the details of which can be found in the attached project description. This report will describe how the system's database is configured, how the front end is implemented, and some of the decisions made when the system was being designed.

## Assumptions

The following assumptions were made about the system in its design:

- Since this is a campus bookstore, customers need to have a student ID number in order to purchase items
- We do not need to store information about employee payroll
- The description of a course does not need to be stored, only its name (if someone wants this information, they can ask the registrar)
- The author of a book will be stored as a single attribute, whose value is what is listed on the book's cover
- Only a student's ID number and what courses he/she is taking is the information we need to store about them in the database
- There is only a single campus store output, and orders are fulfilled from this outlet.  This prevents the case where one outlet has to product in stock while another does not
- The system does not handle or log the exchange of money. This is done using the cash register at the store
- The database administrator will load in the student, enrollment, and course information at the beginning of the school semesters into our bookstore database for us

# Database schema

Below is an overview of the database schema and brief descriptions of what is the purpose of each table.

    Product   id, name, description, quantity, price, enabled, author, isbn
    Category  name, product_id

The product table stores all of the items that are available for purchase from the bookstore.  All products have a unique ID that is used for foreign key references. Products also have a name, description, quantity, and price. Enabled is a boolean value that sets whether a item is visible or not. Items can be added into the system but have their enabled flag turned off so users of the site will not be able to see or purchase them. Items can also have an optional author or ISBN field, which is used for book items.

Products can be placed into categories, which helps users of the site find particular items (e.g. all computer science text books could be in the category "computer-science").

    StoreOrder      id, datestamp, status, student_id, employee_id
    OrderContents   order_id, product_id, price, quantity, returned

Orders made online or in the campus outlet will be stored in the StoreOrder table. Each order has a unique ID and a datestamp.  There is also a status enum, which can be set to: Ordered, the user has placed an order but has not paid for for it; Paid, payment has been made in a campus store outlet; Shipped, the user has paid for the order and is in their possession; or Canceled, the order has been stopped by the user before it has been shipped (an order's status cannot be changed when it has been shipped or canceled). An order also stores the student ID of the user who placed an order, as well as an optional employee ID, if the order was placed in-store.

Each order has one or more products associated with it, and that is stored in the OrderContents table. The order ID and product ID are both stored, as well as the price the user paid for the item.  The price is stored in case the user returns an item and the price now is different than what the user originally paid. The quantity of the item that the user bought is also stored, as well as how many of the item the user returned.

    Employee      id, name, password
    Permission    id, description
    Acl           employee_id, permission_id

Every employee in the system has an entry in the Employee table.  Their unique employee is stored, along with their name and encrypted password.

Every administrative action in the system is locked with a particular permission name.  If an employee wants to perform this action when logged in, he/she must have the corresponding permission ID stored along with their employee ID in the Acl table (e.g. the "change-password" permission allows employees to change their own password).


    Department            id, name
    Course                department_id, id, name
    CourseSection         department_id, course_id, section_id
    CourseRequirements    department_id, course_id, section_id, product_id

In order to satisfy the requirement of viewing products that are required by a particular course, a list of the departments, courses, course sections, and course requirements can be imported into the database.

    Student       id
    Enrollment    student_id, department_id, course_id, section_id

Since this is a campus bookstore, the decision was made to only allow students of the university to purchase products from the university.  This allows for orders to be traced back to a person, which is helpful when orders need to be paid. A list of students as well as the courses that they are enrolled in can be imported into the database as needed.

## Website implementation

The server-side code for the system  is written in PHP, with the page templates being HTML  with CSS style sheets. The PHP code uses the built-in PDO library for database abstraction, meaning the database could be managed by any of PDO's supported DBMS systems, however, the system has only been tested using MariaDB (MySQL). PDO's prepared statements were also used when running SQL queries, which automatically escapes any SQL special characters and prevents someone from performing an SQL injection attack.

The website uses Apache's mod_rewrite via `.htaccess` files to transform the pages of the system to  clean URLs. This changes the URLs from something like `example.com/search.php?query=usb` to `example.com/search/usb`.


A templating engine called *raintpl* is used to provide templating for the pages.  This allows for lightweight logic to be implemented inside of HTML pages without the use of PHP tags, allowing for variables to be printed, along with loops and conditions. When a page of the system is requested, a new template object is created, and key-value pairs are assigned to it.  The page is then rendered given a template name, and the assigned variables are expanded inside of the template.

In order to reduce the amount of page styling that had to be done, the CSS framework *Pure* was used. This reduced the amount of boilerplate CSS that had to be written to style the page. This framework provides a set of CSS classes that can be used to quickly layout a page with responsive capabilities.  The framework also provides styles for menus, buttons, and tables.

## Website guide

The following table lists the pages that are part of the web application, any permissions needed to use the page, along with a description of the page's purpose.

All pages have the same global template, often with only the middle content area changing between pages.  The top of the page has a link bar with links to some common product categories, the left of the page has a search box to find items in the catalog, and the shopping cart can be seen in the top right area above the content.  There is also a menu underneath the search box which changes depending on what permissions you have, as well as a link in the footer to the employee log in page.

    /

This is the initial page where most users will first visit. The page displays the 8 most recently added items to the store.

    /search/<query>

Search queries can be typed into the search box on the left of every page. The query will look for products whose name, description, author, or ISBN number matches. Searching by category can also be done by putting the category in square brackets (e.g. [clothing]). Only one category can be specified in a query.  The user is taken to the search results page after pressing the search button.  The products that matched the query are listed and are linked to their corresponding product page.

    /product/<id>     Optional permission: edit-product, delete-product

The product page for a product lists the name, description, and price of a product. If supplied, the author and the ISBN number are also stored. The current number of in-stock items the store has is also listed, directly beside the "add to cart" button. The button is disabled if the product is not currently in-stock. A list of the categories the product is tagged under is also shown.

If an employee is logged in and has the required permission, he/she can edit the product's properties.  The product can also be deleted if it is not referenced from another table (e.g. has been ordered before).

    /cart     Optional permission: checkout

The cart page displays a list of the items that have been added to the pending order, showing the product name, quantity, unit price, and total price. If too many of one item has been added to the cart, the X beside each product can be clicked to reduce the quantity of that item by one. To clear the entire pending order, pressing the empty cart button will remove all items.

Orders cannot be placed if the student has not logged in to their account.  Pressing the confirm order button will attempt to submit the order, but will fail if there is not enough of a particular product. The user will be redirected to the order history page which gives them an overview of their order and its current status. Refreshing this page will show and changes to the order.

If an employee is logged in and has the required permission, he/she can place an order on behalf of a student.  This is where an transaction would be done completely in-store and not online.

    /order-history
    /order-history/<id>     Optional permission: past-orders

The order history page displays a logged-in student a list of his/her orders. The list displays the date, the number of items in the order, the order status, and a link to the order details page. The order details page displays an itemized list of products that were in the given order, including the price that the user paid for the item (in case the price of a product has changed since time of purchase).

If an employee is logged in and has the required permission, he/she can change the status of the order, and which employee rang the order up. In addition to that, if a user comes back and wished to return some of the items in his/her order, the employee can mark any or all of the items as returned.

    /course-viewer
    /course-viewer/my-courses

The course viewer page displays lists of items that are required by a particular course. A drop down menu lists all of the courses offered by the university, along with the different sections. When a course is selected, a list of the products that are required for the course are shown to the user. The user can check the boxes beside each item then click the "add to cart" button to add all of the selected items to his/her cart.

If a student is logged in, they can select the "My Courses" item from the drop down list.  This will generate a list of required products for all of the courses the student is enrolled in.

    /sso
    /sso/logout

To utilise the single sign on service that most universities already have in place, the SSO page replicates how such a service would operate. Note, since we do not have access to the university database, this is just a dummy page that does not confirm that the student id-password pair is valid.

    /admin/login      Permission required: login

Employees log in to their accounts using this page.  They must have the permission "login" in order for the authentication to be successful.  This allows for the administrator to disable but not delete an employee account.

    /admin/change-password      Permission required: change-password

The employee can change his/her own password using the form on this page.  He/she must type in their existing password, followed by a new password and confirmation of the new password.

    /admin/acl      Permission required: edit-acl
    
The access control list (ACL) can be changed using the form on this page.  Each user is listed, along with a list of their permissions.  Permissions, which are described underneath the list, can be added and removed for a particular user. An employee needs to log out then back in in order for the change to take effect.

    /admin/orders       Permission required: past-orders
    
A full list of the orders placed is displayed on this page, along with the datestamp, number of items in the order, and the order status. When a user comes to the store outlet to pay or pickup their order, the employee will find the user's order from the list and change its status. This is also used when a user wishes to return a purchased item.

    /admin/download-dump      Permission required: download-dump

A comma separated value (CSV) dump of the database can be downloaded from this page. Only the products, orders, order contents, and categories will be included in this dump.
