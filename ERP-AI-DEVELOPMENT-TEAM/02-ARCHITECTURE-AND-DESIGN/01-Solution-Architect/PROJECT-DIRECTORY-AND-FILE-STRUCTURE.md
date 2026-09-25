# TraceFlow-RMG: মডুলার ডোমেন-ড্রিভেন ডিজাইন (Modular DDD) ডিরেক্টরি ও ফাইল আর্কিটেকচার
**Document Code:** TFRMG-ARCH-STR-001  
**Version:** 6.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Enterprise Architecture, Modular Domain-Driven Design (DDD), Hexagonal Architecture  
**Approved By:** Principal Enterprise Architect & Solution Architecture Board  

---

## ১. আর্কিটেকচারাল ফিলোসফি ও বাউন্ডেড কনটেক্সট (Core Philosophy)

TraceFlow-RMG একটি মিশন-ক্রিটিক্যাল, হাই-কনকারেন্সি এন্টারপ্রাইজ আরএমজি ইআরপি প্ল্যাটফর্ম। শত শত ডেটা মডেল, জটিল ফ্লোর স্ক্যানিং এবং বৃহৎ ডেভেলপমেন্ট টিমের সমন্বয়ের জন্য প্রথাগত "লেয়ার্ড আর্কিটেকচার" (যেখানে সব মডেল এক ফোল্ডারে এবং সব কন্ট্রোলার আরেক ফোল্ডারে থাকে) বর্জন করে **মডুলার ডোমেন-ড্রিভেন ডিজাইন (Modular Domain-Driven Design / Modular DDD)** প্রতিষ্ঠা করা হয়েছে।

### বাউন্ডেড কনটেক্সটের ৭টি কোর ডোমেন:
1. **`Core & IAM` (MOD-00):** গ্রুপ, কোম্পানি, ফ্যাক্টরি প্ল্যান্ট, সেন্ট্রাল সেটিংস, সিকোয়েন্স ইঞ্জিন, ইউজার ও রোল পারমিশন।
2. **`Merchandising & Costing` (MOD-01):** বায়ার, স্টাইল লাইব্রেরি, প্রি-কস্টিং, সাইট-অর্ডার ম্যাট্রিক্স, কনজাম্পশন ও পিও ট্র্যাকিং।
3. **`Procurement & Warehouse` (MOD-02):** ইয়ার্ন, ফেব্রিক স্টক, ট্রিমস স্টোর, গেট পাস, রিসিভিং ও স্টক ব্যালেন্স।
4. **`Cutting & Marker` (MOD-03):** ফেব্রিক রোল রিল্যাক্সেশন, মার্কার প্ল্যান, লে স্প্রেডিং, বান্ডেল কার্ড ও কিউআর জেনারেশন।
5. **`Sewing & Floor QC` (MOD-04):** লাইন লোডিং, অ্যান্ডন বোর্ড, আওয়ারলি আউটপুট, এন্ডলাইন ডিফেক্ট লগিং ও রি-ওয়ার্ক রাউটিং।
6. **`Finishing & Packing` (MOD-05):** ওয়াশিং ট্র্যাকিং, আয়রনিং, রেশিও কার্টোনাইজেশন, এক্স-ফ্যাক্টরি ইন্সপেকশন।
7. **`Commercial & Shipping` (MOD-06):** এলসি ম্যানেজমেন্ট, এক্সপোর্ট ইনভয়েস, প্যাকিং লিস্ট, শিপমেন্ট ট্র্যাকিং।

---

## ২. রুট রিপোজিটরি লেআউট (Enterprise Root Layout)

