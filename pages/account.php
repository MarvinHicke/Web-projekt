<?php
/**
 * Mein-Konto-Seite für UC23.
 *
 * Ermöglicht eingeloggten Benutzern das Bearbeiten ihrer Profildaten, das
 * Ändern ihres Passworts sowie das Anzeigen eigener Reviews und Favoriten.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';
require_once __DIR__ . '/../repositories/reviewRepository.php';

// Zugriffsschutz: Nicht eingeloggte Benutzer werden zur Login-Seite weitergeleitet.
if (!isLoggedIn()) {
    header('Location: ' . base_url('pages/login.php'));
    exit;
}

// Aktuell eingeloggten Benutzer aus der Session lesen.
$currentUser = $_SESSION['user'];

// Datenbankverbindung und benötigte Repositories initialisieren.
$db = db();

$customerRepository = new customerRepository($db);
$reviewRepository = new reviewRepository($db);

// Fehler- und Erfolgsmeldungen für das Profilformular vorbereiten.
$profileErrors = [];
$profileSuccessMessage = '';

// Fehler- und Erfolgsmeldungen für das Passwortformular vorbereiten.
$passwordErrors = [];
$passwordSuccessMessage = '';
$passwordMinLength = 8;

// Aktuelle Kontodaten aus der Datenbank laden; falls nicht verfügbar, werden Session-Daten verwendet.
$accountUser = $customerRepository->GetById((int) $currentUser['CustomerID']);

if (!$accountUser)
{
    $accountUser = $currentUser;
}

// Eigene Bewertungen des Benutzers inklusive Kunstwerkdaten laden.
$ownReviews = $reviewRepository->getForCustomerWithArtworkData((int) $currentUser['CustomerID']);

// Kontodaten für die Anzeige und Formularbefüllung vorbereiten.
$firstName = (string) ($accountUser['FirstName'] ?? '');
$lastName = (string) ($accountUser['LastName'] ?? '');
$address = (string) ($accountUser['Address'] ?? '');
$city = (string) ($accountUser['City'] ?? '');
$region = (string) ($accountUser['Region'] ?? '');
$country = (string) ($accountUser['Country'] ?? '');
$postal = (string) ($accountUser['Postal'] ?? '');
$phone = (string) ($accountUser['Phone'] ?? '');
$email = (string) ($accountUser['Email'] ?? $accountUser['UserName'] ?? '');
$userType = (int) ($accountUser['Type'] ?? 1);

// Maximale Eingabelängen für die Formularvalidierung.
$nameMaxLength = 50;
$textMaxLength = 255;
$emailMaxLength = 100;

// Verarbeitung des Formulars für die Kontodaten.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['formType'] ?? '') === 'profile')
{
    // Eingaben aus dem Profilformular lesen und trimmen.
    $firstName = trim((string) ($_POST['firstName'] ?? ''));
    $lastName = trim((string) ($_POST['lastName'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $city = trim((string) ($_POST['city'] ?? ''));
    $region = trim((string) ($_POST['region'] ?? ''));
    $country = trim((string) ($_POST['country'] ?? ''));
    $postal = trim((string) ($_POST['postal'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));

    // Pflichtfelder und maximale Eingabelängen prüfen.
    if ($firstName === '') {
        $profileErrors[] = 'Bitte geben Sie Ihren Vornamen ein.';
    }

    if (strlen($firstName) > $nameMaxLength) {
        $profileErrors[] = 'Der Vorname darf maximal ' . $nameMaxLength . ' Zeichen lang sein.';
    }

    if ($lastName === '') {
        $profileErrors[] = 'Bitte geben Sie Ihren Nachnamen ein.';
    }

    if (strlen($lastName) > $nameMaxLength) {
        $profileErrors[] = 'Der Nachname darf maximal ' . $nameMaxLength . ' Zeichen lang sein.';
    }

    if ($address === '') {
        $profileErrors[] = 'Bitte geben Sie Ihre Adresse ein.';
    }

    if (strlen($address) > $textMaxLength) {
        $profileErrors[] = 'Die Adresse darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if ($city === '') {
        $profileErrors[] = 'Bitte geben Sie Ihre Stadt ein.';
    }

    if (strlen($city) > $textMaxLength) {
        $profileErrors[] = 'Die Stadt darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if ($country === '') {
        $profileErrors[] = 'Bitte geben Sie Ihr Land ein.';
    }

    if (strlen($country) > $textMaxLength) {
        $profileErrors[] = 'Das Land darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if (strlen($region) > $textMaxLength) {
        $profileErrors[] = 'Die Region darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if (strlen($postal) > $textMaxLength) {
        $profileErrors[] = 'Die Postleitzahl darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if (strlen($phone) > $textMaxLength) {
        $profileErrors[] = 'Die Telefonnummer darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if ($email === '') {
        $profileErrors[] = 'Bitte geben Sie Ihre E-Mail ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileErrors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }

    if (strlen($email) > $emailMaxLength) {
        $profileErrors[] = 'Die E-Mail darf maximal ' . $emailMaxLength . ' Zeichen lang sein.';
    }

    // Prüfen, ob die neue E-Mail-Adresse bereits von einem anderen Konto verwendet wird.
    if (empty($profileErrors)) {
        $existingUser = $customerRepository->GetByUsername($email);
        $currentCustomerId = (int) $currentUser['CustomerID'];

        if ($existingUser && (int) ($existingUser['CustomerID'] ?? 0) !== $currentCustomerId) {
            $profileErrors[] = 'Diese E-Mail-Adresse wird bereits verwendet.';
        }
    }

    // Kontodaten speichern, wenn keine Validierungsfehler vorhanden sind.
    if (empty($profileErrors)) {
        $updated = $customerRepository->update(
                (int) $currentUser['CustomerID'],
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
                ]
        );

        if ($updated) {
            // Session-Daten aktualisieren, damit Navigation und Anzeige sofort aktuell sind.
            $_SESSION['user']['FirstName'] = $firstName;
            $_SESSION['user']['LastName'] = $lastName;
            $_SESSION['user']['UserName'] = $email;

            $profileSuccessMessage = 'Ihre Kontodaten wurden gespeichert.';
        } else {
            $profileErrors[] = 'Die Kontodaten konnten nicht gespeichert werden.';
        }
    }
}

// Verarbeitung des Formulars zum Ändern des Passworts.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['formType'] ?? '') === 'password')
{
    // Eingaben aus dem Passwortformular lesen.
    $currentPassword = (string) ($_POST['currentPassword'] ?? '');
    $newPassword = (string) ($_POST['newPassword'] ?? '');
    $confirmNewPassword = (string) ($_POST['confirmNewPassword'] ?? '');

    // Pflichtfelder, Mindestlänge und Passwortbestätigung prüfen.
    if ($currentPassword === '') {
        $passwordErrors[] = 'Bitte geben Sie Ihr aktuelles Passwort ein.';
    }

    if ($newPassword === '') {
        $passwordErrors[] = 'Bitte geben Sie ein neues Passwort ein.';
    } elseif (strlen($newPassword) < $passwordMinLength) {
        $passwordErrors[] = 'Das neue Passwort muss mindestens ' . $passwordMinLength . ' Zeichen lang sein.';
    }

    if ($confirmNewPassword === '') {
        $passwordErrors[] = 'Bitte bestätigen Sie Ihr neues Passwort.';
    } elseif ($newPassword !== $confirmNewPassword) {
        $passwordErrors[] = 'Die neuen Passwörter stimmen nicht überein.';
    }

    // Aktuelles Passwort prüfen, bevor ein neues Passwort gespeichert wird.
    if (empty($passwordErrors)) {
        $loginUser = $customerRepository->GetByUsername($email);

        $currentPasswordIsValid = false;

        // Unterstützt sowohl gehashte Passwörter als auch alte Klartext-Passwörter, bevor das neue Passwort gehasht gespeichert wird.
        if ($loginUser) {
            $storedPassword = (string) ($loginUser['Pass'] ?? '');
            $passwordInfo = password_get_info($storedPassword);

            if (($passwordInfo['algo'] ?? 0) !== 0) {
                $currentPasswordIsValid = password_verify($currentPassword, $storedPassword);
            } else {
                $currentPasswordIsValid = hash_equals($storedPassword, $currentPassword);
            }
        }

        if (!$loginUser || !$currentPasswordIsValid) {
            $passwordErrors[] = 'Das aktuelle Passwort ist falsch.';
        }
    }

    // Neues Passwort hashen und speichern.
    if (empty($passwordErrors)) {
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $passwordUpdated = $customerRepository->updatePassword(
                (int) $currentUser['CustomerID'],
                $newPasswordHash
        );

        if ($passwordUpdated) {
            $passwordSuccessMessage = 'Ihr Passwort wurde geändert.';
        } else {
            $passwordErrors[] = 'Das Passwort konnte nicht geändert werden.';
        }
    }
}

// Weitere Anzeigewerte für die Kontoseite vorbereiten.
$fullName = trim($firstName . ' ' . $lastName);
$roleLabel = $userType === 2 ? 'Admin' : 'User';
$dateJoinedRaw = (string) ($accountUser['DateJoined'] ?? '');
$dateJoinedLabel = 'Nicht angegeben';

// Beitrittsdatum formatieren, falls ein gültiger Wert vorhanden ist.
if(
        $dateJoinedRaw !== ''
        && $dateJoinedRaw !== '0000-00-00'
        && $dateJoinedRaw !== '0000-00-00 00:00:00'
) {
    $timestamp = strtotime($dateJoinedRaw);

    if ($timestamp !== false) {
        $dateJoinedLabel = date('d.m.Y', $timestamp);
    }
}

// Seitentitel setzen und gemeinsamen Header einbinden.
$pageTitle = "Mein Konto";
require_once __DIR__ . "/../includes/header.php";
?>

    <!-- Überschrift der Kontoseite. -->
    <section class="page-heading">
        <h1>Mein Konto</h1>
        <p>Hier können Sie Ihre Kontodaten ansehen und bearbeiten.</p>
    </section>

    <!-- Fehlermeldungen des Profilformulars anzeigen. -->
<?php if (!empty($profileErrors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($profileErrors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <!-- Erfolgsmeldung des Profilformulars anzeigen. -->
<?php if ($profileSuccessMessage !== ''): ?>
    <div class="alert alert-success">
        <?= e($profileSuccessMessage) ?>
    </div>
<?php endif; ?>

    <!-- Fehlermeldungen des Passwortformulars anzeigen. -->
<?php if (!empty($passwordErrors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($passwordErrors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <!-- Erfolgsmeldung des Passwortformulars anzeigen. -->
<?php if ($passwordSuccessMessage !== ''): ?>
    <div class="alert alert-success">
        <?= e($passwordSuccessMessage) ?>
    </div>
<?php endif; ?>

    <!-- Übersicht der aktuellen Kontodaten. -->
    <section class="data-box account-section">
        <h2>Kontodaten</h2>

        <ul class="account-data-list">
            <li>
                <strong>Name:</strong>
                <?= e($fullName !== '' ? $fullName : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Adresse:</strong>
                <?= e($address !== '' ? $address : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Stadt:</strong>
                <?= e($city !== '' ? $city : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Region:</strong>
                <?= e($region !== '' ? $region : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Land:</strong>
                <?= e($country !== '' ? $country : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Postleitzahl:</strong>
                <?= e($postal !== '' ? $postal : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Telefon:</strong>
                <?= e($phone !== '' ? $phone : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>E-Mail:</strong>
                <?= e($email !== '' ? $email : 'Nicht angegeben') ?>
            </li>
            <li>
                <strong>Rolle:</strong>
                <?= e($roleLabel) ?>
            </li>
            <li>
                <strong>Mitglied seit:</strong>
                <?= e($dateJoinedLabel) ?>
            </li>
        </ul>
    </section>

    <!-- Formular zum Bearbeiten der Kontodaten. -->
    <section class="data-box account-section">
        <h2>Kontodaten bearbeiten</h2>

        <form method="post" class="account-form">
            <input type="hidden" name="formType" value="profile">

            <div>
                <label class="form-label" for="firstName">Vorname</label>
                <input
                        class="form-control"
                        type="text"
                        id="firstName"
                        name="firstName"
                        maxlength="<?= $nameMaxLength ?>"
                        value="<?= e($firstName) ?>"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="lastName">Nachname</label>
                <input
                        class="form-control"
                        type="text"
                        id="lastName"
                        name="lastName"
                        maxlength="<?= $nameMaxLength ?>"
                        value="<?= e($lastName) ?>"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="address">Adresse</label>
                <input
                        class="form-control"
                        type="text"
                        id="address"
                        name="address"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($address) ?>"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="city">Stadt</label>
                <input
                        class="form-control"
                        type="text"
                        id="city"
                        name="city"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($city) ?>"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="region">Region</label>
                <input
                        class="form-control"
                        type="text"
                        id="region"
                        name="region"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($region) ?>"
                >
            </div>

            <div>
                <label class="form-label" for="country">Land</label>
                <input
                        class="form-control"
                        type="text"
                        id="country"
                        name="country"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($country) ?>"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="postal">Postleitzahl</label>
                <input
                        class="form-control"
                        type="text"
                        id="postal"
                        name="postal"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($postal) ?>"
                >
            </div>

            <div>
                <label class="form-label" for="phone">Telefon</label>
                <input
                        class="form-control"
                        type="tel"
                        id="phone"
                        name="phone"
                        maxlength="<?= $textMaxLength ?>"
                        value="<?= e($phone) ?>"
                >
            </div>

            <div>
                <label class="form-label" for="email">E-Mail</label>
                <input
                        class="form-control"
                        type="email"
                        id="email"
                        name="email"
                        maxlength="<?= $emailMaxLength ?>"
                        value="<?= e($email) ?>"
                        required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Kontodaten speichern
            </button>
        </form>
    </section>

    <!-- Formular zum Ändern des Passworts. -->
    <section class="data-box account-section">
        <h2>Passwort ändern</h2>

        <form method="post" class="account-form">
            <input type="hidden" name="formType" value="password">

            <div>
                <label class="form-label" for="currentPassword">Aktuelles Passwort</label>
                <input
                        class="form-control"
                        type="password"
                        id="currentPassword"
                        name="currentPassword"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="newPassword">Neues Passwort</label>
                <input
                        class="form-control"
                        type="password"
                        id="newPassword"
                        name="newPassword"
                        minlength="<?= $passwordMinLength ?>"
                        required
                >
            </div>

            <div>
                <label class="form-label" for="confirmNewPassword">Neues Passwort bestätigen</label>
                <input
                        class="form-control"
                        type="password"
                        id="confirmNewPassword"
                        name="confirmNewPassword"
                        minlength="<?= $passwordMinLength ?>"
                        required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Passwort speichern
            </button>
        </form>
    </section>

    <!-- Übersicht der eigenen Bewertungen. -->
    <section class="data-box account-section account-reviews-section">
        <h2>Meine Bewertungen</h2>

        <?php if (empty($ownReviews)): ?>
            <?php
            // Hinweisbox anzeigen, wenn der Benutzer noch keine Bewertungen geschrieben hat.
            $alertType = 'info';
            $alertMessage = 'Du hast noch keine Bewertungen geschrieben.';
            include __DIR__ . '/../components/alert-box.php';
            ?>
        <?php else: ?>
            <div class="account-review-list">
                <?php foreach ($ownReviews as $review): ?>
                    <?php
                    // Bewertungsdaten für die Anzeige vorbereiten.
                    $reviewArtworkId = (int) ($review['ArtWorkId'] ?? 0);
                    $reviewTitle = (string) ($review['ArtworkTitle'] ?? 'Unbekanntes Kunstwerk');
                    $reviewRating = max(1, min(5, (int) ($review['Rating'] ?? 1)));
                    $reviewComment = cleanHtml((string) ($review['Comment'] ?? ''));
                    $reviewDateRaw = (string) ($review['ReviewDate'] ?? '');
                    $reviewDate = 'Datum unbekannt';

                    // Bewertungsdatum formatieren, falls ein gültiges Datum vorhanden ist.
                    if ($reviewDateRaw !== '')
                    {
                        $timestamp = strtotime($reviewDateRaw);

                        if ($timestamp !== false)
                        {
                            $reviewDate = date('d.m.Y', $timestamp);
                        }
                    }

                    // Künstlernamen aus Vor- und Nachname zusammensetzen.
                    $artistName = trim((string) ($review['ArtistFirstName'] ?? '') . ' ' . (string) ($review['ArtistLastName'] ?? ''));

                    if ($artistName === '')
                    {
                        $artistName = 'Unbekannter Künstler';
                    }
                    ?>

                    <article class="account-review-item">
                        <h3 class="h5 mb-1">
                            <a href="<?= e(artworkDetailUrl($reviewArtworkId)); ?>">
                                <?= e($reviewTitle); ?>
                            </a>
                        </h3>

                        <div class="account-review-meta">
                    <span>
                        <strong>Künstler:</strong> <?= e($artistName); ?>
                    </span>

                            <span>
                        <strong>Bewertet am:</strong> <?= e($reviewDate); ?>
                    </span>
                        </div>

                        <p class="mb-2">
                    <span class="text-warning">
                        <?= str_repeat('★', $reviewRating) . str_repeat('☆', 5 - $reviewRating); ?>
                    </span>
                            <span class="ms-1">
                        <?= e((string) $reviewRating); ?>/5
                    </span>
                        </p>

                        <details>
                            <summary>Kommentar anzeigen</summary>
                            <p class="mt-2 mb-0">
                                <?= nl2br(e($reviewComment)); ?>
                            </p>
                        </details>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Bereich mit Links zu Favoriten und Logout. -->
    <section class="data-box account-section account-favorites-section">
        <h2>Meine Favoriten</h2>
        <p>Hier kannst du deine favorisierten Künstler und Kunstwerke ansehen.</p>

        <div class="account-action-row">
            <?php
            // Button zur Favoritenübersicht vorbereiten.
            $buttonText = 'Favoriten anzeigen';
            $buttonHref = base_url('pages/favorites.php');
            $buttonVariant = 'primary';
            include __DIR__ . '/../components/button.php';
            ?>

            <?php
            // Button zum Abmelden vorbereiten.
            $buttonText = 'Abmelden';
            $buttonHref = base_url('pages/logout.php');
            $buttonVariant = 'danger';
            include __DIR__ . '/../components/button.php';
            ?>
        </div>
    </section>

<?php
// Gemeinsamen Footer einbinden.
require_once __DIR__ . "/../includes/footer.php";
?>