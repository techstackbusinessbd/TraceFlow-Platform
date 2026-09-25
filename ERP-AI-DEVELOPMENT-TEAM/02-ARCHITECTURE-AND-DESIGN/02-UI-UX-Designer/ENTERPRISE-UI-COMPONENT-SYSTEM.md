# TraceFlow-RMG: এন্টারপ্রাইজ ইউআই কম্পোনেন্ট সিস্টেম ও ডিজাইন স্ট্যান্ডার্ড
**Document Code:** TFRMG-ARCH-UIUX-002  
**Version:** 6.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Enterprise Design System, Component Library & UX State Matrix  
**Approved By:** Principal Product Designer & Frontend Architecture Council  

---

## ১. ভূমিকা ও আর্কিটেকচারাল ফিলোসফি (Architecture Overview)

TraceFlow-RMG একটি হাই-কনকারেন্সি এন্টারপ্রাইজ আরএমজি/অ্যাপারেল ইআরপি প্ল্যাটফর্ম। শত শত ফ্লোর টার্মিনাল, মার্চেন্ডাইজিং ডেস্ক, মার্চেন্ট অ্যান্ডন স্ক্রিন এবং অ্যাডমিন প্যানেলের ব্যবহারকারীদের কাজের সুবিধার্থে পুরো ফ্রন্টএন্ডে (React 19 + TypeScript + Vanilla/Tailwind CSS Tokens) একটি কঠোর কম্পোনেন্ট অনুশাসন নিশ্চিত করা হয়েছে।

### মূল নীতিসমূহ:
1. **শতভাগ অভিন্নতা (100% Visual Consistency):** সিস্টেমের কোনো স্ক্রিনেই থার্ড-পার্টি ভারী আনস্টাইলড লাইব্রেরি ব্যবহার করা যাবে না। সমস্ত কম্পোনেন্ট সেন্ট্রালাইজড `@/components/ui/` লাইব্রেরি থেকে ইমপোর্ট হবে।
2. **জিরো ইনলাইন স্টাইল (Zero Inline Styles):** কোডে কোনো `style={{ ... }}` অনুমোদিত নয়।
3. **হাই-ডেনসিটি ও কিবোর্ড ফ্রেন্ডলি:** ডাটা এন্ট্রি স্ক্রিনে মাউস ছাড়াও কিবোর্ডের `Tab`, `Arrow`, `Enter`, `Esc` দিয়ে ১০০% কাজ করা যাবে।
4. **স্টেট সচেতনতা:** প্রতিটি উপাদান এবং স্ক্রিনের প্রতিটি স্টেটের জন্য পূর্বনির্ধারিত ভিজ্যুয়াল ট্রিগার থাকবে।

---

## ২. কম্পোনেন্ট লাইব্রেরি স্পেসিফিকেশন (১৬টি কোর ক্যাটাগরি)

```
frontend/src/components/ui/
├── foundation/          # 1. Typography, Icon, Avatar, Image, Divider, Spacer, Badge, Tooltip
├── buttons/             # 2. Button variants, Icon Button, Split, Loading, Action Menu
├── forms/               # 3. Text, Number, Password, Search, Date/Time, Textarea, Readonly
├── selection/           # 4. Checkbox, Radio, Switch, Dropdown, Multi-Select, Combobox
├── datetime/            # 5. DatePicker, RangePicker, TimePicker, Calendar
├── data-display/        # 6. Card, Statistic, KPI, Timeline, Feed, Progress, Skeleton
├── navigation/          # 7. Sidebar, Navbar, Breadcrumb, Tabs, Stepper, Pagination
├── feedback/            # 8. Alert, Toast, Notification, Spinner, Validation
├── overlay/             # 9. Slide-over Drawer, Modal, Popover, Context Menu
├── files/               # 10. File Upload, Drag & Drop, Preview, Attachment List
├── filters/             # 11. Global Search, Filter Panel, Chips, Saved Filters
├── dashboard/           # 12. Metric Card, Charts, Widgets, Trend Indicators
├── erp/                 # 13. Document Number, Approval Panel, Audit Trail, Org Tree
├── table/               # 14. High-Density Data Grid, Fixed Col, Bulk Action, Export
├── auth/                # 15. Login, 2FA/OTP, Session Expiry, 403 Forbidden, 404
└── states/              # 16. Empty State, Error Screen, Offline Indicator
```