```
d:/ERP/TraceFlow-RMG/
├── .agents/                               # AI Governance & Custom Workflow Skills
│   └── skills/traceflow-rmg-governance/   # Enterprise Invariant Enforcement
├── docker/                                # Docker Infrastructure Blueprints
│   ├── nginx/                             # Reverse Proxy, SSL, Gzip Configs
│   ├── php/                               # PHP 8.3 FPM Custom Dockerfile
│   └── redis/                             # Redis 7 Cache & Persistent Queue Config
├── docker-compose.yml                     # Live Container Orchestration (PostgreSQL 16, Redis 7, App)
├── docs/                                  # Module Specifications & Standard Operating Procedures
│   └── modules/
│       ├── 00-core-and-iam/               # SOP.md & SRS.md (Organization & Settings)
│       ├── 01-merchandising/              # SOP.md & SRS.md (Buyer, Style & PO Matrix)
│       ├── 02-inventory/                  # SOP.md & SRS.md (Fabric, Trims, Gate Pass)
│       ├── 03-cutting/                    # SOP.md & SRS.md (Marker, Lay, Bundle QR)
│       ├── 04-sewing-qc/                  # SOP.md & SRS.md (Line, Hourly, Defect)
│       ├── 05-finishing-packing/          # SOP.md & SRS.md (Wash, Iron, Carton)
│       └── 06-commercial/                 # SOP.md & SRS.md (LC, Invoice, Export)
├── ERP-AI-DEVELOPMENT-TEAM/               # Enterprise Engineering Standards & Council Artifacts
│   ├── 00-GOVERNANCE/                     # Human Approval Gates & Change Management
│   ├── 02-ARCHITECTURE-AND-DESIGN/        # ADRs, System Blueprints, UI/UX Guidelines
│   ├── 04-DATA-AND-INTEGRATION/           # Database Schemas & DDL Specifications
│   └── 09-ENGINEERING-STANDARDS/          # Laravel, React, Security & Coding Standards
├── backend/                               # Laravel 13 Backend Engine (PHP 8.3+)
└── frontend/                              # React 19 Frontend SPA (TypeScript + Vite)
```

---

## ৩. ব্যাকএন্ড মডুলার ডিডিডি স্ট্রাকচার (Backend Modular DDD Architecture)

ব্যাকএন্ডে কোনো মনোলিথিক `Models/` বা `Controllers/` স্তূপ থাকবে না। সমস্ত বিজনেস লজিক, ডাটা অ্যাক্সেস ও ইভেন্ট স্ব-স্ব ডোমেনের ভেতরে এনক্যাপসুলেটেড থাকবে:

```
backend/app/
├── Domain/                                # বাউন্ডেড কনটেক্সট ডোমেনসমূহ (Business Core)
│   │
│   ├── Organization/                      # Domain: Core & IAM
│   │   ├── Models/                        # CorporateGroup.php, Company.php, FactoryUnit.php, DocumentSequence.php
│   │   ├── Services/                      # CompanyService.php, SystemSettingService.php, CodeGeneratorService.php
│   │   ├── Repositories/                  # CompanyRepositoryInterface.php, CompanyRepository.php
│   │   ├── Events/                        # CompanyCreated.php, DocumentSequenceReset.php
│   │   ├── Exceptions/                    # DuplicateCompanyException.php, SequenceExhaustedException.php
│   │   └── DTOs/                          # CompanyRegistrationData.php, SequenceConfigData.php
│   │
│   ├── Merchandising/                     # Domain: Merchandising & Costing
│   │   ├── Models/                        # Buyer.php, Style.php, BuyerOrder.php, OrderMatrixBreakdown.php
│   │   ├── Services/                      # OrderBookingService.php, CostingEngineService.php
│   │   ├── Rules/                         # DeliveryDateValidationRule.php, SmvConstraintRule.php
│   │   └── Events/                        # BuyerOrderConfirmed.php, StyleRevised.php
│   │
│   ├── Cutting/                           # Domain: Cutting & Marker
│   │   ├── Models/                        # CutPlan.php, FabricRoll.php, BundleCard.php
│   │   ├── Services/                      # MarkerCalculationService.php, BundleGenerationService.php
│   │   └── Jobs/                          # GenerateBundleBarcodesPdfJob.php (Horizon Queue)
│   │
│   └── Sewing/                            # Domain: Sewing & QC
│       ├── Models/                        # SewingLine.php, HourlyProduction.php, DefectLog.php
│       ├── Services/                      # LineLoadingService.php, DefectClassificationService.php
│       └── Events/                        # BundleScannedAtLine.php, AlterationTriggered.php
│
├── Shared/                                # ক্রস-ডোমেন শেয়ার্ড ইনফ্রাস্ট্রাকচার
│   ├── Infrastructure/                    # RedisCacheManager.php, PessimisticLockEngine.php
│   ├── Traits/                            # HasUuidV7.php, BelongsToCompany.php, Auditable.php
│   ├── Exceptions/                        # DomainException.php, ConcurrencyConflictException.php
│   └── Contracts/                         # TenantScopedInterface.php, AuditableInterface.php
│
└── Http/                                  # ডেলিভারি ও ট্রান্সপোর্ট লেয়ার (API Orchestration)
    ├── Controllers/Api/V1/
    │   ├── Organization/                  # CompanyController.php, SettingController.php (15-20 lines max)
    │   ├── Merchandising/                 # BuyerOrderController.php, StyleController.php
    │   └── Cutting/                       # CutPlanController.php, BundleScanController.php
    ├── Requests/V1/                       # ইনপুট ভ্যালিডেশন (FormRequests)
    │   ├── Organization/                  # StoreCompanyRequest.php, UpdateSequenceRequest.php
    │   └── Merchandising/                 # StoreBuyerOrderRequest.php
    ├── Resources/V1/                      # রেসপন্স ফরম্যাটিং (JsonResources)
    │   ├── Organization/                  # CompanyResource.php, SettingOptionResource.php
    │   └── Merchandising/                 # BuyerOrderResource.php
    └── Middleware/                        # MultiTenantScopeMiddleware.php, FloorTokenAuth.php
```

