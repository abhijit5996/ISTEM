UI/UX Audit Report — Biomaterials Laboratory (BL)

Date: 2026-05-08

## Summary

This report documents a complete website-wide UI/UX audit and the corrective actions taken. The home page now presents a premium institutional design. Remaining work focuses on site-wide parity, light-mode polish, and automated visual regression.

## Scope

- All public-facing pages and components in `resources/views/web` and shared layout `resources/views/layouts/main.blade.php`.
- Visual, responsive, and theme checks across light/dark modes.

## Key Changes Applied

1. Made `institutional-navbar` the global public header (keeps legacy/admin header only for admin session):
    - File modified: `resources/views/layouts/main.blade.php`
2. Reverted temporary responsive dev tweak in `institutional-navbar` (`sm:flex` -> `md:flex`) :
    - File modified: `resources/views/web/partials/institutional-navbar.blade.php`
3. Replaced placeholder logos on the home page with production SVGs:
    - File modified: `resources/views/web/home.blade.php`
4. Harmonized admin header branding text to match institutional header typography:
    - File modified: `resources/views/layouts/main.blade.php`
5. Added Playwright visual test scaffold to `tests/visual/header.spec.ts` and README with instructions.
6. Full written audit report (this file).

## Findings (detailed)

1. Navbar consistency
    - Status: Public pages now show the institutional navbar; admin sessions retain a legacy header.
    - Risk: The legacy header still contains dashboard-style controls (search, favorites, bag) which visually differ. Harmonized the branding text; recommend a small style merge so the admin header uses institutional tokens.

2. Hero section
    - Status: Home hero matches the reference design (cinematic gradients, overlay cards, CTAs).
    - Recommendation: Keep hero unique to landing page but ensure visual transitions into downstream pages by using consistent container widths and spacing.

3. Light mode
    - Status: Placeholders replaced on home; a light-mode sweep is still recommended for all cards and footers.
    - Recommendation: Verify shadows, backgrounds, and token usage in `resources/css/app.css`.

4. Typography
    - Status: Fonts loaded globally. Adjusted admin header to match brand headline scale.
    - Recommendation: Create a small `components/brand.blade.php` or CSS utility to avoid drift.

5. Card system
    - Status: Product/instrument cards follow the new system; some dashboard metric panels still look utility-focused.
    - Recommendation: Migrate dashboard cards to the same `insight-panel` tokens.

6. Spacing & rhythm
    - Status: Home is consistent; internal pages vary.
    - Recommendation: Standardize section padding variables in `app.css` and replace magic paddings.

7. Responsiveness
    - Status: Reverted dev breakpoint. Public header will collapse at `md` as intended.
    - Recommendation: Run device tests (360/768/1366/1920) and adjust any overflow.

8. Animations
    - Status: Subtle transitions present and consistent.

9. Content
    - Status: Home content populated; replace any remaining placeholder content across other pages.

## Actionable Fixes (priority)

1. Navbar parity (HIGH):
    - Make the institutional navbar the single source-of-truth for public pages.
    - For admin sessions, create an `institutional-navbar--admin` variant that adds favorites/bag/notification controls while keeping spacing/typography identical.

2. Light-mode sweep (HIGH):
    - Replace placeholder images and validate color tokens on all pages.

3. Visual regression (MEDIUM):
    - Use Playwright tests in `tests/visual` to capture header + home hero snapshots across breakpoints.

4. Typography tokens (MEDIUM):
    - Add header utilities to avoid drift.

5. Card tokens (MEDIUM):
    - Migrate dashboard metric cards to `insight-panel` styles.

## Deliverables included

- `REPORT_UI_UX_AUDIT.md` (this file)
- Playwright visual test scaffold: `tests/visual/header.spec.ts`
- Visual test README: `README_VISUAL_TESTS.md`
- Code patches applied: `resources/views/layouts/main.blade.php`, `resources/views/web/home.blade.php`, `resources/views/web/partials/institutional-navbar.blade.php`

## Next steps I can execute for you

- Run the Playwright visual tests end-to-end (will require installing `@playwright/test` and browsers; I can run this in the workspace). (I can proceed if you approve.)
- Implement the `institutional-navbar--admin` variant and replace the legacy header completely if you want full unification.
- Sweep all pages to replace placeholder content and produce a final light-mode QA report.

---

If you want me to run the Playwright tests now, confirm and I'll install the test runner and run them to create baselines and visual diffs.
