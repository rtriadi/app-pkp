# Product Requirements Document (PRD)
## APP PKP - Perjanjian Kinerja Pegawai

### 1. OVERVIEW

#### 1.1 Product Description
APP PKP adalah aplikasi manajemen kinerja pegawai yang dirancang khusus untuk instansi pemerintah, khususnya Pengadilan Agama. Aplikasi ini mengelola perjanjian kinerja individu pegawai dan menghasilkan laporan dalam format PDF sesuai standar pemerintahan.

#### 1.2 Technology Stack
- **Backend**: Laravel 12
- **Frontend**: Inertia.js + Vue.js 3
- **Database**: MySQL 8.0+
- **PDF Generation**: DomPDF atau wkhtmltopdf
- **Authentication**: Laravel Sanctum
- **UI Framework**: Tailwind CSS / Bootstrap 5

#### 1.3 Target Users
- Pejabat Penilai
- Atasan Pejabat Penilai
- Sekretaris/Admin
- Pegawai (view only)

---

### 2. FUNCTIONAL REQUIREMENTS

#### 2.1 User Management
- **Role-based Authentication**
  - Super Admin
  - Pejabat Penilai
  - Atasan Pejabat Penilai
  - Pegawai
- **User Registration & Login**
- **Profile Management**

#### 2.2 Master Data Management

##### 2.2.1 Employee Management
- CRUD operations untuk data pegawai
- Field yang diperlukan:
  - Nama Lengkap
  - NIP (Nomor Induk Pegawai)
  - Pangkat/Golongan
  - Jabatan
  - Unit Kerja
  - Status Kepegawaian
  - Foto (optional)

##### 2.2.2 Organizational Structure
- Unit Kerja/Satuan Kerja
- Jabatan dan hierarki
- Pangkat dan golongan

##### 2.2.3 Performance Indicators
- Master Sasaran Kegiatan
- Master Indikator Kinerja
- Target Mutu dan Kuantitas
- Satuan Pengukuran

#### 2.3 Performance Agreement (Perjanjian Kinerja)

##### 2.3.1 Annual Performance Agreement Creation
- Input sasaran kegiatan tahunan
- Definisi indikator kinerja
- Penetapan target mutu dan kuantitas
- Penjadwalan bulanan (checkbox per bulan)
- Approval workflow

##### 2.3.2 Performance Agreement Features
- Template management
- Bulk import dari Excel
- Version control
- Status tracking (Draft, Pending Approval, Approved, Rejected)

#### 2.4 Monthly Performance Assessment

##### 2.4.1 Monthly Evaluation Input
- Input realisasi kinerja per kegiatan
- Perhitungan otomatis nilai capaian
- Upload dokumentasi pendukung
- Catatan/komentar evaluasi

##### 2.4.2 Assessment Calculation Engine
- Rumus perhitungan: (Target + Realisasi) / 2
- Automatic scoring berdasarkan persentase
- Grade mapping (Sangat Baik, Baik, Cukup, Kurang)
- Rekapitulasi nilai keseluruhan

#### 2.5 Reporting & PDF Generation

##### 2.5.1 PDF Templates
- **Formulir Perjanjian Kinerja Individu**
- **Formulir Penilaian Capaian Kinerja Bulanan**
- **Formulir Rekapitulasi Penilaian Capaian Kinerja**

##### 2.5.2 Report Features
- Dynamic PDF generation
- Digital signature integration
- Batch report generation
- Export ke Excel
- Email notification

---

### 3. TECHNICAL SPECIFICATIONS

#### 3.1 Database Schema

##### 3.1.1 Users Table
```sql
users:
- id (bigint, primary key)
- name (varchar 255)
- email (varchar 255, unique)
- password (varchar 255)
- role (enum: super_admin, pejabat_penilai, atasan_pejabat, pegawai)
- is_active (boolean)
- email_verified_at (timestamp)
- created_at/updated_at (timestamps)
```

##### 3.1.2 Employees Table
```sql
employees:
- id (bigint, primary key)
- user_id (bigint, foreign key nullable)
- nip (varchar 20, unique)
- name (varchar 255)
- rank_grade (varchar 50) // Pangkat/Golongan
- position (varchar 255) // Jabatan
- work_unit_id (bigint, foreign key)
- employee_status (enum: active, inactive, retired)
- hire_date (date)
- photo_path (varchar 255, nullable)
- created_at/updated_at (timestamps)
```

##### 3.1.3 Work Units Table
```sql
work_units:
- id (bigint, primary key)
- name (varchar 255)
- code (varchar 20)
- parent_id (bigint, nullable, self-reference)
- head_employee_id (bigint, foreign key nullable)
- address (text)
- created_at/updated_at (timestamps)
```