---

## ৪. ফ্রন্টএন্ড ফিচার-স্লাইসড ডিডিডি স্ট্রাকচার (Frontend Feature-Sliced Architecture)

ফ্রন্টএন্ডেও সমস্ত ডোমেনের স্ক্রিন, এপিআই হুক্স এবং ডোমেন-কম্পোনেন্ট সম্পূর্ণ স্বয়ংসম্পূর্ণ মডিউল হিসেবে থাকবে:

```
frontend/src/
├── app/                                   # অ্যাপ্লিকেশন বুটস্ট্র্যাপ ও গ্লোবাল প্রোভাইডার
│   ├── App.tsx                            # Root Component
│   ├── router.tsx                         # React Router (Modular Lazy Loaded Routes)
│   └── providers.tsx                      # TanStack Query, AuthProvider, ThemeProvider
│
├── components/                            # গ্লোবাল এবং ১৬টি ক্যাটাগরির শেয়ার্ড UI লাইব্রেরি
│   ├── layout/                            # AppLayout.tsx, Sidebar.tsx, Navbar.tsx, Breadcrumb.tsx
│   └── ui/                                # সেন্ট্রালাইজড এন্টারপ্রাইজ কম্পোনেন্ট সিস্টেম
│       ├── foundation/                    # Typography, Icon, Avatar, Badge, StatusIndicator
│       ├── buttons/                       # Button, IconButton, SplitButton, LoadingButton
│       ├── forms/                         # TextInput, NumberInput, PasswordInput, ReadonlyField
│       ├── selection/                     # Select, MultiSelect, Combobox, ToggleSwitch
│       ├── datetime/                      # DatePicker, DateRangePicker, TimePicker
│       ├── data-display/                  # Card, StatisticCard, KPICard, Timeline, SkeletonLoader
│       ├── overlay/                       # SlideOverDrawer (640px), Modal, Popover, ContextMenu
│       ├── table/                         # High-Density Virtualized DataTable (TanStack v8)
│       └── erp/                           # DocumentNumberBadge, ApprovalPanel, AuditTrailViewer
│
├── features/                              # আসল ডোমেন-ড্রিভেন ফিচার মডিউলসমূহ (Business Features)
│   │
│   ├── organization/                      # Module: Core & IAM
│   │   ├── api/                           # useCompaniesQuery.ts, useCompanyMutation.ts, useSettingsQuery.ts
│   │   ├── components/                    # CompanyDrawer.tsx, SequenceConfigModal.tsx, PlantListTable.tsx
│   │   ├── hooks/                         # useCompanyCodeSequence.ts
│   │   ├── types/                         # company.types.ts, sequence.types.ts
│   │   └── pages/                         # CompanyDirectoryPage.tsx, SystemSettingsPage.tsx
│   │
│   ├── merchandising/                     # Module: Merchandising & Costing
│   │   ├── api/                           # useOrdersQuery.ts, useOrderCostingMutation.ts
│   │   ├── components/                    # ColorSizeMatrixGrid.tsx, BOMEditorTable.tsx, StyleCard.tsx
│   │   ├── types/                         # buyer-order.types.ts, costing.types.ts
│   │   └── pages/                         # OrderDirectoryPage.tsx, OrderEntryWizardPage.tsx
│   │
│   ├── cutting/                           # Module: Cutting Floor
│   │   ├── api/                           # useCutPlansQuery.ts, useBundlePrintMutation.ts
│   │   ├── components/                    # LayRatioMatrix.tsx, BarcodeSheetViewer.tsx
│   │   └── pages/                         # CuttingExecutionPage.tsx, BundleHistoryPage.tsx
│   │
│   └── sewing/                            # Module: Sewing & QC
│       ├── api/                           # useLineLoadingQuery.ts, useDefectLoggingMutation.ts
│       ├── components/                    # AndonLineMonitor.tsx, DefectSelectorDrawer.tsx
│       └── pages/                         # LineOperationsPage.tsx, FloorDashboardPage.tsx
│
├── hooks/                                 # গ্লোবাল কাস্টম হুক্স (useAuth, useDebounce, usePessimisticLock)
├── stores/                                # Zustand Global Stores (authStore.ts, tenantStore.ts)
├── styles/                                # variables.css, reset.css, global.css (Zero Inline CSS)
└── types/                                 # গ্লোবাল শেয়ার্ড টাইপস্ক্রিপ্ট টাইপস (api-response.types.ts)
```

