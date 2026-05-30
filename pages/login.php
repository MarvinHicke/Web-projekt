<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once 'includes/init.php';
$pageTitle = "Anmelden";
$errors=[];
$successMessage="";
$email="";

if($_SERVER["REQUEST_METHOD"] === "POST")
{
    $email=trim((string) ($_POST["email"] ?? ""));
    $password= (string) ($_POST["password"] ?? "");

    if($email==="")
    {
        $errors[]="Bitte geben sie Ihre E-Mail ein";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors[]="Bitte geben sie eine gültige E-Mail ein";
    }
    if($password==="")
    {
        $errors[]="Bitte geben sie ein Passwort ein";
    }
    if(empty($errors))
    {
        /* Später:
        * 1. User anhand der E-Mail aus der Datenbank laden.
        * 2. Gespeicherten Passwort-Hash des Users holen.
        * 3. Eingegebenes Passwort mit password_verify() prüfen.
        * 4. Bei Erfolg User-ID und Rolle in der Session speichern.
        */
        $successMessage="Die Eingaben sind gültig. Die echte Anmeldung folgt später.";
    }
}
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Anmelden</h1>
<p>Melden Sie sich mit Ihrer E-Mail und Ihrem Passwort an</p>

<?php foreach ($errors as $error): ?>
    <?php
    $alertType = "danger";
    $alertMessage = $error;
    include __DIR__ . "/../components/alert-box.php";
    ?>
<?php endforeach; ?>

<?php if ($successMessage !== ""): ?>
    <?php
    $alertType = "success";
    $alertMessage = $successMessage;
    include __DIR__ . "/../components/alert-box.php";
    ?>
<?php endif; ?>

<form method="POST" novalidate>
    <div>
        <label for="email">E-Mail</label>
        <input
                type="email"
                id="email"
                name="email"
                required
                maxlength="100"
                value="<?= e($email) ?>"
        >
    </div>

    <div>
        <label for="password">Passwort</label>
        <input type="password" id="password" name="password" required minlength="8">
    </div>

    <button type="submit">Login</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
