-- Create database

CREATE DATABASE IF NOT EXISTS bookstore;

-- Select database

use bookstore;

-- Create tables

AI AI AI 

CREATE TABLE Employee (
    id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE Student (
    id INT NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE Department (
    id VARCHAR(5) NOT NULL,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE Permission (
    id VARCHAR(100) NOT NULL,
    description VARCHAR(1000),
    PRIMARY KEY (id)
);

CREATE TABLE Product (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description VARCHAR(1000) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(8, 2) NOT NULL,
    enabled BOOL NOT NULL DEFAULT FALSE,
    author VARCHAR(200) DEFAULT '',
    isbn VARCHAR(13) DEFAULT '',
    PRIMARY KEY (id)
) AUTO_INCREMENT=8;

CREATE TABLE StoreOrder (
    id INT NOT NULL AUTO_INCREMENT,
    datestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM ('Ordered', 'Paid', 'Shipped', 'Canceled') NOT NULL,
    student_id INT DEFAULT NULL,
    employee_id INT DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT StoreOrder_fk_1 FOREIGN KEY (student_id)
        REFERENCES Student (id),
    CONSTRAINT StoreOrder_fk_2 FOREIGN KEY (employee_id)
        REFERENCES Employee (id)
) AUTO_INCREMENT=37;

CREATE TABLE Course (
    department_id VARCHAR(5) NOT NULL,
    id VARCHAR(4) NOT NULL,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (department_id, id),
    CONSTRAINT Course_fk_1 FOREIGN KEY (department_id)
        REFERENCES Department (id)
        ON DELETE CASCADE
);

CREATE TABLE CourseSection (
    department_id VARCHAR(5) NOT NULL,
    course_id VARCHAR(4) NOT NULL,
    section_id VARCHAR(2) NOT NULL,
    PRIMARY KEY (department_id, course_id, section_id),
    CONSTRAINT CourseSection_fk_1 FOREIGN KEY (department_id, course_id)
            REFERENCES Course (department_id, id) ON DELETE CASCADE
);

CREATE TABLE Acl (
    employee_id INT NOT NULL,
    permission_id VARCHAR(100) NOT NULL,
    PRIMARY KEY (employee_id, permission_id),
    CONSTRAINT Acl_fk_1 FOREIGN KEY (employee_id)
        REFERENCES Employee (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT Acl_fk_2 FOREIGN KEY (permission_id)
        REFERENCES Permission (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Enrollment (
    student_id INT NOT NULL,
    department_id VARCHAR(5) NOT NULL,
    course_id VARCHAR(4) NOT NULL,
    section_id VARCHAR(2) NOT NULL,
    PRIMARY KEY (student_id, department_id, course_id, section_id),
    CONSTRAINT Enrollment_fk_1 FOREIGN KEY (student_id)
        REFERENCES Student (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT Enrollment_fk_2 FOREIGN KEY (department_id, course_id,
            section_id)
        REFERENCES CourseSection (department_id, course_id,
            section_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE OrderContents (
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(8, 2) NOT NULL,
    quantity INT NOT NULL,
    returned INT NOT NULL,
    PRIMARY KEY (order_id, product_id),
    CONSTRAINT OrderContents_fk_1 FOREIGN KEY (order_id)
        REFERENCES StoreOrder (id),
    CONSTRAINT OrderContents_fk_2 FOREIGN KEY (product_id)
        REFERENCES Product (id)
);

CREATE TABLE CourseRequirements (
    department_id VARCHAR(5) NOT NULL
        REFERENCES CourseSection (department_id),
    course_id VARCHAR(4) NOT NULL REFERENCES CourseSection (course_id),
    section_id VARCHAR(2) NOT NULL REFERENCES CourseSection (section_id),
    product_id INT NOT NULL REFERENCES Product (id),
    PRIMARY KEY (department_id, course_id, section_id, product_id),
    CONSTRAINT CourseRequirements_fk_1 FOREIGN KEY (department_id,
            course_id, section_id)
        REFERENCES CourseSection (department_id, course_id,
                section_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT CourseRequirements_fk_2 FOREIGN KEY (product_id)
        REFERENCES Product (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Category (
    name VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    PRIMARY KEY (name, product_id),
    CONSTRAINT ProductCategories_fk_1 FOREIGN KEY (product_id)
        REFERENCES Product (id) ON DELETE CASCADE
);