---

### Category 1: Basic / Foundation Components
| কম্পোনেন্ট | স্ট্যান্ডার্ড প্রপস / ব্যবহার | স্পেসিফিকেশন ও স্টাইলিং |
| :--- | :--- | :--- |
| **Typography** | `variant: 'h1' \| 'h2' \| 'h3' \| 'h4' \| 'subtitle' \| 'body' \| 'caption'` | ফন্ট: Inter; h1 (28px Bold), body (14px Regular), caption (12px Muted)। |
| **Text** | `weight, color, truncate, mono` | আরএমজি মেজারমেন্ট ও কোডের জন্য `mono` ফন্ট ব্যবহার। |
| **Icon** | `name, size: 'sm' \| 'md' \| 'lg', color` | ভেক্টর SVG আইকন (Lucide-react)। |
| **Avatar** | `src, name, size, status` | ব্যবহারকারীর প্রোফাইল ও অ্যাসাইনমেন্ট হিস্টোরি প্রদর্শনে। |
| **Image** | `src, alt, fallback, zoomable` | টেকপ্যাক স্কেচ ও ফেব্রিক সোয়াচ প্রিভিউ। |
| **Divider / Separator** | `orientation: 'horizontal' \| 'vertical'` | ফর্ম সেকশন ও কার্ড আলাদা করতে ১ পিক্সেল সাবটল বর্ডার। |
| **Spacer** | `size: 4 \| 8 \| 12 \| 16 \| 24 \| 32px` | ফ্লেক্সবক্স ও গ্রিডে ইউনিফর্ম স্পেসিং নিশ্চিতকরণ। |
| **Badge** | `variant: 'neutral' \| 'success' \| 'warning' \| 'danger' \| 'info'` | টেক্সট সংলগ্ন স্ট্যাটাস কাউন্টার বা ফ্ল্যাগ। |
| **Status Indicator** | `status: 'online' \| 'busy' \| 'offline' \| 'in_progress'` | ৮ পিক্সেল গ্লোয়িং ডট সহ লাইভ স্ট্যাটাস। |
| **Tooltip** | `content, placement: 'top' \| 'bottom' \| 'left' \| 'right'` | মাইক্রো-ইনফো ও শর্টকাট কি প্রদর্শনে হোভার ওভারলে। |
| **Link** | `href, external, underline` | অ্যাক্সেসিবল ক্লিকেবল লিঙ্ক। |

---

### Category 2: Button Components
সমস্ত বাটনের স্ট্যান্ডার্ড উচ্চতা **`40px`** (ফ্লোর টাচ মোডে **`48px`**), বর্ডার রেডিয়াস **`10px`**।

