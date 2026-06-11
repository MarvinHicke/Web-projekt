<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';

$pageTitle = 'Benutzer verwalten';

if (!isAdmin())
{
    header('Location: ' . base_url('index.php'));
    exit;
}

$customerRepository = new customerRepository(db());
$users = $customerRepository->findAll();

require_once __DIR__ . '/../includes/header.php';
?>

    <h1>Benutzer verwalten</h1>

    <p class="text-muted">
        Übersicht aller registrierten Benutzerkonten.
    </p>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
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
                $typeLabel = 'User';
                if ((int) $user['Type'] === 2)
                {
                    $typeLabel = 'Admin';
                }

                $stateLabel = 'Deaktiviert';
                if ((int) $user['State'] === 1)
                {
                    $stateLabel = 'Aktiv';
                }
                ?>
                <tr>
                    <td><?= e((string) $user['CustomerID']); ?></td>
                    <td><?= e((string) $user['FirstName'] . ' ' . (string) $user['LastName']); ?></td>
                    <td><?= e((string) $user['UserName']); ?></td>
                    <td><?= e((string) $user['City'] . ', ' . (string) $user['Country']); ?></td>
                    <td><?= e($typeLabel); ?></td>
                    <td><?= e($stateLabel); ?></td>
                    <td><?= e((string) $user['DateJoined']); ?></td>
                    <td>
                        <?php
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>