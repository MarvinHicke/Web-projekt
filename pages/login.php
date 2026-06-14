<?php
/**
 * Login-Seite für UC22.
 *
 * Prüft aktive Benutzerkonten gegen die customerlogon-Tabelle und unterstützt
 * sowohl gehashte Passwörter als auch alte Klartext-Passwörter aus Seed-Daten.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';

// Seitentitel und Formularstatus vorbereiten.
$pageTitle = "Anmelden";
$errors=[];
$successMessage="";
$email="";

// Loginformular verarbeiten, wenn die Seite per POST aufgerufen wurde.
if(($_SERVER["REQUEST_METHOD"] ?? "GET") === "POST")
{
    // Eingaben aus dem Formular lesen und vorbereiten.
    $email=trim((string) ($_POST["email"] ?? ""));
    $password= (string) ($_POST["password"] ?? "");

    // E-Mail-Adresse validieren.
    if($email==="")
    {
        $errors[]="Bitte geben sie Ihre E-Mail ein";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors[]="Bitte geben sie eine gültige E-Mail ein";
    }

    // Passwortfeld prüfen.
    if($password==="")
    {
        $errors[]="Bitte geben sie ein Passwort ein";
    }

    // Benutzer nur laden, wenn die Formularvalidierung erfolgreich war.
    if(empty($errors))
    {
        $customerRepository = new customerRepository(db());
        $user = $customerRepository->GetByUsername($email);

        // Unterstützt sowohl neu gehashte Passwörter als auch alte Klartext-Passwörter aus Seed-Daten.
        $passwordIsValid = false;

        // Passwort nur prüfen, wenn ein aktiver Benutzer gefunden wurde.
        if ($user && (int)($user['State'] ?? 0) === 1) {
            $storedPassword = (string)($user['Pass'] ?? '');
            $passwordInfo = password_get_info($storedPassword);

            // Je nach gespeicherter Passwortform Hashprüfung oder sicheren Klartextvergleich verwenden.
            if (($passwordInfo['algo'] ?? 0) !== 0) {
                $passwordIsValid = password_verify($password, $storedPassword);
            } else {
                $passwordIsValid = hash_equals($storedPassword, $password);
            }
        }

        // Fehlermeldung ausgeben, wenn Benutzer, Status oder Passwort ungültig sind.
        if (!$user || (int)($user['State'] ?? 0) !== 1 || !$passwordIsValid) {
            $errors[] = "E-Mail oder Passwort ist falsch.";
        } else {
            // Relevante Benutzerdaten in der Session speichern.
            $_SESSION['user'] = [
                    'CustomerID' => (int)$user['CustomerID'],
                    'UserName' => (string)($user['UserName'] ?? $user['Email'] ?? $email),
                    'Type' => (int)$user['Type'],
                    'FirstName' => (string)($user['FirstName'] ?? ''),
                    'LastName' => (string)($user['LastName'] ?? ''),
            ];

            // Nach erfolgreichem Login zur Startseite weiterleiten.
            header('Location: ' . base_url('index.php'));
            exit;
        }
    }
}

// Gemeinsamen Header einbinden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Loginbereich mit kurzer Einführung und Formular. -->
    <section class="auth-layout">
        <!-- Einführungstext mit Link zur Registrierung. -->
        <div class="auth-intro">
            <p class="eyebrow">Willkommen zurück</p>
            <h1>Anmelden</h1>
            <p>Melden Sie sich mit Ihrer E-Mail-Adresse und Ihrem Passwort an.</p>
            <p class="small mb-0">
                Noch kein Konto?
                <a href="<?= e(base_url('pages/register.php')); ?>">Jetzt registrieren</a>
            </p>
        </div>

        <!-- Formularbox für die Anmeldung. -->
        <div class="card border-0 shadow-sm auth-card">
            <div class="card-body p-4 p-md-5">
                <!-- Validierungs- und Loginfehler anzeigen. -->
                <?php foreach ($errors as $error): ?>
                    <?php
                    $alertType = "danger";
                    $alertMessage = $error;
                    include __DIR__ . "/../components/alert-box.php";
                    ?>
                <?php endforeach; ?>

                <!-- Erfolgsmeldung anzeigen, falls eine gesetzt wurde. -->
                <?php if ($successMessage !== ""): ?>
                    <?php
                    $alertType = "success";
                    $alertMessage = $successMessage;
                    include __DIR__ . "/../components/alert-box.php";
                    ?>
                <?php endif; ?>

                <!-- Loginformular. -->
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">E-Mail</label>
                        <input
                                class="form-control form-control-lg"
                                type="email"
                                id="email"
                                name="email"
                                autocomplete="email"
                                required
                                maxlength="100"
                                value="<?= e($email); ?>"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password">Passwort</label>
                        <input
                                class="form-control form-control-lg"
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required
                                minlength="8"
                        >
                    </div>

                    <button class="btn btn-primary btn-lg w-100" type="submit">Anmelden</button>
                </form>
            </div>
        </div>
    </section>

<?php
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>