| বাটন টাইপ | ব্যবহার ও ইন্টেন্ট | ভিজ্যুয়াল রূপ |
| :--- | :--- | :--- |
| **Primary Button** | পেজের মূল অ্যাকশন (Save, Submit, Create) | ডিপ স্লেট নেভি ব্যাকগ্রাউন্ড (`#0f172a`), সাদা টেক্সট, ব্লু ফোকাস রিং। |
| **Secondary Button** | সেকেন্ডারি অ্যাকশন (Filter, Edit, Next) | হোয়াইট সারফেস, সাবটল গ্রে বর্ডার (`#e2e8f0`), ডার্ক টেক্সট। |
| **Ghost / Tertiary** | পেজের কম গুরুত্বপূর্ণ অ্যাকশন | নো ব্যাকগ্রাউন্ড, হোভারে সাবটল গ্রে শেড। |
| **Outline Button** | বর্ডার বেসড অ্যাকশন (Clear, Reset) | ১ পিক্সেল বর্ডার, ট্রান্সপারেন্ট সারফেস। |
| **Icon Button** | ক্লোজ, সেটিংস, এডিট আইকন | স্কয়ার `40x40px`, বৃত্তাকার হোভার স্টেট। |
| **Success Button** | অনুমোদন বা পাস (Approve, Pass QC) | এমারেল্ড গ্রিন (`#10b981`), হোয়াইট টেক্সট। |
| **Warning Button** | হোল্ড বা রিকুয়েস্ট পরিবর্তন (Hold, Re-inspect) | অ্যাম্বার ইয়েলো (`#f59e0b`), ডার্ক টেক্সট। |
| **Danger / Delete** | মুছে ফেলা বা বাতিল (Delete, Reject) | ক্রিমসন রেড (`#ef4444`), হোয়াইট টেক্সট। |
| **Save & Close / Save & New** | ফাস্ট ডাটা এন্ট্রি ড্রয়ারের অ্যাকশন | একাধিক সাবমিট অপশন। |
| **Split Button** | প্রাইমারি অ্যাকশনের সাথে ড্রপডাউন অপশনস | বামে মূল বাটন, ডানে সেপারেটেড অ্যারো ড্রপডাউন। |
| **Loading Button** | রিকোয়েস্ট সাবমিশন চলাকালীন | টেক্সট ফিকে হয়ে ভেতরে মিনি স্পিনার অ্যানিমেশন। |
| **Disabled Button** | অনুমতি বা ভ্যালিডেশন অপূর্ণ থাকলে | অপাসিটি ৫০%, কার্সর `not-allowed`, নো ক্লিক ইভেন্ট। |

---

### Category 3: Form Components
উচ্চতা বাধ্যতামূলক **`40px`**, বর্ডার রেডিয়াস **`10px`**, লেবেল ডিস্ট্যান্স **`6px`**।

| ইনপুট কম্পোনেন্ট | বৈশিষ্ট্য ও আরএমজি ফিল্ড ব্যবহার |
| :--- | :--- |
| **Text Input** | কোম্পানির নাম, বায়ারের নাম, স্টাইল ডেসক্রিপশন। |
| **Number Input** | কোয়ান্টিটি, কনজাম্পশন, SMV, প্যাক সাইজ (কিবোর্ড অ্যারো কী সাপোর্টেড)। |
| **Email / Password** | সিস্টেম লগইন ও নোটিফিকেশন কনফিগারেশন। |
| **Search Input** | ক্লিয়ার বাটন (`✕`) এবং ম্যাগনিফাইং গ্লাস আইকন সহ ইনস্ট্যান্ট ফিল্টার। |
| **Phone / URL** | বায়ার ও সাপ্লায়ার কন্টাক্ট ইনফরমেশন। |
| **Date / Time Input** | শিপমেন্ট কাট-অফ ডেট, শিফট স্টার্ট টাইম। |
| **Textarea** | ফেব্রিক কোয়ালিটি মন্তব্য, অডিট নোট (অটো-এক্সপ্যান্ডেবল)। |
| **Rich Text Editor** | টেকপ্যাক স্পেসিফিকেশন ও কমপ্লায়েন্স এসওপি ড্রাফটিং। |
| **Read-only / Disabled** | সিস্টেম জেনারেটেড কোড বা লকড অর্ডার ভ্যালু (গ্রে ব্যাকগ্রাউন্ড, নো এডিট)। |
| **Input with Prefix/Suffix** | যেমন: প্রিফিক্স `$`, সাফিক্স `Pcs`, `Kg`, `Yards`, `%`। |
| **Input Group** | একাধিক ইনপুট ও বাটন একসাথে মার্জ করা (e.g. সার্চ ফিল্ড + সার্চ বাটন)। |

