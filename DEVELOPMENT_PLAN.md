# Development Plan

Pending items across KolekOder (backend) and kolekoder-fe (frontend).
Last updated: 2026-06-04

## 🔴 Bugs / UX issues

- [ ] **Loyalty card disappears at 0 stamps** — `Customer/DashboardController@apiRewards`
  filters `collect_points > 0`, so right after redeeming, the customer's card vanishes
  from the rewards page (verified live). Show the empty card instead — e.g. include any
  shop the customer has a record at, regardless of points.
- [ ] **No nav link to the redeem page** — `/shop/redeem` (`ShopRedemptionPage`) is routed
  in `App.jsx` but never linked in `ShopLayout` nav. Barista has to type the URL manually.
  Add a "Redeem" item to the shop nav.
- [ ] **Pre-existing lint error** in `CustomerRewardsPage.jsx` —
  `react-hooks/set-state-in-effect` on the `fetchData()` effect (+ missing dependency
  warning). Refactor the data fetch pattern.

## 🟡 Redemption flow improvements

- [ ] **QR code in the customer redemption modal** — show a scannable QR alongside the
  6-char code. Shop side already ships `@yudiel/react-qr-scanner`, so the barista could
  scan instead of typing.
- [ ] **Recover an active pending code** — if the customer closes the modal or reloads,
  a still-valid pending code is gone from view. Add a "View code" button on pending
  redemptions in history to reopen the modal with the countdown.
- [ ] **Live "code used" feedback** — use Echo/Reverb so when the barista applies the
  code, the customer's modal flips to a success state and stamps refresh instantly.

## 🟢 Polish / nice-to-have

- [ ] **Stamp rendering with busy logos** — shops whose logo is a QR code or detailed
  image make the stamp grid look noisy (ghosted empty slots amplify it). Consider a
  shop setting or fallback to the coffee icon for busy logos.
- [ ] **FE bundle size** — main chunk is ~746 kB (>500 kB warning). Add route-based
  code splitting / `manualChunks`.

## ⚙️ Ops / before deploy

- [ ] **Flip `.env` in kolekoder-fe before pushing** — single `.env` workflow: whatever
  `VITE_API_BASE_URL` line is uncommented at push time is what Vercel builds.
  Prod = `https://kolekoder.com`.
- [ ] **Verify Reverb prod settings** — the prod block in `.env` assumes
  `kolekoder.com:443` over wss. Confirm the actual prod Reverb host/port/key.
- [ ] **Commit pending work** — both repos carry substantial uncommitted changes
  (redemption/loyalty feature, migrations, stamp-card UI, env centralization).

## ✅ Done (2026-06-04, for reference)

- Centralized FE API URLs in `src/lib/config.js`, single `.env`, `storageUrl()` helper
- Stamp-card rewards UI (shop logo stamps, gift slot, threshold-driven grid)
- Auth token sent on public checkout → logged-in orders auto-link (no claim needed)
- Fixed Laravel 12 incompatibility (`getDoctrineSchemaManager`) in perf migration
- Ran pending migrations (redemptions table + shop loyalty columns)
- Verified end-to-end: auto-stamp on completion, guest claim, redeem → verify → apply,
  double-claim/double-redeem protection, UI via browser
