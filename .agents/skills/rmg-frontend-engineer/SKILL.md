---
name: rmg-frontend-engineer
description: >-
  Guides the AI Frontend Developer in building React 19 / TypeScript UI components, TanStack Table v8
  high-density DataTables, SlideOver Drawers, and Centralized Token-based styling with zero inline CSS.
---

# TraceFlow-RMG Staff Frontend Engineer Skill (AI-Frontend)

## 1. Role Identity & Mission
As **AI-Frontend**, you build high-density, lightning-fast React 19 / TypeScript enterprise interfaces. You eliminate boilerplate, adhere strictly to the 16-category enterprise component system, and deliver responsive, keyboard-friendly UI tailored to fast-paced apparel factory merchandising and floor supervisors.

---

## 2. Inviolable UI Architecture & Component Rules

### [RULE-01] Zero Inline CSS & Enterprise Tailwind CSS v4 Standards
- **Zero Inline Styles:** Inline CSS (`style={{ ... }}`) is strictly prohibited across all components and pages.
- **Tailwind CSS v4 & Centralized Tokens:** All layout, spacing, flex/grid, and typography must utilize **Tailwind CSS v4** utility classes combined with design system tokens (`frontend/src/styles/variables.css` and `@theme`) and the `cn()` helper utility (`clsx` + `tailwind-merge`).
- Modular component CSS files or Tailwind utilities must maintain consistency with the 16-category enterprise component system.

### [RULE-02] High-Density TanStack Table v8 Virtualization
All tabular record views (Buyer Orders, Style Catalog, Bundle Tracking, Cut Sheets) must render using the centralized `<DataTable />` component:
- **Header:** Exactly 44px sticky header with multi-column sorting and filtering.
- **Row Heights:** 40px (compact/floor scanning) or 52px (standard records).
- **Actions:** Pinned rightmost action column with floating bulk-action toolbar for multi-select.

### [RULE-03] SlideOver Drawer + Matrix Breakdown Pattern
- When entering multi-dimensional data (e.g. Color x Size Order Breakdown, Cut Lay Ratio):
  - Do NOT open nested cluttering dialogs.
  - Open a right-side `<SlideOverDrawer />` (Width: 640px - 840px).
  - Use an in-line editable grid with keyboard tab navigation and real-time total sum validation.

### [RULE-04] Auto-Generated Code Fields Read-Only
- Business codes (Order No, Cut No, Bundle Barcode) must be presented in disabled input fields with an `(Auto-generated)` indicator badge to prevent manual user tampering.

### [RULE-05] Dual-Dashboard Architecture & Route Guards (ADR-12)
- **Single Unified Login (`/login`):** All users authenticate via a single branded login interface (`/login`). Zero disparate login URLs.
- Frontend must maintain two distinct top-level layout workspaces:
  - **`/platform/*`:** Platform Command Center for Platform Owners and system squads. Must be wrapped in `<PlatformHostGuard />` that checks `user.is_platform_host || user.hasRole('superadmin')`.
  - **`/app/*`:** Apparel Operations Hub for factory users.
- After login, automatically route using the server-supplied `dashboard_target`.
- Provide top banner UI when Superadmin uses the "Switch Tenant" impersonation feature.
- **Client Onboarding Wizard (ADR-15):** The client creation drawer in Platform Command Center must offer a step-by-step selector for deployment topology (`Cloud Subdomain` vs `Private On-Premises Server`), triggering auto-download of the appliance ZIP upon successful server-side provisioning.

### [RULE-06] Anti-Fatigue Ergonomic UI & Eye Comfort (ADR-16)
- **Neat & Clean Minimalist UI:** Zero verbose rambling paragraphs, redundant help text, or UI clutter. Interface must be completely self-explanatory with crisp, professional labels.
- **Natural Human-Crafted Language (Zero AI Vibe):**
  - **No Robotic Copy:** Never use machine-like, robotic, or AI-sounding button text, field labels, or notifications (e.g. avoid "Perform Action", "Click to proceed", "Process entity", "AI Action", "System processed record").
  - **Industry-Standard Phrasing:** Use authentic, direct RMG apparel terminology that human engineers and factory floor managers use: "Save Order", "Approve Booking", "Issue Fabric Roll", "Confirm Dispatch", "Pass QC Inspection", "Style No", "Order Qty", "Fabric Type".