---

### Category 4: Selection Components
| সিলেকশন উপাদান | ব্যবহার ও কার্যকারিতা |
| :--- | :--- |
| **Checkbox** | মাল্টিপল আইটেম সিলেক্ট বা টার্মস অ্যাগ্রিমেন্ট। |
| **Radio Button** | একক বিকল্প বাছাই (e.g. FOB / CIF / C&F)। |
| **Switch / Toggle** | দ্রুত অন/অফ (e.g. Active / Inactive, Enable Floor Scan)। |
| **Select / Dropdown** | ডায়নামিক অপশন সিলেকশন (ক্যাটাগরি ভিত্তিক)। |
| **Multi-Select & Chips** | একাধিক অপশন নির্বাচন যা ইনপুটে চিপ/ট্যাগ আকারে জমা হয়। |
| **Searchable Select** | শত শত বায়ার বা কালারের মধ্য থেকে টাইপ করে সার্চ করার সুবিধা। |
| **Combobox / Autocomplete** | টাইপ করার সাথে সাথে ডাটাবেজ থেকে লাইভ সাজেশনের মাধ্যমে সিলেকশন। |
| **Tree Select** | হায়ারারকিক্যাল ডিপার্টমেন্ট বা অর্গানাইজেশন ইউনিট বাছাই। |
| **Cascading Select** | বায়ার সিলেক্ট করলে স্টাইল লোড হবে, স্টাইল সিলেক্ট করলে কালার লোড হবে। |
| **Range Slider** | ইফিসিয়েন্সি পারসেন্টেজ বা রিকনসিলিয়েশন টলারেন্স রেঞ্জ সিলেকশন। |

---

### Category 5: Date & Time Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Date Picker** | সিঙ্গেল ক্যালেন্ডার ড্রপডাউন (আজকের দিন হাইলাইটেড)। |
| **Date Range Picker** | প্রোডাকশন উইক, শিপমেন্ট উইন্ডো (From Date — To Date)। |
| **Time Picker** | সুইং আওয়ারলি প্রোডাকশন স্লট ও শিফট টাইমিং। |
| **Date-Time Picker** | গেট পাস ট্র্যাকিং ও কিউসি ইন্সপেকশন লগিং। |
| **Month / Year Picker** | ফিনান্সিয়াল ইয়ার ও মান্থলি প্রোডাকশন প্ল্যানিং। |
| **Period Selector** | কুইক ফিল্টার: `Today`, `This Week`, `This Month`, `Quarterly`, `YTD`। |

---

### Category 6: Data Display Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Card & Widget** | হোয়াইট সারফেস, সফট শ্যাডো (`--shadow-card`), বর্ডার রেডিয়াস `16px`। |
| **Statistic & KPI Card** | বড় বোল্ড মেট্রিক ভ্যালু, শতকরা ট্রেন্ড ব্যাজ (↑ ১২% বনাম গতকাল), আইকন। |
| **Timeline** | অর্ডারের লাইফসাইকেল ট্র্যাকিং (Inquiry → Costing → Cutting → Sewing → Shipped)। |
| **Activity Feed** | ফ্লোরে কে কখন কোন বান্ডেল স্ক্যান করল তার রিয়েল-টাইম লাইভ লগ। |
| **Progress Bar / Circle** | কাটিং টার্গেট বনাম প্রকৃত কাট প্রগ্রেস (যেমন: ৮৫% সম্পন্ন)। |
| **Skeleton Loader** | ডাটা ফেচিং অবস্থায় মসৃণ পালসিং ধূসর শেপ (লেআউট শিফট রোধ করে)। |
| **Empty State** | ডাটা না থাকলে প্রফেশনাল ইলাস্ট্রেশন, ব্যাখ্যা এবং অ্যাকশন বাটন (`+ Add First Record`)। |

---

