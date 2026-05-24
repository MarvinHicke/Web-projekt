<?php
$pageTitle = "Anmelden";
require_once __DIR__ . '/../../includes/header.php';
?>

<h1>Anmelden</h1>
<p>Melden Sie sich mit Ihrer E-Mail und Ihrem Passwort an</p>

<form method="POST">
    <div>
        <label for="email">E-Mail</label>
        <input type="email" id="email" name="email" required maxlength="100">
    </div>

    <div>
        <label for="password">Passwort</label>
        <input type="password" id="password" name="password" required minlength="8">
    </div>

    <button type="submit">Login</button>
</form>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
