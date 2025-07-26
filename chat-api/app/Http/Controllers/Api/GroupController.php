<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Create a new group (Admin only)
     */
    public function createGroup(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can create groups'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255|unique:chat_groups,group_name',
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Remove duplicates and current user from member list
        $memberIds = array_unique($request->member_ids);
        $currentUserId = auth()->id();
        
        // Add current user (admin) to the group if not already included
        if (!in_array($currentUserId, $memberIds)) {
            $memberIds[] = $currentUserId;
        }

        try {
            DB::beginTransaction();

            // Create group
            $group = ChatGroup::create([
                'group_name' => $request->group_name,
                'created_by' => $currentUserId,
            ]);

            // Add members to group
            foreach ($memberIds as $memberId) {
                ChatGroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $memberId,
                ]);
            }

            DB::commit();

            // Load relationships for response
            $group->load(['creator', 'members']);

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'data' => [
                    'group' => $this->formatGroup($group)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all groups for current user
     */
    public function getUserGroups()
    {
        $currentUserId = auth()->id();

        $groups = ChatGroup::whereHas('members', function($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId);
        })->with(['creator', 'members', 'latestMessage.sender'])->get();

        return response()->json([
            'success' => true,
            'message' => 'User groups retrieved successfully',
            'data' => [
                'groups' => $groups->map(function($group) {
                    return $this->formatGroup($group);
                })
            ]
        ]);
    }

    /**
     * Get group details
     */
    public function getGroupDetails($groupId)
    {
        $group = ChatGroup::with(['creator', 'members'])->find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        // Check if user is member of the group
        if (!$group->hasMember(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Group details retrieved successfully',
            'data' => [
                'group' => $this->formatGroup($group)
            ]
        ]);
    }

    /**
     * Add users to group (Admin only)
     */
    public function addUsersToGroup(Request $request, $groupId)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can add users to groups'
            ], 403);
        }

        $group = ChatGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userIds = array_unique($request->user_ids);
        $addedUsers = [];

        try {
            DB::beginTransaction();

            foreach ($userIds as $userId) {
                if (!$group->hasMember($userId)) {
                    $group->addMember($userId);
                    $addedUsers[] = User::find($userId);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($addedUsers) . ' user(s) added to group successfully',
                'data' => [
                    'added_users' => collect($addedUsers)->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add users to group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove user from group (Admin only)
     */
    public function removeUserFromGroup(Request $request, $groupId)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can remove users from groups'
            ], 403);
        }

        $group = ChatGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user_id;

        // Check if user is member of the group
        if (!$group->hasMember($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this group'
            ], 422);
        }

        // Prevent removing the group creator
        if ($group->created_by == $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the group creator'
            ], 422);
        }

        try {
            $group->removeMember($userId);
            $removedUser = User::find($userId);

            return response()->json([
                'success' => true,
                'message' => 'User removed from group successfully',
                'data' => [
                    'removed_user' => [
                        'id' => $removedUser->id,
                        'name' => $removedUser->name,
                        'email' => $removedUser->email,
                        'role' => $removedUser->role,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove user from group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update group details (Admin only)
     */
    public function updateGroup(Request $request, $groupId)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can update groups'
            ], 403);
        }

        $group = ChatGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_name' => 'sometimes|required|string|max:255|unique:chat_groups,group_name,' . $groupId,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];
            
            if ($request->has('group_name')) {
                $updateData['group_name'] = $request->group_name;
            }

            $group->update($updateData);
            $group->load(['creator', 'members']);

            return response()->json([
                'success' => true,
                'message' => 'Group updated successfully',
                'data' => [
                    'group' => $this->formatGroup($group)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete group (Admin only)
     */
    public function deleteGroup($groupId)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can delete groups'
            ], 403);
        }

        $group = ChatGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        try {
            $groupName = $group->group_name;
            $group->delete(); // This will cascade delete members and messages

            return response()->json([
                'success' => true,
                'message' => "Group '$groupName' deleted successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Leave group (any member can leave, except creator)
     */
    public function leaveGroup($groupId)
    {
        $group = ChatGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        $currentUserId = auth()->id();

        // Check if user is member of the group
        if (!$group->hasMember($currentUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group'
            ], 422);
        }

        // Prevent creator from leaving
        if ($group->created_by == $currentUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Group creator cannot leave the group. Delete the group instead.'
            ], 422);
        }

        try {
            $group->removeMember($currentUserId);

            return response()->json([
                'success' => true,
                'message' => 'You have left the group successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users for adding to groups (Admin only)
     */
    public function getAvailableUsers()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can view available users'
            ], 403);
        }

        $users = User::select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Available users retrieved successfully',
            'data' => [
                'users' => $users
            ]
        ]);
    }

    /**
     * Get all groups (Admin only)
     */
    public function getAllGroups()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can view all groups'
            ], 403);
        }

        $groups = ChatGroup::with(['creator', 'members', 'latestMessage.sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'All groups retrieved successfully',
            'data' => [
                'groups' => $groups->map(function($group) {
                    return $this->formatGroup($group);
                })
            ]
        ]);
    }

    /**
     * Format group for response
     */
    private function formatGroup($group)
    {
        return [
            'id' => $group->id,
            'group_name' => $group->group_name,
            'created_by' => [
                'id' => $group->creator->id,
                'name' => $group->creator->name,
                'role' => $group->creator->role,
            ],
            'members' => $group->members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->role,
                ];
            }),
            'members_count' => $group->members->count(),
            'latest_message' => $group->latestMessage ? [
                'message' => $group->latestMessage->message,
                'sender_name' => $group->latestMessage->sender->name,
                'created_at' => $group->latestMessage->created_at,
            ] : null,
            'created_at' => $group->created_at,
            'updated_at' => $group->updated_at,
        ];
    }
}
