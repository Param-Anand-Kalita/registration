<?php
// The form is intentionally self-contained: no database is required.
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$submitted = false;
$data = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'dob' => '',
    'gender' => '',
    'course' => '',
    'year' => '',
    'address' => '',
    'interests' => [],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['full_name', 'email', 'phone', 'dob', 'gender', 'course', 'year', 'address'] as $field) {
        $data[$field] = trim((string)($_POST[$field] ?? ''));
    }

    $allowedInterests = ['Web Development', 'Programming', 'Artificial Intelligence', 'Cyber Security'];
    $postedInterests = $_POST['interests'] ?? [];
    if (is_array($postedInterests)) {
        $data['interests'] = array_values(array_intersect($allowedInterests, $postedInterests));
    }

    if ($data['full_name'] === '' || strlen($data['full_name']) < 3) {
        $errors[] = 'Enter your full name (at least 3 characters).';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!preg_match('/^[0-9+()\-\s]{10,16}$/', $data['phone'])) {
        $errors[] = 'Enter a valid phone number (10 to 16 characters).';
    }
    if ($data['dob'] === '') {
        $errors[] = 'Select your date of birth.';
    }
    if (!in_array($data['gender'], ['Male', 'Female', 'Prefer not to say', 'Other'], true)) {
        $errors[] = 'Select a gender option.';
    }
    if (!in_array($data['course'], ['Computer Science', 'Information Science', 'Electronics', 'Mechanical', 'Civil', 'Other'], true)) {
        $errors[] = 'Select a course.';
    }
    if (!in_array($data['year'], ['1st Year', '2nd Year', '3rd Year', '4th Year'], true)) {
        $errors[] = 'Select your current year.';
    }
    if ($data['address'] === '' || strlen($data['address']) < 8) {
        $errors[] = 'Enter your address (at least 8 characters).';
    }
    if (!isset($_POST['declaration'])) {
        $errors[] = 'Please confirm the declaration before submitting.';
    }

    if (!$errors) {
        $submitted = true;
    }
}

function old(string $field, array $data): string {
    return e($data[$field] ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php">Student Registration</a>
    </header>

    <main class="page">
        <?php if ($submitted): ?>
            <section class="result" aria-live="polite">
                <h1>Registration received</h1>
                <dl class="result-list">
                    <dt>Full name</dt><dd><?= e($data['full_name']) ?></dd>
                    <dt>Email</dt><dd><?= e($data['email']) ?></dd>
                    <dt>Phone</dt><dd><?= e($data['phone']) ?></dd>
                    <dt>Date of birth</dt><dd><?= e($data['dob']) ?></dd>
                    <dt>Gender</dt><dd><?= e($data['gender']) ?></dd>
                    <dt>Course</dt><dd><?= e($data['course']) ?></dd>
                    <dt>Year</dt><dd><?= e($data['year']) ?></dd>
                    <dt>Interests</dt><dd><?= $data['interests'] ? e(implode(', ', $data['interests'])) : '&mdash;' ?></dd>
                    <dt>Address</dt><dd><?= nl2br(e($data['address'])) ?></dd>
                </dl>
                <p class="actions">
                    <a class="link" href="index.php">Fill in another</a>
                    <button class="button" type="button" onclick="window.print()">Print</button>
                </p>
            </section>
        <?php else: ?>
            <div class="heading-row">
                <h1>Registration</h1>
                <p class="lead">Fields marked * are required.</p>
            </div>
            <div class="layout">
                <aside class="summary">
                    <h2>So far</h2>
                    <dl>
                        <dt>Name</dt><dd id="previewName">&mdash;</dd>
                        <dt>Email</dt><dd id="previewEmail">&mdash;</dd>
                        <dt>Course</dt><dd id="previewCourse">&mdash;</dd>
                        <dt>Year</dt><dd id="previewYear">&mdash;</dd>
                    </dl>
                </aside>

                <div class="form-side">
                    <?php if ($errors): ?>
                        <div class="error-box" role="alert">
                            <strong>A few things need fixing:</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="registrationForm" method="POST" action="index.php" novalidate>
                        <div class="form-grid">
                            <div class="field">
                                <label for="full_name">Full name *</label>
                                <input type="text" id="full_name" name="full_name" value="<?= old('full_name', $data) ?>" required minlength="3" autocomplete="name">
                                <small class="field-error" data-error-for="full_name"></small>
                            </div>
                            <div class="field">
                                <label for="dob">Date of birth *</label>
                                <input type="date" id="dob" name="dob" value="<?= old('dob', $data) ?>" required>
                                <small class="field-error" data-error-for="dob"></small>
                            </div>
                            <div class="field">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= old('email', $data) ?>" required autocomplete="email">
                                <small class="field-error" data-error-for="email"></small>
                            </div>
                            <div class="field">
                                <label for="phone">Phone *</label>
                                <input type="tel" id="phone" name="phone" value="<?= old('phone', $data) ?>" required autocomplete="tel">
                                <small class="field-error" data-error-for="phone"></small>
                            </div>
                            <div class="field field-full">
                                <div class="label-row"><label for="address">Address *</label><small id="addressCount">0 / 250</small></div>
                                <textarea id="address" name="address" rows="3" maxlength="250" required><?= old('address', $data) ?></textarea>
                                <small class="field-error" data-error-for="address"></small>
                            </div>
                            <div class="field">
                                <label for="course">Course *</label>
                                <select id="course" name="course" required>
                                    <option value="">Choose…</option>
                                    <?php foreach (['Computer Science', 'Information Science', 'Electronics', 'Mechanical', 'Civil', 'Other'] as $option): ?>
                                        <option value="<?= e($option) ?>" <?= $data['course'] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="field-error" data-error-for="course"></small>
                            </div>
                            <div class="field">
                                <label for="year">Year *</label>
                                <select id="year" name="year" required>
                                    <option value="">Choose…</option>
                                    <?php foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $option): ?>
                                        <option value="<?= e($option) ?>" <?= $data['year'] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="field-error" data-error-for="year"></small>
                            </div>
                            <div class="field">
                                <label for="gender">Gender *</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Choose…</option>
                                    <?php foreach (['Male', 'Female', 'Prefer not to say', 'Other'] as $option): ?>
                                        <option value="<?= e($option) ?>" <?= $data['gender'] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="field-error" data-error-for="gender"></small>
                            </div>
                            <fieldset class="field field-full interest-field">
                                <legend>Interests</legend>
                                <div class="checkbox-grid">
                                    <?php foreach (['Web Development', 'Programming', 'Artificial Intelligence', 'Cyber Security'] as $interest): ?>
                                        <label class="check-option"><input type="checkbox" name="interests[]" value="<?= e($interest) ?>" <?= in_array($interest, $data['interests'], true) ? 'checked' : '' ?>> <?= e($interest) ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>
                        </div>

                        <div class="submit-row">
                            <label class="declaration"><input type="checkbox" name="declaration" value="yes" required <?= isset($_POST['declaration']) ? 'checked' : '' ?>> I confirm the details above are correct.</label>
                            <button class="button button-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
