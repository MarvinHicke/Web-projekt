<?php
/**
 * Admin-Bearbeitungsseite für UC21.
 *
 * Ermöglicht Administratoren das Bearbeiten von Profildaten, Rolle, Status und
 * Passwort eines Benutzers. Kritische Selbständerungen werden serverseitig verhindert.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';

// Seitentitel für die Bearbeitungsseite setzen.
$pageTitle = 'Benutzer bearbeiten';

// Zugriffsschutz: Nur Administratoren dürfen Benutzer bearbeiten.
if (!isAdmin())
{
    header('Location: ' . base_url('index.php'));
    exit;
}

// Repository für Benutzerzugriffe initialisieren.
$customerRepository = new customerRepository(db());

// Statusvariablen für Fehlermeldungen, Erfolgsmeldungen und den ausgewählten Benutzer vorbereiten.
$errors = [];
$successMessage = '';
$selectedUser = null;
$selectedUserId = null;

// ID des aktuell eingeloggten Admins vorbereiten.
$currentAdminId = 0;

if (isset($_SESSION['user']['CustomerID']))
{
    $currentAdminId = (int) $_SESSION['user']['CustomerID'];
}

// Maximale Eingabelängen für die Formularvalidierung.
$nameMaxLength = 50;
$textMaxLength = 255;
$emailMaxLength = 100;

// Prüfen, ob eine Benutzer-ID übergeben wurde.
if (!isset($_GET['id']))
{
    $errors[] = 'Es wurde kein Benutzer ausgewählt.';
}
else
{
    // Benutzer-ID aus der URL validieren.
    $selectedUserId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($selectedUserId === false || $selectedUserId <= 0)
    {
        $errors[] = 'Die Benutzer-ID ist ungültig.';
    }
    else
    {
        // Ausgewählten Benutzer aus der Datenbank laden.
        $selectedUser = $customerRepository->GetById($selectedUserId);

        if (!$selectedUser)
        {
            $errors[] = 'Der ausgewählte Benutzer wurde nicht gefunden.';
        }
    }
}

// Formularwerte mit den aktuellen Benutzerdaten vorbelegen.
if ($selectedUser)
{
    $firstName = (string) ($selectedUser['FirstName'] ?? '');
    $lastName = (string) ($selectedUser['LastName'] ?? '');
    $address = (string) ($selectedUser['Address'] ?? '');
    $city = (string) ($selectedUser['City'] ?? '');
    $region = (string) ($selectedUser['Region'] ?? '');
    $country = (string) ($selectedUser['Country'] ?? '');
    $postal = (string) ($selectedUser['Postal'] ?? '');
    $phone = (string) ($selectedUser['Phone'] ?? '');
    $email = (string) ($selectedUser['Email'] ?? $selectedUser['UserName'] ?? '');
}

// Profildatenformular verarbeiten.
if ($selectedUser && $_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['formType'] ?? '') === 'profile')
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
    if ($firstName === '')
    {
        $errors[] = 'Bitte geben Sie einen Vornamen ein.';
    }

    if (strlen($firstName) > $nameMaxLength)
    {
        $errors[] = 'Der Vorname darf maximal ' . $nameMaxLength . ' Zeichen lang sein.';
    }

    if ($lastName === '')
    {
        $errors[] = 'Bitte geben Sie einen Nachnamen ein.';
    }

    if (strlen($lastName) > $nameMaxLength)
    {
        $errors[] = 'Der Nachname darf maximal ' . $nameMaxLength . ' Zeichen lang sein.';
    }

    if ($address === '')
    {
        $errors[] = 'Bitte geben Sie eine Adresse ein.';
    }

    if (strlen($address) > $textMaxLength)
    {
        $errors[] = 'Die Adresse darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if ($city === '')
    {
        $errors[] = 'Bitte geben Sie eine Stadt ein.';
    }

    if (strlen($city) > $textMaxLength)
    {
        $errors[] = 'Die Stadt darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if ($country === '')
    {
        $errors[] = 'Bitte geben Sie ein Land ein.';
    }

    if (strlen($country) > $textMaxLength)
    {
        $errors[] = 'Das Land darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if (strlen($region) > $textMaxLength)
    {
        $errors[] = 'Die Region darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if (strlen($postal) > $textMaxLength)
    {
        $errors[] = 'Die Postleitzahl darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    if (strlen($phone) > $textMaxLength)
    {
        $errors[] = 'Die Telefonnummer darf maximal ' . $textMaxLength . ' Zeichen lang sein.';
    }

    // E-Mail-Adresse prüfen.
    if ($email === '')
    {
        $errors[] = 'Bitte geben Sie eine E-Mail-Adresse ein.';
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }

    if (strlen($email) > $emailMaxLength)
    {
        $errors[] = 'Die E-Mail darf maximal ' . $emailMaxLength . ' Zeichen lang sein.';
    }

    // Prüfen, ob die E-Mail-Adresse bereits von einem anderen Benutzer verwendet wird.
    if (empty($errors))
    {
        $existingUser = $customerRepository->GetByUsername($email);

        if ($existingUser && (int) ($existingUser['CustomerID'] ?? 0) !== (int) $selectedUserId)
        {
            $errors[] = 'Diese E-Mail-Adresse wird bereits verwendet.';
        }
    }

    // Profildaten speichern, wenn keine Validierungsfehler vorhanden sind.
    if (empty($errors))
    {
        $updated = $customerRepository->update(
                (int) $selectedUserId,
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

        if ($updated)
        {
            $successMessage = 'Die Benutzerdaten wurden gespeichert.';

            // Sessiondaten aktualisieren, falls der Admin sein eigenes Profil bearbeitet.
            if (isset($_SESSION['user']) && (int) $_SESSION['user']['CustomerID'] === (int) $selectedUserId)
            {
                $_SESSION['user']['FirstName'] = $firstName;
                $_SESSION['user']['LastName'] = $lastName;
                $_SESSION['user']['UserName'] = $email;
            }

            // Benutzer nach dem Speichern erneut laden.
            $selectedUser = $customerRepository->GetById($selectedUserId);
        }
        else
        {
            $errors[] = 'Die Benutzerdaten konnten nicht gespeichert werden.';
        }
    }
}

// Verarbeitet Rollen- und Statusänderungen und schützt den aktuellen Admin vor unsicheren Selbständerungen.
if ($selectedUser && $_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['formType'] ?? '') === 'statusRole')
{
    // Neue Rollen- und Statuswerte aus dem Formular validieren.
    $newType = filter_var((string) ($_POST['type'] ?? ''), FILTER_VALIDATE_INT);
    $newState = filter_var((string) ($_POST['state'] ?? ''), FILTER_VALIDATE_INT);

    // Aktuelle Rolle normalisieren.
    $currentType = 1;
    if ((int) $selectedUser['Type'] === 2)
    {
        $currentType = 2;
    }

    // Aktuellen Status normalisieren.
    $currentState = 0;
    if ((int) $selectedUser['State'] === 1)
    {
        $currentState = 1;
    }

    // Rolle und Status auf gültige Werte prüfen.
    if ($newType !== 1 && $newType !== 2)
    {
        $errors[] = 'Bitte wählen Sie eine gültige Rolle aus.';
    }

    if ($newState !== 0 && $newState !== 1)
    {
        $errors[] = 'Bitte wählen Sie einen gültigen Status aus.';
    }

    // Verhindert, dass der aktuelle Admin sein eigenes Konto deaktiviert.
    if ((int) $selectedUserId === $currentAdminId && $newState === 0)
    {
        $errors[] = 'Sie können Ihr eigenes Konto nicht deaktivieren.';
    }

    // Verhindert, dass der aktuelle Admin sich selbst die Admin-Rolle entfernt.
    if ((int) $selectedUserId === $currentAdminId && $currentType === 2 && $newType !== 2)
    {
        $errors[] = 'Sie können Ihre eigene Admin-Rolle nicht entfernen.';
    }

    if (empty($errors))
    {
        $changedSomething = false;

        // Rolle ändern, falls ein anderer Rollenwert ausgewählt wurde.
        if ($newType !== $currentType)
        {
            if ($newType === 2)
            {
                $roleUpdated = $customerRepository->elevateToAdmin((int) $selectedUserId);
            }
            else
            {
                $roleUpdated = $customerRepository->demoteAdmin((int) $selectedUserId);
            }

            if (!$roleUpdated)
            {
                $errors[] = 'Die Rolle konnte nicht geändert werden. Möglicherweise muss mindestens ein aktiver Admin bestehen bleiben.';
            }
            else
            {
                $changedSomething = true;
            }
        }

        // Status ändern, falls ein anderer Statuswert ausgewählt wurde.
        if (empty($errors) && $newState !== $currentState)
        {
            $stateUpdated = $customerRepository->updateState((int) $selectedUserId, $newState);

            if (!$stateUpdated)
            {
                $errors[] = 'Der Status konnte nicht geändert werden. Möglicherweise muss mindestens ein aktiver Admin bestehen bleiben.';
            }
            else
            {
                $changedSomething = true;
            }
        }

        if (empty($errors))
        {
            // Rückmeldung abhängig davon setzen, ob tatsächlich etwas geändert wurde.
            if ($changedSomething)
            {
                $successMessage = 'Status und Rolle wurden gespeichert.';
            }
            else
            {
                $successMessage = 'Status und Rolle wurden nicht verändert.';
            }

            // Benutzer nach Änderungen erneut laden.
            $selectedUser = $customerRepository->GetById((int) $selectedUserId);

            // Sessionrolle aktualisieren, falls der Admin sein eigenes Konto bearbeitet hat.
            if (isset($_SESSION['user']) && (int) $_SESSION['user']['CustomerID'] === (int) $selectedUserId)
            {
                $_SESSION['user']['Type'] = (int) $selectedUser['Type'];
            }
        }
    }
}

// Ermöglicht Administratoren, für den ausgewählten Benutzer ein neues Passwort zu setzen.
if ($selectedUser && $_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['formType'] ?? '') === 'adminPassword')
{
    // Neue Passwortwerte aus dem Formular lesen.
    $newPassword = (string) ($_POST['newPassword'] ?? '');
    $confirmPassword = (string) ($_POST['confirmPassword'] ?? '');

    // Passwort validieren.
    if ($newPassword === '')
    {
        $errors[] = 'Bitte geben Sie ein neues Passwort ein.';
    }

    if (strlen($newPassword) < 8)
    {
        $errors[] = 'Das neue Passwort muss mindestens 8 Zeichen lang sein.';
    }

    if ($newPassword !== $confirmPassword)
    {
        $errors[] = 'Die Passwortbestätigung stimmt nicht überein.';
    }

    // Neues Passwort gehasht speichern.
    if (empty($errors))
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $customerRepository->updatePassword((int) $selectedUserId, $hashedPassword);

        if ($updated)
        {
            $successMessage = 'Das Passwort wurde geändert.';
            $selectedUser = $customerRepository->GetById((int) $selectedUserId);
        }
        else
        {
            $errors[] = 'Das Passwort konnte nicht geändert werden.';
        }
    }
}

// Gemeinsamen Header einbinden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Überschrift der Admin-Bearbeitungsseite. -->
    <section class="page-heading manage-user-edit-heading">
        <h1>Benutzer bearbeiten</h1>
        <p>Hier können Profildaten, Status, Rolle und Passwort eines Benutzerkontos verwaltet werden.</p>
    </section>

    <!-- Fehlermeldungen anzeigen. -->
<?php foreach ($errors as $error): ?>
    <?php
    $alertType = 'danger';
    $alertMessage = $error;
    require __DIR__ . '/../components/alert-box.php';
    ?>
<?php endforeach; ?>

    <!-- Erfolgsmeldung anzeigen. -->
<?php if ($successMessage !== ''): ?>
    <?php
    $alertType = 'success';
    $alertMessage = $successMessage;
    require __DIR__ . '/../components/alert-box.php';
    ?>
<?php endif; ?>

<?php if ($selectedUser): ?>
    <?php
    // Rollenanzeige für den ausgewählten Benutzer vorbereiten.
    $typeLabel = 'User';
    $typeClass = 'manage-badge-user';

    if ((int) $selectedUser['Type'] === 2)
    {
        $typeLabel = 'Admin';
        $typeClass = 'manage-badge-admin';
    }

    // Statusanzeige für den ausgewählten Benutzer vorbereiten.
    $stateLabel = 'Deaktiviert';
    $stateClass = 'manage-badge-inactive';

    if ((int) $selectedUser['State'] === 1)
    {
        $stateLabel = 'Aktiv';
        $stateClass = 'manage-badge-active';
    }

    // Vollständigen Namen für die Zusammenfassung vorbereiten.
    $selectedUserFullName = trim((string) ($selectedUser['FirstName'] ?? '') . ' ' . (string) ($selectedUser['LastName'] ?? ''));

    if ($selectedUserFullName === '')
    {
        $selectedUserFullName = 'Name unbekannt';
    }
    ?>

    <!-- Zusammenfassung des aktuell ausgewählten Benutzers. -->
    <section class="data-box manage-user-edit-section user-edit-summary">
        <h2>Ausgewählter Benutzer</h2>

        <div class="user-edit-summary-grid">
            <div>
                <strong>Name</strong>
                <span><?= e($selectedUserFullName); ?></span>
            </div>

            <div>
                <strong>E-Mail / Benutzername</strong>
                <span><?= e((string) $selectedUser['UserName']); ?></span>
            </div>

            <div>
                <strong>Rolle</strong>
                <span class="manage-badge <?= e($typeClass); ?>">
                    <?= e($typeLabel); ?>
                </span>
            </div>

            <div>
                <strong>Status</strong>
                <span class="manage-badge <?= e($stateClass); ?>">
                    <?= e($stateLabel); ?>
                </span>
            </div>
        </div>
    </section>

    <!-- Formular zum Bearbeiten der Profildaten. -->
    <section class="data-box manage-user-edit-section user-edit-profile-section">
        <h2>Profildaten</h2>

        <form method="post" class="manage-edit-form" action="<?= e(base_url('pages/manage-user-edit.php?id=' . (int) $selectedUserId)); ?>">
            <input type="hidden" name="formType" value="profile">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstName" class="form-label">Vorname</label>
                    <input
                            type="text"
                            class="form-control"
                            id="firstName"
                            name="firstName"
                            value="<?= e($firstName); ?>"
                            maxlength="50"
                            required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="lastName" class="form-label">Nachname</label>
                    <input
                            type="text"
                            class="form-control"
                            id="lastName"
                            name="lastName"
                            value="<?= e($lastName); ?>"
                            maxlength="50"
                            required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input
                        type="text"
                        class="form-control"
                        id="address"
                        name="address"
                        value="<?= e($address); ?>"
                        maxlength="255"
                        required
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">Stadt</label>
                    <input
                            type="text"
                            class="form-control"
                            id="city"
                            name="city"
                            value="<?= e($city); ?>"
                            maxlength="255"
                            required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="region" class="form-label">Region</label>
                    <input
                            type="text"
                            class="form-control"
                            id="region"
                            name="region"
                            value="<?= e($region); ?>"
                            maxlength="255"
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="country" class="form-label">Land</label>
                    <input
                            type="text"
                            class="form-control"
                            id="country"
                            name="country"
                            value="<?= e($country); ?>"
                            maxlength="255"
                            required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="postal" class="form-label">Postleitzahl</label>
                    <input
                            type="text"
                            class="form-control"
                            id="postal"
                            name="postal"
                            value="<?= e($postal); ?>"
                            maxlength="255"
                    >
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telefon</label>
                <input
                        type="text"
                        class="form-control"
                        id="phone"
                        name="phone"
                        value="<?= e($phone); ?>"
                        maxlength="255"
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-Mail / Benutzername</label>
                <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= e($email); ?>"
                        maxlength="100"
                        required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Profildaten speichern
            </button>
        </form>
    </section>

    <!-- Formular zum Ändern von Status und Rolle. -->
    <section class="data-box manage-user-edit-section user-edit-status-section">
        <h2>Status und Rolle</h2>

        <p class="text-muted">
            Rolle und Status können nur über feste Optionen geändert werden.
        </p>

        <?php
        // Aktuelle Rolle und aktuellen Status für die Select-Felder vorbereiten.
        $selectedTypeValue = 1;
        if ((int) $selectedUser['Type'] === 2)
        {
            $selectedTypeValue = 2;
        }

        $selectedStateValue = 0;
        if ((int) $selectedUser['State'] === 1)
        {
            $selectedStateValue = 1;
        }
        ?>

        <form method="post" class="manage-edit-form" action="<?= e(base_url('pages/manage-user-edit.php?id=' . (int) $selectedUserId)); ?>">
            <input type="hidden" name="formType" value="statusRole">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Rolle</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="1" <?php if ($selectedTypeValue === 1) { echo 'selected'; } ?>>
                            User
                        </option>
                        <option value="2" <?php if ($selectedTypeValue === 2) { echo 'selected'; } ?>>
                            Admin
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="state" class="form-label">Status</label>
                    <select id="state" name="state" class="form-select" required>
                        <option value="1" <?php if ($selectedStateValue === 1) { echo 'selected'; } ?>>
                            Aktiv
                        </option>
                        <option value="0" <?php if ($selectedStateValue === 0) { echo 'selected'; } ?>>
                            Deaktiviert
                        </option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Status und Rolle speichern
            </button>
        </form>
    </section>

    <!-- Formular zum Setzen eines neuen Passworts durch Administratoren. -->
    <section class="data-box manage-user-edit-section user-edit-password-section">
        <h2>Passwort setzen</h2>

        <p class="text-muted">
            Als Admin können Sie für diesen Benutzer ein neues Passwort vergeben.
        </p>

        <form method="post" class="manage-edit-form" action="<?= e(base_url('pages/manage-user-edit.php?id=' . (int) $selectedUserId)); ?>">
            <input type="hidden" name="formType" value="adminPassword">

            <div class="mb-3">
                <label for="newPassword" class="form-label">Neues Passwort</label>
                <input
                        type="password"
                        class="form-control"
                        id="newPassword"
                        name="newPassword"
                        minlength="8"
                        required
                >
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Neues Passwort bestätigen</label>
                <input
                        type="password"
                        class="form-control"
                        id="confirmPassword"
                        name="confirmPassword"
                        minlength="8"
                        required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Passwort speichern
            </button>
        </form>
    </section>
<?php endif; ?>

    <!-- Navigation zurück zur Benutzerverwaltung. -->
    <div class="manage-user-edit-actions">
        <?php
        $buttonText = 'Zurück zur Benutzerliste';
        $buttonHref = base_url('pages/manage-users.php');
        $buttonVariant = 'primary';
        require __DIR__ . '/../components/button.php';
        ?>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>