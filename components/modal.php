<?php
// Der Titel wird escaped; $modalContent darf bereits vorbereitetes Komponenten-HTML enthalten.
?>
<div class="custom-modal">

    <div class="custom-modal-content">

        <h2>
            <?= htmlspecialchars($modalTitle ?? 'Modal Title') ?>
        </h2>

        <div class="modal-body">
            <?= $modalContent ?? '' ?>
        </div>

        <button class="btn btn-secondary">
            Schließen
        </button>

    </div>

</div>
