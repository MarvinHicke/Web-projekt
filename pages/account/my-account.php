<?php
/**
 * Mein Konto / My Account (UC 23)
 *
 * Zeigt Benutzer-Daten, Passwort-Verwaltung, Reviews und Favoriten.
 * Backend-Logik wird später implementiert.
 */

$pageTitle = 'Mein Konto · Art Gallery';
require_once __DIR__ . '/../../includes/header.php';

// TODO: Authentifizierung prüfen - nur eingeloggte User dürfen diese Seite sehen
// if (!isset($_SESSION['user_id'])) {
//     header('Location: /pages/auth/login.php');
//     exit;
// }
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Mein Konto</h1>

            <!-- Fehler-/Erfolgsmeldungen (Placeholder) -->
            <div id="alertContainer" class="mb-4">
                <!-- Alerts werden hier eingefügt -->
            </div>

            <!-- Tabs für verschiedene Abschnitte -->
            <ul class="nav nav-tabs mb-4" id="accountTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                        Profil
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                        Passwort
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                        Meine Reviews
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites" type="button" role="tab" aria-controls="favorites" aria-selected="false">
                        Meine Favoriten
                    </button>
                </li>
            </ul>

            <!-- Tab-Inhalte -->
            <div class="tab-content" id="accountTabContent">

                <!-- Tab 1: Profil -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Profil-Informationen</h5>

                            <!-- Profil-Daten (Anzeige nur) -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="text-muted small">Vorname</p>
                                    <p class="fw-bold">Max</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted small">Nachname</p>
                                    <p class="fw-bold">Mustermann</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="text-muted small">Email</p>
                                    <p class="fw-bold">max.mustermann@example.com</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted small">Benutzername</p>
                                    <p class="fw-bold">maxmustern</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="text-muted small">Stadt</p>
                                    <p class="fw-bold">Berlin</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted small">Land</p>
                                    <p class="fw-bold">Deutschland</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <p class="text-muted small">Adresse</p>
                                    <p class="fw-bold">Hauptstraße 123</p>
                                </div>
                            </div>

                            <!-- Edit Profile Button (Placeholder) -->
                            <button type="button" class="btn btn-primary" disabled title="Wird später implementiert">
                                Profil bearbeiten
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Passwort -->
                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Passwort ändern</h5>

                            <form id="passwordForm" method="POST" action="my-account.php" novalidate>

                                <!-- Aktuelles Passwort -->
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Aktuelles Passwort <span class="text-danger">*</span></label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="current_password"
                                        name="current_password"
                                        placeholder="••••••••"
                                        required
                                    >
                                </div>

                                <!-- Neues Passwort -->
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Neues Passwort <span class="text-danger">*</span></label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="new_password"
                                        name="new_password"
                                        placeholder="••••••••"
                                        title="Min. 8 Zeichen: Groß-/Kleinbuchstaben, Ziffer, Sonderzeichen"
                                        required
                                    >
                                    <div class="form-text">
                                        Mindestens 8 Zeichen mit Großbuchstaben, Kleinbuchstaben, Ziffer und Sonderzeichen.
                                    </div>
                                </div>

                                <!-- Passwort Bestätigung -->
                                <div class="mb-4">
                                    <label for="new_password_confirm" class="form-label">Passwort bestätigen <span class="text-danger">*</span></label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="new_password_confirm"
                                        name="new_password_confirm"
                                        placeholder="••••••••"
                                        required
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary" disabled title="Wird später implementiert">
                                    Passwort ändern
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Meine Reviews -->
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Meine Reviews</h5>

                            <!-- TODO: Reviews Liste wird hier eingefügt -->
                            <p class="text-muted">Noch keine Reviews geschrieben.</p>

                            <!-- Placeholder für Review-Liste -->
                            <!--
                            <div class="review-item mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6>Artwork-Title</h6>
                                        <p class="mb-1">Rating: ★★★★★</p>
                                        <p class="small text-muted">Datum: 12.05.2026</p>
                                        <p>Review Text...</p>
                                    </div>
                                    <button class="btn btn-sm btn-danger">Löschen</button>
                                </div>
                            </div>
                            -->
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Meine Favoriten -->
                <div class="tab-pane fade" id="favorites" role="tabpanel" aria-labelledby="favorites-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Meine Favoriten</h5>

                            <!-- Unternavigation für Favoriten -->
                            <ul class="nav nav-pills mb-3" id="favoritesNav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="favorite-artists-tab" data-bs-toggle="tab" data-bs-target="#favorite-artists" type="button" role="tab">
                                        Künstler
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="favorite-artworks-tab" data-bs-toggle="tab" data-bs-target="#favorite-artworks" type="button" role="tab">
                                        Kunstwerke
                                    </button>
                                </li>
                            </ul>

                            <!-- Künstler Favoriten -->
                            <div class="tab-content" id="favoritesTabContent">
                                <div class="tab-pane fade show active" id="favorite-artists" role="tabpanel" aria-labelledby="favorite-artists-tab">
                                    <p class="text-muted">Noch keine Künstler zu Favoriten hinzugefügt.</p>
                                    <!-- TODO: Artists Liste wird hier eingefügt -->
                                </div>

                                <!-- Kunstwerk Favoriten -->
                                <div class="tab-pane fade" id="favorite-artworks" role="tabpanel" aria-labelledby="favorite-artworks-tab">
                                    <p class="text-muted">Noch keine Kunstwerke zu Favoriten hinzugefügt.</p>
                                    <!-- TODO: Artworks Liste wird hier eingefügt -->
                                </div>
                            </div>

                            <p class="small text-muted mt-3">
                                Zu <a href="favorites.php">vollständiger Favoritenliste</a> wechseln.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar (optional) -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Account-Menü</h5>
                    <ul class="list-unstyled">
                        <li><a href="my-account.php" class="text-decoration-none">Mein Konto</a></li>
                        <li><a href="favorites.php" class="text-decoration-none">Favoriten</a></li>
                        <li><a href="logout.php" class="text-decoration-none text-danger">Abmelden</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

