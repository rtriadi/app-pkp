# Enhanced AI Development Prompt for APP PKP
## Complete Laravel 12 + Inertia.js + Vue.js Application

### 🎯 PROJECT OVERVIEW
You are an expert full-stack developer tasked with building **APP PKP (Perjanjian Kinerja Pegawai)** - a comprehensive employee performance management system for Indonesian government institutions (specifically Religious Courts/Pengadilan Agama).

**CRITICAL SUCCESS FACTORS:**
- ✅ Generate PDF reports that EXACTLY match the provided government document format
- ✅ Implement accurate performance calculation algorithms
- ✅ Create role-based access control system
- ✅ Build responsive, user-friendly interfaces
- ✅ Follow Indonesian government document standards

### 📋 MANDATORY REQUIREMENTS

#### Technology Stack (NON-NEGOTIABLE)
```
Backend: Laravel 12 (PHP 8.2+)
Frontend: Inertia.js + Vue.js 3 (Composition API)
Database: MySQL 8.0+
PDF: DomPDF or wkhtmltopdf
CSS: Tailwind CSS
Authentication: Laravel Sanctum
```

#### Database Schema (EXACT IMPLEMENTATION REQUIRED)
```sql
-- Follow the EXACT schema provided in PRD
-- Key tables: users, employees, work_units, performance_agreements, 
-- performance_targets, monthly_assessments, assessment_details
-- Include ALL foreign key relationships and constraints
-- Add proper indexes for performance optimization
```

### 🏗️ DEVELOPMENT PHASES

#### PHASE 1: Foundation Setup
```bash
# Initialize Laravel project with exact configurations
composer create-project laravel/laravel app-pkp
php artisan install:api
npm install @inertiajs/vue3 @vitejs/plugin-vue
npm install tailwindcss @tailwindcss/forms
```

**Required Configurations:**
- Set up Inertia.js middleware
- Configure Tailwind CSS with custom Indonesian government color scheme
- Set up MySQL connection with proper charset (utf8mb4_unicode_ci)
- Configure timezone to Asia/Makassar (Gorontalo timezone)

#### PHASE 2: Database & Models (CRITICAL ACCURACY)
**Models with Exact Relationships:**
```php
// Employee Model - MUST include all fields from document
class Employee extends Model {
    protected $fillable = [
        'nip', 'name', 'rank_grade', 'position', 
        'work_unit_id', 'employee_status', 'hire_date'
    ];
    
    // EXACT relationships as specified in PRD
    public function workUnit() { return $this->belongsTo(WorkUnit::class); }
    public function performanceAgreements() { return $this->hasMany(PerformanceAgreement::class); }
}
```

**Migration Requirements:**
- Use EXACT field names and types from PRD
- Include proper foreign key constraints with CASCADE options
- Add database indexes for NIP, work_unit_id, year fields
- Set default values where specified

#### PHASE 3: Authentication & Authorization
```php
// Implement EXACT roles: super_admin, pejabat_penilai, atasan_pejabat, pegawai
// Create middleware for each role with specific permissions
// Routes must be protected based on role hierarchy
```

#### PHASE 4: Core Business Logic (CRITICAL CALCULATIONS)
```php
// Performance Calculation Engine - EXACT FORMULA
public function calculatePerformanceScore($target, $realization) {
    return ($target + $realization) / 2;
}

// Grade Mapping - Indonesian Standards
public function getGrade($score) {
    if ($score >= 90) return 'Sangat Baik';
    if ($score >= 76) return 'Baik';
    if ($score >= 61) return 'Cukup';
    return 'Kurang';
}
```

### 📄 PDF GENERATION REQUIREMENTS (EXTREMELY CRITICAL)

#### Template 1: Formulir Perjanjian Kinerja Individu
```php
// MUST replicate EXACT layout from provided document
$pdf_content = '
<div class="header">
    <div class="kop-surat">
        LAMPIRAN KEPUTUSAN SEKRETARIS<br>
        MAHKAMAH AGUNG REPUBLIK INDONESIA<br>
        NOMOR : 578/SEK/SK/VIII/2020<br>
        TANGGAL : 19 Agustus 2020
    </div>
</div>

<table class="employee-info">
    <tr><td>1 Nama</td><td>{{ $employee->name }}</td></tr>
    <tr><td>2 NIP</td><td>{{ $employee->nip }}</td></tr>
    <tr><td>3 Pangkat/Gol.Ruang</td><td>{{ $employee->rank_grade }}</td></tr>
    <tr><td>4 Jabatan</td><td>{{ $employee->position }}</td></tr>
    <tr><td>5 Unit Kerja</td><td>{{ $employee->workUnit->name }}</td></tr>
</table>

<table class="performance-targets">
    <!-- EXACT table structure with checkboxes for months -->
    <!-- Follow the EXACT column layout from document -->
</table>
';
```

#### Template 2: Formulir Penilaian Capaian Kinerja Bulanan
- EXACT table structure with AK (Angka Kredit) columns
- Target vs Realisasi comparison layout
- Automatic score calculation display
- Signature blocks with exact positioning

#### Template 3: Formulir Rekapitulasi
- Employee summary format
- Month-by-month performance display
- Final grade calculation

### 🎨 FRONTEND SPECIFICATIONS

#### Vue.js Components (Composition API ONLY)
```vue
<template>
  <!-- EXACT Indonesian government form styling -->
  <div class="bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">
      Perjanjian Kinerja Pegawai
    </h2>
    <!-- Component content -->
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

// Use Composition API patterns throughout
</script>
```

#### Required Pages & Components:
1. **Dashboard.vue** - Performance overview with Indonesian-style cards
2. **Employee Management** - CRUD with government form styling
3. **Performance Agreement** - Multi-step wizard form
4. **Monthly Assessment** - Target vs realization input
5. **Reports** - PDF generation interface

