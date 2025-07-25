<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\FeeType;
use App\Models\ExamType;
use App\Models\LearningMaterialCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Academic Year
        $academicYear = AcademicYear::create([
            'name' => '2024-25',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_active' => true,
            'terms' => [
                ['name' => 'First Term', 'start_date' => '2024-04-01', 'end_date' => '2024-08-31'],
                ['name' => 'Second Term', 'start_date' => '2024-09-01', 'end_date' => '2024-12-31'],
                ['name' => 'Third Term', 'start_date' => '2025-01-01', 'end_date' => '2025-03-31']
            ]
        ]);

        // Create Classes
        $classes = [
            ['name' => 'Playgroup', 'code' => 'PG', 'capacity' => 20, 'monthly_fee' => 2000, 'admission_fee' => 5000],
            ['name' => 'Nursery', 'code' => 'NUR', 'capacity' => 25, 'monthly_fee' => 2500, 'admission_fee' => 5000],
            ['name' => 'Jr. KG', 'code' => 'JKG', 'capacity' => 25, 'monthly_fee' => 3000, 'admission_fee' => 6000],
            ['name' => 'Sr. KG', 'code' => 'SKG', 'capacity' => 25, 'monthly_fee' => 3500, 'admission_fee' => 6000]
        ];

        foreach ($classes as $classData) {
            SchoolClass::create($classData);
        }

        // Create Subjects
        $subjects = [
            ['name' => 'English', 'code' => 'ENG', 'description' => 'English Language', 'color' => '#FF6B6B'],
            ['name' => 'Hindi', 'code' => 'HIN', 'description' => 'Hindi Language', 'color' => '#4ECDC4'],
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Basic Mathematics', 'color' => '#45B7D1'],
            ['name' => 'General Knowledge', 'code' => 'GK', 'description' => 'General Knowledge', 'color' => '#96CEB4'],
            ['name' => 'Drawing', 'code' => 'DRAW', 'description' => 'Art and Drawing', 'color' => '#FFEAA7'],
            ['name' => 'Rhymes', 'code' => 'RHYME', 'description' => 'Nursery Rhymes', 'color' => '#DDA0DD'],
            ['name' => 'Physical Activity', 'code' => 'PE', 'description' => 'Physical Education', 'color' => '#98D8C8'],
            ['name' => 'Craft', 'code' => 'CRAFT', 'description' => 'Arts and Crafts', 'color' => '#F7DC6F']
        ];

        foreach ($subjects as $subjectData) {
            Subject::create($subjectData);
        }

        // Create Admin User
        $adminUser = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@preschool.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true
        ]);

        // Create Principal Staff
        $principalUser = User::create([
            'name' => 'Dr. Priya Sharma',
            'email' => 'principal@preschool.com',
            'password' => Hash::make('principal123'),
            'role' => 'staff',
            'is_active' => true
        ]);

        Staff::create([
            'user_id' => $principalUser->id,
            'employee_id' => 'EMP001',
            'first_name' => 'Priya',
            'last_name' => 'Sharma',
            'date_of_birth' => '1980-05-15',
            'gender' => 'female',
            'phone' => '9876543210',
            'emergency_contact' => '9876543211',
            'address' => '123 School Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'designation' => 'principal',
            'qualification' => 'M.Ed, B.Ed',
            'experience' => '15 years in education',
            'joining_date' => '2020-04-01',
            'salary' => 50000,
            'status' => 'active'
        ]);

        // Create Sample Teachers
        $teachers = [
            [
                'name' => 'Sunita Patel',
                'email' => 'sunita@preschool.com',
                'employee_id' => 'EMP002',
                'first_name' => 'Sunita',
                'last_name' => 'Patel',
                'designation' => 'teacher',
                'qualification' => 'B.Ed, BA',
                'phone' => '9876543212'
            ],
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh@preschool.com',
                'employee_id' => 'EMP003',
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'designation' => 'teacher',
                'qualification' => 'B.Ed, BSc',
                'phone' => '9876543213'
            ]
        ];

        foreach ($teachers as $teacher) {
            $user = User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('teacher123'),
                'role' => 'staff',
                'is_active' => true
            ]);

            Staff::create([
                'user_id' => $user->id,
                'employee_id' => $teacher['employee_id'],
                'first_name' => $teacher['first_name'],
                'last_name' => $teacher['last_name'],
                'date_of_birth' => '1985-01-01',
                'gender' => 'female',
                'phone' => $teacher['phone'],
                'emergency_contact' => $teacher['phone'],
                'address' => '456 Teacher Colony',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400002',
                'designation' => $teacher['designation'],
                'qualification' => $teacher['qualification'],
                'experience' => '5 years in teaching',
                'joining_date' => '2022-04-01',
                'salary' => 25000,
                'status' => 'active'
            ]);
        }

        // Create Fee Types
        $feeTypes = [
            ['name' => 'Monthly Fee', 'description' => 'Monthly tuition fee'],
            ['name' => 'Admission Fee', 'description' => 'One-time admission fee'],
            ['name' => 'Annual Fee', 'description' => 'Annual charges'],
            ['name' => 'Transport Fee', 'description' => 'School bus charges'],
            ['name' => 'Activity Fee', 'description' => 'Extra-curricular activity charges'],
            ['name' => 'Book Fee', 'description' => 'Books and stationery charges']
        ];

        foreach ($feeTypes as $feeType) {
            FeeType::create($feeType);
        }

        // Create Exam Types
        $examTypes = [
            ['name' => 'Unit Test', 'description' => 'Monthly unit tests', 'weightage' => 20],
            ['name' => 'Mid-term Exam', 'description' => 'Mid-term examination', 'weightage' => 30],
            ['name' => 'Final Exam', 'description' => 'Final examination', 'weightage' => 50],
            ['name' => 'Oral Test', 'description' => 'Oral assessment', 'weightage' => 10],
            ['name' => 'Practical Test', 'description' => 'Practical assessment', 'weightage' => 15]
        ];

        foreach ($examTypes as $examType) {
            ExamType::create($examType);
        }

        // Create Learning Material Categories
        $categories = [
            ['name' => 'Worksheets', 'description' => 'Practice worksheets', 'icon' => 'document'],
            ['name' => 'Videos', 'description' => 'Educational videos', 'icon' => 'video'],
            ['name' => 'Audio', 'description' => 'Audio files and rhymes', 'icon' => 'volume-up'],
            ['name' => 'Images', 'description' => 'Pictures and illustrations', 'icon' => 'image'],
            ['name' => 'Activities', 'description' => 'Fun learning activities', 'icon' => 'puzzle'],
            ['name' => 'Books', 'description' => 'Digital books and stories', 'icon' => 'book'],
            ['name' => 'Games', 'description' => 'Educational games', 'icon' => 'gamepad']
        ];

        foreach ($categories as $category) {
            LearningMaterialCategory::create($category);
        }

        // Assign subjects to classes
        $this->assignSubjectsToClasses();

        echo "Database seeded successfully!\n";
        echo "Admin Login: admin@preschool.com / admin123\n";
        echo "Principal Login: principal@preschool.com / principal123\n";
        echo "Teacher Login: sunita@preschool.com / teacher123\n";
    }

    private function assignSubjectsToClasses()
    {
        $classes = SchoolClass::all();
        $subjects = Subject::all();

        // Assign different subjects to different classes with periods per week
        foreach ($classes as $class) {
            switch ($class->code) {
                case 'PG': // Playgroup
                    $class->subjects()->attach([
                        $subjects->where('code', 'RHYME')->first()->id => ['periods_per_week' => 3],
                        $subjects->where('code', 'DRAW')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'PE')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'CRAFT')->first()->id => ['periods_per_week' => 1]
                    ]);
                    break;

                case 'NUR': // Nursery
                    $class->subjects()->attach([
                        $subjects->where('code', 'ENG')->first()->id => ['periods_per_week' => 3],
                        $subjects->where('code', 'RHYME')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'DRAW')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'PE')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'CRAFT')->first()->id => ['periods_per_week' => 1]
                    ]);
                    break;

                case 'JKG': // Jr. KG
                    $class->subjects()->attach([
                        $subjects->where('code', 'ENG')->first()->id => ['periods_per_week' => 4],
                        $subjects->where('code', 'HIN')->first()->id => ['periods_per_week' => 3],
                        $subjects->where('code', 'MATH')->first()->id => ['periods_per_week' => 3],
                        $subjects->where('code', 'DRAW')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'PE')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'CRAFT')->first()->id => ['periods_per_week' => 1],
                        $subjects->where('code', 'GK')->first()->id => ['periods_per_week' => 1]
                    ]);
                    break;

                case 'SKG': // Sr. KG
                    $class->subjects()->attach([
                        $subjects->where('code', 'ENG')->first()->id => ['periods_per_week' => 5],
                        $subjects->where('code', 'HIN')->first()->id => ['periods_per_week' => 4],
                        $subjects->where('code', 'MATH')->first()->id => ['periods_per_week' => 4],
                        $subjects->where('code', 'GK')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'DRAW')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'PE')->first()->id => ['periods_per_week' => 2],
                        $subjects->where('code', 'CRAFT')->first()->id => ['periods_per_week' => 1]
                    ]);
                    break;
            }
        }
    }
}