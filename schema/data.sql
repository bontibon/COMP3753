-- Select database

use bookstore;

-- Load CSV files

LOAD DATA LOCAL INFILE 'data/acl.csv' INTO TABLE Acl
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/category.csv' INTO TABLE Category
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";

LOAD DATA LOCAL INFILE 'data/course.csv' INTO TABLE Course
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";

LOAD DATA LOCAL INFILE 'data/courserequirements.csv' INTO TABLE CourseRequirements
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/coursesection.csv' INTO TABLE CourseSection
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/department.csv' INTO TABLE Department
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/employee.csv' INTO TABLE Employee
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/enrollment.csv' INTO TABLE Enrollment
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/ordercontents.csv' INTO TABLE OrderContents
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/permission.csv' INTO TABLE Permission
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/product.csv' INTO TABLE Product
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";


LOAD DATA LOCAL INFILE 'data/storeorder.csv' INTO TABLE StoreOrder
    FIELDS TERMINATED BY ","
    ENCLOSED BY "'"
    ESCAPED BY "\\"
    LINES TERMINATED BY "\n";