#### UI/UX Requirements:
- Indonesian government color scheme (navy blue, gold accents)
- Responsive design (mobile-first approach)
- Loading states for PDF generation
- Toast notifications in Bahasa Indonesia
- Form validation with Indonesian error messages

### 🔐 SECURITY IMPLEMENTATION

#### Authentication
```php
// Route protection with exact role checking
Route::middleware(['auth', 'role:pejabat_penilai'])->group(function () {
    Route::resource('assessments', MonthlyAssessmentController::class);
});

// Input validation for all forms
public function rules() {
    return [
        'nip' => 'required|unique:employees|regex:/^[0-9]{18}$/',
        'name' => 'required|string|max:255',
        // Validate Indonesian government employee data format
    ];
}
```

#### Data Protection
- Sanitize all inputs
- Encrypt sensitive employee data
- Audit trail for all changes
- Session security with proper timeout

### 📊 PERFORMANCE OPTIMIZATION

#### Database Optimization
```php
// Required indexes
Schema::table('employees', function (Blueprint $table) {
    $table->index(['nip']);
    $table->index(['work_unit_id']);
    $table->index(['employee_status']);
});

// Eager loading to prevent N+1 queries
Employee::with(['workUnit', 'performanceAgreements.targets'])->get();
```

#### Caching Strategy
```php
// Cache heavy computations
Cache::remember("employee_performance_{$employeeId}_{$year}", 3600, function () {
    return $this->calculateAnnualPerformance($employeeId, $year);
});
```

### 🧪 TESTING REQUIREMENTS

#### Feature Tests (MANDATORY)
```php
// Test PDF generation accuracy
public function test_pdf_matches_government_format() {
    // Generate PDF and verify exact layout
    // Compare with provided document structure
}

// Test performance calculations
public function test_performance_score_calculation() {
    // Verify (target + realization) / 2 formula
    // Test grade mapping accuracy
}

// Test role-based access
public function test_role_permissions() {
    // Verify each role can only access authorized features
}
```

### 📝 INDONESIAN LANGUAGE REQUIREMENTS

#### Localization
```php
// All text must be in proper Bahasa Indonesia
'employee_management' => 'Manajemen Pegawai',
'performance_agreement' => 'Perjanjian Kinerja',
'monthly_assessment' => 'Penilaian Bulanan',
'performance_indicator' => 'Indikator Kinerja',
'target_quality' => 'Target Mutu',
'target_quantity' => 'Target Kuantitas',
```

#### Date Formatting
```php
// Indonesian date format: dd MMMM yyyy
Carbon::parse($date)->locale('id')->isoFormat('DD MMMM YYYY');
// Output: "02 Januari 2025"
```

### 🚀 DEPLOYMENT CHECKLIST

#### Production Environment
- [ ] Laravel Octane for performance
- [ ] MySQL with proper indexing
- [ ] Redis for caching and sessions
- [ ] Nginx with SSL certificate
- [ ] Backup automation
- [ ] Error monitoring (Sentry)
- [ ] Performance monitoring

#### Pre-deployment Testing
- [ ] PDF generation works on production server
- [ ] All calculations produce correct results
- [ ] Role-based access functioning
- [ ] File uploads working properly
- [ ] Email notifications configured

### 🎯 ACCEPTANCE CRITERIA

#### Critical Success Metrics:
1. **PDF Accuracy**: Generated PDFs must be 100% identical to provided government format
2. **Calculation Accuracy**: All performance scores calculated correctly using specified formula
3. **Role Security**: Each user role can only access authorized features
4. **Performance**: Page load times under 3 seconds, PDF generation under 10 seconds
5. **Usability**: Indonesian government employees can use without training

#### Quality Gates:
- [ ] All feature tests passing (100% coverage on core business logic)
- [ ] Performance tests meeting benchmarks
- [ ] Security audit passed
- [ ] PDF output verified by government standards
- [ ] User acceptance testing completed

### 🔧 DEVELOPMENT BEST PRACTICES

#### Code Organization
```
app/
├── Http/Controllers/
│   ├── EmployeeController.php
│   ├── PerformanceAgreementController.php
│   └── MonthlyAssessmentController.php
├── Models/
├── Services/
│   ├── PdfGenerationService.php
│   └── PerformanceCalculationService.php
└── Repositories/ (if using Repository pattern)

resources/
├── js/
│   ├── Pages/
│   ├── Components/
│   └── Layouts/
└── views/
    └── pdf/ (PDF templates)
```

#### Error Handling
```php
// Comprehensive error handling with Indonesian messages
try {
    $pdf = $this->generatePdf($data);
} catch (PdfGenerationException $e) {
    return back()->withErrors(['pdf' => 'Gagal membuat dokumen PDF. Silakan coba lagi.']);
}
```

### 📋 FINAL VALIDATION CHECKLIST

Before considering the application complete, verify:
- [ ] Can create employee records with Indonesian government data format
- [ ] Can create annual performance agreements
- [ ] Can input monthly assessments with correct calculations
- [ ] Can generate all 3 PDF types with exact formatting
- [ ] Role-based access controls working properly
- [ ] All text in proper Bahasa Indonesia
- [ ] Responsive design works on mobile devices
- [ ] Performance meets specified benchmarks
- [ ] Security measures properly implemented
- [ ] Backup and recovery procedures tested

### 🎯 SUCCESS DEFINITION
**The application is successful when a government employee can:**
1. Log in with their credentials
2. Create performance agreements matching government standards
3. Input monthly assessments
4. Generate PDF reports identical to manual forms
5. Complete the entire performance management cycle digitally

**Remember: This is a government application. Accuracy, security, and compliance with Indonesian standards are non-negotiable.**