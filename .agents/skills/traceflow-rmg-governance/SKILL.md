---
name: traceflow-rmg-governance
description: >-
  Enforces TraceFlow-RMG architectural invariants, domain rules, and development standards.
  Activates whenever writing backend code, designing database schemas, creating API endpoints,
  building frontend interfaces, or drafting requirements for the TraceFlow-RMG ERP platform.
---

# TraceFlow-RMG Enterprise Development Workflow & Standards

## Purpose
This skill equips the agent with direct access to TraceFlow-RMG's enterprise architecture, domain models, coding conventions, and strict operational invariants to prevent context drift and ensure zero architecture violations.

---

## 1. Pre-Flight Inspection Checklist
Before generating or refactoring code:
1. Identify the target domain module:
   - Cutting & Bundle (`01-PRODUCT-AND-BUSINESS/02-Business-Analyst/SRS-CUTTING-AND-BUNDLE-MODULE.md`)
   - Sewing & QC (`SRS-SEWING-AND-QC-MODULE.md`)
   - Finishing & Packing (`SRS-FINISHING-AND-PACKING-MODULE.md`)
   - Merchandising & Costing (`SRS-MERCHANDISING-AND-COSTING-MODULE.md`)
   - Inventory & Warehouse (`SRS-INVENTORY-AND-WAREHOUSE-MODULE.md`)
   - Commercial & Shipping (`SRS-COMMERCIAL-AND-SHIPPING-MODULE.md`)
   - IAM & RBAC (`SRS-IAM-MODULE.md`)
2. Verify Data Schemas:
   - Check `ERP-AI-DEVELOPMENT-TEAM/04-DATA-AND-INTEGRATION/01-Database-Engineer/DATABASE-ARCHITECTURE-AND-SCHEMA.md`
3. Verify Architectural Decisions:
   - Check `ERP-AI-DEVELOPMENT-TEAM/02-ARCHITECTURE-AND-DESIGN/01-Solution-Architect/ARCHITECTURAL-DECISION-RECORDS.md`
4. **Autonomous Self-Updating Protocol:**
   - Any architectural decision, schema modification, or user specification MUST immediately and automatically trigger updates across `docs/`, `AGENTS.md`, and all related `.agents/skills/*/SKILL.md` files without requiring user prompts or reminders.
5. **Proactive Next-Task Recommendation Protocol:**
   - Upon completing any feature, bug-fix, or architectural task, the agent MUST proactively analyze the project roadmap and present the recommended next logical task with clear options and rationale without waiting for the user to ask.
6. **Mandatory SRS Gate (Zero Code Before SRS):**
   - Check if an authoritative, enterprise-grade `SRS.md` and `SOP.md` exist under `docs/modules/<module-name>/`. If absent, draft and finalize them first before writing any code. Writing implementation code without an approved SRS is strictly prohibited.
7. **Pre-Migration Column Approval Gate:**
   - Never create or execute a database migration without explicit human approval. Present a full schema specification table (table name, column names, data types, UUID v7 PK/FK, indexes) to the user and obtain explicit confirmation before touching migrations.
8. **Periodic Auto-Audit & 5-Minute Rules Realignment Protocol:**
   - Every 5 minutes or after every major development step, the agent MUST autonomously review and audit the project against `ERP-AI-DEVELOPMENT-TEAM/` (ADRs, Schema, Seeding policies), `AGENTS.md`, `docs/modules/`, and skill rules.
   - **Mandatory Audit Log:** Record audit execution timestamp, files verified (`ERP-AI-DEVELOPMENT-TEAM/`, `AGENTS.md`, `docs/modules/`, `.agents/skills/`), issues checked, and corrective actions taken in `logs/audit/auto_audit.log` for user verification.
9. **Base Document Authority (`ERP-AI-DEVELOPMENT-TEAM/`) & Mandatory SRS Derivation:**
   - The `ERP-AI-DEVELOPMENT-TEAM/` directory houses the foundational Base Documents for the entire platform.
   - When creating, updating, or reviewing any SRS or SOP under `docs/modules/`, requirements must be strictly derived from these base documents (`UI-UX-GUIDELINES.md`, `ENTERPRISE-UI-COMPONENT-SYSTEM.md`, `ARCHITECTURAL-DECISION-RECORDS.md`, `DATABASE-ARCHITECTURE-AND-SCHEMA.md`).
   - Never introduce conflicting design rules, rogue color themes, or diverging data schemas in any module SRS.
10. **Single Task Isolation & Minimal Migration Scope Protocol:**
    - Never present or execute migrations, models, or code for multiple future sub-tasks all at once.
    - Focus strictly and exclusively on the active step/feature currently being developed.
    - Each development step must only create the minimal database tables, controllers, and services strictly required for that specific step. Extra tables or speculative future schemas must NOT be bundled together.

---

## 2. Inviolable Coding Guardrails