##### 3.1.4 Performance Agreements Table
```sql
performance_agreements:
- id (bigint, primary key)
- employee_id (bigint, foreign key)
- year (year)
- status (enum: draft, pending, approved, rejected)
- approved_by (bigint, foreign key nullable)
- approved_at (timestamp nullable)
- created_by (bigint, foreign key)
- created_at/updated_at (timestamps)
```

##### 3.1.5 Performance Targets Table
```sql
performance_targets:
- id (bigint, primary key)
- agreement_id (bigint, foreign key)
- activity_goal (text) // Sasaran Kegiatan
- performance_indicator (text) // Indikator Kinerja
- quality_target (decimal 5,2) // Target Mutu
- quantity_target (decimal 10,2) // Target Kuantitas
- unit (varchar 50) // Satuan
- jan through dec (boolean) // Checkbox untuk setiap bulan
- created_at/updated_at (timestamps)
```

##### 3.1.6 Monthly Assessments Table
```sql
monthly_assessments:
- id (bigint, primary key)
- agreement_id (bigint, foreign key)
- month (tinyint 1-12)
- year (year)
- status (enum: draft, submitted, approved)
- total_score (decimal 5,2)
- grade (varchar 20)
- assessor_id (bigint, foreign key) // Pejabat Penilai
- supervisor_id (bigint, foreign key) // Atasan Pejabat Penilai
- notes (text nullable)
- created_at/updated_at (timestamps)
```

##### 3.1.7 Assessment Details Table
```sql
assessment_details:
- id (bigint, primary key)
- assessment_id (bigint, foreign key)
- target_id (bigint, foreign key)
- ak_target (decimal 5,2) // Angka Kredit Target
- quantity_realization (decimal 10,2)
- quality_realization (decimal 5,2)
- score (decimal 5,2)
- created_at/updated_at (timestamps)
```

#### 3.2 API Endpoints Structure

##### 3.2.1 Authentication
- POST /api/login
- POST /api/logout
- POST /api/register
- GET /api/user

##### 3.2.2 Employee Management
- GET /api/employees
- POST /api/employees
- GET /api/employees/{id}
- PUT /api/employees/{id}
- DELETE /api/employees/{id}

##### 3.2.3 Performance Agreement
- GET /api/performance-agreements
- POST /api/performance-agreements
- GET /api/performance-agreements/{id}
- PUT /api/performance-agreements/{id}
- POST /api/performance-agreements/{id}/approve
- POST /api/performance-agreements/{id}/reject

##### 3.2.4 Monthly Assessment
- GET /api/monthly-assessments
- POST /api/monthly-assessments
- GET /api/monthly-assessments/{id}
- PUT /api/monthly-assessments/{id}
- POST /api/monthly-assessments/{id}/submit

##### 3.2.5 Reports & PDF
- GET /api/reports/performance-agreement/{id}/pdf
- GET /api/reports/monthly-assessment/{id}/pdf
- GET /api/reports/recap/{employee_id}/{year}/pdf

#### 3.3 Frontend Components (Vue.js)

##### 3.3.1 Layout Components
- AppLayout.vue
- Sidebar.vue
- Header.vue
- Breadcrumb.vue

##### 3.3.2 Page Components
- Dashboard.vue
- Employee/Index.vue
- Employee/Create.vue
- Employee/Edit.vue
- PerformanceAgreement/Index.vue
- PerformanceAgreement/Create.vue
- MonthlyAssessment/Index.vue
- MonthlyAssessment/Create.vue
- Reports/Index.vue

##### 3.3.3 Shared Components
- DataTable.vue
- Modal.vue
- FormInput.vue
- SelectInput.vue
- DatePicker.vue
- FileUpload.vue

---

### 4. USER INTERFACE REQUIREMENTS

#### 4.1 Dashboard
- Overview statistik kinerja
- Quick actions
- Recent activities
- Calendar view untuk deadline

#### 4.2 Employee Management
- Data table dengan search dan filter
- Bulk import from Excel
- Employee detail view
- Photo upload functionality

#### 4.3 Performance Agreement Form
- Multi-step form wizard
- Dynamic target addition
- Month selection checkboxes
- Preview before submit
- Approval workflow interface

#### 4.4 Monthly Assessment Form
- Target vs Realization comparison
- Auto-calculation display
- File upload for documentation
- Comments section
- Score visualization

#### 4.5 Report Interface
- Report selection dropdown
- Date range picker
- Preview before PDF generation
- Download and email options

---

### 5. PDF TEMPLATE REQUIREMENTS

