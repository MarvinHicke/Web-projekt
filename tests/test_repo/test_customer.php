<?php

require_once __DIR__ . "/../../repositories/customerRepository.php";
require_once __DIR__ . '/../../includes/dbaccess.php';

$db = new dbaccess();
$db->connect();

$customerRepo = new customerRepository($db);

$cData =
    [
    'FirstName' => 'Max',
    'LastName'  => 'Mustermann',
    'Address'   => 'Musterstraße 1',
    'City'      => 'Musterstadt',
    'Region'    => null,
    'Country'   => 'Germany',
    'Postal'    => '12345',
    'Phone'     => '012345',
    'Email'     => 'max@mustermann.de'
];

$password = 'passwort123';
$username = 'max_' . time();
$lData =
[
    'UserName' => $username,
    'Pass'     => password_hash($password, PASSWORD_BCRYPT)
];

if ($customerRepo->create($cData, $lData))
{
    echo "Test 1 (create) erfolgreich.\n";
} else
{
    echo "Test 1 (create) FEHLGESCHLAGEN.\n";
    exit;
}

$user = $customerRepo->GetByUsername($username);
if ($user && password_verify($password, $user['Pass']))
{
    echo "Test 2 (GetByUsername) erfolgreich.\n";
    $id = $user['CustomerID'];
} else
{
    echo "Test 2 (GetByUsername) FEHLGESCHLAGEN.\n";
}

$userById = $customerRepo->GetById($id);
if ($userById && $userById['FirstName'] === 'Max')
{
    echo "Test 3 (GetById) erfolgreich.\n";
} else
{
    echo "Test 3 (GetById) FEHLGESCHLAGEN.\n";
}

$cData['FirstName'] = 'Maximilian';
if ($customerRepo->update($id, $cData))
{
    $updatedUser = $customerRepo->GetById($id);
    if ($updatedUser && $updatedUser['FirstName'] === 'Maximilian')
    {
        echo "Test 4 (update) erfolgreich.\n";
    } else
    {
        echo "Test 4 (update) FEHLGESCHLAGEN: Name nicht geändert.\n";
    }
} else
{
    echo "Test 4 (update) FEHLGESCHLAGEN: Query-Fehler.\n";
}

$newPassword = 'neuespasswort456';
$newHash = password_hash($newPassword, PASSWORD_BCRYPT);
if ($customerRepo->updatePassword($id, $newHash))
{
    $checkUser = $customerRepo->GetByUsername($username);
    if ($checkUser && password_verify($newPassword, $checkUser['Pass']))
    {
        echo "Test 5 (updatePassword) erfolgreich.\n";
    } else
    {
        echo "Test 5 (updatePassword) FEHLGESCHLAGEN: Passwort verifiziert nicht.\n";
    }
} else
{
    echo "Test 5 (updatePassword) FEHLGESCHLAGEN.\n";
}

if ($customerRepo->updateState($id, 0))
{
    $checkUser = $customerRepo->GetById($id);
    if ($checkUser && (int)$checkUser['State'] === 0)
    {
        echo "Test 6 (updateState) erfolgreich.\n";
    } else
    {
        echo "Test 6 (updateState) FEHLGESCHLAGEN.\n";
    }
} else
{
    echo "Test 6 (updateState) FEHLGESCHLAGEN.\n";
}

if ($customerRepo->elevateToAdmin($id))
{
    $checkUser = $customerRepo->GetById($id);
    if ($checkUser && (int)$checkUser['Type'] === 2)
    {
        echo "Test 7 (elevateToAdmin) erfolgreich.\n";
    } else
    {
        echo "Test 7 (elevateToAdmin) FEHLGESCHLAGEN.\n";
    }
} else
{
    echo "Test 7 (elevateToAdmin) FEHLGESCHLAGEN.\n";
}

$allUsers = $customerRepo->findAll();
if (is_array($allUsers) && count($allUsers) > 0)
{
    echo "Test 8 (findAll) erfolgreich. Anzahl User: " . count($allUsers) . "\n";
} else
{
    echo "Test 8 (findAll) FEHLGESCHLAGEN.\n";
}

$db->close();