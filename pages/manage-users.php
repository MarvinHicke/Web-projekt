<?php
/**
 * Admin-Übersicht für UC21.
 *
 * Zeigt alle Benutzerkonten mit Rolle, Status und Registrierungsdatum an und
 * verlinkt auf die Detailbearbeitung einzelner Benutzer.
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';

// Seitentitel für die Benutzerverwaltung setzen.
$pageTitle = 'Benutzer verwalten';

// Zugriffsschutz: Nur Administratoren dürfen die Benutzerübersicht öffnen.
if (!isAdmin())
{
    header('Location: ' . base_url('index.php'));
    exit;
}

// Repository initialisieren und alle Benutzerkonten laden.
$customerRepository = new customerRepository(db());
$users = $customerRepository->findAll();

// Gemeinsamen Header einbinden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Überschrift der Admin-Benutzerübersicht. -->
    <section class="page-heading manage-users-heading">
        <h1>Benutzer verwalten</h1>
        <p>Übersicht aller registrierten Benutzerkonten.</p>
    </section>

    <!-- Tabellenbereich mit allen Benutzerkonten. -->
    <section class="data-box manage-users-section">
        <div class="manage-users-table-wrapper">
            <table class="table table-striped align-middle manage-users-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-Mail / Benutzername</th>
                    <th>Ort</th>
                    <th>Rolle</th>
                    <th>Status</th>
                    <th>Registriert seit</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                    // Rollenbezeichnung und CSS-Klasse für den aktuellen Benutzer vorbereiten.
                    $typeLabel = 'User';
                    $typeClass = 'manage-badge-user';

                    if ((int) $user['Type'] === 2)
                    {
                        $typeLabel = 'Admin';
                        $typeClass = 'manage-badge-admin';
                    }

                    // Statusbezeichnung und CSS-Klasse für den aktuellen Benutzer vorbereiten.
                    $stateLabel = 'Deaktiviert';
                    $stateClass = 'manage-badge-inactive';

                    if ((int) $user['State'] === 1)
                    {
                        $stateLabel = 'Aktiv';
                        $stateClass = 'manage-badge-active';
                    }

                    // Ort aus Stadt und Land zusammensetzen.
                    $city = trim((string) ($user['City'] ?? ''));
                    $country = trim((string) ($user['Country'] ?? ''));
                    $location = trim($city . ', ' . $country, ' ,');

                    // Fallback anzeigen, falls kein Ort hinterlegt ist.
                    if ($location === '')
                    {
                        $location = '–';
                    }

                    // Registrierungsdatum vorbereiten und falls möglich formatieren.
                    $dateJoined = '–';

                    if (!empty($user['DateJoined']))
                    {
                        $timestamp = strtotime((string) $user['DateJoined']);

                        if ($timestamp !== false)
                        {
                            $dateJoined = date('d.m.Y', $timestamp);
                        }
                    }
                    ?>
                    <tr>
                        <td><?= e((string) $user['CustomerID']); ?></td>
                        <td><?= e((string) $user['FirstName'] . ' ' . (string) $user['LastName']); ?></td>
                        <td><?= e((string) $user['UserName']); ?></td>
                        <td><?= e($location); ?></td>

                        <td>
                        <span class="manage-badge <?= e($typeClass); ?>">
                            <?= e($typeLabel); ?>
                        </span>
                        </td>

                        <td>
                        <span class="manage-badge <?= e($stateClass); ?>">
                            <?= e($stateLabel); ?>
                        </span>
                        </td>

                        <td><?= e($dateJoined); ?></td>
                        <td>
                            <?php
                            // Button zur Bearbeitungsseite des aktuellen Benutzers vorbereiten.
                            $buttonText = 'Bearbeiten';
                            $buttonHref = base_url('pages/manage-user-edit.php?id=' . (int) $user['CustomerID']);
                            $buttonVariant = 'outline-primary';
                            require __DIR__ . '/../components/button.php';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>