#### 5.1 Header Information
- Logo instansi
- Nomor surat keputusan
- Tanggal keputusan
- Identitas pegawai (Nama, NIP, Pangkat, Jabatan, Unit Kerja)

#### 5.2 Performance Agreement PDF
- Tabel sasaran kegiatan dengan kolom:
  - No
  - Sasaran Kegiatan
  - Indikator Kinerja
  - Target Mutu
  - Target Kuantitas
  - Satuan
  - Waktu Penyelesaian (Jan-Dec checkboxes)
- Tanda tangan Atasan Pejabat Penilai dan Pejabat Penilai

#### 5.3 Monthly Assessment PDF
- Identitas bulan dan tahun
- Tabel penilaian per kegiatan:
  - AK (Angka Kredit)
  - Target (Kuantitas, Satuan, Mutu)
  - Realisasi (Kuantitas, Satuan, Mutu)
  - Nilai Capaian Kinerja
- Rekapitulasi total nilai
- Grade (Sangat Baik, Baik, Cukup, Kurang)
- Catatan dan tanda tangan

#### 5.4 Recap Report PDF
- Summary per pegawai
- Tabel rekapitulasi bulanan
- Keterangan tambahan
- Tanda tangan pejabat yang berwenang

---

### 6. SECURITY REQUIREMENTS

#### 6.1 Authentication & Authorization
- Multi-factor authentication (optional)
- Role-based access control
- Session management
- Password policy enforcement

#### 6.2 Data Security
- Input validation dan sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Data encryption for sensitive information

#### 6.3 Audit Trail
- User activity logging
- Data change tracking
- Login/logout logging
- PDF generation logging

---

### 7. PERFORMANCE REQUIREMENTS

#### 7.1 Response Time
- Page load time < 3 seconds
- PDF generation < 10 seconds
- API response time < 1 second

#### 7.2 Scalability
- Support up to 1000 concurrent users
- Database optimization untuk large datasets
- Caching strategy (Redis)

#### 7.3 Availability
- 99.5% uptime
- Backup strategy
- Disaster recovery plan

---

### 8. INTEGRATION REQUIREMENTS

#### 8.1 External Systems
- SIMPEG (Sistem Informasi Manajemen Kepegawaian)
- Email server untuk notifications
- Digital signature service (optional)

#### 8.2 File Storage
- Local file storage untuk documents
- Cloud storage integration (optional)

---

### 9. DEPLOYMENT & MAINTENANCE

#### 9.1 Development Environment
- Docker containerization
- Git version control
- Automated testing (PHPUnit, Jest)
- CI/CD pipeline

#### 9.2 Production Requirements
- Linux server (Ubuntu/CentOS)
- Nginx web server
- MySQL 8.0+
- PHP 8.2+
- SSL certificate
- Regular backups

#### 9.3 Monitoring
- Application monitoring
- Error tracking (Sentry)
- Performance monitoring
- Database monitoring

---

### 10. SUCCESS METRICS

#### 10.1 User Adoption
- User registration rate
- Monthly active users
- Feature usage statistics

#### 10.2 Performance Metrics
- PDF generation success rate
- System uptime
- Page load times
- Error rates

#### 10.3 Business Impact
- Time reduction in performance assessment process
- Accuracy improvement in scoring
- User satisfaction scores

---

### 11. TIMELINE & MILESTONES

#### Phase 1 (Month 1-2)
- Database design and setup
- Authentication system
- Basic CRUD operations
- Employee management

#### Phase 2 (Month 3-4)
- Performance agreement module
- Monthly assessment module
- Basic reporting

#### Phase 3 (Month 5-6)
- PDF generation implementation
- Advanced reporting
- User interface refinement

#### Phase 4 (Month 7-8)
- Testing and debugging
- Performance optimization
- Security audit
- Deployment preparation

---

### 12. RISK ANALYSIS

#### 12.1 Technical Risks
- PDF template complexity
- Performance with large datasets
- Browser compatibility issues

#### 12.2 Business Risks
- User adoption challenges
- Data migration complexity
- Changing government regulations

#### 12.3 Mitigation Strategies
- Regular stakeholder communication
- Iterative development approach
- Comprehensive testing strategy
- Training and documentation

---

### 13. APPENDIX

#### 13.1 Government Document Standards
- Formulir berdasarkan standar pemerintah Indonesia
- Compliance dengan regulasi kepegawaian
- Format sesuai dengan Mahkamah Agung RI

#### 13.2 Additional Features (Future Enhancement)
- Mobile application
- Advanced analytics dashboard
- Integration with e-signature
- Automated reminder system
- Multi-language support