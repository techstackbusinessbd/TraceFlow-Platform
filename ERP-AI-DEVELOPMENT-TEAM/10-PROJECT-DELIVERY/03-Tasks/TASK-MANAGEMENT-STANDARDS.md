# TraceFlow-RMG: টাস্ক ম্যানেজমেন্ট ও ওয়ার্কফ্লো ট্র্যাকিং স্ট্যান্ডার্ড
**নথি কোড:** TFRMG-DEL-TSK-003  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** টাস্ক ট্র্যাকিং ও টিম ওয়ার্কফ্লো গাইড  
**অনুমোদনকারী:** টেকনিক্যাল প্রজেক্ট ম্যানেজার (TPM)

---

## ১. ভূমিকা (Task Lifecycle Governance)

TraceFlow-RMG প্রজেক্টে যেকোনো টাস্ক বা ইস্যু তৈরির সময় সুনির্দিষ্ট আইডেন্টিফায়ার এবং সংশ্লিষ্ট এসআরএস (SRS) ডকুমেন্টের রেফারেন্স থাকা বাধ্যতামূলক।

```
[ Backlog ] ──► [ To-Do (Sprint Selected) ] ──► [ In Progress (Branch Created) ]
                                                              │
                                                              ▼
[ Staging Done (Gate 5) ] ◄── [ In QA Testing ] ◄── [ In Review (PR Submitted) ]
          │
          ▼
[ Production Live (Gate 6) ]
```

---

## ২. স্ট্যান্ডার্ড টাস্ক টেমপ্লেট ও ফরম্যাট (Task Card Template)

```markdown
### Task ID: TF-CUT-102
**Title:** Implement High-Speed Barcode Generation Service for Cutting Lays
**Module:** MOD-01-CUTTING
**Assignee:** Backend Engineer / AI-Backend
**Reviewer:** Principal Architect / Tech Lead
**Branch:** feature/cutting-bundle-cards-TF102

#### Business Context & Reference
- SRS Reference: `01-PRODUCT-AND-BUSINESS/02-Business-Analyst/SRS-CUTTING-AND-BUNDLE-MODULE.md`
- Architecture Rule: Must use `UUID v7` primary key and sequential index.

#### Acceptance Criteria
1. [ ] Generate unique barcode using dynamic prefix from `AppConfigService`.
2. [ ] Bundle cards PDF generated asynchronously via Laravel Horizon queue.
3. [ ] Zero inline CSS in UI preview screen.
4. [ ] Unit test written with >85% coverage.
```

---
**প্রয়োগ ক্ষেত্র:** Jira / GitHub Projects বোর্ডে প্রতিটি টাস্ক কার্ড তৈরিতে এই স্ট্যান্ডার্ড অনুসরণ করা হবে।