### Backend (Laravel 13 / PHP 8.3+)
- **Primary Keys:** Always `$table->uuid('id')->primary()` with UUID v7.
- **Modular DDD Architecture:** Encapsulate domain logic inside `app/Domain/<Context>/` (`Models/`, `Services/`, `Repositories/`, `Events/`, `Exceptions/`).
- **Controllers:** Maximum 15-20 lines. Must ONLY invoke FormRequest and Service layer.
- **Services:** All business logic, DB transactions (`DB::transaction`), and distributed locks (`Cache::lock`) belong here.
- **System Options & Settings:** Never use hardcoded enums. Fetch dynamic choices from `system_categories` and `system_options` cached in Redis.
- **Universal Code Generation:** All tracking numbers/codes (PO, Cut No, Bundle Barcode, Company Code) must be auto-generated via `CodeGeneratorService` backed by `document_sequences` and configurable from Admin Settings. Never permit manual user input for codes.
- **Centralized Static Data:** Zero database tables for invariant choices (Shifts, Genders, Incoterms). Use PHP 8.3 Backed Enums (`app/Shared/Constants/`) and TS Dictionaries (`frontend/src/shared/constants/`). Zero magic strings.
- **Seeder Separation & Demo Isolation:** Zero unnecessary demo seeders. Core bootstrap (`bootstrap/`) is immutable. Demo data (`demo/`) must carry `'is_demo' => true` so that `traceflow:purge-demo` cleanly wipes dummy records without harming system settings.
- **Queues:** Offload barcode generation, PDF rendering, and bulk Excel imports to Horizon queue jobs.

### Frontend (React + TypeScript)
- **Modular Feature Architecture:** Organize business modules under `frontend/src/features/<context>/` (`api/`, `components/`, `hooks/`, `types/`, `pages/`).
- **16-Category UI System:** Zero inline styles. Use centralized components from `@/components/ui/`.
- **High-Density DataTable:** Render tabular records with `<DataTable />` powered by TanStack Table v8 + Virtualization (44px header, 52px/40px row, pinned columns).
- **Labels:** Standard English labels only (`Style No`, `Line No`, `Cut Qty`, `Bundle Qty`).
- **Matrix Entry:** Multi-dimensional matrix (e.g. Color x Size breakdowns) must use Slide-over Drawer + Editable Matrix Grid.

### Human-Crafted Code Quality (Zero AI Tells & Bilingual Commentary)
- **Zero Robotic Comments:** Never write obvious comments like `// Function to calculate sum`, `// Return result`, or `// Step 1`.
- **Bilingual Code Commentary:** Follow `BILINGUAL-CODE-COMMENTING-POLICY.md`. All public domain services, complex algorithms, and critical hooks must include English technical summary (PHPDoc/TSDoc) alongside natural Bengali (বাংলা) business context explanation.
- **Domain Intent Comments Only:** Only explain business edge cases (e.g., `// Add 2% allowance for Lycra/Spandex relaxation shrinkage`).
- **Real-World Senior Developer Naming:** Use domain terms (`markerEfficiencyRatio`, `bundlePieceSequence`, `inspectedGarmentCount`), avoiding generic variables (`res`, `data`, `arr`, `temp`).
- **No Half-Baked Placeholders:** Zero `// TODO: Implement later`. All code must be complete, idiomatic, and clean.
- **Zero AI Metadata:** No references to prompts, LLM, or AI generation in docblocks, commit messages, or comments.

### Official Framework Documentation Compliance & Zero-Defect Mandate
- **Laravel 13 & PHP 8.3+:** Follow official [Laravel Documentation](https://laravel.com/docs). Use constructor property promotion, native typed properties, match expressions, modern Form Requests, and Pipeline pattern. Never use deprecated or pre-Laravel 10 anti-patterns.
- **React 19 & TypeScript:** Follow official [React Documentation](https://react.dev). Use modern functional components, standard React 19 hooks, strict TypeScript interfaces/types, and clean effect teardowns. Never use legacy lifecycle methods or class components.
- **PostgreSQL 16+:** Follow official PostgreSQL standards for indexing, JSONB queries, and transactions.
- **Mandatory Self-Verification Protocol:** Follow `ZERO-ERROR-AND-ZERO-WARNING-PROTOCOL.md`. Every backend edit must be verified via `php -l`, route list, and test runs. Every frontend edit must pass `npx tsc --noEmit` and `npm run lint`. Zero syntax errors, zero warnings.

### Temporary File Auto-Cleanup & Zero Garbage Code (ADR-17)
- **Immediate File Purge:** Any temporary scripts, test fixtures, scratch files, or preview dumps generated during testing/development MUST be immediately deleted upon completion.
- **Zero Dead/Debug Code:** Never commit `dd()`, `dump()`, `console.log()`, `var_dump()`, or commented-out blocks.
- **Repository Hygiene:** All transient scratch work must be kept strictly inside `<appDataDir>/brain/<conversation-id>/scratch/` or git-ignored `storage/temp/`.

---

## 3. Human Approval Gates
- **Gate 1:** SRS / Requirements sign-off by Lead BA / PO.
- **Gate 2:** ADR / System design sign-off by Principal Architect.
- **Gate 3:** Database migration approval by Lead DBA.
- **Gate 4:** Pull Request code review by Tech Lead.
- **Gate 5:** QA Acceptance sign-off by SQA Lead.
- **Gate 6:** Deployment / Rollout sign-off by DevOps Lead.
