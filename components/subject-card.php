<article class="col">
    <div class="card h-100 shadow-sm ui-card">
        <img
            src="<?= e(subjectImageUrl((int) $subject->getSubjectid())); ?>"
            class="card-img-top entity-card-img"
            alt="<?= e($subject->getSubjectname()); ?>"
        >
        <div class="card-body">

            <h2 class="h5 card-title">
                <?= e($subject->getSubjectname()); ?>
            </h2>

            <p class="card-text text-muted">
                Themen-ID: <?= e((string) $subject->getSubjectid()); ?>
            </p>

            <?php
            $buttonText = 'Ansehen';
            $buttonHref = 'single-subject.php?id=' . urlencode($subject->getSubjectid());
            $buttonVariant = 'primary';
            include __DIR__ . '/button.php';
            ?>

        </div>
    </div>
</article>
