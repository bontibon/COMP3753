<?php
require 'shared.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = isset($_POST['course']) ? $_POST['course'] : '';
    $course = explode(".", $course);
    $course = array_map(function($piece) {
        return preg_replace('/[^\w-]/', '', $piece);
    }, $course);

    if (count($course) == 1) {
        header('Location: /course-viewer/' . $course[0]);
        die;
    } else if (count($course) == 3) {
        header('Location: /course-viewer/' . $course[0] . '/'
            . $course[1] . '/' . $course[2] . '/');
        die;
    }
}

$tpl = new RainTpl();

$query = $pdo->prepare('
SELECT CourseSection.section_id AS section_id, Course.id AS course_id,
    Course.name AS course_name, Department.id AS department_id,
    Department.name AS department_name,
    CONCAT(Department.id, ".", Course.id, ".", CourseSection.section_id) AS uid
FROM CourseSection
LEFT JOIN Course
    ON Course.department_id = CourseSection.department_id
        AND Course.id = CourseSection.course_id
LEFT JOIN Department
    ON Department.id = CourseSection.department_id');
$query->execute();

$departments = array();
while ($obj = $query->fetchObject()) {
    if (!array_key_exists($obj->department_id, $departments)) {
        $departments[$obj->department_id] = new stdClass();
        $departments[$obj->department_id]->name = $obj->department_name;
        $departments[$obj->department_id]->sections = array();
    }
    $departments[$obj->department_id]->sections[] = $obj;
}

$d = isset($_GET['department']) ? $_GET['department'] : '';
$c = isset($_GET['course']) ? $_GET['course'] : '';
$s = isset($_GET['section']) ? $_GET['section'] : '';
$e = isset($_GET['extra']) ? $_GET['extra'] : '';

if (!empty($d) && !empty($c) && !empty($s)) {
    $query = $pdo->prepare('
        SELECT Product.id AS id, Product.name AS name
        FROM CourseRequirements
        LEFT JOIN Product
            ON Product.id = CourseRequirements.product_id
        WHERE department_id = ? AND course_id = ? AND section_id = ?');
    $query->execute(array($d, $c, $s));

    $tpl->assign('requirements', $query->fetchAll(PDO::FETCH_OBJ));
} else if ($e == 'my-courses' && !empty($shared->studentId)) {
    $query = $pdo->prepare('
        SELECT
            CONCAT(Enrollment.department_id, " ", Enrollment.course_id, " ",
                   Enrollment.section_id) as course,
            Product.id AS id, Product.name AS name
        FROM Enrollment
        RIGHT JOIN CourseRequirements
            ON Enrollment.department_id = CourseRequirements.department_id AND
                Enrollment.course_id = CourseRequirements.course_id AND
                Enrollment.section_id = CourseRequirements.section_id
        LEFT JOIN Product
            ON Product.id = CourseRequirements.product_id
        WHERE Enrollment.student_id = ?');
    $query->execute(array($shared->studentId));

    $tpl->assign('requirements', $query->fetchAll(PDO::FETCH_OBJ));
}


if (empty($e)) {
    $tpl->assign('current', sprintf('%s.%s.%s', $d, $c, $s));
} else {
    $tpl->assign('current', $e);
}
$tpl->assign('departments', $departments);
$tpl->assign('shared', $shared);
$tpl->draw("course-viewer");
