<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Institute;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Student;
use App\Models\FeeStructure;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Institutes
        $preschool = Institute::create([
            'name' => 'Little Stars Preschool',
            'type' => 'preschool',
            'code' => 'LSP',
            'address' => '123 School Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'phone' => '+91-9876543210',
            'email' => 'admin@littlestars.edu',
            'website' => 'https://littlestars.edu',
            'description' => 'Premier preschool education',
            'status' => 'active'
        ]);

        $school = Institute::create([
            'name' => 'Bright Future School',
            'type' => 'school',
            'code' => 'BFS',
            'address' => '456 Education Lane',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'phone' => '+91-9876543211',
            'email' => 'admin@brightfuture.edu',
            'website' => 'https://brightfuture.edu',
            'description' => 'Quality school education from 1st to 10th',
            'status' => 'active'
        ]);

        $college = Institute::create([
            'name' => 'Excellence College',
            'type' => 'college',
            'code' => 'EC',
            'address' => '789 College Road',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'pincode' => '560001',
            'phone' => '+91-9876543212',
            'email' => 'admin@excellence.edu',
            'website' => 'https://excellence.edu',
            'description' => 'Higher education from 11th to PG',
            'status' => 'active'
        ]);

        // Create Super Admin
        $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@erp.com',
            'mobile' => '9999999999',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        // Create Institute Admins
        $preschoolAdmin = User::create([
            'institute_id' => $preschool->id,
            'first_name' => 'Preschool',
            'last_name' => 'Admin',
            'email' => 'admin@littlestars.edu',
            'mobile' => '9876543210',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        $schoolAdmin = User::create([
            'institute_id' => $school->id,
            'first_name' => 'School',
            'last_name' => 'Admin',
            'email' => 'admin@brightfuture.edu',
            'mobile' => '9876543211',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        $collegeAdmin = User::create([
            'institute_id' => $college->id,
            'first_name' => 'College',
            'last_name' => 'Admin',
            'email' => 'admin@excellence.edu',
            'mobile' => '9876543212',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Create Classes for Preschool
        $preschoolClasses = [
            ['name' => 'Playgroup', 'section' => null],
            ['name' => 'Nursery', 'section' => null],
            ['name' => 'Jr. KG', 'section' => null],
            ['name' => 'Sr. KG', 'section' => null],
        ];

        foreach ($preschoolClasses as $classData) {
            ClassModel::create([
                'institute_id' => $preschool->id,
                'name' => $classData['name'],
                'section' => $classData['section'],
                'capacity' => 20,
                'annual_fee' => 50000,
                'status' => 'active'
            ]);
        }

        // Create Classes for School
        $schoolClasses = [
            ['name' => 'Class 1', 'section' => 'A'],
            ['name' => 'Class 1', 'section' => 'B'],
            ['name' => 'Class 2', 'section' => 'A'],
            ['name' => 'Class 3', 'section' => 'A'],
            ['name' => 'Class 4', 'section' => 'A'],
            ['name' => 'Class 5', 'section' => 'A'],
            ['name' => 'Class 6', 'section' => 'A'],
            ['name' => 'Class 7', 'section' => 'A'],
            ['name' => 'Class 8', 'section' => 'A'],
            ['name' => 'Class 9', 'section' => 'A'],
            ['name' => 'Class 10', 'section' => 'A'],
        ];

        foreach ($schoolClasses as $classData) {
            ClassModel::create([
                'institute_id' => $school->id,
                'name' => $classData['name'],
                'section' => $classData['section'],
                'capacity' => 30,
                'annual_fee' => 75000,
                'status' => 'active'
            ]);
        }

        // Create Classes for College
        $collegeClasses = [
            ['name' => 'Class 11', 'section' => 'Science'],
            ['name' => 'Class 11', 'section' => 'Commerce'],
            ['name' => 'Class 12', 'section' => 'Science'],
            ['name' => 'Class 12', 'section' => 'Commerce'],
            ['name' => 'BSc', 'section' => 'Year 1'],
            ['name' => 'BSc', 'section' => 'Year 2'],
            ['name' => 'BSc', 'section' => 'Year 3'],
            ['name' => 'BCom', 'section' => 'Year 1'],
            ['name' => 'BCom', 'section' => 'Year 2'],
            ['name' => 'BCom', 'section' => 'Year 3'],
        ];

        foreach ($collegeClasses as $classData) {
            ClassModel::create([
                'institute_id' => $college->id,
                'name' => $classData['name'],
                'section' => $classData['section'],
                'capacity' => 40,
                'annual_fee' => 100000,
                'status' => 'active'
            ]);
        }

        // Create Subjects
        $subjects = [
            // Preschool subjects
            ['institute_id' => $preschool->id, 'name' => 'English', 'code' => 'ENG'],
            ['institute_id' => $preschool->id, 'name' => 'Math', 'code' => 'MATH'],
            ['institute_id' => $preschool->id, 'name' => 'Art & Craft', 'code' => 'ART'],
            
            // School subjects
            ['institute_id' => $school->id, 'name' => 'English', 'code' => 'ENG'],
            ['institute_id' => $school->id, 'name' => 'Mathematics', 'code' => 'MATH'],
            ['institute_id' => $school->id, 'name' => 'Science', 'code' => 'SCI'],
            ['institute_id' => $school->id, 'name' => 'Social Studies', 'code' => 'SST'],
            ['institute_id' => $school->id, 'name' => 'Hindi', 'code' => 'HIN'],
            
            // College subjects
            ['institute_id' => $college->id, 'name' => 'Physics', 'code' => 'PHY'],
            ['institute_id' => $college->id, 'name' => 'Chemistry', 'code' => 'CHEM'],
            ['institute_id' => $college->id, 'name' => 'Mathematics', 'code' => 'MATH'],
            ['institute_id' => $college->id, 'name' => 'Biology', 'code' => 'BIO'],
            ['institute_id' => $college->id, 'name' => 'Accountancy', 'code' => 'ACC'],
            ['institute_id' => $college->id, 'name' => 'Business Studies', 'code' => 'BS'],
            ['institute_id' => $college->id, 'name' => 'Economics', 'code' => 'ECO'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // Create Teachers
        $teachers = [
            [
                'institute_id' => $preschool->id,
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.j@littlestars.edu',
                'mobile' => '9876543220'
            ],
            [
                'institute_id' => $school->id,
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.s@brightfuture.edu',
                'mobile' => '9876543221'
            ],
            [
                'institute_id' => $school->id,
                'first_name' => 'Mary',
                'last_name' => 'Davis',
                'email' => 'mary.d@brightfuture.edu',
                'mobile' => '9876543222'
            ],
            [
                'institute_id' => $college->id,
                'first_name' => 'Dr. Robert',
                'last_name' => 'Wilson',
                'email' => 'robert.w@excellence.edu',
                'mobile' => '9876543223'
            ],
            [
                'institute_id' => $college->id,
                'first_name' => 'Dr. Emily',
                'last_name' => 'Brown',
                'email' => 'emily.b@excellence.edu',
                'mobile' => '9876543224'
            ],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'institute_id' => $teacher['institute_id'],
                'first_name' => $teacher['first_name'],
                'last_name' => $teacher['last_name'],
                'email' => $teacher['email'],
                'mobile' => $teacher['mobile'],
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'employee_id' => 'T' . rand(1000, 9999),
                'status' => 'active'
            ]);
        }

        // Create Sample Students
        $students = [
            [
                'institute_id' => $preschool->id,
                'class_name' => 'Jr. KG',
                'first_name' => 'Emma',
                'last_name' => 'Wilson',
                'email' => 'emma.w@parent.com',
                'mobile' => '9876543230',
                'father_name' => 'David Wilson',
                'mother_name' => 'Lisa Wilson',
                'father_phone' => '9876543231'
            ],
            [
                'institute_id' => $school->id,
                'class_name' => 'Class 5',
                'first_name' => 'Alex',
                'last_name' => 'Johnson',
                'email' => 'alex.j@parent.com',
                'mobile' => '9876543232',
                'father_name' => 'Mike Johnson',
                'mother_name' => 'Sarah Johnson',
                'father_phone' => '9876543233'
            ],
            [
                'institute_id' => $college->id,
                'class_name' => 'BSc',
                'first_name' => 'Raj',
                'last_name' => 'Patel',
                'email' => 'raj.p@student.com',
                'mobile' => '9876543234',
                'father_name' => 'Suresh Patel',
                'mother_name' => 'Priya Patel',
                'father_phone' => '9876543235'
            ],
        ];

        foreach ($students as $studentData) {
            // Create user account for student
            $studentUser = User::create([
                'institute_id' => $studentData['institute_id'],
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'email' => $studentData['email'],
                'mobile' => $studentData['mobile'],
                'password' => Hash::make('password123'),
                'role' => 'student',
                'date_of_birth' => '2010-01-01',
                'gender' => 'male',
                'address' => '123 Student Street',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'status' => 'active'
            ]);

            // Find the class
            $class = ClassModel::where('institute_id', $studentData['institute_id'])
                             ->where('name', $studentData['class_name'])
                             ->first();

            if ($class) {
                // Generate admission number
                $year = date('Y');
                $institute = Institute::find($studentData['institute_id']);
                $admissionNumber = $institute->code . $year . str_pad(rand(1, 999), 4, '0', STR_PAD_LEFT);

                // Create student record
                Student::create([
                    'user_id' => $studentUser->id,
                    'institute_id' => $studentData['institute_id'],
                    'class_id' => $class->id,
                    'admission_number' => $admissionNumber,
                    'admission_date' => now(),
                    'father_name' => $studentData['father_name'],
                    'mother_name' => $studentData['mother_name'],
                    'father_phone' => $studentData['father_phone'],
                    'status' => 'active'
                ]);
            }
        }

        // Create Fee Structures for each institute
        $institutes = Institute::all();
        foreach ($institutes as $institute) {
            $classes = $institute->classes;
            foreach ($classes as $class) {
                // Tuition fee
                FeeStructure::create([
                    'institute_id' => $institute->id,
                    'class_id' => $class->id,
                    'fee_type' => 'Tuition Fee',
                    'amount' => $class->annual_fee / 12, // Monthly fee
                    'frequency' => 'monthly',
                    'due_date' => now()->addDays(10),
                    'late_fee' => 500,
                    'grace_period_days' => 5,
                    'description' => 'Monthly tuition fee',
                    'status' => 'active'
                ]);

                // Additional fees
                $additionalFees = [
                    ['type' => 'Library Fee', 'amount' => 1000, 'frequency' => 'yearly'],
                    ['type' => 'Lab Fee', 'amount' => 2000, 'frequency' => 'yearly'],
                    ['type' => 'Sports Fee', 'amount' => 1500, 'frequency' => 'yearly'],
                ];

                foreach ($additionalFees as $fee) {
                    FeeStructure::create([
                        'institute_id' => $institute->id,
                        'class_id' => $class->id,
                        'fee_type' => $fee['type'],
                        'amount' => $fee['amount'],
                        'frequency' => $fee['frequency'],
                        'due_date' => now()->addDays(30),
                        'late_fee' => 200,
                        'grace_period_days' => 10,
                        'description' => $fee['type'] . ' for academic year',
                        'status' => 'active'
                    ]);
                }
            }
        }

        echo "Database seeded successfully!\n";
        echo "Login credentials:\n";
        echo "Super Admin: superadmin@erp.com / password123\n";
        echo "Preschool Admin: admin@littlestars.edu / password123\n";
        echo "School Admin: admin@brightfuture.edu / password123\n";
        echo "College Admin: admin@excellence.edu / password123\n";
        echo "All teachers and students: password123\n";
    }
}