---

## ৫. নেমিং ও কেসিং কনভেনশন (Strict Naming Standards)

| কনটেক্সট / উপাদান | কেসিং স্ট্যান্ডার্ড | বাস্তব উদাহরণ |
| :--- | :--- | :--- |
| **ডোমেন ফোল্ডার** | PascalCase | `Domain/Organization`, `Domain/Merchandising`, `Domain/Cutting` |
| **মডেল ও সার্ভিস** | PascalCase | `CompanyService.php`, `BuyerOrder.php`, `CodeGeneratorService.php` |
| **ডাটাবেজ টেবিল** | snake_case (বহুবচন) | `corporate_groups`, `document_sequences`, `bundle_cards` |
| **ডাটাবেজ কলাম** | snake_case | `company_id`, `format_pattern`, `current_number` |
| **রিঅ্যাক্ট কম্পোনেন্ট** | PascalCase.tsx | `SlideOverDrawer.tsx`, `ColorSizeMatrixGrid.tsx`, `DataTable.tsx` |
| **রিঅ্যাক্ট হুক্স** | camelCase.ts (use...) | `useCompanyCodeSequence.ts`, `usePessimisticScan.ts` |
| **টাইপস্ক্রিপ্ট ইন্টারফেস** | PascalCase | `interface BuyerOrder { ... }`, `interface CompanyData { ... }` |
| **সিএসএস ক্লাস** | kebab-case | `.table-header`, `.drawer-backdrop`, `.btn-primary` |

---

## ৬. আর্কিটেকচারাল ইনভ্যারিয়েন্টস ও টিম পলিসি

1. **জিরো ডোমেন ক্রস-পলিউশন (No Leaky Abstractions):** `Cutting` ডোমেনের সার্ভিস কখনো সরাসরি `Merchandising` ডোমেনের ইন্টারনাল মডেলে অপ্রয়োজনীয় মিউটেশন করবে না; ডেটা সমন্বয়ের জন্য ডোমেন ইভেন্ট (`BuyerOrderConfirmed`) বা সার্ভিস কন্ট্রাক্ট ব্যবহার করতে হবে।
2. **কন্ট্রোলারের চরম ক্ষিপ্রতা (Skinny Delivery Layer):** কন্ট্রোলারের একমাত্র দায়িত্ব হলো HTTP ইনপুট গ্রহণ করা, ফর্ম রিকোয়েস্টে ভ্যালিডেশন চালানো এবং সংশ্লিষ্ট ডোমেন সার্ভিসের মেথড কল করে আউটপুট প্রদান করা (১৫-২০ লাইনের বেশি নয়)।
3. **ক্লিয়ার কোড ও নো এআই টেলস:** কোডে কোনো অপ্রয়োজনীয় রোবোটিক কমেন্ট বা জেনেরিক ভ্যারিয়েবল (`data`, `res`, `item`) থাকবে না; প্রতিটি চলক ডোমেনের প্রকৃত আরএমজি পরিভাষাকে প্রতিনিধিত্ব করবে।
