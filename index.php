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
        <section class="card form-card" aria-labelledby="registration-title">
            <h2 id="registration-title">Event registration</h2>
            <p class="intro">Save your place and choose a workshop track.</p>

            <form method="POST" action="process.php">
                <div class="field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" autocomplete="name" required>
                </div>

                <div class="field">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" min="1" max="120" step="1" required>
                </div>

                <div class="field">
                    <label for="course">Course</label>
                    <select id="course" name="course" required>
                        <option value="">Select your course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= htmlspecialchars($course, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($course, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="session">Workshop track</label>
                    <select id="session" name="session" required>
                        <option value="">Select a workshop track</option>
                        <?php foreach ($sessions as $session): ?>
                            <option value="<?= htmlspecialchars($session, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($session, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <fieldset class="field hobby-field">
                    <legend>Hobbies</legend>
                    <div class="hobby-options">
                        <?php foreach ($hobbyOptions as $hobby): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="hobbies[]" value="<?= htmlspecialchars($hobby, ENT_QUOTES, 'UTF-8') ?>">
                                <span><?= htmlspecialchars($hobby, ENT_QUOTES, 'UTF-8') ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <button type="submit">Register for TechFest</button>
            </form>
        </section>
    </main>
</body>
</html>
