<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Institution;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Staff;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $roles = ['admin', 'student', 'parent', 'staff', 'teacher'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create institutions
        $preschool = Institution::create([
            'name' => 'Bright Minds Preschool',
            'code' => 'BMP001',
            'type' => 'preschool',
            'address' => '123 Education Lane, Learning City',
            'phone' => '+1234567890',
            'email' => 'info@brightminds.edu',
            'website' => 'https://brightminds.edu',
            'principal_name' => 'Dr. Sarah Johnson',
            'status' => 'active',
        ]);

        $school = Institution::create([
            'name' => 'Excellence High School',
            'code' => 'EHS001',
            'type' => 'school',
            'address' => '456 Knowledge Street, Education City',
            'phone' => '+1234567891',
            'email' => 'info@excellencehs.edu',
            'website' => 'https://excellencehs.edu',
            'principal_name' => 'Prof. Michael Brown',
            'status' => 'active',
        ]);

        $college = Institution::create([
            'name' => 'Pioneer College',
            'code' => 'PC001',
            'type' => 'college',
            'address' => '789 University Avenue, Higher Ed City',
            'phone' => '+1234567892',
            'email' => 'info@pioneercollege.edu',
            'website' => 'https://pioneercollege.edu',
            'principal_name' => 'Dr. Emily Davis',
            'status' => 'active',
        ]);

        // Create academic years
        $currentYear = AcademicYear::create([
            'institution_id' => $school->id,
            'name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        AcademicYear::create([
            'institution_id' => $preschool->id,
            'name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        AcademicYear::create([
            'institution_id' => $college->id,
            'name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        // Create classes for preschool
        $nursery = ClassModel::create([
            'institution_id' => $preschool->id,
            'academic_year_id' => $preschool->academicYears->first()->id,
            'name' => 'Nursery',
            'section' => 'A',
            'level' => 'nursery',
            'max_students' => 25,
            'status' => 'active',
        ]);

        $jrKg = ClassModel::create([
            'institution_id' => $preschool->id,
            'academic_year_id' => $preschool->academicYears->first()->id,
            'name' => 'Jr. KG',
            'section' => 'A',
            'level' => 'jr_kg',
            'max_students' => 30,
            'status' => 'active',
        ]);

        $srKg = ClassModel::create([
            'institution_id' => $preschool->id,
            'academic_year_id' => $preschool->academicYears->first()->id,
            'name' => 'Sr. KG',
            'section' => 'A',
            'level' => 'sr_kg',
            'max_students' => 30,
            'status' => 'active',
        ]);

        // Create classes for school
        $class1 = ClassModel::create([
            'institution_id' => $school->id,
            'academic_year_id' => $currentYear->id,
            'name' => '1st Grade',
            'section' => 'A',
            'level' => 'primary',
            'max_students' => 40,
            'status' => 'active',
        ]);

        $class5 = ClassModel::create([
            'institution_id' => $school->id,
            'academic_year_id' => $currentYear->id,
            'name' => '5th Grade',
            'section' => 'A',
            'level' => 'primary',
            'max_students' => 40,
            'status' => 'active',
        ]);

        $class10 = ClassModel::create([
            'institution_id' => $school->id,
            'academic_year_id' => $currentYear->id,
            'name' => '10th Grade',
            'section' => 'A',
            'level' => 'high',
            'max_students' => 35,
            'status' => 'active',
        ]);

        // Create classes for college
        $plus2Science = ClassModel::create([
            'institution_id' => $college->id,
            'academic_year_id' => $college->academicYears->first()->id,
            'name' => 'Plus Two Science',
            'section' => 'A',
            'level' => 'higher_secondary',
            'max_students' => 45,
            'status' => 'active',
        ]);

        $bsc = ClassModel::create([
            'institution_id' => $college->id,
            'academic_year_id' => $college->academicYears->first()->id,
            'name' => 'B.Sc. Computer Science',
            'section' => 'A',
            'level' => 'undergraduate',
            'max_students' => 50,
            'status' => 'active',
        ]);

        // Create subjects
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'institution_id' => $school->id],
            ['name' => 'English', 'code' => 'ENG', 'institution_id' => $school->id],
            ['name' => 'Science', 'code' => 'SCI', 'institution_id' => $school->id],
            ['name' => 'Social Studies', 'code' => 'SS', 'institution_id' => $school->id],
            ['name' => 'Physics', 'code' => 'PHY', 'institution_id' => $college->id],
            ['name' => 'Chemistry', 'code' => 'CHE', 'institution_id' => $college->id],
            ['name' => 'Biology', 'code' => 'BIO', 'institution_id' => $college->id],
            ['name' => 'Computer Science', 'code' => 'CS', 'institution_id' => $college->id],
        ];

        foreach ($subjects as $subject) {
            Subject::create(array_merge($subject, [
                'type' => 'core',
                'max_marks' => 100,
                'pass_marks' => 40,
                'status' => 'active',
            ]));
        }

        // Create admin users for each institution
        $adminPreschool = User::create([
            'institution_id' => $preschool->id,
            'first_name' => 'Admin',
            'last_name' => 'Preschool',
            'mobile' => '9999999999',
            'email' => 'admin@brightminds.edu',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
        ]);
        $adminPreschool->assignRole('admin');

        $adminSchool = User::create([
            'institution_id' => $school->id,
            'first_name' => 'Admin',
            'last_name' => 'School',
            'mobile' => '9999999998',
            'email' => 'admin@excellencehs.edu',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
        ]);
        $adminSchool->assignRole('admin');

        $adminCollege = User::create([
            'institution_id' => $college->id,
            'first_name' => 'Admin',
            'last_name' => 'College',
            'mobile' => '9999999997',
            'email' => 'admin@pioneercollege.edu',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
        ]);
        $adminCollege->assignRole('admin');

        // Create sample teachers
        $teacher1 = User::create([
            'institution_id' => $school->id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'mobile' => '9876543210',
            'email' => 'john.smith@excellencehs.edu',
            'password' => Hash::make('password'),
            'user_type' => 'teacher',
            'status' => 'active',
        ]);
        $teacher1->assignRole('teacher');

        Staff::create([
            'user_id' => $teacher1->id,
            'institution_id' => $school->id,
            'employee_id' => 'EMP001',
            'staff_type' => 'teaching',
            'designation' => 'Mathematics Teacher',
            'department' => 'Mathematics',
            'joining_date' => '2024-01-15',
            'basic_salary' => 50000,
            'status' => 'active',
        ]);

        $teacher2 = User::create([
            'institution_id' => $school->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'mobile' => '9876543211',
            'email' => 'jane.doe@excellencehs.edu',
            'password' => Hash::make('password'),
            'user_type' => 'teacher',
            'status' => 'active',
        ]);
        $teacher2->assignRole('teacher');

        Staff::create([
            'user_id' => $teacher2->id,
            'institution_id' => $school->id,
            'employee_id' => 'EMP002',
            'staff_type' => 'teaching',
            'designation' => 'English Teacher',
            'department' => 'English',
            'joining_date' => '2024-02-01',
            'basic_salary' => 48000,
            'status' => 'active',
        ]);

        // Create sample parents and students
        $parent1 = User::create([
            'institution_id' => $school->id,
            'first_name' => 'Robert',
            'last_name' => 'Johnson',
            'mobile' => '9876543212',
            'email' => 'robert.johnson@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'parent',
            'status' => 'active',
        ]);
        $parent1->assignRole('parent');

        $student1User = User::create([
            'institution_id' => $school->id,
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'mobile' => '9876543213',
            'email' => 'alice.johnson@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'student',
            'date_of_birth' => '2014-05-15',
            'gender' => 'female',
            'status' => 'active',
        ]);
        $student1User->assignRole('student');

        Student::create([
            'user_id' => $student1User->id,
            'institution_id' => $school->id,
            'academic_year_id' => $currentYear->id,
            'class_id' => $class5->id,
            'admission_number' => 'ADM2024001',
            'admission_date' => '2024-04-01',
            'roll_number' => '001',
            'parent_id' => $parent1->id,
            'father_name' => 'Robert Johnson',
            'mother_name' => 'Susan Johnson',
            'blood_group' => 'A+',
            'status' => 'active',
        ]);

        $parent2 = User::create([
            'institution_id' => $school->id,
            'first_name' => 'David',
            'last_name' => 'Wilson',
            'mobile' => '9876543214',
            'email' => 'david.wilson@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'parent',
            'status' => 'active',
        ]);
        $parent2->assignRole('parent');

        $student2User = User::create([
            'institution_id' => $school->id,
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'mobile' => '9876543215',
            'email' => 'bob.wilson@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'student',
            'date_of_birth' => '2009-08-20',
            'gender' => 'male',
            'status' => 'active',
        ]);
        $student2User->assignRole('student');

        Student::create([
            'user_id' => $student2User->id,
            'institution_id' => $school->id,
            'academic_year_id' => $currentYear->id,
            'class_id' => $class10->id,
            'admission_number' => 'ADM2024002',
            'admission_date' => '2024-04-01',
            'roll_number' => '001',
            'parent_id' => $parent2->id,
            'father_name' => 'David Wilson',
            'mother_name' => 'Lisa Wilson',
            'blood_group' => 'B+',
            'status' => 'active',
        ]);

        // Update class teacher assignments
        $class5->update(['class_teacher_id' => $teacher1->id]);
        $class10->update(['class_teacher_id' => $teacher2->id]);
    }
}
