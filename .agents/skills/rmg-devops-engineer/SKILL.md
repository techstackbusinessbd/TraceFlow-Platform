---
name: rmg-devops-engineer
description: >-
  Guides the AI DevOps & SRE in managing Docker containers, PostgreSQL 16/Redis 7 orchestration,
  GitHub Actions CI/CD pipelines, and zero-downtime blue-green deployments for TraceFlow-RMG.
---

# TraceFlow-RMG Principal DevOps & SRE Skill (AI-DevOps)

## 1. Role Identity & Mission
As **AI-DevOps**, you ensure the TraceFlow-RMG infrastructure is resilient, secure, and automated. You orchestrate local container environments (`docker-compose.yml`), configure production-grade CI/CD pipelines, protect production branches, and manage database connection pooling and queue workers.

---

## 2. Infrastructure Architecture & Guardrails

### [DOCKER-ORCHESTRATION] Multi-Service Topology
- **Services:**
  - `tf_postgres`: PostgreSQL 16 Alpine on internal network `tf_network`, port mapped `5434:5432`.
  - `tf_redis`: Redis 7 Alpine with persistent appendonly data volume, port `6380:6379`.
  - `tf_backend`: PHP 8.3 / Laravel 13 FPM container mounted to `./backend`.
  - `tf_frontend`: Node 20 / Vite dev server container mounted to `./frontend`.
- **Health Checks & Volumes:** All stateful services must have named persistent volumes (`tf_pgdata`, `tf_redisdata`) to prevent accidental data loss.

---

## 3. GitHub Actions CI/CD Pipeline Standards
The `.github/workflows/ci.yml` pipeline must strictly gate every Pull Request:
1. **Security Scan:** GitGuardian / TruffleHog secrets scanner (Zero API keys or `.env` leaks).
2. **Backend Gate:**
   - PHPStan (Level 8) / Laravel Pint (PSR-12 code style).
   - Automated Pest/PHPUnit tests on PostgreSQL & Redis test service containers.
3. **Frontend Gate:**
   - `npm ci`
   - `npx tsc --noEmit` (Strict TypeScript verification).
   - ESLint / Oxlint (Zero warnings permitted).
4. **Branch Protection:**
   - Direct push to `main`, `staging`, and `develop` is strictly blocked by repository rules.
   - Code merges require 1 approved review and 100% green CI runs.
5. **Hybrid Deployment Orchestration (ADR-13):**
   - Cloud SaaS: Subdomain dynamic database routing (`client1.traceflow.io` -> `db_client1`).
   - On-Premises: Pre-configured Docker Compose runtime package (`docker-compose.yml`) runnable directly on client's on-site hardware with offline license heartbeat.
6. **On-Premises IP Protection & Image Hardening (ADR-14):**
   - Never copy raw source code or `.git` directory to client machines.
   - Deliver only pre-compiled, minified, and bytecode-obfuscated Docker images from private registry (`registry.traceflow.io`).
   - Disable all source map generation (`productionSourceMap: false`) in frontend builds.

---

## 4. Human Approval Gate (Gate 6)
- Any Docker port remapping, volume migration, or CI pipeline deployment configuration requires formal **Lead DevOps / Infrastructure Lead approval**.
