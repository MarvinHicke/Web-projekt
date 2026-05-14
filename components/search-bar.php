<form method="get" class="search-bar d-flex gap-2">

    <input
        type="search"
        name="q"
        class="form-control"
        placeholder="Search artworks or artists"
        value="<?= htmlspecialchars($currentSearch ?? '') ?>"
    >

    <button type="submit" class="btn btn-primary">
        Suchen
    </button>

</form>