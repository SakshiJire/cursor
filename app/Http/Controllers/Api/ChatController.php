<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // Chat Groups
    public function getGroups(Request $request)
    {
        $user = $request->user();
        
        $groups = ChatGroup::whereHas('members', function($query) use ($user) {
                              $query->where('user_id', $user->id)
                                    ->where('is_active', true);
                          })
                          ->with(['creator', 'class', 'members.user'])
                          ->where('is_active', true)
                          ->orderBy('updated_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:public,private,class,staff,parent',
            'class_id' => 'nullable|exists:classes,id',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            $group = ChatGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'created_by' => $request->user()->id,
                'class_id' => $request->class_id
            ]);

            // Add creator as admin
            ChatGroupMember::create([
                'group_id' => $group->id,
                'user_id' => $request->user()->id,
                'role' => 'admin',
                'joined_at' => now()
            ]);

            // Add other members
            foreach ($request->members as $memberId) {
                if ($memberId != $request->user()->id) {
                    ChatGroupMember::create([
                        'group_id' => $group->id,
                        'user_id' => $memberId,
                        'role' => 'member',
                        'joined_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'data' => $group->load(['creator', 'members.user'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateGroup(Request $request, $groupId)
    {
        $group = ChatGroup::findOrFail($groupId);
        
        // Check if user is admin of the group
        $membership = ChatGroupMember::where('group_id', $groupId)
                                    ->where('user_id', $request->user()->id)
                                    ->where('role', 'admin')
                                    ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this group'
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $updateData = $request->only(['name', 'description']);

        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $request->file('avatar')->store('group-avatars', 'public');
        }

        $group->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'data' => $group->fresh()
        ]);
    }

    public function addMembers(Request $request, $groupId)
    {
        $request->validate([
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id'
        ]);

        $group = ChatGroup::findOrFail($groupId);
        
        // Check if user is admin or moderator
        $membership = ChatGroupMember::where('group_id', $groupId)
                                    ->where('user_id', $request->user()->id)
                                    ->whereIn('role', ['admin', 'moderator'])
                                    ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to add members'
            ], 403);
        }

        $addedMembers = 0;

        foreach ($request->members as $memberId) {
            $existingMember = ChatGroupMember::where('group_id', $groupId)
                                            ->where('user_id', $memberId)
                                            ->first();

            if (!$existingMember) {
                ChatGroupMember::create([
                    'group_id' => $groupId,
                    'user_id' => $memberId,
                    'role' => 'member',
                    'joined_at' => now()
                ]);
                $addedMembers++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$addedMembers} members added successfully"
        ]);
    }

    public function removeMembers(Request $request, $groupId)
    {
        $request->validate([
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id'
        ]);

        // Check if user is admin
        $membership = ChatGroupMember::where('group_id', $groupId)
                                    ->where('user_id', $request->user()->id)
                                    ->where('role', 'admin')
                                    ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to remove members'
            ], 403);
        }

        $removedMembers = ChatGroupMember::where('group_id', $groupId)
                                        ->whereIn('user_id', $request->members)
                                        ->delete();

        return response()->json([
            'success' => true,
            'message' => "{$removedMembers} members removed successfully"
        ]);
    }

    // Messages
    public function getMessages(Request $request, $groupId = null, $userId = null)
    {
        $user = $request->user();
        $query = Message::with(['sender', 'receiver', 'group', 'repliedToMessage.sender', 'reactions.user']);

        if ($groupId) {
            // Group messages
            $query->where('group_id', $groupId);
            
            // Check if user is member of the group
            $isMember = ChatGroupMember::where('group_id', $groupId)
                                      ->where('user_id', $user->id)
                                      ->where('is_active', true)
                                      ->exists();
            
            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

        } elseif ($userId) {
            // Private messages
            $query->where(function($q) use ($user, $userId) {
                $q->where(function($subQ) use ($user, $userId) {
                    $subQ->where('sender_id', $user->id)
                         ->where('receiver_id', $userId);
                })->orWhere(function($subQ) use ($user, $userId) {
                    $subQ->where('sender_id', $userId)
                         ->where('receiver_id', $user->id);
                });
            });
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Either group_id or user_id is required'
            ], 400);
        }

        $messages = $query->where('is_deleted', false)
                         ->orderBy('created_at', 'desc')
                         ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required_without:file|string',
            'type' => 'required|in:text,image,file,voice,announcement',
            'group_id' => 'nullable|exists:chat_groups,id',
            'receiver_id' => 'nullable|exists:users,id',
            'replied_to' => 'nullable|exists:messages,id',
            'file' => 'sometimes|file|mimes:jpeg,png,jpg,pdf,doc,docx,mp3,wav|max:10240'
        ]);

        // Validate that either group_id or receiver_id is provided
        if (!$request->group_id && !$request->receiver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Either group_id or receiver_id is required'
            ], 400);
        }

        $user = $request->user();
        $messageData = [
            'sender_id' => $user->id,
            'content' => $request->content,
            'type' => $request->type,
            'group_id' => $request->group_id,
            'receiver_id' => $request->receiver_id,
            'replied_to' => $request->replied_to
        ];

        // Check permissions for group messages
        if ($request->group_id) {
            $isMember = ChatGroupMember::where('group_id', $request->group_id)
                                      ->where('user_id', $user->id)
                                      ->where('is_active', true)
                                      ->exists();
            
            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('messages', 'public');
            
            $messageData['file_path'] = $path;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_size'] = $file->getSize();
        }

        $message = Message::create($messageData);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message->load(['sender', 'receiver', 'group', 'repliedToMessage.sender'])
        ], 201);
    }

    public function updateMessage(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // Check if user is the sender
        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own messages'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string'
        ]);

        $message->update([
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message updated successfully',
            'data' => $message->fresh()
        ]);
    }

    public function deleteMessage(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // Check if user is the sender
        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own messages'
            ], 403);
        }

        $message->update(['is_deleted' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    public function markAsRead(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        $user = $request->user();

        // Check if user is the receiver (for private messages) or member of group
        $canMarkAsRead = false;

        if ($message->receiver_id === $user->id) {
            $canMarkAsRead = true;
        } elseif ($message->group_id) {
            $canMarkAsRead = ChatGroupMember::where('group_id', $message->group_id)
                                          ->where('user_id', $user->id)
                                          ->where('is_active', true)
                                          ->exists();
        }

        if (!$canMarkAsRead) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot mark this message as read'
            ], 403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    public function reactToMessage(Request $request, $messageId)
    {
        $request->validate([
            'reaction' => 'required|string|max:10'
        ]);

        $message = Message::findOrFail($messageId);
        $user = $request->user();

        // Check if reaction already exists
        $existingReaction = MessageReaction::where('message_id', $messageId)
                                          ->where('user_id', $user->id)
                                          ->where('reaction', $request->reaction)
                                          ->first();

        if ($existingReaction) {
            // Remove reaction if it already exists
            $existingReaction->delete();
            $action = 'removed';
        } else {
            // Add new reaction
            MessageReaction::create([
                'message_id' => $messageId,
                'user_id' => $user->id,
                'reaction' => $request->reaction
            ]);
            $action = 'added';
        }

        return response()->json([
            'success' => true,
            'message' => "Reaction {$action} successfully"
        ]);
    }

    public function getPrivateChats(Request $request)
    {
        $user = $request->user();
        
        // Get all users that the current user has chatted with
        $chatUsers = Message::where(function($q) use ($user) {
                               $q->where('sender_id', $user->id)
                                 ->orWhere('receiver_id', $user->id);
                           })
                           ->whereNull('group_id')
                           ->where('is_deleted', false)
                           ->with(['sender', 'receiver'])
                           ->orderBy('created_at', 'desc')
                           ->get()
                           ->map(function($message) use ($user) {
                               return $message->sender_id === $user->id ? 
                                      $message->receiver : $message->sender;
                           })
                           ->unique('id')
                           ->values();

        return response()->json([
            'success' => true,
            'data' => $chatUsers
        ]);
    }
}