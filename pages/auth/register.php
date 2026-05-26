<?php
$pageTitle = "Registrieren";
$errors=[];
$successMessage="";
$firstName="";
$lastName="";
$email="";
$nameMaxLength=50;
$emailMaxLength=100;
$passwordMinLength=8;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName=trim((string) ($_POST["firstName"] ?? ""));
    $lastName=trim((string) ($_POST["lastName"] ?? ""));
    $email=trim((string) ($_POST["email"] ?? ""));
    $password= (string) ($_POST["password"] ?? "");
    $confirmPassword= (string) ($_POST["confirmPassword"] ?? "");

    if($firstName==="")
    {
        $errors[]="Bitte geben sie Ihren Vornamen ein";
    }
    if(strlen($firstName)>$nameMaxLength)
    {
        $errors[]="Der Vornamen darf maximal " . $nameMaxLength . " Zeichen lang sein";
    }
    if($lastName==="")
    {
        $errors[]="Bitte geben sie Ihren Nachnamen ein";
    }
    if(strlen($lastName)>$nameMaxLength)
    {
        $errors[]="Der Nachname darf maximal " . $nameMaxLength . " Zeichen lang sein";
    }
    if($email==="")
    {
        $errors[]="Bitte geben sie Ihre E-Mail ein";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors[]="Bitte geben sie eine gültige E-Mail ein";
    }
    if(strlen($email)>$emailMaxLength)
    {
        $errors[]="Die E-Mail darf maximal " . $emailMaxLength . " Zeichen lang sein";
    }
    if($password==="")
    {
        $errors[]="Bitte geben sie ein Passwort ein";
    }
    elseif(strlen($password)<$passwordMinLength)
    {
        $errors[]="Das Passwort muss mindestens " . $passwordMinLength . " Zeichen lang sein";
    }
    if($confirmPassword==="")
    {
        $errors[]="Bitte bestätigen Sie Ihr Passwort";
    }
    elseif($password!==$confirmPassword)
    {
        $errors[]="Die Passwörter stimmen nicht überein";
    }
    if(empty($errors))
    {
        $passwordHash=password_hash($password,PASSWORD_DEFAULT);

        $successMessage="Die Eingaben sind gültig. Das Paswort kann sicher gespeichert werden.";
    }
}
require_once __DIR__ . "/../../includes/header.php";
?>

<h1>Registrieren</h1>
<p>Erstellen Sie ein neues Konto</p>

<?php foreach ($errors as $error): ?>
    <?php
    $alertType = "danger";
    $alertMessage = $error;
    include __DIR__ . "/../../components/alert-box.php";
    ?>
<?php endforeach; ?>

<?php if ($successMessage !== ""): ?>
    <?php
    $alertType = "success";
    $alertMessage = $successMessage;
    include __DIR__ . "/../../components/alert-box.php";
    ?>
<?php endif; ?>

<form method="POST" novalidate>
    <div>
        <label for="firstName">Vorname</label>
        <input
                type="text"
                id="firstName"
                name="firstName"
                required
                maxlength="<?= $nameMaxLength ?>"
                value="<?= e($firstName) ?>"
        >
    </div>

    <div>
        <label for="lastName">Nachname</label>
        <input
                type="text"
                id="lastName"
                name="lastName"
                required
                maxlength="<?= $nameMaxLength ?>"
                value="<?= e($lastName) ?>"
        >
    </div>

    <div>
        <label for="email">E-Mail</label>
        <input
               type="email"
               id="email"
               name="email"
               required
               maxlength="<?= $emailMaxLength ?>"
               value="<?= e($email) ?>"
        >
    </div>

    <div>
        <label for="password">Passwort</label>
        <input
                type="password"
                id="password"
                name="password"
                required
                minlength="<?= $passwordMinLength ?>"
        >
    </div>

    <div>
        <label for="confirmPassword">Passwort bestätigen</label>
        <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                required
                minlength="<?= $passwordMinLength ?>"
        >
    </div>

    <button type="submit">Registrieren</button>
</form>

<?php
require_once __DIR__ . "/../../includes/footer.php";
?>

