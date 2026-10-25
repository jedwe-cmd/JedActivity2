<?php
$courses = [
    'Computer Science',
    'Information Technology',
    'Business Administration',
    'Engineering',
    'Education',
];

$sessions = [
    'Web Development',
    'Artificial Intelligence',
    'Cybersecurity',
    'Game Development',
];

$hobbyOptions = [
    'Reading',
    'Music',
    'Sports',
    'Gaming',
    'Photography',
    'Travel',
];

$name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
$age = isset($_POST['age']) && is_string($_POST['age']) ? trim($_POST['age']) : '';
$course = isset($_POST['course']) && is_string($_POST['course']) ? trim($_POST['course']) : '';
$session = isset($_POST['session']) && is_string($_POST['session']) ? trim($_POST['session']) : '';
$submittedHobbies = isset($_POST['hobbies']) && is_array($_POST['hobbies']) ? $_POST['hobbies'] : [];
$hobbies = array_values(array_filter(
    $submittedHobbies,
    static fn ($hobby) => is_string($hobby) && in_array($hobby, $hobbyOptions, true)
));
$errors = [];
$databaseError = '';
$registrationId = null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errors[] = 'Please submit the registration form to continue.';
} else {
    if (!isset($_POST['name']) || !is_string($_POST['name']) || empty(trim($_POST['name']))) {
        $errors[] = 'Name is required.';
    }

    if (!isset($_POST['age']) || !is_string($_POST['age']) || empty(trim($_POST['age']))) {
        $errors[] = 'Age is required.';
    } elseif (!ctype_digit(trim($_POST['age'])) || (int) trim($_POST['age']) < 1 || (int) trim($_POST['age']) > 120) {
        $errors[] = 'Age must be a number between 1 and 120.';
    }

    if (!isset($_POST['course']) || !is_string($_POST['course']) || empty(trim($_POST['course']))) {
        $errors[] = 'Course is required.';
    } elseif (!in_array($course, $courses, true)) {
        $errors[] = 'Please choose a valid course.';
    }

    if (!isset($_POST['session']) || !is_string($_POST['session']) || empty(trim($_POST['session']))) {
        $errors[] = 'Workshop track is required.';
    } elseif (!in_array($session, $sessions, true)) {
        $errors[] = 'Please choose a valid workshop track.';
    }

    if (!isset($_POST['hobbies']) || empty($_POST['hobbies']) || $hobbies === []) {
        $errors[] = 'Please select at least one hobby.';
    }
}

if ($errors === []) {
    try {
        require __DIR__ . '/database.php';
        $statement = $pdo->prepare(
            'INSERT INTO registrations (name, age, course, workshop_track, hobbies)
             VALUES (:name, :age, :course, :workshop_track, :hobbies)'
        );
        $statement->execute([
            'name' => $name,
            'age' => (int) $age,
            'course' => $course,
            'workshop_track' => $session,
            'hobbies' => json_encode($hobbies, JSON_THROW_ON_ERROR),
        ]);
        $registrationId = (int) $pdo->lastInsertId();
    } catch (PDOException $exception) {
        error_log('TechFest registration database error: ' . $exception->getMessage());
        $databaseError = 'We could not save your registration. Make sure MySQL is running and the database has been set up, then try again.';
    } catch (JsonException $exception) {
        error_log('TechFest registration encoding error: ' . $exception->getMessage());
        $databaseError = 'We could not process your registration. Please try again.';
    }
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechFest 2026 Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="card result-card <?= $errors !== [] || $databaseError !== '' ? 'error-card' : 'ticket' ?>" aria-labelledby="result-title">
            <?php if ($errors !== [] || $databaseError !== ''): ?>
                <div class="ticket-top error-top">
                    <p class="ticket-kicker">TECHFEST 2026</p>
                    <h2 id="result-title">Registration needs attention</h2>
                </div>
                <div class="ticket-divider"></div>
                <div class="error-content" role="alert">
                    <p>Please correct the following:</p>
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li class="error-tag"><?= escape($error) ?></li>
                        <?php endforeach; ?>
                        <?php if ($databaseError !== ''): ?>
                            <li class="error-tag"><?= escape($databaseError) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="ticket-top" role="status">
                    <p class="ticket-kicker">ADMIT ONE · TECHFEST 2026</p>
                    <h2 id="result-title">You're registered!</h2>
                    <p>Your registration has been saved.</p>
                    <p class="ticket-number">Registration #<?= escape((string) $registrationId) ?></p>
                </div>
                <div class="ticket-divider"></div>
                <div class="ticket-details">
                <dl class="summary">
                    <div>
                        <dt>Name</dt>
                        <dd><?= escape($name) ?></dd>
                    </div>
                    <div>
                        <dt>Age</dt>
                        <dd><?= escape($age) ?></dd>
                    </div>
                    <div>
                        <dt>Course</dt>
                        <dd><?= escape($course) ?></dd>
                    </div>
                    <div>
                        <dt>Workshop track</dt>
                        <dd><?= escape($session) ?></dd>
                    </div>
                    <div>
                        <dt>Hobbies</dt>
                        <dd><?= escape(implode(', ', $hobbies)) ?></dd>
                    </div>
                </dl>
                </div>
            <?php endif; ?>

            <a class="back-link" href="index.php">Return to registration form</a>
        </section>
    </main>
</body>
</html>
