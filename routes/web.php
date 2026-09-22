<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\CourseTermController;
use App\Http\Controllers\CourseTopicController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserLicenseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// การจัดการหลักสูตร (§1.1) — เฉพาะ System Admin / Admin ส่วนกลาง
Route::middleware(['auth', 'role:system_admin|central_admin'])->group(function () {
    Route::resource('courses', CourseController::class);
    Route::post('courses/{course}/terms', [CourseTermController::class, 'store'])->name('course-terms.store');
    Route::patch('terms/{term}', [CourseTermController::class, 'update'])->name('course-terms.update');
    Route::delete('terms/{term}', [CourseTermController::class, 'destroy'])->name('course-terms.destroy');
    Route::post('terms/{term}/topics', [CourseTopicController::class, 'store'])->name('course-topics.store');
    Route::patch('topics/{topic}', [CourseTopicController::class, 'update'])->name('course-topics.update');
    Route::delete('topics/{topic}', [CourseTopicController::class, 'destroy'])->name('course-topics.destroy');
    Route::post('terms/{term}/exam', [ExamController::class, 'storeForTerm'])->name('exams.store-for-term');
    Route::patch('exam-attempts/{attempt}/grade', [ExamController::class, 'grade'])->name('exam-attempts.grade');
});

// การอนุมัติเปิดหลักสูตร (§1.2) — Admin สาขาขอเปิด, Admin ส่วนกลาง/System Admin อนุมัติ
Route::middleware(['auth', 'role:system_admin|central_admin|branch_admin'])->group(function () {
    Route::resource('offerings', CourseOfferingController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('offerings/{offering}/attachment', [CourseOfferingController::class, 'downloadAttachment'])->name('offerings.attachment');
    Route::patch('offerings/{offering}/approve', [CourseOfferingController::class, 'approve'])->name('offerings.approve');
    Route::patch('offerings/{offering}/reject', [CourseOfferingController::class, 'reject'])->name('offerings.reject');
});

// การลงทะเบียนเรียน (§1.3) — สมัครด้วยตัวเอง (ต้องรออนุมัติ)
Route::middleware('auth')->group(function () {
    Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('offerings/{offering}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
});

// การลงทะเบียนเรียน (§1.3.2/§1.3.3) — Admin สาขาเพิ่มสิทธิ์/นำเข้ารายชื่อ/อนุมัติคำขอ
Route::middleware(['auth', 'role:system_admin|branch_admin'])->group(function () {
    Route::get('offerings/{offering}/licenses', [UserLicenseController::class, 'index'])->name('offerings.licenses.index');
    Route::post('offerings/{offering}/licenses', [UserLicenseController::class, 'store'])->name('offerings.licenses.store');
    Route::post('offerings/{offering}/licenses/import', [UserLicenseController::class, 'import'])->name('offerings.licenses.import');
    Route::patch('licenses/{license}/approve', [UserLicenseController::class, 'approve'])->name('licenses.approve');
    Route::patch('licenses/{license}/reject', [UserLicenseController::class, 'reject'])->name('licenses.reject');
    Route::delete('licenses/{license}', [UserLicenseController::class, 'revoke'])->name('licenses.revoke');
});

// การเรียนการสอน (§1.4) และการสอบ (§1.5) — ผู้เรียนที่มี User License ที่ active เท่านั้น
Route::middleware('auth')->group(function () {
    Route::get('terms/{term}/learn', [LearningController::class, 'term'])->name('learning.term');
    Route::get('topics/{topic}', [LearningController::class, 'topic'])->name('topics.show');
    Route::post('topics/{topic}/heartbeat', [LearningController::class, 'heartbeat'])->name('topics.heartbeat');
    Route::post('topics/{topic}/quiz', [LearningController::class, 'submitQuiz'])->name('topics.quiz');
    Route::post('topics/{topic}/practice-log', [LearningController::class, 'submitPracticeLog'])->name('topics.practice-log');

    Route::get('terms/{term}/exam', [ExamController::class, 'show'])->name('exams.show');
    Route::post('exams/{exam}/attempts', [ExamController::class, 'store'])->name('exam-attempts.store');
});

// ระบบทะเบียน (§1.6) — ประวัติการเข้าอบรม
Route::middleware(['auth', 'role:system_admin|central_admin|branch_admin'])->group(function () {
    Route::get('reports/enrollments', [ReportController::class, 'index'])->name('reports.enrollments');
    Route::get('reports/enrollments/export', [ReportController::class, 'export'])->name('reports.enrollments.export');
});

require __DIR__.'/auth.php';
