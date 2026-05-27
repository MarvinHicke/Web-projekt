<form method="get" class="sort-bar d-flex gap-2 align-items-center mb-4">

    <label for="sort" class="form-label mb-0">Sort by</label>

    <select id="sort" name="sort" class="form-select w-auto">
        <?php foreach (($sortOptions ?? []) as $value => $label): ?>
            <option
                value="<?= htmlspecialchars($value) ?>"
                <?= (($currentSort ?? '') === $value) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-outline-primary">
        Apply
    </button>

</form>