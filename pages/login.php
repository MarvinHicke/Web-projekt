<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';
$pageTitle = "Anmelden";
$errors=[];
$successMessage="";
$email="";

if(($_SERVER["REQUEST_METHOD"] ?? "GET") === "POST")
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
        $customerRepository = new customerRepository(db());
        $user = $customerRepository->GetByUsername($email);

        $passwordIsValid = false;

        if ($user && (int)($user['State'] ?? 0) === 1) {
            $storedPassword = (string)($user['Pass'] ?? '');
            $passwordInfo = password_get_info($storedPassword);

            if (($passwordInfo['algo'] ?? 0) !== 0) {
                $passwordIsValid = password_verify($password, $storedPassword);
            } else {
                $passwordIsValid = hash_equals($storedPassword, $password);
            }
        }

        if (!$user || (int)($user['State'] ?? 0) !== 1 || !$passwordIsValid) {
            $errors[] = "E-Mail oder Passwort ist falsch.";
        } else {
            $_SESSION['user'] = [
                    'CustomerID' => (int)$user['CustomerID'],
                    'UserName' => (string)($user['UserName'] ?? $user['Email'] ?? $email),
                    'Type' => (int)$user['Type'],
                    'FirstName' => (string)($user['FirstName'] ?? ''),
                    'LastName' => (string)($user['LastName'] ?? ''),
            ];

            header('Location: ' . base_url('index.php'));
            exit;
        }
    }
}
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-heading">
    <h1>Anmelden</h1>
    <p>Melden Sie sich mit Ihrer E-Mail und Ihrem Passwort an.</p>
</section>

<section class="data-box auth-panel">
    <?php foreach ($errors as $error): ?>
        <?php
        $alertType = "danger";
        $alertMessage = $error;
        include __DIR__ . "/../components/alert-box.php";
        ?>
    <?php endforeach; ?>

    <form method="POST" class="auth-form">
        <div class="mb-3">
            <label for="email" class="form-label">E-Mail</label>
            <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    required
                    maxlength="100"
                    value="<?= e($email) ?>"
            >
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Passwort</label>
            <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    minlength="8"
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Anmelden
        </button>
    </form>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
