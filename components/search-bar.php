<form method="get" class="search-bar d-flex gap-2">

    <input
        type="search"
        name="q"
        class="form-control"
        placeholder="Kunstwerke oder Künstler suchen"
        value="<?= htmlspecialchars($currentSearch ?? '') ?>"
    >

    <button type="submit" class="btn btn-primary">
        Anwenden
    </button>

</form>