import { test, expect } from '@playwright/test';

const PAGES = ['/', '/instruments', '/bag', '/login'];

for (const page of PAGES) {
  test(`visual: ${page}`, async ({ page: p }) => {
    await p.goto(`http://127.0.0.1:8000${page}`, { waitUntil: 'networkidle' });
    await p.setViewportSize({ width: 1280, height: 900 });
    // capture header + hero region
    const header = await p.locator('header').first();
    await expect(header).toHaveScreenshot(`header${page === '/' ? '-home' : page.replace('/', '-')}.png`, { maxDiffPixelRatio: 0.002 });

    // full page screenshot for manual review
    await p.screenshot({ path: `visual-${page === '/' ? 'home' : page.replace('/', '-')}.png`, fullPage: true });
  });
}
