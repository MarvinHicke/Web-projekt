<?php
/**
 * Registrierungsseite für UC20.
 *
 * Validiert Profildaten und erstellt danach einen
 * neuen Kunden inklusive Login-Datensatz.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';

// Seitentitel und Formularstatus vorbereiten.
$pageTitle = "Registrieren";
$errors=[];
$successMessage="";
$firstName="";
$lastName="";
$address="";
$city="";
$region="";
$country="";
$postal="";
$phone="";
$email="";

// Maximale Eingabelängen und Mindestlänge für das Passwort festlegen.
$nameMaxLength=50;
$textMaxLength=255;
$emailMaxLength=100;
$passwordMinLength=8;

// Registrierungsformular verarbeiten, wenn die Seite per POST aufgerufen wurde.
if (($_SERVER["REQUEST_METHOD"] ?? "GET") === "POST") {
    // Eingaben aus dem Formular lesen und vorbereiten.
    $firstName=trim((string) ($_POST["firstName"] ?? ""));
    $lastName=trim((string) ($_POST["lastName"] ?? ""));
    $address = trim((string) ($_POST["address"] ?? ""));
    $city = trim((string) ($_POST["city"] ?? ""));
    $region=trim((string) ($_POST["region"] ?? ""));
    $country = trim((string) ($_POST["country"] ?? ""));
    $postal=trim((string) ($_POST["postal"] ?? ""));
    $phone=trim((string) ($_POST["phone"] ?? ""));
    $email=trim((string) ($_POST["email"] ?? ""));
    $password= (string) ($_POST["password"] ?? "");
    $confirmPassword= (string) ($_POST["confirmPassword"] ?? "");

    // Vorname validieren.
    if($firstName==="")
    {
        $errors[]="Bitte geben sie Ihren Vornamen ein";
    }
    if(strlen($firstName)>$nameMaxLength)
    {
        $errors[]="Der Vornamen darf maximal " . $nameMaxLength . " Zeichen lang sein";
    }

    // Nachname validieren.
    if($lastName==="")
    {
        $errors[]="Bitte geben sie Ihren Nachnamen ein";
    }
    if(strlen($lastName)>$nameMaxLength)
    {
        $errors[]="Der Nachname darf maximal " . $nameMaxLength . " Zeichen lang sein";
    }

    // Adresse validieren.
    if ($address === "")
    {
        $errors[] = "Bitte geben Sie eine Adresse ein.";
    }

    if (strlen($address) > $textMaxLength)
    {
        $errors[] = "Die Adresse darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    // Stadt validieren.
    if ($city === "")
    {
        $errors[] = "Bitte geben Sie eine Stadt ein.";
    }

    if (strlen($city) > $textMaxLength)
    {
        $errors[] = "Die Stadt darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    // Land validieren.
    if ($country === "")
    {
        $errors[] = "Bitte geben Sie ein Land ein.";
    }

    if (strlen($country) > $textMaxLength)
    {
        $errors[] = "Das Land darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    // Optionale Adressfelder auf maximale Länge prüfen.
    if (strlen($region) > $textMaxLength)
    {
        $errors[] = "Die Region darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    if (strlen($postal) > $textMaxLength)
    {
        $errors[] = "Die Postleitzahl darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    if (strlen($phone) > $textMaxLength)
    {
        $errors[] = "Die Telefonnummer darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    // E-Mail-Adresse validieren.
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

    // Passwort und Passwortbestätigung validieren.
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

    // Benutzerkonto anlegen, wenn keine Validierungsfehler vorhanden sind.
    if(empty($errors))
    {
        $passwordHash=password_hash($password,PASSWORD_DEFAULT);
        $customerRepository = new customerRepository(db());

        // Verwendet die E-Mail-Adresse als Login-Benutzernamen und verhindert doppelte Konten.
        if ($customerRepository->GetByUsername($email)) {
            $errors[] = "Diese E-Mail ist bereits registriert.";
        } else {
            $created = $customerRepository->create(
                    [
                            'FirstName' => $firstName,
                            'LastName' => $lastName,
                            'Address' => $address,
                            'City' => $city,
                            'Region' => $region,
                            'Country' => $country,
                            'Postal' => $postal,
                            'Phone' => $phone,
                            'Email' => $email,
                    ],
                    [
                            'UserName' => $email,
                            'Pass' => $passwordHash,
                    ]
            );

            // Nach erfolgreicher Registrierung den neuen Benutzer direkt einloggen.
            if ($created) {
                $user = $customerRepository->GetByUsername($email);
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

            $errors[] = "Die Registrierung konnte nicht gespeichert werden.";
        }
    }
}

// Gemeinsamen Header einbinden.
require_once __DIR__ . "/../includes/header.php";
?>

    <!-- Seitenüberschrift der Registrierung. -->
    <section class="page-heading">
        <h1>Registrieren</h1>
        <p>Erstellen Sie ein neues Konto.</p>
    </section>

    <!-- Registrierungsformular mit Validierungsfehlern. -->
    <section class="data-box auth-panel">
        <?php foreach ($errors as $error): ?>
            <?php
            // Fehlermeldung über die gemeinsame Alert-Komponente anzeigen.
            $alertType = "danger";
            $alertMessage = $error;
            include __DIR__ . "/../components/alert-box.php";
            ?>
        <?php endforeach; ?>

        <!-- Formular zur Eingabe der Konto- und Login-Daten. -->
        <form method="POST" class="auth-form">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstName" class="form-label">Vorname *</label>
                    <input
                            type="text"
                            id="firstName"
                            name="firstName"
                            class="form-control"
                            required
                            maxlength="<?= $nameMaxLength ?>"
                            value="<?= e($firstName) ?>"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="lastName" class="form-label">Nachname *</label>
                    <input
                            type="text"
                            id="lastName"
                            name="lastName"
                            class="form-control"
                            required
                            maxlength="<?= $nameMaxLength ?>"
                            value="<?= e($lastName) ?>"
                    >
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input
                        type="text"
                        id="address"
                        name="address"
                        class="form-control"
                        required
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($address) ?>"
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">Stadt</label>
                    <input
                            type="text"
                            id="city"
                            name="city"
                            class="form-control"
                            required
                            maxlength="<?= $textMaxLength ?>"
                            value="<?= e($city) ?>"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="region" class="form-label">Region</label>
                    <input
                            type="text"
                            id="region"
                            name="region"
                            class="form-control"
                            maxlength="<?= $textMaxLength ?>"
                            value="<?= e($region) ?>"
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="country" class="form-label">Land</label>
                    <input
                            type="text"
                            id="country"
                            name="country"
                            class="form-control"
                            required
                            maxlength="<?= $textMaxLength ?>"
                            value="<?= e($country) ?>"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="postal" class="form-label">Postleitzahl</label>
                    <input
                            type="text"
                            id="postal"
                            name="postal"
                            class="form-control"
                            maxlength="<?= $textMaxLength ?>"
                            value="<?= e($postal) ?>"
                    >
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telefon</label>
                <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($phone) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-Mail *</label>
                <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        required
                        maxlength="<?= $emailMaxLength ?>"
                        value="<?= e($email) ?>"
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Passwort *</label>
                    <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                            minlength="<?= $passwordMinLength ?>"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="confirmPassword" class="form-label">Passwort bestätigen *</label>
                    <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            class="form-control"
                            required
                            minlength="<?= $passwordMinLength ?>"
                    >
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Registrieren
            </button>
        </form>
    </section>

<?php
// Gemeinsamen Footer einbinden.
require_once __DIR__ . "/../includes/footer.php";
?>