### Category 7: Navigation Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Sidebar** | কলাপসিবল বাম সাইডবার, আইকন ও গ্রুপড সাব-মেনু সহ। |
| **Navbar / Header** | গ্লোবাল সার্চ বার, কোম্পানি সুইচার, নোটিফিকেশন বেল, ইউজার প্রোফাইল। |
| **Breadcrumb** | পেজের রুট ট্র্যাকিং: `Home > Merchandising > Orders > PO-2026-0001`। |
| **Tabs** | পেজের ভেতরে সেকশন সুইচিং (e.g. `Order Details`, `BOM`, `Costing Sheet`, `Audits`)। |
| **Stepper** | মাল্টি-স্টেপ উইজার্ড (e.g. Step 1: Info → Step 2: Matrix → Step 3: Confirmation)। |
| **Pagination** | পেজ সাইজ সুইচার (`25`, `50`, `100`), কারেন্ট পেজ ও টোটাল রেকর্ডস। |
| **Quick Actions Menu** | প্রায়শই ব্যবহৃত কাজের শর্টকাট ফ্লোটিং ড্রপডাউন। |

---

### Category 8: Feedback & System Messages
| মেসেজ টাইপ | ভিজ্যুয়াল ও প্লেসমেন্ট |
| :--- | :--- |
| **Toast / Notification** | স্ক্রিনের উপরে ডানে ৩-৫ সেকেন্ডের জন্য ভেসে থাকা নোটিফিকেশন। |
| **Inline Alert Banner** | পেজের শীর্ষে সতর্কবার্তা (e.g. "Fabric stock is below safety limit!")। |
| **Validation Message** | প্রতিটি ইনপুটের ঠিক নিচে লাল টেক্সটে ফিল্ডের ত্রুটি প্রদর্শন। |
| **Confirmation Dialog** | গুরুত্বপূর্ণ অ্যাকশনের পূর্বে নিরাপত্তা যাচাই (e.g. "Are you sure you want to cancel this Cut Plan?")। |
| **Loading Spinner** | পেজ বা কার্ডের সেন্টারে ঘূর্ণায়মান অ্যাকসেন্ট স্পিনার। |

---

### Category 9: Overlay Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Slide-over Drawer (640px)** | এন্টারপ্রাইজ স্ট্যান্ডার্ড। নতুন রেকর্ড তৈরি, মাস্টার-ডিটেইল এন্ট্রি বা ফিল্টারের জন্য ডানদিক থেকে মসৃণ ট্রানজিশন। |
| **Modal / Dialog** | সংক্ষিপ্ত কনফার্মেশন ও ছোট ফর্মের জন্য স্ক্রিনের কেন্দ্রস্থলে ওভারলে। |
| **Popover** | নির্দিষ্ট বাটনের পাশে ছোট ইনফরমেশন বক্স। |
| **Context Menu** | টেবিলে রাইট ক্লিক করলে নির্দিষ্ট রো-এর অ্যাকশন মেনু (Print, Edit, Delete)। |
| **Command Palette (`Ctrl + K`)** | পুরো ইআরপির যেকোনো পেজ বা অর্ডারে দ্রুত জাম্প করার ইউনিভার্সাল সার্চ। |

---

### Category 10: File & Attachment Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Drag & Drop Upload Zone** | টেকপ্যাক PDF, বায়ার স্পেক্স বা ল্যাব টেস্ট রিপোর্ট ড্র্যাগ করে আপলোড। |
| **Image & Document Preview** | ফাইল ডাউনলোড না করে ব্রাউজারেই সরাসরি প্রিভিউ দেখার ভিউয়ার। |
| **Attachment Panel** | আপলোডকৃত ফাইলের নাম, সাইজ, আপলোডকারী এবং ডিলিট অ্যাকশন তালিকা। |
| **Upload Progress Bar** | বড় ফাইলের আপলোড শতাংশ প্রদর্শন। |

---

