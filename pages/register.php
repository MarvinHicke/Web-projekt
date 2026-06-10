<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../repositories/customerRepository.php';
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

$nameMaxLength=50;
$textMaxLength=255;
$emailMaxLength=100;
$passwordMinLength=8;
if (($_SERVER["REQUEST_METHOD"] ?? "GET") === "POST") {
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
    if ($address === "")
    {
        $errors[] = "Bitte geben Sie eine Adresse ein.";
    }

    if (strlen($address) > $textMaxLength)
    {
        $errors[] = "Die Adresse darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    if ($city === "")
    {
        $errors[] = "Bitte geben Sie eine Stadt ein.";
    }

    if (strlen($city) > $textMaxLength)
    {
        $errors[] = "Die Stadt darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }

    if ($country === "")
    {
        $errors[] = "Bitte geben Sie ein Land ein.";
    }

    if (strlen($country) > $textMaxLength)
    {
        $errors[] = "Das Land darf maximal " . $textMaxLength . " Zeichen lang sein.";
    }
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
        $customerRepository = new customerRepository(db());

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

            if ($created) {
                $user = $customerRepository->GetByUsername($email);
                $_SESSION['user'] = [
                    'CustomerID' => (int)$user['CustomerID'],
                    'UserName' => (string)$user['UserName'],
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
require_once __DIR__ . "/../includes/header.php";
?>

<h1>Registrieren</h1>
<p>Erstellen Sie ein neues Konto</p>

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

<form method="POST">
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
        <label for="address">Adresse</label>
        <input
                type="text"
                id="address"
                name="address"
                required
                maxlength="<?= $textMaxLength ?>"
                value="<?= e($address) ?>"
        >
    </div>

    <div>
        <label for="city">Stadt</label>
        <input
                type="text"
                id="city"
                name="city"
                required
                maxlength="<?= $textMaxLength ?>"
                value="<?= e($city) ?>"
        >
    </div>

    <div>
        <label for="region">Region</label>
        <input
                type="text"
                id="region"
                name="region"
                maxlength="<?= $textMaxLength ?>"
                value="<?= e($region) ?>"
        >
    </div>

    <div>
        <label for="country">Land</label>
        <input
                type="text"
                id="country"
                name="country"
                required
                maxlength="<?= $textMaxLength ?>"
                value="<?= e($country) ?>"
        >
    </div>

    <div>
        <label for="postal">Postleitzahl</label>
        <input
                type="text"
                id="postal"
                name="postal"
                maxlength="<?= $textMaxLength ?>"
                value="<?= e($postal) ?>"
        >
    </div>

    <div>
        <label for="phone">Telefon</label>
        <input
                type="tel"
                id="phone"
                name="phone"
                maxlength="<?= $textMaxLength ?>"
                value="<?= e($phone) ?>"
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
require_once __DIR__ . "/../includes/footer.php";
?>
