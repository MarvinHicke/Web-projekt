<?php
/**
 * Login-Formular für authentifizierte Benutzer (UC 22)
 *
 * Zeigt ein Login-Formular mit HTML5-Validierung.
 * Backend-Logik wird später implementiert.
 */

$pageTitle = 'Anmelden · Art Gallery';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title mb-4">Anmelden</h1>

                    <!-- Fehler-/Erfolgsmeldungen (Placeholder) -->
                    <div id="alertContainer" class="mb-4">
                        <!-- Alerts werden hier eingefügt -->
                    </div>

                    <!-- Login-Formular -->
                    <form id="loginForm" method="POST" action="login.php" novalidate>

                        <!-- Email oder Username -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email oder Benutzername <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="your.email@example.com oder username"
                                required
                                autofocus
                            >
                        </div>

                        <!-- Passwort -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Passwort <span class="text-danger">*</span></label>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                            >
                        </div>

                        <!-- Remember Me (optional, für später) -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="remember"
                                    name="remember"
                                >
                                <label class="form-check-label" for="remember">
                                    Anmeldedaten merken
                                </label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Anmelden
                        </button>
                    </form>

                    <!-- Links -->
                    <div class="text-center">
                        <p class="mb-2">
                            <a href="forgot-password.php" class="text-decoration-none small">Passwort vergessen?</a>
                        </p>
                        <p class="mb-0">
                            Noch kein Account?
                            <a href="register.php">Hier registrieren</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