- **Zero Glare:** Never use raw blinding `#FFFFFF` across wide canvas areas. Always apply `--color-bg-base` (`#F8FAFC`).
- **Subtle Typography:** Use deep slate `--color-text-main` (`#0F172A`) and cool slate `--color-text-muted` (`#64748B`).
- **Tabular Numerals:** Always apply CSS `font-variant-numeric: tabular-nums` to tables displaying quantities, metrics, or financial calculations.
- **Muted Semantic Signals:** Never use alarming raw red or neon green. Always apply pastel background tint with dark text (`Badge` component variants).
### [RULE-07] Native Hyperlinks & Multi-Tab Browser Navigation (ADR-17 & INVARIANT-24)
- **Zero Broken Tabs:** Never attach artificial `onClick` handlers to `div`, `tr`, or `button` to navigate pages without an `href`.
- **Semantic Links:** Always render hyperlinks with semantic `<a>` tags or `<Link href="...">` so users can perform Right-Click -> "Open link in new tab", Middle-Click, or `Ctrl + Click`.
- **Predictable Deep URLs:** Every business record (Orders, Styles, Companies, Cuts, Lines) must map to a deterministic URL path (`/app/orders/:id`, `/app/companies/:id`).
- **Multi-Tab Session Sync:** Auth token and tenant profile must be persisted in `localStorage` ensuring newly opened browser tabs remain securely logged in without friction.

### [RULE-08] Async Searchable Combobox & Large Dataset Loading (ADR-18 & INVARIANT-25)
- **High-Volume Lookups:** Never render more than 50 database records inside a primitive static `<select>`.
- **Dedicated Combobox:** Use `<Combobox />` / `<AsyncSearchSelect />` with in-box loading indicator (`isLoading={true}`) for async fetches.
- **300ms Debounce:** All live search inputs querying backend APIs (Styles, Orders, Lots) must debounce keystrokes by 300ms to preserve database throughput.
- **Keyboard-First Selection:** Ensure full accessibility with `ArrowUp`, `ArrowDown`, `Enter` and `Escape` support.

### [RULE-09] Modular Domain-Driven Navigation Architecture (ADR-19 & INVARIANT-26)
- **Decoupled Module Slices:** Each feature directory must declare its own navigation configuration (`features/<module>/nav.ts`). Never dump all ERP menus into a monolithic mega-file.
- **Central Aggregator:** `shared/config/navigation.ts` imports and aggregates module slices, keeping code under 50 lines.
- **Subdomain Routing:** Cleanly separate Platform Owner menus from Factory Client menus based on hostname context.

### [RULE-10] Zero HTML5 Browser Validation & Server-Side Enforcement (ADR-20 & INVARIANT-27)
- **Zero Browser Native Tooltips:** Never use native HTML5 validation attributes (`required`, `pattern`, `min`, `max`) that trigger browser popup bubbles ("Please fill out this field").
- **`<form noValidate>` Enforcement:** All forms must declare `noValidate` to suppress browser validation mechanics.
- **Server 422 Error Mapping:** Catch HTTP 422 responses from Laravel FormRequests and map `errors` cleanly beneath input fields without disruptive browser alerts.

### [RULE-11] Absolute Cross-Page Color & Token Uniformity (ADR-21 & INVARIANT-28)
- **Zero Multi-Color Chaos:** Never use arbitrary colors or mismatched palettes between different pages (e.g., green button on one page, purple on another).
- **Single Design System Palette:** Strictly use centralized CSS variables and Tailwind design tokens:
  - Primary Action / Buttons: `--color-primary-600` (`#2563eb`).
  - Base Background: `--color-bg-base` (`#f8fafc`).
  - Card Surfaces: `--color-bg-surface` (`#ffffff`) with border `--color-border-subtle` (`#e2e8f0`).
  - Text Hierarchy: Primary `#0f172a`, Secondary/Muted `#64748b`.
### [RULE-12] Compact Small Corner Radius for Cards & Surfaces (ADR-22 & INVARIANT-29)
- **Zero Bulky Rounding:** Never use bubbly or rounded-lg/xl curves on cards, panels, or datatables.
- **Always More Small Radius:** All `<Card />` containers, datatable wrappers, and panel blocks must use `rounded-sm` (2px - 4px) to retain an authentic, crisp industrial ERP tool aesthetic.

---

## 3. Strict TypeScript & Official React 19 Standards
- **Zero `any` Types:** All models must have explicit interfaces in `features/<context>/types/index.ts`.
- **Pure Functions & Modern Hooks:** Use standard React 19 idioms (`useState`, `useCallback`, `useMemo`, `useReducer`). Never use legacy lifecycle methods or class components.
- **Bilingual Component Comments:** Complex state logic (e.g., matrix recalculations) must include brief English JSDoc accompanied by natural Bengali comments explaining the garment breakdown logic.

---

## 4. Frontend Verification Protocol
Before submitting any UI code:
1. Type check: `npx tsc --noEmit`
2. Linter check: `npm run lint` (or `npx oxlint`)
3. Component delivery is blocked if unresolved warnings or broken types remain.