### Category 11: Search & Filter Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Global Search** | নেভবারে সার্বক্ষণিক কিবোর্ড-ফ্রেন্ডলি এন্টারপ্রাইজ সার্চ। |
| **Filter Panel** | ড্রয়ারে একাধিক শর্ত (Buyer, Date, Status, Unit) একত্রে ফিল্টার করার সুবিধা। |
| **Active Filter Chips** | কোন কোন ফিল্টার সক্রিয় আছে তা চিপ আকারে দেখা এবং এক ক্লিকে রিমুভ করার সুবিধা (`✕ Clear All`)। |
| **Saved Filters** | প্রায়শই ব্যবহৃত জটিল ফিল্টার সেভ করে এক ক্লিকে পুনরায় ব্যবহারের সুবিধা। |

---

### Category 12: Dashboard & Analytics Components
| উপাদান | বিবরণ |
| :--- | :--- |
| **Trend & Metric Card** | টার্গেট বনাম অর্জন, সুইং DHU (Defect per Hundred Units) রেট। |
| **Bar / Line Charts** | আওয়ারলি সুইং প্রোডাকশন ট্রেন্ড, এফিসিয়েন্সি চার্ট। |
| **Donut / Pie Charts** | ডিফেক্ট ক্লাসিফিকেশন (Critical / Major / Minor) অনুপাত। |
| **Dashboard Widget** | ড্র্যাগ-অ্যান্ড-ড্রপ সাইজেবল অ্যানালিটিক্স উইজেট। |

---

### Category 13: Enterprise & RMG ERP-Specific Components
TraceFlow-RMG-এর ব্যাকবোন হিসেবে এই উপাদানগুলো ব্যবহৃত হবে:

1. **Document Number Component (`Auto-generated`):**
   - ইনপুট ডিসেবলড ও রিড-অনলি।
   - সিস্টেমের সিকোয়েন্স ইঞ্জিন থেকে প্রিফিক্স ও প্যাটার্ন অনুযায়ী স্বয়ংক্রিয়ভাবে কোড শো করবে।
2. **Approval Workflow Panel:**
   - ড্রাফট → পেন্ডিং রিভিউ → মার্চেন্ডাইজিং জিএম অ্যাপ্রুভড → ফ্যাক্টরি লকড।
   - কে কোন তারিখে অ্যাপ্রুভ করেছেন তা টাইমস্ট্যাম্প ও ডিজিটাল সাইন সহ প্রদর্শন।
3. **Audit Trail & Activity Log:**
   - প্রতিটি পরিবর্তনের পূর্বে ও পরের মানের তুলনা (Diff Viewer: Before vs After)।
4. **Organization Tree Selector:**
   - গ্রুপ → কোম্পানি → ফিজিক্যাল প্ল্যান্ট → ফ্লোর → সুইং লাইন হায়ারার্কি পিকার।
5. **Buyer / Style / PO Cascading Selector:**
   - কমার্শিয়াল ও প্রোডাকশনের লাইভ লিঙ্কিং পিকার।
6. **Issue / RCA / CAPA Form:**
   - কোয়ালিটি ডিফেক্টের রুট কজ অ্যানালিসিস (RCA) ও কারেক্টিভ প্রিভেনটিভ অ্যাকশন (CAPA) উইজেট।
7. **Revision & Change History:**
   - টেকপ্যাক রিভিশন (Rev 1, Rev 2) এবং সাইজ চার্ট আপডেটের হিস্ট্রি লগার।

---

### Category 14: Table-Specific Components (High-Density Data Grid)
গার্মেন্টস ইআরপিতে ডাটা টেবিল হলো সবচেয়ে বেশি ব্যবহৃত উপাদান। 

