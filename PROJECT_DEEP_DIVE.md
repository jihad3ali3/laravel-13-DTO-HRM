# Project Deep Dive

> **نسخة التحليل:** 2026-09-29 — مبنية على الكود الموجود في المستودع والفرع الحالي، لا على README عام أو افتراضات عن Laravel.
>
> **الإصدارات المعلنة:** Laravel `^13.17` وPHP `^8.3` كما في `composer.json`؛ لم أفترض patch version مثبتًا لأن `vendor/` غير موجود في checkout.
>
> **نطاق المراجعة:** `routes/`، `bootstrap/`، `app/` بكل طبقاته الموجودة، `database/`، `resources/views/`، ملفات الإعداد، الحزم، الاختبارات، و`HRM_API_Postman_Collection.json`.
>
> **تنبيه مهم للقارئ:** هذا الملف يشرح ما يفعله الكود فعليًا. عندما أذكر مشكلة أو تفسيرًا غير مثبت صراحةً أستخدم وسمًا واضحًا: **FACT** للحقيقة من الكود، **INFERENCE** للاستنتاج، و**ENGINEERING OPINION** للاقتراح الهندسي.

## جدول المحتويات

- [1. Project Overview](#1-project-overview)
- [2. Architecture](#2-architecture)
- [3. Project Structure](#3-project-structure)
- [4. Routes](#4-routes)
- [5. Controllers](#5-controllers)
- [6. Models](#6-models)
- [7. Database](#7-database)
- [8. Services](#8-services)
- [9. Repositories](#9-repositories)
- [10. Middleware](#10-middleware)
- [11. Authentication](#11-authentication)
- [12. Authorization](#12-authorization)
- [13. Events](#13-events)
- [14. Listeners](#14-listeners)
- [15. Jobs](#15-jobs)
- [16. Queues](#16-queues)
- [17. Notifications](#17-notifications)
- [18. Mail](#18-mail)
- [19. Views / Frontend](#19-views--frontend)
- [20. Configuration](#20-configuration)
- [21. Packages](#21-packages)
- [22. Feature Data Flows](#22-feature-data-flows)
- [23. Dependency Maps](#23-dependency-maps)
- [24. Database Map](#24-database-map)
- [25. Route Map](#25-route-map)
- [26. Architecture Observations](#26-architecture-observations)
- [27. How to Read the Project](#27-how-to-read-the-project)
- [28. Final Summary](#28-final-summary)

---

## 1. Project Overview

هذا المشروع هو **HRM Enterprise**: تطبيق إدارة موارد بشرية مبني على Laravel 13.17 وPHP 8.3 أو أحدث، ويجمع بين ثلاث واجهات HTTP مختلفة:

1. **Admin Web Panel** تحت `/admin`، يعرض Blade pages ويعيد `View` أو `RedirectResponse`.
2. **Employee Self-Service Portal** تحت `/portal`، وهو أيضًا Blade لكنه موجه للموظف المسجل.
3. **Versioned REST API** تحت `/api/v1`، ويعيد JSON موحدًا عبر `App\Http\Responses\ApiResponse`.

المجالات الموجودة فعلًا هي: المستخدمون والموظفون، الأقسام والمسميات الوظيفية، الحضور والانصراف، الورديات، الإجازات، العطلات، المصروفات، الرواتب، تقييم الأداء، الإعلانات، المستندات، الإشعارات، التقارير، الصلاحيات، وإضافات AI تجريبية.

### خلاصة ما يفعله النظام

- ينشئ `User` وحسابًا مرتبطًا بـ`Employee`.
- يربط الموظف بـ`Department` و`Designation` و`Shift`.
- يسجل `Attendance` مع check-in/check-out وحساب ساعات العمل.
- ينشئ `LeaveRequest` ويتحقق من الرصيد عند الإنشاء.
- ينشئ `ExpenseClaim` ويراقب حالته.
- ينشئ `SalaryStructure` ثم يولد `Payroll` للموظفين النشطين.
- يخزن الأهداف والمراجعات في `PerformanceGoal` و`PerformanceReview`.
- يعرض الإعلانات والعطلات والتنبيهات في لوحة الإدارة والـPortal.
- يوفر JSON APIs محمية أساسًا بـ`auth:sanctum`.

### ما وجدته وما لم أجده

| العنصر | النتيجة الفعلية |
|---|---|
| Service Layer | موجود في `app/Http/Services`، لكنه في الغالب طبقة تفويض إلى Repository |
| Repository Pattern | موجود في `app/Repositories`، ويحتوي معظم استعلامات وقواعد المجال |
| DTO | موجود داخل `app/Domain`، و`FormRequest::toDto()` هو جسر التحويل |
| Form Requests | موجودة داخل `app/Http/Requests/Api/V1` وتُعاد استخدامها في Web وAPI |
| API Resources | **غير موجودة**؛ الـAPI يعيد Eloquent models/paginators مباشرة داخل `data` |
| Policies / Gates مخصصة | لا توجد Policies؛ يوجد `Gate::before` واحد في `AppServiceProvider` |
| Custom Middleware | لا توجد ملفات في `app/Http/Middleware` |
| Events / Listeners | المجلدات غير موجودة ولا توجد استدعاءات `event()` |
| Jobs / Workers | لا توجد Jobs مخصصة ولا `dispatch()` |
| Notifications Laravel | لا توجد classes في `app/Notifications`؛ يوجد Model مخصص اسمه `Notification` |
| Mailables | أربع classes في `app/Mail`، لكن لا يوجد مسار يستدعي `Mail::to()` في الكود الحالي |
| Vue / React / Livewire / Inertia | غير موجودة |
| Blade | هو الـFrontend الأساسي |
| AJAX / Fetch | يوجد `fetch()` في صفحة AI فقط؛ بقية النماذج HTML عادية |
| Modules / DDD كامل | لا يوجد Modular package؛ يوجد تنظيم Domain DTOs فقط |
| Contracts / Interfaces / Actions / Observers / Traits مخصصة | غير موجودة؛ Traits المستخدمة من packages مثل `HasRoles` و`HasApiTokens` |

---

## 2. Architecture

### 2.1 نوع الـArchitecture الحقيقي

**FACT:** التنظيم الحالي هو Laravel MVC موسع بطبقات Service وRepository وDTO، وليس DDD كاملًا ولا CQRS ولا Hexagonal Architecture.

المسار العام المطبق في أغلب API وعمليات الكتابة هو:

```text
HTTP Client / Blade Form
        ↓
Route (routes/admin.php أو routes/api.php)
        ↓
Laravel web أو api middleware
        ↓
Auth middleware / Spatie permission middleware عند وجوده
        ↓
FormRequest أو Request validation
        ↓
FormRequest::toDto()
        ↓
Controller
        ↓
Service (يُحقن عبر Constructor)
        ↓
Repository
        ↓
Eloquent Model / Query Builder
        ↓
Database
        ↓
ApiResponse أو View أو Redirect
```

لكن هذا **ليس مسارًا إلزاميًا لكل شاشة**. توجد مسارات مباشرة مثل:

```text
Admin EmployeeController::show
        ↓
Employee::with(...)->findOrFail()
        ↓
Blade View
```

و:

```text
PortalController::dashboard
        ↓
Attendance / Leave / ExpenseClaim / Payroll / PerformanceGoal / Announcement / Holiday مباشرة
        ↓
Blade View
```

أي أن المشروع يحتوي مسارين متجاورين:

- مسار منظم نسبيًا: `Controller → FormRequest → DTO → Service → Repository → Model`.
- مسار مباشر: `Controller → Model`، خصوصًا في صفحات القراءة والـPortal.

### 2.2 مسؤولية كل Layer

#### Route layer

`bootstrap/app.php` يسجل:

- `routes/admin.php` كمسارات Web، لذلك يشملها Web middleware group.
- `routes/api.php` كمسارات API، لذلك يضيف Laravel prefix `/api` وAPI middleware group.
- `routes/console.php` للأوامر، وهو فارغ حاليًا.

#### Middleware layer

- `auth`: جلسة `web` للـAdmin والـPortal.
- `guest`: يمنع المستخدم المسجل من صفحة Admin login.
- `auth:sanctum`: يتحقق من Bearer token أو guard session حسب إعداد Sanctum.
- `permission:*`: aliases مسجلة في `bootstrap/app.php` من Spatie.

#### Form Request layer

كل `FormRequest` يعرف rules ويحتوي غالبًا `toDto()`. معظم `authorize()` ترجع `true`، أي أن FormRequest نفسها لا تقوم بتحديد الصلاحيات.

#### Controller layer

يفترض أن يستقبل request ويدير شكل الـresponse. لكن بعض Controllers، خاصة Portal وAdmin صفحات القراءة، تنفذ Eloquent queries داخلها مباشرة.

#### DTO layer

الـDTOs في `app/Domain/<Feature>` تحمل بيانات typed من HTTP إلى Service/Repository. هي ليست Models ولا تحفظ شيئًا في قاعدة البيانات.

#### Service layer

Services مثل `EmployeeService` و`LeaveService` تحفظ حدودًا موحدة لكل Feature وتفوض إلى Repository. `AuthService` و`AiService` لديهما منطق أكبر داخل Service نفسها.

#### Repository layer

هنا توجد استعلامات Eloquent والحسابات وقواعد مثل التحقق من رصيد الإجازة وتوليد صافي الراتب. `RepositoryException` يستخدم للإبلاغ عن not found أو قواعد فشل محددة.

#### Model layer

Models هي Eloquent records وrelations وcasts وبعض accessors/scopes. لا توجد أحداث Model أو Observers مخصصة.

#### View layer

Blade templates في `resources/views`. كل صفحات Admin وPortal تستخدم `layouts/admin-layout.blade.php` وتضمّن sidebar/navbar/alerts/footer.

### 2.3 أين يبدأ التنفيذ؟

```text
Browser أو API client
  ↓
public/index.php
  ↓
bootstrap/app.php: Application::configure(...)
  ↓
Service Providers ومنها AppServiceProvider::boot()
  ↓
web/api middleware pipeline
  ↓
Router
  ↓
Controller أو closure
```

`public/index.php` لا يحتوي business logic؛ وظيفته إنشاء التطبيق من `bootstrap/app.php` ثم تمرير الطلب إلى Kernel/handler. `bootstrap/app.php` هو الذي يحدد route files وaliases والاستجابة JSON للأخطاء.

### 2.4 أين يوجد كل نوع من المنطق؟

| السؤال | المكان الفعلي |
|---|---|
| Business logic للحضور | `AttendanceRepository`، مع بعض Query logic في Admin/Portal controllers |
| Business logic للإجازات | `LeaveRepository`، خصوصًا `getBalancesForType` و`process` |
| Business logic للرواتب | `PayrollRepository` |
| Validation | `app/Http/Requests/Api/V1/**`، وبعض `Request::validate()` في Controllers |
| Authentication | `config/auth.php` + `AuthService` + Laravel session/Sanctum |
| Authorization | Spatie middleware و`Gate::before` وBlade `@can`؛ ليس Policies |
| Database access | Repositories، Models مباشرة في بعض Controllers، Seeders |
| الملفات | `EmployeeDocumentStoreRequest::toDto()`، Portal expense، AI Controllers؛ التخزين على disk `public` |
| Emails | Mail classes موجودة فقط؛ لا يوجد sender call فعليًا |
| Queues | config ومigrations موجودة؛ لا يوجد Job أو dispatch فعلي |
| Events | غير موجودة فعليًا |
| API logic | `routes/api.php` و`app/Http/Controllers/Api/V1` |
| Web UI | Blade في `resources/views`، مع Bootstrap/template assets و`fetch` في AI |

### 2.5 FACT مقابل INFERENCE حول التصميم

- **FACT:** وجود Service وRepository وDTOs يدل على محاولة فصل HTTP/data-transfer عن persistence.
- **FACT:** هذا الفصل غير مطبق بالتساوي، لأن عدة Controllers تستخدم Models مباشرة.
- **INFERENCE:** غالبًا أضيفت طبقات Service/Repository لتوحيد API وإتاحة إعادة الاستخدام بين Admin وAPI وPortal.
- **ENGINEERING OPINION:** لو أصبح النظام إنتاجيًا، ينبغي اختيار سياسة واضحة: إما نقل domain logic المركزي إلى Services/Repositories بالكامل، أو توثيق صراحةً أن Controllers القراءة البسيطة مسموح لها باستخدام Eloquent.

---

## 3. Project Structure

```text
app/
├── Domain/                         DTOs typed حسب الـFeature
├── Exceptions/                     RepositoryException
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                  Blade Admin controllers
│   │   ├── Api/V1/                 JSON controllers
│   │   └── Portal/                 Employee self-service controllers
│   ├── Requests/Api/V1/             FormRequests مشتركة بين الواجهات
│   ├── Responses/                   ApiResponse
│   └── Services/                    Service layer
├── Mail/                            Mailable classes غير مستخدمة حاليًا
├── Models/                          Eloquent models
├── Providers/                       AppServiceProvider
└── Repositories/                    Query/persistence + domain calculations

bootstrap/
├── app.php                          routes, middleware aliases, JSON errors
└── providers.php                    AppServiceProvider

config/                              auth, database, queue, mail, permission, ...
database/
├── factories/                       UserFactory فقط
├── migrations/                     schema
└── seeders/                        roles + enterprise demo data
resources/views/                     Blade Admin, Portal, Emails, layout
resources/css/                       Tailwind entry file
resources/js/                        entry file شبه فارغ
public/                              front controller و assets template
routes/
├── admin.php                        welcome + Portal + Admin Web
├── api.php                          /api/v1 REST API
└── console.php                      فارغ
 tests/                              Pest feature/unit tests
```

### ملفات غير معتادة

- `HRM_API_Postman_Collection.json`: مجموعة اختبار/تجربة لكل API تقريبًا، وتؤكد أن العنوان المتوقع هو `/api/v1`.
- `public/assets/json/ajax.php`: ملف بيانات تجريبية بصيغة PHP للـDataTables، ولا يظهر في route أو controller مستخدمًا فعليًا.
- `public/assets/`: assets كثيرة من Admin template، وليست ناتجة عن `resources/js/app.js`.
- `.junie/skills` و`.ai`: إرشادات أدوات التطوير، لا تدخل في runtime.
- `README.md`: ما زال README skeleton الخاص بـLaravel ولا يشرح HRM.

### Tests التي تمت مراجعتها

- `tests/Feature/AdminWebPagesTest.php`: صفحات لوحة الإدارة، وdynamic sidebar حسب الأدوار.
- `tests/Feature/AdminWebActionsTest.php`: onboarding employee، attendance، leave approval، payroll، expense، announcements/AI، settings، وemployee self-service عبر web routes.
- `tests/Feature/Api/RestApiTest.php`: login/profile، attendance، leaves، holidays، expenses، performance، announcements، notifications، AI، dashboard/reports، employees، settings/roles.
- `tests/Feature/PortalWebTest.php`: Portal dashboard وصفحات attendance/leaves/expenses/payroll/performance.
- `tests/Feature/ExportTest.php`: CSV/print exports.
- `tests/Feature/DashboardTest.php` و`RoleAndPermissionSeederTest.php`: dashboard metrics وRBAC/seed data.
- `tests/Feature/HrmApiTest.php`: API coverage إضافية.
- `tests/Feature/ExampleTest.php` و`tests/Unit/ExampleTest.php`: tests skeleton بسيطة.

الاختبارات تستخدم Pest و`RefreshDatabase` في ملفات Feature التي تحتاج DB، بينما `tests/Pest.php` يربط Feature بـ`Tests\TestCase`. `phpunit.xml` يضبط SQLite in-memory وqueue/cache/session test settings. لم أشغل suite في هذا checkout لأن `vendor/` غير موجود وbinary `php` غير متاح في بيئة التحليل؛ لذلك هذه مراجعة static وليست ادعاءً بأن الاختبارات اجتازت runtime.
---

## 4. Routes

### 4.1 كيف تُقرأ أسماء الـRoutes

- `routes/admin.php` مسجل كـWeb file، لذلك `/admin/...` و`/portal/...` مسارات Browser/session.
- `routes/api.php` مسجل كـAPI file، لذلك المسار المكتوب `v1/...` يصبح فعليًا `/api/v1/...`.
- أسماء Admin تبدأ من `admin::`، وأسماء Portal من `portal::`، وأسماء API من `api::`.
- `Route::resource` يولد أكثر من Route حتى لو ظهر كسطر واحد.

### 4.2 Web: Welcome وPortal

| Method | URL | Route name | Controller/action | Middleware | النتيجة |
|---|---|---|---|---|---|
| GET | `/` | `welcome` | Closure → `view('welcome')` | web | Welcome view |
| GET | `/portal` | `portal::dashboard` | `PortalController@dashboard` | `auth` | `portal.dashboard` |
| GET | `/portal/dashboard` | `portal::dashboard.index` | `PortalController@dashboard` | `auth` | نفس الصفحة |
| GET | `/portal/attendance` | `portal::attendance.index` | `PortalAttendanceController@index` | `auth` | attendance view |
| POST | `/portal/attendance/check-in` | `portal::attendance.checkIn` | `checkIn` | `auth` | redirect back |
| POST | `/portal/attendance/check-out` | `portal::attendance.checkOut` | `checkOut` | `auth` | redirect back |
| GET | `/portal/leaves` | `portal::leaves.index` | `PortalLeaveController@index` | `auth` | leaves view |
| POST | `/portal/leaves` | `portal::leaves.store` | `store` | `auth` | creates pending leave ثم redirect |
| GET | `/portal/expenses` | `portal::expenses.index` | `PortalExpenseController@index` | `auth` | expenses view |
| POST | `/portal/expenses` | `portal::expenses.store` | `store` | `auth` | validates/uploads/creates claim |
| GET | `/portal/payroll` | `portal::payroll.index` | `PortalPayrollController@index` | `auth` | payroll view |
| GET | `/portal/payroll/{id}/payslip` | `portal::payroll.payslip` | `payslip` | `auth` | payslip Blade |
| GET | `/portal/performance` | `portal::performance.index` | `PortalPerformanceController@index` | `auth` | performance view |
| PATCH | `/portal/performance/{id}/progress` | `portal::performance.updateProgress` | `updateProgress` | `auth` | redirect back |

### 4.3 Web: Admin authentication and generated CRUD

| Method | URL | Route name | Action | Middleware | Response |
|---|---|---|---|---|---|
| GET | `/admin/login` | `admin::login` | Closure → `admin.auth.login` | `guest` | login Blade |
| POST | `/admin/login` | `admin::authenticate` | `Admin\Auth\AuthController@login` | `guest` | redirect dashboard |
| POST | `/admin/logout` | `admin::logout` | `Admin\Auth\AuthController@logout` | `auth` | redirect login |
| GET | `/admin/dashboard` | `admin::dashboard.admin` | `DashboardController@index` | `auth` | dashboard view |
| GET | `/admin/employees` | `admin::employees.index` | `EmployeeController@index` | `auth` | paginated employees |
| GET | `/admin/employees/create` | `admin::employees.create` | `create` | `auth` | create form |
| POST | `/admin/employees` | `admin::employees.store` | `store` | `auth` | User + Employee ثم redirect |
| GET | `/admin/employees/{employee}` | `admin::employees.show` | `show` | `auth` | employee details |
| GET | `/admin/employees/{employee}/edit` | `admin::employees.edit` | `edit` | `auth` | edit form |
| PUT/PATCH | `/admin/employees/{employee}` | `admin::employees.update` | `update` | `auth` | redirect show |
| DELETE | `/admin/employees/{employee}` | `admin::employees.destroy` | `destroy` | `auth` | redirect index |
| GET | `/admin/holidays` | `admin::holidays.index` | `HolidayController@index` | `auth` | calendar |
| POST | `/admin/holidays` | `admin::holidays.store` | `store` | `auth` | create |
| PUT/PATCH | `/admin/holidays/{holiday}` | `admin::holidays.update` | `update` | `auth` | update |
| DELETE | `/admin/holidays/{holiday}` | `admin::holidays.destroy` | `destroy` | `auth` | delete |
| GET | `/admin/announcements` | `admin::announcements.index` | `AnnouncementController@index` | `auth` | notices |
| POST | `/admin/announcements` | `admin::announcements.store` | `store` | `auth` | create |
| PUT/PATCH | `/admin/announcements/{announcement}` | `admin::announcements.update` | `update` | `auth` | generated but **لا يوجد method update في Admin controller** |
| DELETE | `/admin/announcements/{announcement}` | `admin::announcements.destroy` | `destroy` | `auth` | delete |

> `Route::resource('holidays')->except(['create','show','edit'])` و`Route::resource('announcements')->except(['create','show','edit'])` ما زالا يولدان update routes. في `Admin\Announcement\AnnouncementController` لا توجد `update()`، لذلك هذا المسار قابل للفشل عند استدعائه.

### 4.4 Web: Admin feature routes

| Method | URL | Route name | Controller method | ما الذي يرجع؟ |
|---|---|---|---|---|
| GET | `/admin/documents` | `admin::documents.index` | `DocumentController@index` | view |
| POST | `/admin/documents` | `admin::documents.store` | `store` | redirect |
| PATCH | `/admin/documents/{id}/verify` | `admin::documents.verify` | `verify` | redirect |
| DELETE | `/admin/documents/{id}` | `admin::documents.destroy` | `destroy` | redirect |
| GET | `/admin/attendance` | `admin::attendance.index` | `AttendanceController@index` | view + counts |
| POST | `/admin/attendance/check-in` | `admin::attendance.checkIn` | `checkIn` | redirect |
| POST | `/admin/attendance/check-out` | `admin::attendance.checkOut` | `checkOut` | redirect |
| GET | `/admin/shifts` | `admin::shifts.index` | `ShiftController@index` | view |
| POST | `/admin/shifts` | `admin::shifts.store` | `store` | redirect |
| PUT | `/admin/shifts/{id}` | `admin::shifts.update` | `update` | redirect |
| DELETE | `/admin/shifts/{id}` | `admin::shifts.destroy` | `destroy` | redirect |
| POST | `/admin/shifts/assign` | `admin::shifts.assign` | `assign` | redirect |
| GET | `/admin/leaves/requests` | `admin::leaves.requests` | `LeaveController@requests` | requests view |
| GET | `/admin/leaves/index` | `admin::leaves.index` | `requests` | نفس view |
| POST | `/admin/leaves/requests` | `admin::leaves.store` | `store` | redirect |
| PATCH | `/admin/leaves/requests/{id}/status` | `admin::leaves.process` | `process` | redirect |
| GET | `/admin/leaves/types` | `admin::leaves.types` | `types` | leave types view |
| GET | `/admin/payroll` | `admin::payroll.index` | `PayrollController@index` | view |
| POST | `/admin/payroll/generate` | `admin::payroll.generate` | `generate` | redirect with count |
| PATCH | `/admin/payroll/{id}/pay` | `admin::payroll.pay` | `markAsPaid` | redirect |
| GET | `/admin/payroll/{id}/payslip` | `admin::payroll.payslip` | `payslip` | payslip view |
| GET | `/admin/payroll/structures` | `admin::payroll.structures` | `structures` | salary structures |
| POST | `/admin/payroll/structures` | `admin::payroll.structures.update` | `updateStructure` | redirect |
| GET | `/admin/expenses` | `admin::expenses.index` | `ExpenseController@index` | view |
| POST | `/admin/expenses` | `admin::expenses.store` | `store` | redirect |
| PATCH | `/admin/expenses/{id}/status` | `admin::expenses.process` | `process` | redirect |
| GET | `/admin/performance/goals` | `admin::performance.goals` | `goals` | view |
| POST | `/admin/performance/goals` | `admin::performance.goals.store` | `storeGoal` | redirect |
| PATCH | `/admin/performance/goals/{id}/progress` | `admin::performance.goals.progress` | `updateGoalProgress` | redirect |
| DELETE | `/admin/performance/goals/{id}` | `admin::performance.goals.destroy` | `destroyGoal` | redirect |
| GET | `/admin/performance/reviews` | `admin::performance.reviews` | `reviews` | view |
| POST | `/admin/performance/reviews` | `admin::performance.reviews.store` | `storeReview` | redirect |
| GET | `/admin/ai` | `admin::ai.index` | `AiController@index` | AI view |
| POST | `/admin/ai/chat` | `admin::ai.chat` | `chat` | raw JSON |
| POST | `/admin/ai/craft-announcement` | `admin::ai.craftAnnouncement` | `craftAnnouncement` | raw JSON |
| POST | `/admin/ai/generate-review` | `admin::ai.generateReview` | `generateReview` | raw JSON |
| POST | `/admin/ai/scan-receipt` | `admin::ai.scanReceipt` | `scanReceipt` | raw JSON |
| POST | `/admin/ai/parse-resume` | `admin::ai.parseResume` | `parseResume` | raw JSON |
| GET | `/admin/notifications` | `admin::notifications.index` | `NotificationController@index` | view |
| PATCH | `/admin/notifications/mark-all-read` | `admin::notifications.markAllRead` | `markAllRead` | redirect |
| POST | `/admin/notifications/send-test` | `admin::notifications.sendTest` | `sendTest` | redirect |
| GET | `/admin/reports` | `admin::reports.index` | `ReportController@index` | dashboard report view |
| GET | `/admin/reports/headcount` | `admin::reports.headcount` | `headcount` | view |
| GET | `/admin/reports/attendance` | `admin::reports.attendance` | `attendance` | view |
| GET | `/admin/reports/leaves` | `admin::reports.leaves` | `leaves` | view |
| GET | `/admin/reports/payroll` | `admin::reports.payroll` | `payroll` | view |
| GET | `/admin/roles` | `admin::roles.index` | `RolePermissionController@index` | view |
| POST | `/admin/roles` | `admin::roles.store` | `store` | redirect |
| PUT | `/admin/roles/{id}` | `admin::roles.update` | `update` | redirect |
| DELETE | `/admin/roles/{id}` | `admin::roles.destroy` | `destroy` | redirect |
| GET | `/admin/roles/permissions` | `admin::roles.permissions` | `permissions` | view |
| POST | `/admin/roles/assign-user` | `admin::roles.assignUser` | `assignUser` | redirect |
| GET | `/admin/settings/departments` | `admin::settings.departments` | `departments` | departments view |
| POST | `/admin/settings/departments` | `admin::settings.departments.store` | `storeDepartment` | redirect |
| PUT | `/admin/settings/departments/{id}` | `admin::settings.departments.update` | `updateDepartment` | redirect |
| DELETE | `/admin/settings/departments/{id}` | `admin::settings.departments.destroy` | `destroyDepartment` | redirect |
| GET | `/admin/settings/designations` | `admin::settings.designations` | `designations` | designations view |
| POST | `/admin/settings/designations` | `admin::settings.designations.store` | `storeDesignation` | redirect |
| PUT | `/admin/settings/designations/{id}` | `admin::settings.designations.update` | `updateDesignation` | redirect |
| DELETE | `/admin/settings/designations/{id}` | `admin::settings.designations.destroy` | `destroyDesignation` | redirect |
| GET | `/admin/settings/company` | `admin::settings.company` | `company` | view |
| PUT | `/admin/settings/company` | `admin::settings.company.update` | `updateCompany` | redirect |
| GET | `/admin/export/employees/csv` | `admin::export.employees.csv` | `employeesCsv` | streamed CSV |
| GET | `/admin/export/employees/print` | `admin::export.employees.print` | `employeesPrint` | print view |
| GET | `/admin/export/attendance/csv` | `admin::export.attendance.csv` | `attendanceCsv` | streamed CSV |
| GET | `/admin/export/payroll/csv` | `admin::export.payroll.csv` | `payrollCsv` | streamed CSV |
| GET | `/admin/export/payroll/print` | `admin::export.payroll.print` | `payrollPrint` | print view |
| GET | `/admin/export/leaves/csv` | `admin::export.leaves.csv` | `leavesCsv` | streamed CSV |
| GET | `/admin/export/expenses/csv` | `admin::export.expenses.csv` | `expensesCsv` | streamed CSV |

`/admin/settings/departments[/{id}]` تعني بالتحديد: GET collection، POST collection، PUT/DELETE على `/{id}`، ونفس الشكل لـ`designations`.

### 4.5 API authentication routes

| Method | URL | Route name | Middleware | Action |
|---|---|---|---|---|
| POST | `/api/v1/auth/login` | `api::login` | API فقط | `Api\V1\Auth\AuthController@login` |
| POST | `/api/v1/auth/logout` | `api::logout` | `auth:sanctum` | revoke current token |
| GET | `/api/v1/auth/profile` | `api::profile` | `auth:sanctum` | current user JSON |

### 4.6 API route map الكامل

كل routes التالية داخل group `Route::middleware('auth:sanctum')` ما لم يُذكر غير ذلك. Middleware permission موجود فقط حيث هو ظاهر في الجدول.

| Method | URL | Name | Controller method | Permission |
|---|---|---|---|---|
| GET | `/api/v1/attendance` | `api::attendance.index` | `AttendanceController@index` | — |
| POST | `/api/v1/attendance/check-in` | `api::attendance.check-in` | `checkIn` | — |
| POST | `/api/v1/attendance/check-out` | `api::attendance.check-out` | `checkOut` | — |
| GET | `/api/v1/attendance/daily-summary` | `api::attendance.daily-summary` | `dailySummary` | — |
| GET | `/api/v1/attendance/month-summary/{employeeId}` | `api::attendance.month-summary` | `monthlySummary` | — |
| GET | `/api/v1/leaves/types` | `api::leaves.types` | `LeaveController@types` | — |
| GET | `/api/v1/leaves/requests` | `api::leaves.requests` | `requests` | — |
| POST | `/api/v1/leaves/requests` | `api::leaves.store` | `store` | — |
| GET | `/api/v1/leaves/balance/{employeeId}` | `api::leaves.balances` | `balances` | — |
| PATCH | `/api/v1/leaves/requests/{id}/status` | `api::leaves.process` | `process` | `leaves.approve` |
| GET | `/api/v1/holidays` | `api::holidays.index` | `HolidayController@index` | — |
| GET | `/api/v1/holidays/{id}` | `api::holidays.show` | `show` | — |
| POST | `/api/v1/holidays` | `api::holidays.store` | `store` | `announcements.manage` |
| PUT | `/api/v1/holidays/{id}` | `api::holidays.update` | `update` | `announcements.manage` |
| DELETE | `/api/v1/holidays/{id}` | `api::holidays.destroy` | `destroy` | `announcements.manage` |
| GET | `/api/v1/expenses` | `api::expenses.index` | `ExpenseController@index` | — |
| POST | `/api/v1/expenses` | `api::expenses.store` | `store` | — |
| GET | `/api/v1/expenses/{id}` | `api::expenses.show` | `show` | — |
| PATCH | `/api/v1/expenses/{id}/status` | `api::expenses.process` | `process` | `expenses.approve` |
| GET | `/api/v1/performance/goals/{employeeId}` | `api::performance.goals` | `goals` | — |
| PATCH | `/api/v1/performance/goals/{id}/progress` | `api::performance.goals.progress` | `updateGoalProgress` | — |
| GET | `/api/v1/performance/reviews` | `api::performance.reviews` | `reviews` | — |
| GET | `/api/v1/performance/reviews/{id}` | `api::performance.reviews.show` | `showReview` | — |
| POST | `/api/v1/performance/goals` | `api::performance.goals.store` | `storeGoal` | `performance.manage` |
| DELETE | `/api/v1/performance/goals/{id}` | `api::performance.goals.destroy` | `destroyGoal` | `performance.manage` |
| POST | `/api/v1/performance/reviews` | `api::performance.reviews.store` | `storeReview` | `performance.manage` |
| GET | `/api/v1/announcements` | `api::announcements.index` | `AnnouncementController@index` | — |
| GET | `/api/v1/announcements/{id}` | `api::announcements.show` | `show` | — |
| POST | `/api/v1/announcements` | `api::announcements.store` | `store` | `announcements.manage` |
| PUT | `/api/v1/announcements/{id}` | `api::announcements.update` | `update` | `announcements.manage` |
| DELETE | `/api/v1/announcements/{id}` | `api::announcements.destroy` | `destroy` | `announcements.manage` |
| GET | `/api/v1/documents/employee/{employeeId}` | `api::documents.employee.index` | `DocumentController@index` | — |
| POST | `/api/v1/documents` | `api::documents.store` | `store` | — |
| GET | `/api/v1/documents/{id}` | `api::documents.show` | `show` | — |
| DELETE | `/api/v1/documents/{id}` | `api::documents.destroy` | `destroy` | — |
| PATCH | `/api/v1/documents/{id}/verify` | `api::documents.verify` | `verify` | `employees.edit` |
| GET | `/api/v1/shifts/employee/{employeeId}` | `api::shifts.employee` | `employeeShift` | — |
| GET | `/api/v1/shifts` | `api::shifts.index` | `index` | `shifts.view` |
| GET | `/api/v1/shifts/{id}` | `api::shifts.show` | `show` | `shifts.view` |
| POST | `/api/v1/shifts` | `api::shifts.store` | `store` | `shifts.manage` |
| PUT | `/api/v1/shifts/{id}` | `api::shifts.update` | `update` | `shifts.manage` |
| DELETE | `/api/v1/shifts/{id}` | `api::shifts.destroy` | `destroy` | `shifts.manage` |
| POST | `/api/v1/shifts/assign` | `api::shifts.assign` | `assign` | `shifts.manage` |
| GET | `/api/v1/notifications/channels` | `api::notifications.channels` | `channels` | — |
| GET | `/api/v1/notifications` | `api::notifications.index` | `index` | — |
| PATCH | `/api/v1/notifications/mark-all-read` | `api::notifications.mark-all-read` | `markAllRead` | — |
| PATCH | `/api/v1/notifications/{id}/read` | `api::notifications.read` | `markAsRead` | — |
| POST | `/api/v1/notifications/send-test` | `api::notifications.send-test` | `sendTest` | `settings.manage` |
| POST | `/api/v1/ai/chat` | `api::ai.chat` | `AiController@chat` | — |
| POST | `/api/v1/ai/scan-receipt` | `api::ai.scan-receipt` | `scanReceipt` | — |
| POST | `/api/v1/ai/generate-review` | `api::ai.generate-review` | `generateReview` | — |
| POST | `/api/v1/ai/parse-resume` | `api::ai.parse-resume` | `parseResume` | — |
| POST | `/api/v1/ai/craft-announcement` | `api::ai.craft-announcement` | `craftAnnouncement` | — |
| GET | `/api/v1/dashboard/metrics` | `api::dashboard.metrics` | `DashboardController@metrics` | `reports.view` |
| GET | `/api/v1/employees` | `api::employees.index` | `EmployeeController@index` | `employees.edit` |
| POST | `/api/v1/employees` | `api::employees.store` | `store` | `employees.create` |
| GET | `/api/v1/employees/{employee}` | `api::employees.show` | `show` | `employees.edit` |
| PUT | `/api/v1/employees/{employee}` | `api::employees.update` | `update` | `employees.edit` |
| DELETE | `/api/v1/employees/{employee}` | `api::employees.destroy` | `destroy` | `employees.delete` |
| GET | `/api/v1/payroll` | `api::payroll.index` | `PayrollController@index` | `payroll.view` |
| POST | `/api/v1/payroll/generate` | `api::payroll.generate` | `generate` | `payroll.generate` |
| GET | `/api/v1/payroll/{id}/payslip` | `api::payroll.payslip` | `payslip` | `payroll.view` |
| GET | `/api/v1/payroll/salary-structure/{employeeId}` | `api::payroll.salary-structure` | `salaryStructure` | `payroll.view` |
| POST | `/api/v1/payroll/salary-structure` | `api::payroll.salary-structure.update` | `updateSalaryStructure` | `payroll.generate` |
| PATCH | `/api/v1/payroll/{id}/pay` | `api::payroll.pay` | `markAsPaid` | `payroll.pay` |
| GET | `/api/v1/reports/attendance` | `api::reports.attendance` | `attendance` | `reports.view` |
| GET | `/api/v1/reports/leaves` | `api::reports.leaves` | `leaves` | `reports.view` |
| GET | `/api/v1/reports/payroll` | `api::reports.payroll` | `payroll` | `reports.view` |
| GET | `/api/v1/reports/headcount` | `api::reports.headcount` | `headcount` | `reports.view` |
| GET | `/api/v1/settings/departments` | `api::settings.departments` | `departments` | `settings.manage` |
| POST | `/api/v1/settings/departments` | `api::settings.departments.store` | `storeDepartment` | `settings.manage` |
| PUT | `/api/v1/settings/departments/{id}` | `api::settings.departments.update` | `updateDepartment` | `settings.manage` |
| DELETE | `/api/v1/settings/departments/{id}` | `api::settings.departments.destroy` | `destroyDepartment` | `settings.manage` |
| GET | `/api/v1/settings/designations` | `api::settings.designations` | `designations` | `settings.manage` |
| POST | `/api/v1/settings/designations` | `api::settings.designations.store` | `storeDesignation` | `settings.manage` |
| PUT | `/api/v1/settings/designations/{id}` | `api::settings.designations.update` | `updateDesignation` | `settings.manage` |
| DELETE | `/api/v1/settings/designations/{id}` | `api::settings.designations.destroy` | `destroyDesignation` | `settings.manage` |
| GET | `/api/v1/settings/company` | `api::settings.company` | `getCompanySettings` | `settings.manage` |
| PUT | `/api/v1/settings/company` | `api::settings.company.update` | `updateCompanySettings` | `settings.manage` |
| GET | `/api/v1/roles/permissions` | `api::roles.permissions` | `permissions` | `roles.manage` |
| POST | `/api/v1/roles/assign-user` | `api::roles.assign-user` | `assignUserRole` | `roles.manage` |
| GET | `/api/v1/roles` | `api::roles.index` | `index` | `roles.manage` |
| POST | `/api/v1/roles` | `api::roles.store` | `store` | `roles.manage` |
| GET | `/api/v1/roles/{id}` | `api::roles.show` | `show` | `roles.manage` |
| PUT | `/api/v1/roles/{id}` | `api::roles.update` | `update` | `roles.manage` |
| DELETE | `/api/v1/roles/{id}` | `api::roles.destroy` | `destroy` | `roles.manage` |

### 4.7 ماذا يحدث عند استدعاء Route API؟

مثال `POST /api/v1/employees`:

```text
auth:sanctum
  ↓
EmployeeStoreRequest::rules()
  ↓
EmployeeStoreRequest::toDto()
  ↓
Api\V1\Employee\EmployeeController::store()
  ↓
EmployeeService::store(EmployeeStoreDto)
  ↓
EmployeeRepository::store()
  ├── DB transaction
  ├── User::create + Hash::make
  ├── assignRole()
  └── Employee::create
  ↓
ApiResponse::created($employee)
  ↓
HTTP 201 { data: ..., message: ... }
```

مثال `GET /api/v1/announcements`: الـRequest يحدد `publishedOnly` وdepartment بحسب صلاحية المستخدم داخل `AnnouncementFilterRequest::toDto()`، ثم Repository يطبق `where('is_published')` وdepartment/global logic، ثم JSON.

---

## 5. Controllers

### 5.1 Controllers الخاصة بالـAPI

كل API Controller تقريبًا يتبع الشكل التالي: constructor injection لـService، ثم تحويل Request إلى DTO، ثم `ApiResponse`. التفاصيل المهمة:

| Controller | Methods | السلوك الفعلي |
|---|---|---|
| `Api\V1\Auth\AuthController` | `login`, `logout`, `profile` | `login` يستعمل `LoginRequest → AuthService`؛ `logout` يحذف token الحالي؛ `profile` يعيد user |
| `Api\V1\Attendance\AttendanceController` | `index`, `checkIn`, `checkOut`, `dailySummary`, `monthlySummary` | logs/check-in/check-out/summaries؛ `checkIn` يمسك `RepositoryException` |
| `Api\V1\Employee\EmployeeController` | `index`, `show`, `store`, `update`, `destroy` | CRUD كامل عبر `EmployeeService` |
| `Api\V1\Leave\LeaveController` | `types`, `requests`, `store`, `balances`, `process` | أنواع، طلبات، إنشاء، أرصدة، موافقة/رفض |
| `Api\V1\Expense\ExpenseController` | `index`, `show`, `store`, `process` | claims مع status processing |
| `Api\V1\Payroll\PayrollController` | `index`, `generate`, `payslip`, `salaryStructure`, `updateSalaryStructure`, `markAsPaid` | يولد batches ويعدل salary structures ويصرف payroll |
| `Api\V1\Performance\PerformanceController` | `goals`, `updateGoalProgress`, `storeGoal`, `destroyGoal`, `reviews`, `showReview`, `storeReview` | goals/reviews؛ `reviews` يفلتر الموظف العادي إلى نفسه |
| `Api\V1\Announcement\AnnouncementController` | `index`, `show`, `store`, `update`, `destroy` | notice board؛ الـRequest يفرض published/department بحسب user |
| `Api\V1\Document\DocumentController` | `index`, `show`, `store`, `destroy`, `verify` | رفع/عرض/حذف/تحقق مستند |
| `Api\V1\Holiday\HolidayController` | `index`, `show`, `store`, `update`, `destroy` | calendar CRUD |
| `Api\V1\Shift\ShiftController` | `employeeShift`, `index`, `show`, `store`, `update`, `destroy`, `assign` | schedules وemployee_shifts |
| `Api\V1\Notification\NotificationController` | `channels`, `index`, `markAllRead`, `markAsRead`, `sendTest` | custom DB notifications بحسب employee |
| `Api\V1\Ai\AiController` | `chat`, `scanReceipt`, `generateReview`, `parseResume`, `craftAnnouncement` | validation inline ثم `AiService` |
| `Api\V1\Dashboard\DashboardController` | `metrics` | يعيد dashboard aggregate data |
| `Api\V1\Report\ReportController` | `attendance`, `leaves`, `payroll`, `headcount` | يعيد arrays من ReportService |
| `Api\V1\Setting\SettingController` | departments/designations/company CRUD | يدير `SettingService` |
| `Api\V1\RolePermission\RolePermissionController` | permissions, assign, roles CRUD | يدير Spatie roles/permissions عبر repository |

### 5.2 Controllers الخاصة بالـAdmin Web

| Controller | Methods المهمة | مصدر البيانات/المسار |
|---|---|---|
| `Admin\Auth\AuthController` | `login`, `logout` | يستخدم `AuthService` في login لكن logout يدير `Auth::guard('web')` وsession مباشرة |
| `Admin\Dashboard\DashboardController` | `index` | `DashboardService → DashboardRepository → admin.dashboard.index` |
| `Admin\Employee\EmployeeController` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` | list/create/store/update/delete عبر Service؛ `show/edit` يستخدمان Eloquent مباشرة |
| `Admin\Attendance\AttendanceController` | `index`, `checkIn`, `checkOut` | `index` query مباشر مع counts؛ الكتابة عبر AttendanceService |
| `Admin\Document\DocumentController` | `index`, `store`, `verify`, `destroy` | القراءة query مباشر والكتابة Service |
| `Admin\Leave\LeaveController` | `requests`, `store`, `process`, `types` | requests Service + stats مباشرة؛ `types` Model مباشر |
| `Admin\Payroll\PayrollController` | `index`, `generate`, `markAsPaid`, `payslip`, `structures`, `updateStructure` | القراءة المباشرة لبعض الشاشات، generation/updates Service |
| `Admin\Performance\PerformanceController` | goals CRUD/progress وreviews | queries مباشرة للعرض وService للكتابة |
| `Admin\Announcement\AnnouncementController` | `index`, `store`, `destroy` | index مباشر، store/destroy Service؛ لا يوجد `update` رغم resource route |
| `Admin\Holiday\HolidayController` | `index`, `store`, `update`, `destroy` | index مباشر، mutations Service |
| `Admin\Expense\ExpenseController` | `index`, `store`, `process` | claims query/stats مباشرة؛ mutations Service |
| `Admin\Notification\NotificationController` | `index`, `markAllRead`, `sendTest` | بعض operations مباشرة على Model، send عبر Service |
| `Admin\Ai\AiController` | `index` + خمس عمليات AI | `AiService`؛ responses raw `response()->json` وليس ApiResponse |
| `Admin\Report\ReportController` | `index`, `headcount`, `attendance`, `leaves`, `payroll` | ReportService ثم views |
| `Admin\RolePermission\RolePermissionController` | roles/permissions/assign | العرض Model/Spatie مباشر؛ mutations Service |
| `Admin\Setting\SettingController` | departments, designations, company | عرض query مباشر وmutations/settings عبر Service |
| `Admin\Shift\ShiftController` | `index`, `store`, `update`, `destroy`, `assign` | Service للـShift، وemployee shifts query مباشر |
| `Admin\Export\ExportController` | 7 عمليات export/print | Eloquent query مباشر ثم streamed CSV أو print view |

### 5.3 Controllers الخاصة بالـPortal

- `PortalController::dashboard`: يحدد الموظف من `Auth::user()->id`، مع fallback إلى `Employee::first()`، ثم يجمع attendance وleave وexpense وgoal وpayroll وannouncement وholiday ويعيد `portal.dashboard`.
- `PortalAttendanceController::index`: يجلب logs بالشهر وtoday record وactive shift. `checkIn` ينشئ `AttendanceCheckInDto` بنفسه، و`checkOut` ينشئ `AttendanceCheckoutRequest` يدويًا ثم يمرره إلى Service.
- `PortalLeaveController::index/store`: يعرض الطلبات والأنواع ويحسب balances؛ `store` يستعمل inline validation ثم DTO ثم `LeaveService`.
- `PortalExpenseController::index/store`: يعرض claims، يرفع receipt إلى `public` disk، يبني `ExpenseClaimStoreDto` ثم يحفظ.
- `PortalPayrollController::index/payslip`: يستخدم Models مباشرة ويقيد payslip بـemployee الحالي.
- `PortalPerformanceController::index`: يعرض goals/reviews مباشرة؛ `updateProgress` يتحقق ويضمن أن goal يتبع employee، ثم يستدعي Service.

### 5.4 Request Validation وDTO catalog

كل Request classes موجودة تحت `app/Http/Requests/Api/V1` رغم أن بعضها يُستعمل من Web/Portal أيضًا. هذه نقطة مهمة: اسم المجلد يقول API، لكن class ليست محصورة بالـAPI.

| Request | أهم rules | DTO الناتج/السلوك |
|---|---|---|
| `LoginRequest` | email/password/remember_me | `LoginDTO` |
| `EmployeeFilterRequest` | search، department/designation exists، per_page | `EmployeeFilterDto` |
| `EmployeeStoreRequest` | code/email unique، identity، dates، employment type، salary، password، role | `EmployeeStoreDto`؛ ينشئ User وEmployee |
| `EmployeeUpdateRequest` | profile/job fields، salary، is_active | `EmployeeUpdateDto` |
| `AttendanceFilterRequest` | employee/department exists، date formats، status، per_page 1..100 | `AttendanceFilterDto` |
| `AttendanceCheckInRequest` | rules فارغة | `AttendanceCheckInDto` من authenticated employee، لا من body |
| `AttendanceCheckoutRequest` | rules فارغة | `AttendanceCheckOutDto` من authenticated employee |
| `AttendanceSummaryRequest` | rules فارغة | `AttendanceSummaryDto`، لكن لا يتحول إلى signature المناسبة في Repository |
| `AnnouncementFilterRequest` | department exists | يحدد `publishedOnly` وdepartment من permission/user ثم `AnnouncementFilterDto` |
| `AnnouncementStoreRequest` | title/priority/date/publish flags | `AnnouncementStoreDto` مع `createdBy` من user |
| `LeaveFilterRequest` | employee/status/per_page | `LeaveFilterDto` |
| `LeaveRequestStoreRequest` | leave exists، dates وend after start | `LeaveRequestStoreDto` |
| `LeaveRequestProcessRequest` | status approved/rejected، reject reason conditional | `LeaveRequestProcessDto` مع actioned user |
| `ExpenseClaimFilterRequest` | employee/status/per_page | `ExpenseClaimFilterDto` |
| `ExpenseClaimStoreRequest` | title/category/amount/date، receipt file | `ExpenseClaimStoreDto`؛ يحدد employee من user إذا لم يرسل |
| `ExpenseClaimProcessRequest` | accepted/rejected/reimbursed، reject reason conditional | `ExpenseClaimProcessDto` |
| `HolidayStoreRequest` | name/date/type enum | `HolidayStoreDto` |
| `EmployeeDocumentStoreRequest` | employee optional، document/file optional، mime/size | `EmployeeDocumentStoreDto`؛ يخزن الملف داخل `toDto()` |
| `PayrollGenerateRequest` | month 1..12، year 2020..2100 | `PayrollGenerateDto` |
| `SalaryStructureUpdateRequest` | employee + كل salary/allowance/deduction numeric >=0 | `SalaryStructureUpdateDto` |
| `PerformanceGoalProgressRequest` | progress 0..100 | `PerformanceGoalProgressDto` |
| `PerformanceGoalStoreRequest` | employee/title/dates/priority | `PerformanceGoalStoreDto` |
| `PerformanceReviewStoreRequest` | employee/cycle/rating 1..5/text/status | `PerformanceReviewStoreDto`، reviewer من authenticated user |
| `NotificationSendTestRequest` | employee/type/title/message/channel enum | `NotificationSendTestDto` |
| `RoleAssignRequest` | user exists، role name exists | `RoleAssignDto` |
| `RoleStoreRequest` | unique role name، permissions array/items exists | `RoleStoreDto` |
| `DepartmentStoreRequest` | unique name/code، description/active | `DepartmentStoreDto` |
| `DesignationStoreRequest` | department exists، name/active | `DesignationStoreDto` |
| `CompanySettingUpdateRequest` | `settings` array؛ `prepareForValidation` يجمع body كله إذا غاب key | `CompanySettingUpdateDto` |
| `ShiftStoreRequest` | unique code، `H:i` times، numeric hours | `ShiftStoreDto` |
| `ShiftAssignRequest` | employee/shift exists، start/end dates | `ShiftAssignDto` |

جميع هذه Requests تقريبًا `authorize(): true`; لذلك validation هنا **data validation** وليست authorization. الاستثناءات البارزة خارج FormRequest هي:

- `Admin\Ai\AiController` و`Api\V1\Ai\AiController` يستعملان `$request->validate()` مباشرة.
- `PortalLeaveController` و`PortalExpenseController` و`PortalPerformanceController` تستعمل inline validation.
- Admin document verify وAPI document verify يستعملان inline validation.

### 5.5 مثال تعليمي: `EmployeeController::store`

```php
public function store(EmployeeStoreRequest $request): RedirectResponse
{
    $this->employeeService->store($request->toDto());

    return redirect()->route('admin::employees.index')
        ->with('success', 'Employee registered successfully.');
}
```

التسلسل:

1. Route `POST /admin/employees` تستدعي method.
2. Laravel ينفذ `EmployeeStoreRequest` قبل method؛ إذا فشلت rules لا يصل التنفيذ إلى Controller.
3. `toDto()` يحول أسماء HTTP مثل `first_name` إلى `EmployeeStoreDto::$firstName`.
4. `EmployeeService` لا يعرف HTTP؛ يستقبل DTO فقط ويفوض إلى Repository.
5. `EmployeeRepository::store()` يفتح `DB::transaction`.
6. ينشئ `User` مع password hash، ثم `assignRole`، ثم `Employee` مع `user_id`.
7. عند نجاح المعاملة يرجع Employee، لكن Controller لا يعرضه؛ يرجع Redirect وflash message.
8. عند استثناء validation أو DB، لا يوجد catch في Web Controller؛ يعالج Laravel الاستثناء حسب البيئة.

---

## 6. Models

### 6.1 User وEmployee

#### `App\Models\User`

- الجدول الافتراضي: `users`.
- `#[Fillable(['name', 'email', 'password'])]` و`#[Hidden(['password', 'remember_token])]`.
- casts: `email_verified_at: datetime`، `password: hashed`.
- Traits: `HasApiTokens` من Sanctum، `HasFactory`، `HasRoles` من Spatie، `Notifiable`.
- relation: `employee(): HasOne`.

#### `App\Models\Employee`

- الجدول: `employees`.
- fillable: `user_id`, `employee_code`, `first_name`, `last_name`, `email`, `phone`, `gender`, `date_of_birth`, `joining_date`, `department_id`, `designation_id`, `employment_type`, `is_active`, `salary`, `avatar`, `address`, `emergency_contact`.
- casts: birth/joining `date`، `is_active` boolean، `salary` decimal:2.
- scope: `active($query)` يضيف `where('is_active', true)`.
- accessor: `status` يرجع `active` أو `inactive`.
- relations: `user` belongsTo، `department` belongsTo، `designation` belongsTo، وhasMany لـ`attendances`, `leaveRequests`, `documents`, `payrollRecords`, `employeeShifts`؛ وhasOne `salaryStructure`.

### 6.2 Models حسب Feature

| Model | Table | Fillable/حقول المجال | Casts/accessors/scopes | Relations |
|---|---|---|---|---|
| `Department` | `departments` | name, code, description, is_active | is_active boolean | hasMany `designations`, `employees` |
| `Designation` | `designations` | department_id, name, description, is_active | is_active boolean؛ `title` accessor = name | belongsTo department؛ hasMany employees |
| `Attendance` | `attendances` | employee_id, attendance_date, check_in/out, status, working_hours, notes | date، decimal؛ status constants | belongsTo employee |
| `EmployeeDocument` | `employee_documents` | employee_id, title, document_type, file_name/path, expiry_date, verification_status, verified_at, notes | date/datetime | belongsTo employee |
| `EmployeeShift` | `employee_shifts` | employee_id, shift_id, start/end_date, is_active | dates/boolean؛ `active` scope | belongsTo employee وshift |
| `Shift` | `shifts` | name, shift_code, start/end_time, mark_late_after_minutes, total_hours, is_active | integer/decimal/boolean | hasMany employeeShifts |
| `Leave` | `leaves` | name, code, max_day_per_year, is_paid, is_active | bool/int؛ `daysAllowed` accessor | hasMany leaveRequests |
| `LeaveRequest` | `leave_requests` | employee_id, leave_id, dates, status, reason, actioned_by/at, reject_reason | dates/datetime؛ status constants | belongsTo employee, leave, actionedBy User |
| `Holiday` | `holidays` | name, holiday_date, type, holiday_year, description | date/int | لا relations |
| `ExpenseClaim` | `expenses_claims` | employee_id, claim_code, title/category/amount/date, receipt, status, action fields | decimal/date/datetime؛ status constants | belongsTo employee وactionedBy User |
| `SalaryStructure` | `salary_structures` | employee_id، basic/allowances/deductions/net | كل المبالغ decimal:2 | belongsTo employee |
| `Payroll` | `payrolls` | employee_id, payroll_code, month/year، salary totals، status، payment fields | int/decimal/date؛ status constants | belongsTo employee |
| `PerformanceGoal` | `performance_goals` | employee_id، title/description/dates/progress/priority/status | dates/int؛ `endDate` accessor = due_date | belongsTo employee |
| `PerformanceReview` | `performance_reviews` | employee_id, reviewer_id, review_cycle/date, rating, strengths, improvements, feedback, status | datetime/int؛ accessors reviewPeriod/feedback/areasForImprovement/reviewDate | belongsTo employee وreviewer User |
| `Announcement` | `announcements` | title/content, department_id, created_by, priority, pinned/published, dates | bool/dates | belongsTo department وcreator User |
| `Notification` | `notifications` | employee_id, type, title/message/channel/status/read_at | read_at datetime | belongsTo employee |
| `CompanySetting` | `company_settings` | key/value/group/description | لا شيء مخصص | لا relations |

### 6.3 العلاقة بين الجداول

```text
users 1 ─────── 0..1 employees
                  │
                  ├── belongsTo departments
                  ├── belongsTo designations
                  ├── hasMany attendances
                  ├── hasMany leave_requests ── belongsTo leaves
                  ├── hasMany expenses_claims
                  ├── hasMany payrolls
                  ├── hasOne salary_structures
                  ├── hasMany employee_shifts ── belongsTo shifts
                  ├── hasMany employee_documents
                  ├── hasMany performance_goals
                  ├── hasMany performance_reviews (employee)
                  └── hasMany notifications

users ──< created_by / actioned_by / reviewer_id ── announcements,
       expense_claims, leave_requests, performance_reviews

departments 1 ──< designations
           1 ──< employees
           1 ──< announcements (optional)

roles >──< permissions عبر Spatie:
roles ── role_has_permissions ── permissions
users ── model_has_roles (polymorphic model_id + model_type)
```

لا توجد علاقات `belongsToMany` معرفة يدويًا في Models المحلية؛ علاقات roles/permissions تأتي من `HasRoles` في package. كذلك `personal_access_tokens` هي polymorphic table خاصة بـSanctum.

---

## 7. Database

### 7.1 ترتيب تكوين قاعدة البيانات

عند تشغيل `php artisan migrate` تُنفذ migrations حسب timestamp:

1. `users`, `password_reset_tokens`, `sessions`.
2. `cache`, `cache_locks`.
3. `jobs`, `job_batches`, `failed_jobs`.
4. `departments` ثم `designations`.
5. `employees`.
6. `attendances`.
7. `leaves`.
8. `salary_structures` ثم `payrolls`.
9. `company_settings`.
10. `holidays`.
11. `expenses_claims`.
12. `performance_goals` ثم `performance_reviews`.
13. `announcements`.
14. `employee_documents`.
15. `shifts` ثم `employee_shifts`.
16. custom `notifications`.
17. Spatie permission tables.
18. Sanctum `personal_access_tokens`.
19. `leave_requests`.

سبب الترتيب هو أن الجداول التي تُشار إليها Foreign Keys يجب أن توجد قبل الجداول التابعة.

### 7.2 الجداول التطبيقية وقيودها

| Table | الأعمدة المهمة | PK/FK/Unique/Index | On delete |
|---|---|---|---|
| `departments` | name, code, description, is_active | id؛ name وcode unique | — |
| `designations` | department_id, name, description, is_active | id؛ FK department؛ unique(department_id,name) | department cascade |
| `employees` | user_id، code، identity، department/designation، salary | id؛ user FK؛ code/email unique | default FK behavior |
| `attendances` | employee_id، date/time، status، hours | id؛ employee FK | default FK behavior |
| `leaves` | name/code، max days، paid/active | id؛ name/code unique | — |
| `leave_requests` | employee/leave، dates، status، actioned_by | id؛ employee/leave FK، actioned_by nullable | employee/leave cascade؛ user set null |
| `salary_structures` | employee_id، allowances/deductions | id؛ employee unique FK = one-to-one | default FK behavior |
| `payrolls` | employee_id، code، month/year، totals/status | id؛ code unique؛ unique(employee,month,year) | default FK behavior |
| `company_settings` | key/value/group | id؛ key unique | — |
| `holidays` | date/year/type | id؛ unique(name,holiday_year) | — |
| `expenses_claims` | employee_id، code، amount/status، action fields | id؛ code unique؛ employee/user FKs | default FK behavior |
| `performance_goals` | employee_id، dates/progress/status | id؛ employee FK | default FK behavior |
| `performance_reviews` | employee_id/reviewer_id، cycle/rating/status | id؛ employee/reviewer FK | كلاهما cascade |
| `announcements` | optional department/creator، publish flags/dates | id؛ nullable FKs | default FK behavior |
| `employee_documents` | employee_id، file metadata/verification | id؛ employee FK | default FK behavior |
| `shifts` | schedule/time/late threshold | id | — |
| `employee_shifts` | employee_id/shift_id، dates/active | id؛ كلاهما FK | default FK behavior |
| `notifications` | employee_id، content/status/read_at | id؛ indexes(employee_id,read_at) وtype | default FK behavior |

### 7.3 Foreign-key behavior بالتفصيل

- `designations.department_id` يستخدم `cascadeOnDelete()`؛ حذف Department يحذف Designations التابعة.
- `leave_requests.employee_id` و`leave_requests.leave_id` يستخدمان `cascadeOnDelete()`.
- `leave_requests.actioned_by` يستخدم `nullOnDelete()`، لذلك حذف User المعالج يترك الطلب ويحذف المرجع فقط.
- `performance_reviews.employee_id` و`reviewer_id` يستخدمان `cascadeOnDelete()`.
- بقية `constrained(...)` في migrations لا تضيف `cascadeOnDelete` أو `nullOnDelete` صراحةً؛ nullable column وحده لا يعني Set Null عند حذف المرجع. سلوك قاعدة البيانات الافتراضي غالبًا restrict/no action، ويجب اختباره حسب driver قبل اعتبار الحذف آمنًا.
- `salary_structures.employee_id` unique، ولذلك العلاقة one-to-one enforced بقاعدة البيانات.
- `payrolls(employee_id, month, year)` unique، ولذلك لا يسمح batch بأكثر من record لنفس employee/period.
- `departments.name/code`, `employees.employee_code/email`, `leaves.name/code`, `payrolls.payroll_code`, `company_settings.key`, `holidays(name,year)` و`roles/permissions` لها unique constraints كما هو موضح أعلاه.
- indexes الصريحة موجودة في `sessions`, `cache`, `jobs`, `notifications` وSpatie pivots؛ لا يوجد index مخصص واضح لكل أعمدة البحث المتكررة مثل `attendance_date` أو `employee_id + attendance_date`.

### 7.4 جداول Laravel وPackages

- `sessions`: مفعل في `.env.example` عبر `SESSION_DRIVER=database`.
- `cache` و`cache_locks`: مفعلان افتراضيًا عبر `CACHE_STORE=database`.
- `jobs`, `job_batches`, `failed_jobs`: بنية queue موجودة رغم عدم وجود Jobs مخصصة.
- `roles`: id، name، guard_name، timestamps، وteam column اختياري فقط إذا teams/testing في config.
- `permissions`: id، name، guard_name، timestamps، unique(name,guard_name).
- `model_has_roles` و`model_has_permissions`: `model_id + model_type` polymorphic، مع role/permission FK وcomposite primary key.
- `role_has_permissions`: role_id وpermission_id، كلاهما FK مع cascade، وcomposite primary key.
- `personal_access_tokens`: يخزن Sanctum tokens، مع `morphs('tokenable')`.
- `password_reset_tokens`: بنية reset الافتراضية، ولا توجد واجهة reset في routes الحالية.

### 7.5 ملاحظات Schema مهمة

- `employees.user_id` ليس معلنًا `unique` في migration، مع أن Model يعامل علاقة User/Employee كـone-to-one.
- `attendances` يحتوي `attendance_date`، لكن بعض methods في `AttendanceRepository` تستعلم عن عمود اسمه `date`؛ هذا mismatch موثق في قسم الملاحظات.
- `employee_shifts.end_date` غير nullable في migration، بينما `checkIn()` يتعامل مع `orWhereNull('end_date')`.
- `expenses_claims.description` و`reject_reason` معرفان `string` بينما Models/DTOs تسمح أحيانًا بنص طويل.
- `employees.gender` enum قيمه lowercase لكن default في migration هو `'Male'` بحرف كبير.

### 7.6 Seeders

`DatabaseSeeder` يستدعي بالترتيب:

1. `RoleAndPermissionSeeder`: ينشئ permission matrix، أدوار `admin`, `hr_manager`, `employee`، ويربطها.
2. `EnterpriseSeeder`: ينشئ departments/designations/shifts/leaves/holidays، حسابات وemployees تجريبية، attendance، leaves، payroll، goals/reviews، announcements، notifications، settings.
3. يمسح permission cache.

`UserFactory` ينشئ User عشوائيًا password hash من قيمة اختبارية افتراضية، لكنه ليس مسار seed الأساسي للـEnterprise data.

---

## 8. Services

### 8.1 دور Service فعليًا

في معظم Features، Service رقيقة:

```php
readonly class LeaveService
{
    public function __construct(private LeaveRepository $leaveRepository) {}

    public function store(LeaveRequestStoreDto $dto)
    {
        return $this->leaveRepository->store($dto);
    }
}
```

معنى ذلك أن Controller لا يعرف تفاصيل Eloquent، وRepository لا يعرف شكل Redirect أو JSON. لكن Service ليست دائمًا المكان الذي يوجد فيه business logic؛ مثلًا حساب balance موجود في Repository، و`AuthService` و`AiService` يحتويان منطقًا حقيقيًا.

### 8.2 Service inventory

| Service | يعتمد على | الوظائف الأساسية |
|---|---|---|
| `AuthService` | `User`, `Auth`, `Hash` | login/logout، token وroles/permissions payload |
| `EmployeeService` | `EmployeeRepository` | pagination, show, store, update, destroy |
| `AttendanceService` | `AttendanceRepository` | logs/check-in/check-out/summaries |
| `LeaveService` | `LeaveRepository` | types, requests, store, process, balances |
| `ExpenseService` | `ExpenseRepository` | list/show/store/process |
| `PayrollService` | `PayrollRepository` | list/generate/show/salary structure/pay |
| `PerformanceService` | `PerformanceRepository` | goals/reviews/progress |
| `AnnouncementService` | `AnnouncementRepository` | notice CRUD/filter |
| `DocumentService` | `DocumentRepository` | document CRUD/verification |
| `HolidayService` | `HolidayRepository` | holiday CRUD |
| `ShiftService` | `ShiftRepository` | schedules/assignment |
| `NotificationService` | `NotificationRepository` | channels/list/read/test |
| `SettingService` | `SettingRepository` | departments/designations/company settings |
| `ReportService` | `ReportRepository` | attendance/leaves/payroll/headcount aggregates |
| `DashboardService` | `DashboardRepository` | all dashboard metrics |
| `AiService` | HTTP client + Log | Gemini attempt، fallback NLP، fake OCR/resume/review |
| `RolePermissionService` | `RolePermissionRepository` | roles/permissions operations |

### 8.3 Dependency Injection

كل Service تقريبًا تستخدم PHP constructor property promotion و`readonly`:

```php
public function __construct(private readonly PayrollRepository $payrollRepository) {}
```

Laravel Service Container يقرأ type hint ويحل class concrete تلقائيًا لأن Repositories classes عادية ولا تحتاج bindings خاصة. لا يوجد Interface/implementation mapping ولا `bind()` في `AppServiceProvider::register()`؛ `register()` فارغة.

الـControllers أيضًا تستخدم نفس الأسلوب:

```php
public function __construct(private readonly EmployeeService $employeeService) {}
```

عند resolve للـController، Container ينشئ Service، ثم Repository، ويمررها تلقائيًا. لا يوجد constructor في `PortalController` لأنه يستخدم Models مباشرة.

---

## 9. Repositories

### 9.1 ماذا يوجد داخل Repositories؟

- استعلامات Eloquent (`with`, `where`, `paginate`, `updateOrCreate`).
- eager loading لتقليل بعض N+1 مثل `with('employee')`.
- عمليات كتابة ذرية في `EmployeeRepository` و`AttendanceRepository` باستخدام transactions/row lock.
- validation/business rules بسيطة مثل leave balance وstatus transitions.
- تحويل not found إلى `RepositoryException` لكي تحولها API Controllers إلى JSON.

### 9.2 أهم السلاسل

#### EmployeeRepository

- `getEmployees`: filters search/department/designation، eager loads department/designation/user، pagination.
- `store`: transaction، ينشئ User ويعمل Hash ثم role ثم Employee.
- `update`: transaction، يحدث Employee ويحدث اسم User.
- `destroy`: transaction، يحذف Employee ثم User يدويًا.

#### AttendanceRepository

- `getLogs`: filters employee/date/department/status، لكن يستخدم `created_at` للتواريخ المدخلة.
- `checkIn`: يبحث عن active EmployeeShift، يحمّل Shift، يحدد status، ينشئ attendance.
- `checkOut`: transaction + `lockForUpdate()`، يبحث عن record اليوم بلا check_out ثم يحسب hours.
- `dailySummary` و`monthlySummary`: موجودان لكن signatures لا تتوافق مع DTO/service الحالي؛ انظر المشاكل.

#### LeaveRepository

`store()` يحسب الأيام inclusive، يجلب Leave، يستدعي private `getBalancesForType`، يمنع الإنشاء إذا الرصيد غير كاف، ثم ينشئ pending request. `process()` يسمح فقط بتغيير الطلب pending إلى approved/rejected، ويملأ action metadata. `getBalances()` يحسب كل نوع من approved requests في السنة الحالية.

#### PayrollRepository

`generate()` يجلب active employees، يمنع تكرار employee/month/year، ينشئ SalaryStructure default عند عدم وجوده، ثم يحسب:

```text
allowance = hra + medical_allowance + transport_allowance + special_allowance
deduction = tax_deduction + provident_fund + other_deductions
net = basic_salary + allowance - deduction
```

ثم ينشئ Payroll status `generated`. `markAsPaid()` يغير status وpayment_date فقط.

#### PerformanceRepository

`updateGoalProgress()` يجعل goal `completed` عند 100، و`in_progress` إذا progress > 0 وكان `not_started`. `storeReview()` يضع `reviewed_at` فقط عندما status=`submitted`.

#### ReportRepository وDashboardRepository

هما read-model/report aggregation داخل Repository وليس SQL views. `DashboardRepository::getMetrics()` يجمع عدد الموظفين، الحضور اليومي، الإجازات، المصروفات، payroll paid، توزيع الأقسام، أحدث الطلبات/الإعلانات والعطلات.

#### RolePermissionRepository

يستخدم Spatie Models، و`syncRoles` و`syncPermissions`. لا يفرض داخل Repository أن role operation متاح لمن يطلبه؛ enforcement يأتي من route middleware عند وجوده.

---

## 10. Middleware

### 10.1 التسجيل

في `bootstrap/app.php`:

```php
$middleware->alias([
    'role' => RoleMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'role_or_permission' => RoleOrPermissionMiddleware::class,
]);
```

هذه classes من `spatie/laravel-permission`، وليست custom classes محلية.

### 10.2 ترتيب Request عمليًا

للـAPI المحمي:

```text
api middleware group
  ↓
auth:sanctum
  ↓
permission:... (عند وجوده)
  ↓
FormRequest validation
  ↓
Controller
```

للـAdmin:

```text
web middleware group
  ↓
guest أو auth
  ↓
permission middleware (قليل جدًا في Admin routes؛ معظمها auth فقط)
  ↓
Controller
```

للـPortal: `web → auth → Portal Controller`.

### 10.3 redirectGuestsTo

إذا كان request على `admin/*`، يعيد middleware غير المسجل إلى `route('admin::login')`. هذا يغطي Admin login المخصص. لا يوجد redirect مخصص للـPortal في callback، لذلك behavior يعتمد على Laravel default إذا لم يكن authenticated.

### 10.4 ما لا يوجد

- لا توجد rate limiting routes.
- لا توجد custom audit middleware.
- لا توجد tenant/company middleware.
- لا توجد custom file/security middleware.
- لا توجد middleware permissions على معظم Admin write routes؛ وجود `@can` في sidebar يخفي روابط UI لكنه لا يحمي URL بذاته.

---

## 11. Authentication

### 11.1 Web session login

`POST /admin/login` يمر إلى `AuthService::login`:

1. `LoginRequest` يتحقق من email/password/remember_me.
2. `toDto()` يرجع `LoginDTO`.
3. Service يبحث عن User مع employee.
4. `Hash::check` يتحقق من password.
5. `Auth::attempt` ينشئ session في guard `web`.
6. Service ينشئ أيضًا Sanctum token باسم `auth-token` ويعيده، لكن Admin Controller يتجاهل array ويرجع redirect.
7. يذهب المستخدم إلى `admin::dashboard.admin`.

`POST /api/v1/auth/login` يستخدم نفس Service، لكن Controller يعيد الـarray داخل `ApiResponse::success`، فيحصل العميل على `token` وبيانات user وroles/permissions.

### 11.2 Guard/provider/config

`config/auth.php`:

- default guard: `web` من `AUTH_GUARD` أو `web`.
- `web` driver: `session`.
- provider `users`: Eloquent model `App\Models\User`.
- reset table `password_reset_tokens`، لكن routes الخاصة به غير موجودة.

`User` يرث `Authenticatable` ويستخدم `HasApiTokens`، لذلك يصلح للـsession وSanctum.

### 11.3 Logout

- Web: `Admin\Auth\AuthController::logout` يستعمل `Auth::guard('web')->logout()`، يبطل session، ويجدد CSRF token.
- API: `Api\V1\Auth\AuthController::logout` يستعمل `AuthService::logout`، ويحذف `currentAccessToken()` فقط.

### 11.4 ملاحظة أمنية

**FACT:** Login API ينشئ token، لكن Service يستدعي `Auth::attempt` أيضًا، لذلك login API قد يغير session إذا كانت موجودة.

**ENGINEERING OPINION:** الأفضل الفصل بين session login وtoken login أو جعل Service تحدد guard المقصود صراحةً، مع وضع token expiry/revocation policy واضحة.

---

## 12. Authorization

### 12.1 Roles وPermissions

`User` يستخدم `Spatie\Permission\Traits\HasRoles`. `RoleAndPermissionSeeder` ينشئ permissions مثل:

- `employees.view/create/edit/delete`
- `attendance.view/manage`
- `leaves.view/create/approve/manage`
- `payroll.view/generate/pay/manage`
- `reports.view`
- `settings.manage`
- `roles.view/manage`
- `ai.use`
- وغيرها بحسب كل module.

الأدوار:

- `admin`: كل permissions.
- `hr_manager`: أغلب operations HR دون roles administration.
- `employee`: self-service permissions محدودة.

### 12.2 Gate

في `AppServiceProvider::boot`:

```php
Gate::before(function ($user, string $ability) {
    return $user->hasRole('admin') ? true : null;
});
```

هذا يعني أن admin يتجاوز فحص abilities في Gate/`@can`، أما غيره فيعود إلى Spatie permission check.

### 12.3 أين يُفرض permission فعليًا؟

API يفرض permission على routes الحساسة الظاهرة صراحةً: approve leave، approve expense، holiday write، performance manage، documents verify، shifts، payroll، reports، settings، roles، employees.

أما Admin Web group الأساسية فهي `middleware('auth')` فقط تقريبًا، وحتى الصفحات التي يخفيها `sidebar.blade.php` عبر `@can` لا تحتوي route-level permission. هذا فرق مهم بين UI visibility وactual authorization.

### 12.4 FormRequest authorize

كل FormRequests تقريبًا ترجع `true`. لذلك `authorize()` لا تمنع user؛ الحماية المفترضة من route middleware، لكنها غير موجودة لكل routes.

### 12.5 لا توجد Policies

لا يوجد `app/Policies` ولا `authorize($ability, $model)`؛ لذلك access ownership مثل أن موظفًا يعدل goal موظف آخر لا يُفرض في كل API method. بعض Controllers تضع فلترة ذاتية يدويًا (`PerformanceController::reviews`)، لكنها ليست سياسة عامة.

---

## 13. Events

**FACT:** لا يوجد `app/Events`، ولا توجد calls إلى `event()` أو `Event::dispatch()` في كود التطبيق.

Spatie config لديه `events_enabled=false`، لذلك حتى events الخاصة بالـpackage غير مفعلة من الإعداد الحالي.

النتيجة: نشر announcement، قبول leave، صرف payroll، أو إرسال notification لا يطلق event محليًا؛ كلها عمليات synchronous مباشرة في request.

---

## 14. Listeners

لا يوجد `app/Listeners`. لذلك لا توجد سلاسل من نوع:

```text
Controller → Event → Listener
```

الـMailables الموجودة ليست listeners ولا تُسجل تلقائيًا في أي مكان.

---

## 15. Jobs

لا يوجد `app/Jobs` ولا class يطبق `ShouldQueue` ولا `dispatch()`.

هذا يعني أن كل operations الظاهرة للمستخدم تُنفذ داخل HTTP request:

- Payroll generation يمر على كل active employees في نفس الطلب.
- CSV export يحمّل collection كاملة قبل streaming.
- AI Gemini call (إذا كان key موجودًا) synchronous.
- File storage synchronous.

---

## 16. Queues

### الموجود في config

- `.env.example`: `QUEUE_CONNECTION=database`.
- `config/queue.php`: database driver إلى `jobs`، بالإضافة إلى sync/redis/sqs وغيرها.
- migration تنشئ `jobs`, `job_batches`, `failed_jobs`.

### غير الموجود في runtime

لا توجد Jobs ولا dispatch ولا Mailable queue send. وجود queue infrastructure لا يعني أن queue مستخدمة حاليًا.

**INFERENCE:** أُعدت البنية تحسبًا للتوسع أو لأن Laravel skeleton يولدها، لكن لا يوجد دليل أن worker شُغل أو أن queue name استُخدمت.

**ENGINEERING OPINION:** generation payroll والإشعارات والبريد وAI الخارجي مرشحة لـJobs، مع retries و`failed_jobs` وidempotency، إذا زاد حجم البيانات.

---

## 17. Notifications

ينبغي التفريق بين مفهومين:

1. `App\Models\Notification`: جدول مخصص `notifications` مرتبط بـEmployee، وله `type/title/message/channel/status/read_at`.
2. Laravel Notification system: غير مستخدم؛ لا توجد classes في `app/Notifications` ولا `Notifiable::notify()`.

`NotificationRepository::sendTest()` ينفذ `Notification::create` بحالة `pending`. `markAsRead` و`markAllRead` يحدثان DB، و`AppServiceProvider` يحقن آخر 8 notifications في `admin.partials.navbar` عبر View Composer.

لا يوجد إرسال فعلي للبريد/SMS/Slack رغم أن `getChannels()` يعرضها كخيارات. القنوات `email`, `sms`, `slack` بيانات وصفية فقط في الوضع الحالي.

---

## 18. Mail

الموجود:

| Mailable | View | البيانات |
|---|---|---|
| `AnnouncementBroadcastMail` | `emails.announcement` | Announcement |
| `ExpenseClaimProcessedMail` | `emails.expense-status` | ExpenseClaim |
| `LeaveStatusUpdatedMail` | `emails.leave-status` | LeaveRequest |
| `PayrollDisbursedMail` | `emails.payroll-disbursed` | Payroll |

كلها تستخدم `Queueable` و`SerializesModels`، لكن هذا يجعلها قابلة للـqueue فقط؛ لا يعني أنها أُرسلت.

**FACT:** بحث الاستدعاءات لا يجد `Mail::to`, `send`, `queue`, أو `Mailable` usage من Controllers/Repositories. لذلك لا يوجد حاليًا flow فعلي من status change إلى email.

`MAIL_MAILER=log` في `.env.example` يعني أنه عند استدعاء mail في local سيكتب log بدل SMTP، لكن لا يوجد caller في code path الحالي.

---

## 19. Views / Frontend

### 19.1 التقنية الفعلية

- Blade server-rendered.
- `layouts/admin-layout.blade.php` layout مشترك.
- Bootstrap/template assets في `public/assets` عبر relative links من partials.
- `resources/css/app.css` يحتوي Tailwind v4 import وVite sources.
- `resources/js/app.js` فارغ تقريبًا (`//`).
- لا يوجد Vue/React/Alpine/Livewire/Inertia.
- أغلب forms تستخدم POST/PUT/PATCH/DELETE مع `@csrf` و`@method`، ثم Redirect وflash alerts.
- `resources/views/admin/ai/index.blade.php` يستخدم native `fetch()` لخمس endpoints؛ يرسل CSRF token غالبًا من الصفحة ويتعامل مع JSON.

### 19.2 Layout flow

```text
أي admin/portal Blade
  ↓ @extends('layouts.admin-layout')
meta + template CSS/JS
sidebar
navbar ← View Composer يزوده بالـnotifications/current user
alerts
@yield('container')
footer
page scripts
```

### 19.3 الصفحات وبياناتها

| الصفحة | Route | Controller | البيانات الرئيسية | Forms/actions |
|---|---|---|---|---|
| `admin/dashboard/index` | `admin::dashboard.admin` | DashboardController | metrics array | links إلى modules |
| `admin/employees/index` | employees.index | EmployeeController | paginator, departments, designations | filters، links CRUD |
| `admin/employees/create/edit/show` | resource routes | EmployeeController | form options أو Employee eager-loaded | create/update/docs/history |
| `admin/attendance/index` | attendance.index | AttendanceController | paginator/counts/depts/employees | filter + check in/out |
| `admin/leaves/requests` | leaves.requests | LeaveController | requests/types/employees/counts | submit/process |
| `admin/expenses/index` | expenses.index | ExpenseController | claims/employees/stats | submit/process |
| `admin/payroll/index` | payroll.index | PayrollController | period/paginator/totals | generate/pay |
| `admin/payroll/structures` | payroll.structures | PayrollController | structures/employees | salary update |
| `admin/performance/goals` | performance.goals | PerformanceController | goals/employees/counts | create/progress/delete |
| `admin/performance/reviews` | performance.reviews | PerformanceController | reviews/employees/avg | create review |
| `admin/announcements/index` | announcements.index | AnnouncementController | announcements/depts/stats | create/delete |
| `admin/documents/index` | documents.index | DocumentController | docs/employees | upload/verify/delete |
| `admin/holidays/index` | holidays.index | HolidayController | year paginator | create/update/delete |
| `admin/shifts/index` | shifts.index | ShiftController | shifts/assignments/employees | create/assign |
| `admin/notifications/index` | notifications.index | NotificationController | notifications/channels/employees | send/mark all |
| `admin/reports/*` | reports routes | ReportController | arrays from ReportService | date filter/exports |
| `admin/roles/*` | roles routes | RolePermissionController | roles/permissions/users | role + assign |
| `admin/settings/*` | settings routes | SettingController | departments/designations/settings | CRUD forms |
| `portal/dashboard` | portal dashboard | PortalController | employee dashboard aggregates | attendance links |
| `portal/attendance/index` | portal attendance | PortalAttendanceController | logs/today/shift | check in/out + month filter |
| `portal/leaves/index` | portal leaves | PortalLeaveController | own requests/types/balances | apply leave |
| `portal/expenses/index` | portal expenses | PortalExpenseController | own claims/totals | multipart expense form |
| `portal/payroll/index` | portal payroll | PortalPayrollController | own payroll/structure | payslip links |
| `portal/performance/index` | portal performance | PortalPerformanceController | own goals/reviews/counts | progress update |

### 19.4 Forms وdata flow

Form Admin employee مثلًا يرسل إلى `admin::employees.store`. Browser يرسل normal HTTP POST، Laravel CSRF يتحقق، FormRequest يتحقق، ثم redirect. لا توجد REST JSON calls لهذه النماذج.

Portal expense يرسل `multipart/form-data` لأن `receipt` file. Controller يخزن الملف عبر `store('receipts', 'public')`، ويضع path في `receipt_file`.

AI page استثناء واضح: JavaScript يستعمل `fetch(route(...))` ويعيد JSON، ويغطي chat، craft announcement، generate review، scan receipt، parse resume.

### 19.5 Welcome view

`resources/views/welcome.blade.php` ما زالت صفحة Laravel الافتراضية. تحتوي links إلى `route('login')` و`route('register')`، بينما routes المسجلة فعليًا هي `admin::login` ولا يوجد `register`. هذا mismatch قد يجعل root view تفشل عند rendering في runtime.

---

## 20. Configuration

### 20.1 Environment flow

```text
.env / .env.example
       ↓
config/*.php عبر env()
       ↓
config('...') أو Laravel internals
       ↓
Application runtime
```

لا أعرض أي قيمة secret أو password هنا. `.env.example` يحدد defaults فقط.

### 20.2 الإعدادات المهمة

- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`: هوية/بيئة/تشفير/روابط.
- `DB_CONNECTION=sqlite` افتراضيًا، مع templates لـMySQL/PostgreSQL/SQL Server.
- `SESSION_DRIVER=database`: sessions في DB.
- `CACHE_STORE=database`: cache في DB.
- `QUEUE_CONNECTION=database`: queue في DB، رغم عدم وجود Jobs.
- `MAIL_MAILER=log`: emails إلى log إذا استُدعيت.
- `FILESYSTEM_DISK=local` افتراضيًا، لكن uploads المحددة تستعمل disk `public` صراحةً.
- `BROADCAST_CONNECTION=log`: لا يوجد broadcasting flow في code.
- `APP_TIMEZONE` لا يُقرأ؛ `config/app.php` يثبت timezone على `UTC`.
- `config/services.php` يحتفظ بـPostmark/Resend/SES/Slack credentials عبر `env()`، ولا يستخدمها أي flow محلي ظاهر.

### 20.3 AppServiceProvider

`register()` فارغة. `boot()` تفعل شيئين:

1. Admin role bypass عبر `Gate::before`.
2. View composer لـ`admin.partials.navbar`: يقرأ user/employee، ثم آخر 8 custom Notifications وunread count ويمرر current user/employee للnavbar.

هذا View Composer يُنفذ عند كل render للnavbar، لذلك هو جزء من request lifecycle وليس queue/background work.

---

## 21. Packages

### Composer direct dependencies

| Package | استخدامه المثبت في هذا المشروع |
|---|---|
| `laravel/framework ^13.17` | routing, MVC, Eloquent, validation, views, config |
| `laravel/sanctum ^4.0` | `HasApiTokens`, personal_access_tokens, `auth:sanctum` |
| `spatie/laravel-permission ^8.3` | `HasRoles`, Role/Permission Models، middleware وpermissions tables |
| `laravel/tinker` | console/debug dependency؛ لا يوجد استعمال محلي داخل code paths |

### Composer dev dependencies

- `pestphp/pest` و`pest-plugin-laravel`: Feature/Unit tests.
- `fakerphp/faker`: UserFactory.
- `laravel/pint`: formatting.
- `laravel/boost`: agent/dev workflow.
- `laravel/pail`, `laravel/pao`, `collision`: tooling/debugging.
- `mockery`: testing mocks.

### NPM

- `vite` + `laravel-vite-plugin`: asset build.
- `tailwindcss` + `@tailwindcss/vite`: CSS entry pipeline.
- `concurrently`: dev tooling dependency، ولا يظهر استدعاؤه في code runtime.
- `@laravel/multiplex`: optional dependency.

**ملاحظة:** Admin pages لا تعتمد فعليًا على `resources/js/app.js` لإدارة UI؛ فهي تحمل assets template من `public/assets` عبر Blade partials. Vite entry مخصص للـwelcome/standard Laravel pipeline.

---

## 22. Feature Data Flows

### 22.1 Login

```text
POST /admin/login أو POST /api/v1/auth/login
  ↓
LoginRequest
  ├── email required email
  ├── password required min 6
  └── remember_me boolean
  ↓
LoginDTO::fromArray
  ↓
AuthService::login
  ├── User::where(email)->with(employee)
  ├── Hash::check
  ├── Auth::attempt
  └── User::createToken('auth-token')
  ↓
Admin: redirect dashboard
API: ApiResponse::success(data.token, data.user)
```

### 22.2 Employee onboarding

```text
GET /admin/employees/create
  ↓
Department + Designation + Role Models
  ↓
admin.employees.create

POST /admin/employees أو POST /api/v1/employees
  ↓
EmployeeStoreRequest
  ↓
EmployeeStoreDto
  ↓
EmployeeService::store
  ↓
EmployeeRepository::store
  ↓ DB::transaction
  ├── User::create(password hash)
  ├── assignRole(role)
  └── Employee::create(user_id + profile)
  ↓
Web redirect أو API 201
```

### 22.3 Leave request

```text
Portal form / Admin form / API POST /api/v1/leaves/requests
  ↓
LeaveRequestStoreRequest أو inline Portal validation
  ↓
LeaveRequestStoreDto(employeeId, leaveId, startDate, endDate, reason)
  ↓
LeaveService::store
  ↓
LeaveRepository::store
  ├── diffInDays + 1
  ├── Leave::find
  ├── approved requests الحالية
  ├── remaining balance check
  └── LeaveRequest::create(status=pending)
  ↓
Web redirect أو API 201
```

عند المعالجة:

```text
PATCH .../requests/{id}/status
  ↓
LeaveRequestProcessRequest
  ↓
LeaveRequestProcessDto(status, rejectReason, actionedBy)
  ↓
LeaveRepository::process
  ├── find request
  ├── يرفض إن لم تكن pending
  └── update status/actioned metadata
  ↓
success response
```

لا Event ولا email ولا notification تلقائي بعد approval.

### 22.4 Attendance check-in/check-out

```text
POST /portal/attendance/check-in أو /api/v1/attendance/check-in
  ↓
Portal يبني DTO من Auth user
API Request::toDto() يأخذ employee من auth user
  ↓
AttendanceService::checkIn
  ↓
AttendanceRepository::checkIn
  ├── active EmployeeShift + Shift
  ├── compare now مع start/end
  ├── status present/late/half_day
  └── Attendance::save
  ↓
redirect أو JSON
```

للـcheckout:

```text
Request → AttendanceCheckoutDto → DB::transaction
  ├── find today's attendance where check_out is null
  ├── lockForUpdate
  ├── set check_out
  ├── calculate working_hours
  └── save
```

Admin check-in يستخدم نفس Service، لكن Request `toDto()` يتجاهل employee_id القادم ويأخذ authenticated employee؛ وهذا يتعارض مع test/واجهة Admin التي تتوقع اختيار موظف. هذه نقطة سلوكية مهمة.

### 22.5 Expense claim

```text
POST /portal/expenses أو /api/v1/expenses
  ↓
validation
  ↓
receipt upload إلى storage/app/public/receipts أو documents/temp...
  ↓
ExpenseClaimStoreDto
  ↓
ExpenseService → ExpenseRepository
  ├── EXP-{uniqid}
  └── ExpenseClaim::create(status=pending)
  ↓
redirect / 201
```

المعالجة تقبل `accepted`, `rejected`, `reimbursed` وتكتب `actioned_by`, `actioned_at`، وتضيف `reject_reason` أو `reimbursed_date` بحسب الحالة.

### 22.6 Payroll generation

```text
POST /admin/payroll/generate أو POST /api/v1/payroll/generate
  ↓
PayrollGenerateRequest(month, year)
  ↓
PayrollGenerateDto
  ↓
PayrollService → PayrollRepository::generate
  ├── Employee::active()->get()
  ├── skip existing employee/month/year
  ├── SalaryStructure::first أو create default
  ├── allowance/deduction/net calculations
  └── Payroll::create(status=generated)
  ↓
Admin redirect with count / API JSON array
```

`markAsPaid` يغير status إلى `paid` ويضع `payment_date`; لا Mailable ولا Notification رغم وجود classes لذلك.

### 22.7 Performance goals/reviews

إنشاء goal يمر من Request إلى DTO إلى Repository؛ تعديل progress يغير status تلقائيًا عند `100` أو عند progress > 0. Portal يضمن أن goal يتبع employee الحالي قبل الاستدعاء.

إنشاء review يضع `reviewerId` من authenticated user داخل `PerformanceReviewStoreRequest::toDto()`، ثم Repository يضع `reviewed_at` فقط في `submitted`.

### 22.8 Documents and file handling

`EmployeeDocumentStoreRequest::toDto()`:

1. يقبل field `document` أو `file`.
2. يخزن الملف في disk `public` تحت `documents`.
3. يحتفظ بالـoriginal name والpath.
4. يحاول تحديد employee من request/user، مع fallbacks إلى أول Employee.
5. ينشئ DTO.

`DocumentRepository` يخزن metadata فقط؛ لا يحذف file من filesystem عند `destroy`، بل يحذف row فقط.

### 22.9 AI flow

- `chat`: يحاول Gemini عبر `Http::post` إذا `GEMINI_API_KEY` موجود، وإلا fallback keyword engine.
- `scanReceipt` و`parseResume`: يخزنان file لكن service يرجع بيانات ثابتة demo، ولا يقرأ الملف فعليًا.
- `generateReview`: ينتج markdown ثابتًا حسب rating.
- `craftAnnouncement`: يبني content string.

هذا **FACT** من `AiService`: النتيجة الحالية mock/contextual وليست OCR/Resume parser حقيقيًا.

### 22.10 Dashboard/report flow

Controller → Service → Repository، ثم repository يرجع arrays وcollections. `DashboardRepository` يستخدم عدة queries مستقلة، ثم Blade تعرض keys مثل `total_employees`, `attendance_rate`, `department_distribution`, `recent_leave_requests`.

---

## 23. Dependency Maps

### 23.1 Map عام

```text
bootstrap/app.php
├── routes/admin.php
│   ├── Admin Controllers ──┐
│   └── Portal Controllers ─┤
└── routes/api.php           │
    └── Api/V1 Controllers ─┘
            ↓ constructor injection
       Services
            ↓
       Repositories
            ↓
       Models / Spatie Models
            ↓
       Database
```

### 23.2 Feature maps

```text
EmployeeController (Admin/API)
├── EmployeeStoreRequest / EmployeeUpdateRequest / FilterRequest
├── EmployeeStoreDto / EmployeeUpdateDto / FilterDto
└── EmployeeService
    └── EmployeeRepository
        ├── User + Hash + HasRoles
        └── Employee
            ├── Department
            ├── Designation
            └── related HR tables
```

```text
LeaveController (Admin/API) أو PortalLeaveController
├── Leave*Request أو inline validation
├── LeaveRequestStoreDto / ProcessDto / FilterDto
└── LeaveService
    └── LeaveRepository
        ├── Leave
        └── LeaveRequest
```

```text
PayrollController (Admin/API)
├── PayrollGenerateRequest / SalaryStructureUpdateRequest
├── PayrollGenerateDto / SalaryStructureUpdateDto
└── PayrollService
    └── PayrollRepository
        ├── Employee::active()
        ├── SalaryStructure
        └── Payroll
```

```text
AiController (Admin/API)
└── AiService
    ├── Http facade → Gemini (optional)
    ├── Log facade
    └── fallback/demo generators
```

```text
Admin navbar
└── AppServiceProvider::boot View::composer
    ├── Auth::user()
    ├── User::employee
    └── Notification queries
```

```text
RolePermissionController
└── RolePermissionService
    └── RolePermissionRepository
        ├── Spatie Role
        ├── Spatie Permission
        └── model_has_roles / role_has_permissions
```

---

## 24. Database Map

| Table | Purpose | Primary key | Important foreign keys | Relations |
|---|---|---|---|---|
| users | حساب الدخول | id | — | one User has one Employee؛ reviewer/creator/actioner refs |
| departments | الهيكل التنظيمي | id | — | has Designations/Employees/Announcements |
| designations | المسميات | id | department_id | belongs Department، has Employees |
| employees | سجل الموظف | id | user_id, department_id, designation_id | محور معظم HR tables |
| employee_shifts | assignment history | id | employee_id, shift_id | joins Employees وShifts |
| attendances | daily logs | id | employee_id | many per employee |
| leaves | types | id | — | has LeaveRequests |
| leave_requests | applications | id | employee_id, leave_id, actioned_by | belongs Employee/Leave/User |
| salary_structures | one current structure | id | unique employee_id | one-to-one Employee |
| payrolls | monthly outputs | id | employee_id | many per employee by month |
| expenses_claims | reimbursements | id | employee_id, actioned_by | belongs Employee/User |
| performance_goals | goals | id | employee_id | belongs Employee |
| performance_reviews | appraisals | id | employee_id, reviewer_id | belongs Employee/User |
| announcements | notices | id | department_id, created_by | optional Department/User |
| employee_documents | files/meta | id | employee_id | belongs Employee |
| shifts | schedules | id | — | has EmployeeShifts |
| notifications | custom in-app messages | id | employee_id | belongs Employee |
| company_settings | key/value settings | id | — | no relations |
| roles/permissions/pivots | RBAC | id / composite pivots | model morph + role/permission | Spatie relations |
| personal_access_tokens | Sanctum tokens | id | tokenable morph | User token auth |

```text
Department ──< Employee >── User
     │             │
     ├──< Designation
     ├──< Announcement
     ├──< Attendance
     ├──< LeaveRequest >── Leave
     ├──< ExpenseClaim
     ├──< Payroll >── SalaryStructure (one current structure)
     ├──< PerformanceGoal
     ├──< PerformanceReview >── User(reviewer)
     ├──< EmployeeDocument
     ├──< Notification
     └──< EmployeeShift >── Shift
```

لا توجد pivot table business custom لـmany-to-many؛ الـmany-to-many الحقيقي الظاهر هو roles↔permissions وusers↔roles من Spatie، وهو polymorphic على user model.

---

## 25. Route Map

### ملخص حسب السطح

| Surface | Prefix | Authentication | Response |
|---|---|---|---|
| Welcome | `/` | none | Blade |
| Admin Web | `/admin` | `auth` أو `guest` | Blade/redirect/CSV/JSON AI |
| Employee Portal | `/portal` | `auth` | Blade/redirect |
| REST API | `/api/v1` | login public، الباقي `auth:sanctum` | JSON |

### API route groups

| Group | Operations |
|---|---|
| `auth` | login/logout/profile |
| `attendance` | list/check-in/check-out/daily/month summary |
| `leaves` | types/requests/store/balance/process |
| `holidays` | list/show/create/update/delete |
| `expenses` | list/store/show/process |
| `performance` | goals/progress/reviews/show/store/delete |
| `announcements` | list/show/create/update/delete |
| `documents` | employee list/store/show/delete/verify |
| `shifts` | employee list/list/show/create/update/delete/assign |
| `notifications` | channels/list/mark all/read/send test |
| `ai` | chat/receipt/review/resume/announcement |
| `dashboard` | metrics |
| `employees` | CRUD |
| `payroll` | list/generate/payslip/structure/pay |
| `reports` | attendance/leaves/payroll/headcount |
| `settings` | departments/designations/company |
| `roles` | permissions/assignment/CRUD |

### Web route groups

| Group | Operations |
|---|---|
| `portal` | dashboard, attendance, leaves, expenses, payroll/payslip, performance/progress |
| `admin employees` | resource CRUD |
| `admin attendance` | index/check-in/check-out |
| `admin leaves` | requests/types/store/process |
| `admin payroll` | index/generate/pay/payslip/structures |
| `admin performance` | goals/reviews CRUD-ish |
| `admin announcements` | index/store/delete + broken generated update route |
| `admin documents` | list/store/verify/delete |
| `admin shifts` | list/store/update/delete/assign |
| `admin reports/export` | report pages + 7 downloads/prints |
| `admin settings/roles` | organization/RBAC management |

للحصول على الـauthoritative list وقت التشغيل، الأمر المقصود هو `php artisan route:list`؛ هذا التحليل static لأن checkout لا يحتوي `vendor/` وPHP runtime غير متاح في بيئة التحليل الحالية.

---

## 26. Architecture Observations

هذا القسم لا يعدل الكود؛ يسجل ما يحتاج انتباهًا مع تحديد مستوى الثقة.

### 26.1 أخطاء محتملة مؤكدة من القراءة

#### [BUG] Attendance summary signatures لا تتطابق

- `AttendanceService::dailySummary(AttendanceSummaryRequest $request)` يستدعي `AttendanceRepository::dailySummary($request->toDto())`.
- Repository يعلن `dailySummary(string $date)`، لكنه يستقبل `AttendanceSummaryDto`.
- `monthlySummary` في Repository يطلب `(int $employeeId, int $month, int $year)`، بينما Service يمرر DTO واحدًا.
- `AttendanceSummaryRequest::rules()` فارغة، مع أن Postman يرسل `date` أو `month/year`.

**الأثر:** هذه API paths مرشحة لـTypeError أو نتيجة غير صحيحة بدل summary.

#### [BUG] أسماء أعمدة attendance غير متناسقة

Migration وModel يستخدمان `attendance_date`. لكن `dailySummary` تستعمل `whereDate('date', ...)`، و`monthlySummary` تستعمل `whereMonth('date')` و`whereYear('date')`.

#### [BUG] `PerformanceGoalProgressDto` لا يملك `status`

`app/Domain/Performance/PerformanceGoalProgressDto.php` constructor يقبل `progress` فقط. لكن `PortalPerformanceController::updateProgress` ينشئه هكذا:

```php
new PerformanceGoalProgressDto(
    progress: (int) $request->progress,
    status: $request->status
);
```

هذا named argument غير معروف، لذلك Portal progress update يفشل عند التنفيذ قبل الوصول إلى Repository.

#### [BUG] `EmployeeDocument::verifiedBy()` غير موجود

`Admin\Document\DocumentController@index` يستعمل `EmployeeDocument::with(['employee.department', 'verifiedBy'])`، لكن Model لا يحتوي `verifiedBy` relation، وmigration لا يحتوي `verified_by` column. هذه الصفحة مرشحة لـRelationNotFoundException.

#### [BUG] Admin announcements resource update بلا method

`Route::resource('announcements')->except(['create','show','edit'])` ينشئ update route، لكن Controller يحتوي `index`, `store`, `destroy` فقط.

#### [BUG] روابط welcome إلى routes غير مسجلة

`welcome.blade.php` يستعمل `route('login')` و`route('register')`. المسجل هو `admin::login`، ولا توجد `register` route في `routes/admin.php`.

#### [BUG] fallback غير آمن لتحديد employee

Portal Controllers و`EmployeeDocumentStoreRequest` تستخدم أحيانًا `Employee::first()` أو أول id إذا لم تجد employee للمستخدم. قد يكتب user بيانات على سجل موظف آخر بدل أن يحصل على 404.

#### [BUG] Attendance check-in status calculation

الكود يحسب `differenceInMinutes` و`differenceInHours` بين `shiftStartTime` و`shiftEndTime`، وليس بين الوقت الحالي ووقت بداية الوردية. لذلك شرط late/half-day لا يعكس الوصف المتوقع.

#### [BUG] end_date nullable mismatch

Migration `employee_shifts.end_date` required، لكن `AttendanceRepository::checkIn` يتعامل مع null end date.

#### [BUG] gender default mismatch

Migration يحدد enum `male,female,others` ثم default `Male`. على قواعد بيانات strict قد يفشل insert أو يعطي قيمة غير صالحة.

### 26.2 Authorization/security concerns

1. معظم Admin routes محمية بـ`auth` فقط؛ `@can` في sidebar ليس authorization حقيقيًا. أي user authenticated قد يجرب URL مباشرة لعمليات admin مثل payroll/settings/roles إذا لم يوجد permission route middleware.
2. أغلب FormRequests `authorize(): true`.
3. بعض API write routes الحساسة لا تحتوي permission middleware، مثل بعض AI والـdocument upload وperformance progress، والـcontrollers لا تفرض ownership بشكل شامل.
4. `EmployeeDocumentStoreRequest` لديه fallback إلى أول Employee.
5. AI file paths تُخزن على disk public تحت temporary directories، ولا يوجد delete/retention policy ظاهر.
6. `AiService` يستعمل `env('GEMINI_API_KEY')` داخل Service مع `config()` fallback؛ الأفضل جعل config source موحدًا وعدم استعمال `env()` خارج config بعد config cache.
7. `ApiResponse::notFound()` خطأ في تمرير arguments: يستعمل HTTP 404 ثابتًا ويمرر `$code` كـerrors، لا كـstatus code المقصود.
8. لا يظهر rate limit مخصص لـlogin أو AI أو file endpoints.

### 26.3 Data integrity/business issues

1. `LeaveRepository` يحسب الأيام approved requests كـعدد requests في Portal لبعض الشاشات، لا مجموع الأيام inclusive؛ Portal balance قد يختلف عن Repository balance.
2. لا يوجد منع واضح لتداخل leave requests.
3. لا يوجد unique constraint يمنع أكثر من attendance لنفس employee/date.
4. `ShiftRepository::assign` يعطل assignments القديمة ثم ينشئ الجديد بلا transaction.
5. payroll generation يعتمد على `uniqid()`، وهو ليس generator قويًا لتحديد uniqueness في أنظمة متوازية.
6. Payroll index يحسب `totalPending` من status `draft` بينما generator ينشئ `generated`.
7. `DocumentRepository::destroy` يحذف DB row ولا يحذف الملف الفعلي.
8. FK delete behavior غير موحد؛ بعض العلاقات cascade وبعضها default restrict/no action.
9. لا توجد `verified_by` في document schema رغم استعمال مفهومها في Controller.

### 26.4 Performance concerns / N+1

- Dashboard ينفذ عدة queries منفصلة في كل request، وقد يصبح ثقيلًا مع البيانات.
- Portal dashboard يجلب collections ويحسب في PHP؛ مناسب للـdemo لكنه قد لا يناسب بيانات كبيرة.
- `Designation::with(['department','employees'])` يحمل كل employees للصفحة، مع pagination للdesignations.
- بعض exports تعمل `get()` كاملًا في الذاكرة قبل streaming؛ streaming response هنا لا يجعل DB iteration streaming.
- `LeaveRepository::getBalances()` يجلب leave types، ثم لكل type query approved requests؛ هذا N+1 على عدد leave types.
- توجد eager loads جيدة في Repositories (`with`) لكنها ليست موحدة في Controllers.

### 26.5 Code duplication / coupling

- Admin وAPI Controllers متكررة في كل module، مع اختلاف response فقط.
- Portal يكرر lookup: `Employee::where('user_id', $user->id)->first() ?? Employee::first()` في عدة files.
- Services كثيرة لا تضيف behavior؛ هي facades رقيقة فوق repositories. هذا ليس خطأ بذاته، لكنه abstraction زائد إذا لم تُستخدم لإضافة use cases.
- Admin Controllers تمزج Service access وdirect Eloquent، ما يجعل boundary غير واضح.
- `App\Models\Notification` مخصص بينما الاسم نفسه معروف في Laravel، وقد يربك القارئ.

### 26.6 Dead/unused code أو code paths غير مؤكدة

- أربع Mail classes وemail views موجودة لكن لا توجد references sender.
- jobs/events/listeners directories غير موجودة؛ queue tables infrastructure فقط.
- `public/assets/json/ajax.php` لا توجد references محلية ظاهرة.
- `routes/console.php` فارغ.
- بعض `$request` parameters في export/PortalPayroll لا تستخدم إلا لقراءة query أو لا تستخدم على الإطلاق.
- `AppServiceProvider::register()` فارغة.
- `AnnouncementService` لديه `show` و`update` وRepository يدعمهما، لكن Admin controller لا يعرض show/update، بينما API يستعملهما.

### 26.7 Deprecated practices

لا يمكن وصف ممارسة بعينها بأنها deprecated من الكود وحده دون تشغيل package/version tooling. **FACT:** المشروع Laravel 13 ويستخدم APIs حديثة مثل `Application::configure` وPHP attributes في User. أما mismatches السابقة فهي bugs/design smells لا يلزم أن تكون deprecated API.

---

## 27. How to Read the Project

إذا أردت فهم المشروع بنفسك، اتبع هذه الخطة بدل قراءة كل Blade وSeeder دفعة واحدة:

### المرحلة 1: نقطة الدخول

1. اقرأ `composer.json` لتعرف Laravel/Sanctum/Spatie.
2. اقرأ `bootstrap/app.php` لتعرف route files وmiddleware aliases وJSON exception behavior.
3. اقرأ `routes/admin.php` و`routes/api.php` لبناء feature map.
4. شغّل `php artisan route:list` في بيئة مثبتة dependencies، وابحث بـ`--path=api` أو `--name=employees`.

### المرحلة 2: نموذج البيانات

5. اقرأ migrations بترتيب timestamp.
6. ارسم `employees` كجدول مركزي.
7. اقرأ Models وrelations بعد فهم schema.
8. اقرأ `DatabaseSeeder` ثم `RoleAndPermissionSeeder` و`EnterpriseSeeder` لمعرفة البيانات الواقعية والأدوار.

### المرحلة 3: أول Feature عملية

ابدأ بـ**Leave request** لأنها توضّح Request → DTO → Service → Repository → Model:

1. route `api::leaves.store` أو `portal::leaves.store`.
2. `LeaveRequestStoreRequest`.
3. `LeaveRequestStoreDto`.
4. `LeaveService::store`.
5. `LeaveRepository::store`.
6. migration/model `leave_requests`.
7. response والـBlade form.

بعدها اقرأ **Employee onboarding** لتفهم transaction وUser/Employee وrole.

### المرحلة 4: تتبع Route

لكل route اسأل:

1. هل هو web أم api؟ ما prefix/middleware؟
2. ما Controller/method؟
3. هل parameter اسمه `{id}` أم `{employee}`؟ هل يوجد implicit binding فعلًا أم method يأخذ int؟
4. ما FormRequest؟
5. ما DTO الناتج؟
6. هل Controller يستعمل Service أم Model مباشرة؟
7. ما Repository/Model query؟
8. ما response: View، Redirect، JSON، stream؟

### المرحلة 5: تتبع Request والـValidation

- ابدأ من type-hint في Controller.
- افتح `rules()`، ثم `toDto()`.
- افحص هل `authorize()` true أم هناك authorization.
- افحص hidden defaults مثل employee من authenticated user و`isPublished` و`remember`.

### المرحلة 6: تتبع Database query

- ابحث عن method Service.
- انتقل إلى Repository إن وجد.
- اقرأ `with`, `where`, `when`, `paginate`, `updateOrCreate`.
- افتح Model relation لتعرف JOIN/Eloquent relation.
- ارجع إلى migration لتتأكد أن العمود موجود فعلًا؛ هذه الخطوة كانت ستكشف mismatch `date`/`attendance_date`.

### المرحلة 7: تتبع Event/Job/Mail

في هذا المشروع لا يوجد Event/Job فعلي، لكن الطريقة العامة:

1. ابحث عن `event(` و`dispatch(` و`Mail::` و`notify(`.
2. إن لم تجدها، لا تفترض أن workflow asynchronous موجود.
3. افحص `app/Events`, `Listeners`, `Jobs`, `Mail`, وqueue config منفصلة؛ وجود config ليس دليل استعمال.

### المرحلة 8: تتبع View

1. افتح route.
2. افتح Controller الذي يرجع `view(...)`.
3. سجّل أسماء variables في `compact()`.
4. افتح Blade وابحث عن `route(...)`, `action`, `@include`, `@extends`.
5. تتبع form method و`@method` إلى route.
6. ابحث عن `fetch`, `axios`, `$.ajax` لمعرفة إن كان هناك async call.

### المرحلة 9: فهم Feature جديدة

استخدم checklist:

```text
Route
→ Controller
→ Request/rules
→ toDto
→ DTO constructor
→ Service
→ Repository/query
→ Model relation/cast
→ Migration/FK/index
→ View/API response
→ tests
→ security middleware
```

ثم قارن المسارات المختلفة: هل Admin وAPI وPortal تستعمل نفس Service؟ في هذا المشروع أحيانًا نعم في الكتابة، وأحيانًا لا في القراءة.

### المرحلة 10: التحقق قبل الوثوق

- لا تعتمد على اسم method فقط؛ اقرأ body.
- لا تعتبر route محمية لأن sidebar أخفاها؛ اقرأ middleware.
- لا تعتبر Mailable مستخدمة لأنها موجودة؛ ابحث عن sender.
- لا تعتبر Service فيها business logic؛ افحص إن كانت مجرد delegation.
- شغّل tests، ثم راجع الاستثناءات المحتملة في runtime مثل relations والأعمدة المذكورة.

---

## 28. Final Summary

هذا Laravel HRM يملك أساسًا واضحًا: MVC مع API versioning، Sanctum، Spatie RBAC، DTOs، Services، Repositories، Eloquent، Blade، وschema واسع يغطي أغلب عمليات الموارد البشرية. أقوى مسار تعليمي لفهمه هو Leave أو Employee onboarding ثم Attendance وPayroll.

لكن التنفيذ الحالي **demo/enterprise prototype أكثر من كونه architecture مكتملة الإنتاج**: توجد طبقات جيدة لكنها غير موحدة، وWeb Admin يعتمد على `auth` أكثر من permission middleware، وEvents/Jobs/Mail غير موصولة، وهناك mismatches مؤكدة في Attendance summaries، Performance DTO، Document relation، welcome routes، وAnnouncement resource update. لذلك ينبغي التمييز دائمًا بين ما تصفه أسماء الملفات وما يحدث فعلًا عند تشغيل الكود.
