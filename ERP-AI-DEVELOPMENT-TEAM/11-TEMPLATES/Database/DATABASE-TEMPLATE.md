# TraceFlow-RMG: ডাটাবেজ টেবিল ও মাইগ্রেশন ডিজাইন টেমপ্লেট
**টেবিল কোড:** `DB-TBL-[XXX]`  
**টেবিলের নাম:** `[table_names (plural, snake_case)]`  
**মডিউল:** `[MOD-01-CUTTING / MOD-02-SEWING etc.]`  
**পার্টিশনিং প্রয়োজন:** [হ্যাঁ / না (যদি কোটি কোটি স্ক্যান লগ হয়)]

---

## ১. কলাম স্পেসিফিকেশন ও ডাটা টাইপস

| কলামের নাম | ডাটা টাইপ | নালযোগ্য? | ডিফল্ট মান | বিবরণ ও কনস্ট্রেইন্ট |
|---|---|:---:|---|---|
| `id` | `UUID` | No | `gen_random_uuid()` | প্রাইমারি কি (**UUID v7**) |
| `[entity]_id` | `UUID` | No | — | ফরেন কি রেফারেন্স |
| `status_id` | `UUID` | No | — | `lookup_values(id)` থেকে ডায়নামিক স্ট্যাটাস |
| `created_by` | `UUID` | No | — | অডিট ট্রেইল ইউজার রেফারেন্স |
| `updated_by` | `UUID` | No | — | অডিট ট্রেইল ইউজার রেফারেন্স |
| `created_at` | `TIMESTAMPTZ` | No | `CURRENT_TIMESTAMP`| রেকর্ড তৈরির সময় |
| `updated_at` | `TIMESTAMPTZ` | No | `CURRENT_TIMESTAMP`| সর্বশেষ সংশোধনের সময় |
| `deleted_at` | `TIMESTAMPTZ` | Yes | `NULL` | সফট ডিলিট ট্র্যাকিং |

---

## ২. ইনডেক্সিং ও কনস্ট্রেইন্ট আর্কিটেকচার
* **ইউনিক ইনডেক্স:** `CREATE UNIQUE INDEX uidx_[table]_[col] ON [table] ([col]);`
* **কম্পোজিট ইনডেক্স:** `CREATE INDEX idx_[table]_[col1]_[col2] ON [table] ([col1], [col2]);`

---

## ৩. রিভার্সিবল মাইগ্রেশন স্কেলিটন (Laravel 13 Migration)
```php
return new class extends Migration {
    public function up(): void {
        Schema::create('table_names', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Columns...
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('table_names');
    }
};
```
