<?php

// ExamResult.php
file_put_contents('app/Models/ExamResult.php', '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        "exam_id", "student_id", "subject_id", "marks_obtained", "max_marks", "grade", "status", "remarks"
    ];

    protected $casts = [
        "marks_obtained" => "decimal:2",
        "max_marks" => "decimal:2"
    ];

    public function exam() { return $this->belongsTo(Exam::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
}');

// Timetable.php
file_put_contents('app/Models/Timetable.php', '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        "institute_id", "class_id", "subject_id", "teacher_id", "day_of_week", "start_time", "end_time", "room_number", "period_type"
    ];

    protected $casts = [
        "start_time" => "datetime",
        "end_time" => "datetime"
    ];

    public function institute() { return $this->belongsTo(Institute::class); }
    public function class() { return $this->belongsTo(ClassModel::class, "class_id"); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function teacher() { return $this->belongsTo(User::class, "teacher_id"); }
}');

// LeaveApplication.php
file_put_contents('app/Models/LeaveApplication.php', '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "institute_id", "leave_type", "start_date", "end_date", "total_days", "reason", "status", "approved_by", "admin_remarks", "approved_at"
    ];

    protected $casts = [
        "start_date" => "date",
        "end_date" => "date",
        "approved_at" => "datetime"
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(Institute::class); }
    public function approver() { return $this->belongsTo(User::class, "approved_by"); }
}');

// SalaryStructure.php
file_put_contents('app/Models/SalaryStructure.php', '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "institute_id", "basic_salary", "house_allowance", "transport_allowance", "medical_allowance", "other_allowances", "pf_deduction", "tax_deduction", "other_deductions", "gross_salary", "net_salary", "effective_from", "effective_till", "status"
    ];

    protected $casts = [
        "basic_salary" => "decimal:2",
        "gross_salary" => "decimal:2",
        "net_salary" => "decimal:2",
        "effective_from" => "date",
        "effective_till" => "date"
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function institute() { return $this->belongsTo(Institute::class); }
    public function payments() { return $this->hasMany(SalaryPayment::class); }
}');

// Additional models...
$models = [
    'SalaryPayment', 'LearningMaterial', 'Assignment', 'AssignmentSubmission', 
    'Chat', 'Message', 'Vehicle', 'TransportRoute', 'StudentTransport',
    'Hostel', 'HostelRoom', 'StudentHostel'
];

foreach ($models as $model) {
    $content = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$model} extends Model\n{\n    use HasFactory;\n    \n    protected \$guarded = [];\n}";
    file_put_contents("app/Models/{$model}.php", $content);
}

echo "Models created successfully!";
?>