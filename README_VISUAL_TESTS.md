Visual regression tests (Playwright)

Setup

1. Install dev dependencies:

```powershell
npm install -D @playwright/test
npx playwright install
```

2. Run tests and generate baseline snapshots:

```powershell
# create baseline (first run)
npx playwright test --update-snapshots

# run comparisons
npx playwright test
```

Notes

- Tests expect the dev server at `http://127.0.0.1:8000` (run `php artisan serve`).
- Baseline images will be stored by Playwright under `tests/visual/test-results` (default snapshot folder). Adjust test config as needed.
