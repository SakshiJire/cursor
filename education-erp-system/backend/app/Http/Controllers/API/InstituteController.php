<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class InstituteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Institute::query();
            
            // Apply filters
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                });
            }
            
            $institutes = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $institutes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch institutes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:preschool,school,college',
            'code' => 'required|string|max:255|unique:institutes',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('institute-logos', 'public');
                $data['logo'] = $logoPath;
            }
            
            $institute = Institute::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Institute created successfully',
                'data' => $institute
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create institute',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $institute = Institute::with(['users', 'classes'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $institute
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Institute not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:preschool,school,college',
            'code' => 'required|string|max:255|unique:institutes,code,' . $id,
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $institute = Institute::findOrFail($id);
            $data = $request->all();
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($institute->logo) {
                    Storage::disk('public')->delete($institute->logo);
                }
                $logoPath = $request->file('logo')->store('institute-logos', 'public');
                $data['logo'] = $logoPath;
            }
            
            $institute->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Institute updated successfully',
                'data' => $institute
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update institute',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $institute = Institute::findOrFail($id);
            
            // Check if institute has associated data
            if ($institute->users()->count() > 0 || $institute->classes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete institute with associated data'
                ], 400);
            }
            
            // Delete logo if exists
            if ($institute->logo) {
                Storage::disk('public')->delete($institute->logo);
            }
            
            $institute->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Institute deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete institute',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStats($id)
    {
        try {
            $institute = Institute::findOrFail($id);
            
            $stats = [
                'total_students' => $institute->students()->count(),
                'total_staff' => $institute->users()->where('role', '!=', 'student')->count(),
                'total_classes' => $institute->classes()->count(),
                'total_subjects' => $institute->subjects()->count(),
                'active_students' => $institute->students()->where('status', 'active')->count(),
                'inactive_students' => $institute->students()->where('status', 'inactive')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch institute stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}