<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['class', 'academicYear', 'subject', 'staff']);

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->has('day')) {
            $query->where('day', $request->day);
        }

        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $timetables = $query->orderBy('day')
                           ->orderBy('start_time')
                           ->get();

        // Group by day for better presentation
        $groupedTimetables = $timetables->groupBy('day');

        return response()->json([
            'success' => true,
            'data' => $groupedTimetables
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'subject_id' => 'required|exists:subjects,id',
            'staff_id' => 'nullable|exists:staff,id',
            'room' => 'nullable|string',
            'period_type' => 'required|in:regular,break,lunch,activity',
            'notes' => 'nullable|string'
        ]);

        // Check for time conflicts
        $conflict = Timetable::where('class_id', $request->class_id)
                            ->where('academic_year_id', $request->academic_year_id)
                            ->where('day', $request->day)
                            ->where(function($query) use ($request) {
                                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                                      ->orWhere(function($q) use ($request) {
                                          $q->where('start_time', '<=', $request->start_time)
                                            ->where('end_time', '>=', $request->end_time);
                                      });
                            })
                            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Time slot conflicts with existing timetable entry'
            ], 422);
        }

        $timetable = Timetable::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Timetable entry created successfully',
            'data' => $timetable->load(['class', 'academicYear', 'subject', 'staff'])
        ], 201);
    }

    public function show($id)
    {
        $timetable = Timetable::with(['class', 'academicYear', 'subject', 'staff'])
                             ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $timetable
        ]);
    }

    public function update(Request $request, $id)
    {
        $timetable = Timetable::findOrFail($id);

        $request->validate([
            'day' => 'sometimes|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'subject_id' => 'sometimes|exists:subjects,id',
            'staff_id' => 'nullable|exists:staff,id',
            'room' => 'nullable|string',
            'period_type' => 'sometimes|in:regular,break,lunch,activity',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        // Check for time conflicts if time is being updated
        if ($request->has('start_time') || $request->has('end_time') || $request->has('day')) {
            $startTime = $request->get('start_time', $timetable->start_time);
            $endTime = $request->get('end_time', $timetable->end_time);
            $day = $request->get('day', $timetable->day);

            $conflict = Timetable::where('class_id', $timetable->class_id)
                                ->where('academic_year_id', $timetable->academic_year_id)
                                ->where('day', $day)
                                ->where('id', '!=', $id)
                                ->where(function($query) use ($startTime, $endTime) {
                                    $query->whereBetween('start_time', [$startTime, $endTime])
                                          ->orWhereBetween('end_time', [$startTime, $endTime])
                                          ->orWhere(function($q) use ($startTime, $endTime) {
                                              $q->where('start_time', '<=', $startTime)
                                                ->where('end_time', '>=', $endTime);
                                          });
                                })
                                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Time slot conflicts with existing timetable entry'
                ], 422);
            }
        }

        $timetable->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Timetable updated successfully',
            'data' => $timetable->fresh()->load(['class', 'academicYear', 'subject', 'staff'])
        ]);
    }

    public function destroy($id)
    {
        $timetable = Timetable::findOrFail($id);
        $timetable->delete();

        return response()->json([
            'success' => true,
            'message' => 'Timetable entry deleted successfully'
        ]);
    }

    public function getClassTimetable($classId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        $query = Timetable::with(['subject', 'staff'])
                          ->where('class_id', $classId)
                          ->where('is_active', true);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $timetables = $query->orderBy('day')
                           ->orderBy('start_time')
                           ->get();

        $groupedTimetables = $timetables->groupBy('day');

        return response()->json([
            'success' => true,
            'data' => $groupedTimetables
        ]);
    }

    public function getStaffTimetable($staffId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        $query = Timetable::with(['class', 'subject'])
                          ->where('staff_id', $staffId)
                          ->where('is_active', true);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $timetables = $query->orderBy('day')
                           ->orderBy('start_time')
                           ->get();

        $groupedTimetables = $timetables->groupBy('day');

        return response()->json([
            'success' => true,
            'data' => $groupedTimetables
        ]);
    }

    public function generateTimetable(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'periods_per_day' => 'required|integer|min:4|max:8',
            'period_duration' => 'required|integer|min:30|max:60', // minutes
            'start_time' => 'required|date_format:H:i',
            'break_duration' => 'required|integer|min:15|max:30',
            'lunch_duration' => 'required|integer|min:30|max:60',
            'days' => 'required|array|min:5',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday'
        ]);

        $class = SchoolClass::with('subjects')->findOrFail($request->class_id);
        
        if ($class->subjects->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No subjects assigned to this class'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Clear existing timetable for this class and academic year
            Timetable::where('class_id', $request->class_id)
                     ->where('academic_year_id', $request->academic_year_id)
                     ->delete();

            $subjects = $class->subjects;
            $totalPeriodsPerWeek = $subjects->sum('pivot.periods_per_week');
            $periodsPerDay = $request->periods_per_day;
            $days = $request->days;

            // Calculate time slots
            $startTime = $request->start_time;
            $periodDuration = $request->period_duration;
            $breakDuration = $request->break_duration;
            $lunchDuration = $request->lunch_duration;

            $createdEntries = 0;

            foreach ($days as $day) {
                $currentTime = $startTime;
                $periodCount = 0;

                for ($period = 1; $period <= $periodsPerDay; $period++) {
                    $endTime = date('H:i', strtotime($currentTime . " +{$periodDuration} minutes"));

                    // Add break after 2nd period
                    if ($period == 3) {
                        Timetable::create([
                            'class_id' => $request->class_id,
                            'academic_year_id' => $request->academic_year_id,
                            'day' => $day,
                            'start_time' => $currentTime,
                            'end_time' => date('H:i', strtotime($currentTime . " +{$breakDuration} minutes")),
                            'subject_id' => $subjects->first()->id, // Dummy subject for break
                            'period_type' => 'break',
                            'notes' => 'Tea Break'
                        ]);
                        $currentTime = date('H:i', strtotime($currentTime . " +{$breakDuration} minutes"));
                        continue;
                    }

                    // Add lunch after 4th period
                    if ($period == 5) {
                        Timetable::create([
                            'class_id' => $request->class_id,
                            'academic_year_id' => $request->academic_year_id,
                            'day' => $day,
                            'start_time' => $currentTime,
                            'end_time' => date('H:i', strtotime($currentTime . " +{$lunchDuration} minutes")),
                            'subject_id' => $subjects->first()->id, // Dummy subject for lunch
                            'period_type' => 'lunch',
                            'notes' => 'Lunch Break'
                        ]);
                        $currentTime = date('H:i', strtotime($currentTime . " +{$lunchDuration} minutes"));
                        continue;
                    }

                    // Assign subject based on weekly distribution
                    $subject = $this->getNextSubject($subjects, $day, $periodCount);

                    if ($subject) {
                        Timetable::create([
                            'class_id' => $request->class_id,
                            'academic_year_id' => $request->academic_year_id,
                            'day' => $day,
                            'start_time' => $currentTime,
                            'end_time' => $endTime,
                            'subject_id' => $subject->id,
                            'period_type' => 'regular',
                            'is_active' => true
                        ]);

                        $createdEntries++;
                        $periodCount++;
                    }

                    $currentTime = $endTime;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Timetable generated successfully with {$createdEntries} periods",
                'data' => $this->getClassTimetable($request->class_id, $request)->getData()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate timetable',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getNextSubject($subjects, $day, $periodCount)
    {
        // Simple round-robin distribution
        $totalSubjects = $subjects->count();
        $subjectIndex = $periodCount % $totalSubjects;
        
        return $subjects->values()[$subjectIndex] ?? $subjects->first();
    }

    public function copyTimetable(Request $request)
    {
        $request->validate([
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id',
            'from_academic_year_id' => 'required|exists:academic_years,id',
            'to_academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $sourceTimetables = Timetable::where('class_id', $request->from_class_id)
                                    ->where('academic_year_id', $request->from_academic_year_id)
                                    ->get();

        if ($sourceTimetables->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No timetable found for the source class'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Clear existing timetable for destination
            Timetable::where('class_id', $request->to_class_id)
                     ->where('academic_year_id', $request->to_academic_year_id)
                     ->delete();

            $copiedEntries = 0;

            foreach ($sourceTimetables as $timetable) {
                Timetable::create([
                    'class_id' => $request->to_class_id,
                    'academic_year_id' => $request->to_academic_year_id,
                    'day' => $timetable->day,
                    'start_time' => $timetable->start_time,
                    'end_time' => $timetable->end_time,
                    'subject_id' => $timetable->subject_id,
                    'staff_id' => $timetable->staff_id,
                    'room' => $timetable->room,
                    'period_type' => $timetable->period_type,
                    'notes' => $timetable->notes,
                    'is_active' => $timetable->is_active
                ]);

                $copiedEntries++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Timetable copied successfully. {$copiedEntries} entries created."
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to copy timetable',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}