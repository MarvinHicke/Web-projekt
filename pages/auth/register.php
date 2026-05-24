<?php
$pageTitle = "Registrieren";
require_once __DIR__ . '/../../includes/header.php';
?>

<h1>Registrieren</h1>
<p>Erstellen Sie ein neues Konto</p>

<form method="Post">
    <div>
        <label for="firstName">Vorname</label>
        <input type="text" id="firstName" name="firstName" required maxlength="50">
    </div>

    <div>
        <label for="lastName">Nachname</label>
        <input type="text" id="lastName" name="lastName" required maxlength="50">
    </div>

    <div>
        <label for="email">E-Mail</label>
        <input type="email" id="email" name="email" required maxlength="100">
    </div>

    <div>
        <label for="password">Passwort</label>
        <input type="password" id="password" name="password" required minlength="8">
    </div>

    <div>
        <label for="confirmPassword">Passwort bestätigen</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required minlength="8">
    </div>

    <button type="submit">Registrieren</button>
</form>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>