👉 **সম্পূর্ণ ডেডিকেটেড আর্কিটেকচার, ভার্চুয়ালাইজেশন ও কোড স্পেসিফিকেশন দেখুন:**  
[ENTERPRISE-DATA-TABLE-SPECIFICATION.md](file:///d:/ERP/TraceFlow-RMG/ERP-AI-DEVELOPMENT-TEAM/02-ARCHITECTURE-AND-DESIGN/02-UI-UX-Designer/ENTERPRISE-DATA-TABLE-SPECIFICATION.md)

| টেবিল ফিচার | স্ট্যান্ডার্ড ও আচরণ |
| :--- | :--- |
| **Header & Rows** | হেডার উচ্চতা `44px` (বোল্ড ও গ্রে ব্যাকগ্রাউন্ড), রো উচ্চতা `52px`, অল্টারনেট স্ট্রাইপ বা হোভার লাইট ব্যাকগ্রাউন্ড। |
| **Column Sorting & Filter** | কলাম হেডারে ক্লিক করে অ্যাসেন্ডিং/ডিসেন্ডিং সর্ট এবং ইন-কলাম কুইক সার্চ। |
| **Fixed / Pinned Columns** | অনুভূমিক স্ক্রোলিংয়ের সময় `Order No` ও `Buyer Name` বামে এবং `Actions` ডানে ফিক্সড থাকবে। |
| **Column Resize & Reorder** | ইউজার ড্র্যাগ করে কলামের প্রশস্ততা ছোট-বড় করতে পারবেন। |
| **Row Selection & Bulk Actions** | চেক করে একাধিক অর্ডার একসাথে বাল্ক অ্যাপ্রুভ, বাল্ক স্ট্যাটাস চেঞ্জ বা বাল্ক এক্সপোর্ট। |
| **Inline Editing** | সেলে ডাবল ক্লিক করে সাথে সাথে ভ্যালু আপডেট (`Esc` চাপলে ক্যান্সেল, `Enter` চাপলে সেভ)। |
| **Nested Row Expand** | একটি অর্ডারে ক্লিক করলে টেবিলের ভেতরেই তার কালার/সাইজ ব্রেকডাউন সাব-টেবিল ওপেন হবে। |
| **Export Engine** | এক ক্লিকে বর্তমান ফিল্টারকৃত ডাটা Excel (.xlsx) অথবা PDF আকারে রফতানি। |
| **Summary / Total Row** | টেবিলের সর্বশেষে মোট কোয়ান্টিটি, মোট ভ্যালু এবং এভারেজ ব্যালেন্সের ফিক্সড রো। |

---

### Category 15: Authentication & Security UI
| সিকিউরিটি স্ক্রিন | উপাদান ও ফ্লো |
| :--- | :--- |
| **Login Screen** | ওয়ার্কস্পেস ডোমেন, কোম্পানি কোড, ইউজারনেম/ইমেইল ও পাসওয়ার্ড। |
| **2FA / OTP Verification** | ৬ ডিজিটের ডিজিটাল ওটিপি ইনপুট উইজেট (অটো-ফোকাস টু নেক্সট বক্স)। |
| **Session Expiry Overlay** | অলস বসে থাকলে সেশন শেষ হওয়ার ওয়ার্নিং এবং পুনরায় পাসওয়ার্ড দিয়ে তাৎক্ষণিক রিজুমের মোডাল। |
| **403 Access Denied Screen** | অনুমতিহীন মডিউলে প্রবেশের চেষ্টা করলে পরিচ্ছন্ন "Access Restricted" স্ক্রিন ও রিকোয়েস্ট অ্যাকসেস বাটন। |
| **User Profile & Security** | পাসওয়ার্ড পরিবর্তন, অ্যাক্টিভ সেশন লগআউট এবং রোল পারমিশন ভিউয়ার। |

---

### Category 16: System States & Feedback Matrix (Universal States)

প্রতিটি ইনপুট, বাটন, টেবিল এবং পূর্ণাঙ্গ স্ক্রিনের জন্য নিচের স্টেটগুলো স্ট্যান্ডার্ড সিএসএস ক্লাসে ডিফাইন থাকবে:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        COMPONENT STATES MATRIX                         │
├─────────────────┬──────────────────────────────────────────────────────┤
│ 1. Normal State │ Default → Hover → Focus → Active → Selected          │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 2. Form State   │ Required (*) │ Optional │ Disabled │ Read-only      │
│                 │ Valid (Green) │ Invalid (Red Border) │ Warning (Amber)│
├─────────────────┼──────────────────────────────────────────────────────┤
│ 3. Page State   │ Loading (Skeleton) │ Empty State (No records)        │
│                 │ Success Confirmation │ Error Alert                   │
│                 │ Offline Banner │ 403 Forbidden │ 404 Not Found       │
└─────────────────┴──────────────────────────────────────────────────────┘
```

1. **ইন্টারেক্টিভ স্টেট:**
   - **Default:** সুনির্দিষ্ট বর্ডার ও ব্যাকগ্রাউন্ড।
   - **Hover:** ব্যাকগ্রাউন্ড বা বর্ডারে সাবটল ট্রানজিশন (`0.2s smooth`).
   - **Focus:** বাধ্যতামূলক **`3px` ব্লু গ্লো রিং** (`--border-focus: #0ea5e9`).
   - **Active / Pressed:** ডাউন-স্কেল ট্রান্সফর্ম (`scale(0.98)`).
   - **Selected:** হালকা নীল ব্যাকগ্রাউন্ড শেড ও বোল্ড টেক্সট।

2. **ফর্ম ভ্যালিডেশন স্টেট:**
   - **Required:** লেবেলের পাশে লাল তারকাচিহ্ন (`*`).
   - **Invalid / Error:** লাল বর্ডার, নিচে ত্রুটি বার্তা এবং ফোকাসে লাল গ্লো।
   - **Valid:** হালকা সবুজ চেক আইকন।
   - **Read-only / Disabled:** ব্যাকগ্রাউন্ড `#f1f5f9`, কার্সর `not-allowed`, কোনো ইভেন্ট ফায়ার করবে না।

3. **সিস্টেম ও পেজ স্টেট:**
   - **Loading:** কোনো ব্ল্যাঙ্ক হোয়াইট পেজ থাকবে না; হুবহু টেবিল ও কার্ডের আদলে স্কেলিটন শিমার করবে।
   - **Empty State:** টেবিল ফাঁকা থাকলে পরিচ্ছন্ন বার্তা: "No production orders found for this filter" এবং একটি প্রাইমারি অ্যাকশন বাটন।
   - **Offline State:** ইন্টারনেট ড্রপ করলে স্ক্রিনের উপরে একটি অ্যাম্বার নোটিফিকেশন বার ভেসে উঠবে: "Network connection lost. Live scanning cached locally."
   - **Not Found (404) & Forbidden (403):** ইউজার ফ্রেন্ডলি ইলাস্ট্রেশন ও "Back to Dashboard" গাইডেন্স বাটন।

---

## ৩. ফ্রন্টএন্ড কোডিং ও ইমপ্লিমেন্টেশন রুলস

1. **ডিরেক্টরি ও নেমিং স্ট্যান্ডার্ড:**
   - সমস্ত ফাইল পাসকেল কেসে (`SlideOverDrawer.tsx`, `DataGrid.tsx`, `DocumentNumberBadge.tsx`) হবে।
2. **টাইপস্ক্রিপ্ট ইন্টারফেস:** প্রতিটি কম্পোনেন্টের প্রপস কঠোরভাবে টাইপড থাকবে (নো `any` টাইপ)।
3. **একক সোর্স অব ট্রুথ:**
   - ফ্রন্টএন্ডের সকল ডিজাইনার ও ডেভেলপার এই ডকুমেন্টটি রেফারেন্স হিসেবে মেনে নতুন কম্পোনেন্ট তৈরি ও পেজ ডিজাইন করবেন।
