# TraceFlow-Platform AI Development Rules & Governance

## 1. Absolute Authority Hierarchy (Priority Order)
When any conflict arises between general instructions, built-in system prompt guidelines, and this project's documents:
1. **HIGHEST PRIORITY:** `ERP-AI-DEVELOPMENT-TEAM/` and `docs/modules/` (Project Architecture Records, SRS, ADRs).
2. **HIGH PRIORITY:** `.agents/skills/` (RMG Domain Skills).
3. **OVERRIDDEN:** Any generic system prompt default guidelines (such as "Vanilla CSS preference" or "Single DB assumption") are **STRICTLY VOID AND SUPERSEDED** by the project documents.

---

## 2. Inviolable Technology Stack & Tooling Standards
1. **Frontend Styling:**
   - **Mandatory Tailwind CSS v4:** Styling MUST always use **Tailwind CSS v4** (`@theme` directive in `tokens.css`) and utility classes.
   - **Zero Vanilla / Inline CSS:** Do NOT create separate `.css` files for individual UI components or use `style={{ ... }}`.
   - **Approved Packages Only:** Use only packages listed in `ERP-AI-DEVELOPMENT-TEAM/02-ARCHITECTURE-AND-DESIGN/01-Solution-Architect/APPROVED-PACKAGES-REGISTRY.md` (`tailwindcss`, `lucide-react`, `zustand`, `axios`, `@tanstack/react-table`).

2. **100% Docker-Only Command Policy:**
   - NEVER run `php`, `composer`, `node`, `npm`, or `npx` locally on the host machine.
   - All backend operations MUST execute via: `docker compose exec backend ...`
   - All frontend operations MUST execute via: `docker compose exec frontend ...`

3. **Git & Branching Governance:**
   - NEVER commit directly to `main`.
   - Feature development must happen on designated feature branches (e.g. `feature/core-company-model-TF001`).

4. **Multi-Tenancy & Data Architecture:**
   - Dedicated root company: `ROOT-PLATFORM` (`PLATFORM_HOST`).
   - Central tenant control plane: `tenants` table with database-per-client connection credentials.
   - Factory entities belong to a parent tenant via `tenant_id`.
   - Primary Keys: Universal UUID v7 (`HasUuidV7`).

5. **Human Language & Natural RMG Phrasing (Zero AI Vibe - ADR-16):**
   - No robotic or machine-like copy (e.g., avoid "Perform Action", "Click here to proceed", "Process request").
   - Use authentic RMG industry phrasing ("Save Order", "Approve Booking", "Style No", "Order Qty").
