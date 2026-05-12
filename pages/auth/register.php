<?php
/**
 * Register-Formular für neue Benutzer (UC 20)
 *
 * Zeigt ein Registrierungsformular mit HTML5-Validierung.
 * Backend-Logik wird später implementiert.
 */

$pageTitle = 'Registrieren · Art Gallery';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title mb-4">Registrieren</h1>
                    <p class="text-muted mb-4">Erstellen Sie einen neuen Account um Reviews zu schreiben und Favoriten zu speichern.</p>

                    <!-- Fehler-/Erfolgsmeldungen (Placeholder) -->
                    <div id="alertContainer" class="mb-4">
                        <!-- Alerts werden hier eingefügt -->
                    </div>

                    <!-- Register-Formular -->
                    <form id="registerForm" method="POST" action="register.php" novalidate>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="your.email@example.com"
                                required
                            >
                            <div class="form-text">Wird als Login verwendet.</div>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Benutzername <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                placeholder="username"
                                pattern="[a-zA-Z0-9_]{3,20}"
                                title="3-20 Zeichen, nur Buchstaben, Zahlen und Unterstrich"
                                required
                            >
                            <div class="form-text">3-20 Zeichen, alphanumerisch + Unterstrich.</div>
                        </div>

                        <!-- Vorname -->
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Vorname <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="firstname"
                                name="firstname"
                                placeholder="Max"
                                required
                            >
                        </div>

                        <!-- Nachname -->
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Nachname <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="lastname"
                                name="lastname"
                                placeholder="Mustermann"
                                required
                            >
                        </div>

                        <!-- Stadt -->
                        <div class="mb-3">
                            <label for="city" class="form-label">Stadt <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="city"
                                name="city"
                                placeholder="Berlin"
                                required
                            >
                        </div>

                        <!-- Adresse -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="address"
                                name="address"
                                placeholder="Hauptstraße 123"
                                required
                            >
                        </div>

                        <!-- Land -->
                        <div class="mb-3">
                            <label for="country" class="form-label">Land <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="country"
                                name="country"
                                placeholder="Deutschland"
                                required
                            >
                        </div>

                        <!-- Passwort -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Passwort <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
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
                            <label for="password_confirm" class="form-label">Passwort bestätigen <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                class="form-control"
                                id="password_confirm"
                                name="password_confirm"
                                placeholder="••••••••"
                                required
                            >
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Registrieren
                        </button>
                    </form>

                    <!-- Link zu Login -->
                    <div class="text-center">
                        <p class="mb-0">
                            Bereits registriert?
                            <a href="login.php">Hier anmelden</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

