<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';

if (!isLoggedIn()) {
    header('Location: ' . base_url('pages/login.php'));
    exit;
}

$currentUser = $_SESSION['user'];

$customerRepository = new customerRepository(db());
$accountUser = $customerRepository->GetById((int) $currentUser['CustomerID']);

if (!$accountUser)
{
    $accountUser = $currentUser;
}

$firstName = (string) ($accountUser['FirstName'] ?? '');
$lastName = (string) ($accountUser['LastName'] ?? '');
$email = (string) ($accountUser['Email'] ?? $accountUser['UserName'] ?? '');
$userType = (int) ($accountUser['Type'] ?? 1);

$fullName = trim($firstName . ' ' . $lastName);
$roleLabel = $userType === 2 ? 'Admin' : 'User';
$dateJoinedRaw = (string) ($accountUser['DateJoined'] ?? '');
$dateJoinedLabel = 'Nicht angegeben';

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

$pageTitle = "Mein Konto";
require_once __DIR__ . "/../includes/header.php";
?>

<h1>Mein Konto</h1>
<p>Hier werden später deine Kontodaten angezeigt.</p>

<h2>Kontodaten</h2>
<ul>
    <li>
        <strong>Name:</strong>
        <?= e($fullName !== '' ? $fullName : 'Nicht angegeben') ?>
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

<h2>Meine Reviews</h2>
<p>Hier werden später deine eigenen Bewertungen angezeigt.</p>

<h2>Meine Favoriten</h2>
<p>Hier kannst du deine favorisierten Künstler und Kunstwerke ansehen.</p>

<?php
$buttonText = 'Favoriten anzeigen';
$buttonHref = base_url('pages/favorites.php');
$buttonVariant = 'primary';
include __DIR__ . '/../components/button.php';
?>
<?php
$buttonText = 'Abmelden';
$buttonHref = base_url('pages/logout.php');
$buttonVariant = 'danger';
include __DIR__ . '/../components/button.php';
?>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
