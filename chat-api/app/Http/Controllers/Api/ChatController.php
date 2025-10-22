<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Attachment;
use App\Models\ChatGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by routes
    }

    /**
     * Send a private message to another user
     */
    public function sendPrivateMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $senderId = auth()->id();
        $receiverId = $request->receiver_id;

        // Check if sender is trying to send message to themselves
        if ($senderId == $receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send message to yourself'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create message
            $message = ChatMessage::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'message_type' => $request->hasFile('attachments') ? 'file' : 'text',
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $this->handleAttachments($request->file('attachments'), $message->id);
            }

            DB::commit();

            // Load relationships for response
            $message->load(['sender', 'receiver', 'attachments']);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'message' => $this->formatMessage($message)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message to a group
     */
    public function sendGroupMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:chat_groups,id',
            'message' => 'required|string',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $senderId = auth()->id();
        $groupId = $request->group_id;

        // Check if user is member of the group
        $group = ChatGroup::find($groupId);
        if (!$group->hasMember($senderId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Create message
            $message = ChatMessage::create([
                'sender_id' => $senderId,
                'group_id' => $groupId,
                'message' => $request->message,
                'message_type' => $request->hasFile('attachments') ? 'file' : 'text',
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $this->handleAttachments($request->file('attachments'), $message->id);
            }

            DB::commit();

            // Load relationships for response
            $message->load(['sender', 'group', 'attachments']);

            return response()->json([
                'success' => true,
                'message' => 'Group message sent successfully',
                'data' => [
                    'message' => $this->formatMessage($message)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send group message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get private conversation between two users
     */
    public function getPrivateConversation(Request $request, $userId)
    {
        $currentUserId = auth()->id();
        
        // Validate if the other user exists
        if (!User::find($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        $messages = ChatMessage::privateMessages($currentUserId, $userId)
            ->with(['sender', 'receiver', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Mark messages as seen (messages sent to current user)
        ChatMessage::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_seen', false)
            ->update(['is_seen' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Private conversation retrieved successfully',
            'data' => [
                'messages' => $messages->getCollection()->map(function ($message) {
                    return $this->formatMessage($message);
                }),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]
        ]);
    }

    /**
     * Get group conversation
     */
    public function getGroupConversation(Request $request, $groupId)
    {
        $currentUserId = auth()->id();
        
        // Check if group exists and user is member
        $group = ChatGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found'
            ], 404);
        }

        if (!$group->hasMember($currentUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group'
            ], 403);
        }

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        $messages = ChatMessage::groupMessages($groupId)
            ->with(['sender', 'group', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Group conversation retrieved successfully',
            'data' => [
                'group' => [
                    'id' => $group->id,
                    'group_name' => $group->group_name,
                    'created_by' => $group->creator->name,
                ],
                'messages' => $messages->getCollection()->map(function ($message) {
                    return $this->formatMessage($message);
                }),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]
        ]);
    }

    /**
     * Get chat previews (last message with each user/group)
     */
    public function getChatPreviews()
    {
        $currentUserId = auth()->id();

        // Get latest private conversations
        $privateConversations = DB::table('chat_messages as cm1')
            ->select([
                'cm1.*',
                'u.name as other_user_name',
                'u.email as other_user_email'
            ])
            ->join('users as u', function($join) use ($currentUserId) {
                $join->on(function($query) use ($currentUserId) {
                    $query->where('cm1.sender_id', $currentUserId)
                          ->whereColumn('u.id', 'cm1.receiver_id');
                })->orOn(function($query) use ($currentUserId) {
                    $query->where('cm1.receiver_id', $currentUserId)
                          ->whereColumn('u.id', 'cm1.sender_id');
                });
            })
            ->whereIn('cm1.id', function($query) use ($currentUserId) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('chat_messages as cm2')
                      ->where(function($q) use ($currentUserId) {
                          $q->where('cm2.sender_id', $currentUserId)
                            ->orWhere('cm2.receiver_id', $currentUserId);
                      })
                      ->whereNull('cm2.group_id')
                      ->groupBy(DB::raw('LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)'));
            })
            ->orderBy('cm1.created_at', 'desc')
            ->get();

        // Get latest group conversations
        $groupConversations = DB::table('chat_messages as cm')
            ->select([
                'cm.*',
                'cg.group_name',
                'cg.id as group_id'
            ])
            ->join('chat_groups as cg', 'cm.group_id', '=', 'cg.id')
            ->join('chat_group_members as cgm', 'cg.id', '=', 'cgm.group_id')
            ->where('cgm.user_id', $currentUserId)
            ->whereIn('cm.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('chat_messages as cm2')
                      ->whereNotNull('cm2.group_id')
                      ->groupBy('cm2.group_id');
            })
            ->orderBy('cm.created_at', 'desc')
            ->get();

        // Get unseen message counts
        $unseenCounts = [];
        
        // Private messages unseen count
        foreach ($privateConversations as $conversation) {
            $otherUserId = $conversation->sender_id == $currentUserId ? $conversation->receiver_id : $conversation->sender_id;
            $unseenCounts['private_' . $otherUserId] = ChatMessage::where('sender_id', $otherUserId)
                ->where('receiver_id', $currentUserId)
                ->where('is_seen', false)
                ->count();
        }

        // Group messages unseen count (not implemented for groups as it's more complex)

        return response()->json([
            'success' => true,
            'message' => 'Chat previews retrieved successfully',
            'data' => [
                'private_conversations' => $privateConversations->map(function($conversation) use ($currentUserId, $unseenCounts) {
                    $otherUserId = $conversation->sender_id == $currentUserId ? $conversation->receiver_id : $conversation->sender_id;
                    return [
                        'type' => 'private',
                        'user_id' => $otherUserId,
                        'user_name' => $conversation->other_user_name,
                        'user_email' => $conversation->other_user_email,
                        'last_message' => $conversation->message,
                        'last_message_time' => $conversation->created_at,
                        'unseen_count' => $unseenCounts['private_' . $otherUserId] ?? 0,
                    ];
                }),
                'group_conversations' => $groupConversations->map(function($conversation) {
                    return [
                        'type' => 'group',
                        'group_id' => $conversation->group_id,
                        'group_name' => $conversation->group_name,
                        'last_message' => $conversation->message,
                        'last_message_time' => $conversation->created_at,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Search messages
     */
    public function searchMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string|min:3',
            'type' => 'sometimes|in:private,group,all',
            'user_id' => 'sometimes|exists:users,id',
            'group_id' => 'sometimes|exists:chat_groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $currentUserId = auth()->id();
        $keyword = $request->keyword;
        $type = $request->get('type', 'all');

        $query = ChatMessage::search($keyword)
            ->with(['sender', 'receiver', 'group'])
            ->where(function($q) use ($currentUserId) {
                // User must be involved in the conversation
                $q->where('sender_id', $currentUserId)
                  ->orWhere('receiver_id', $currentUserId)
                  ->orWhereHas('group.members', function($subQuery) use ($currentUserId) {
                      $subQuery->where('user_id', $currentUserId);
                  });
            });

        // Filter by type
        if ($type === 'private') {
            $query->whereNull('group_id');
            if ($request->has('user_id')) {
                $query->where(function($q) use ($currentUserId, $request) {
                    $q->where('sender_id', $currentUserId)->where('receiver_id', $request->user_id)
                      ->orWhere('sender_id', $request->user_id)->where('receiver_id', $currentUserId);
                });
            }
        } elseif ($type === 'group') {
            $query->whereNotNull('group_id');
            if ($request->has('group_id')) {
                $query->where('group_id', $request->group_id);
            }
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => [
                'messages' => $messages->getCollection()->map(function ($message) {
                    return $this->formatMessage($message);
                }),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]
        ]);
    }

    /**
     * Mark message as seen
     */
    public function markAsSeen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message_id' => 'required|exists:chat_messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = ChatMessage::find($request->message_id);
        
        // Check if current user is the receiver
        if ($message->receiver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to mark this message as seen'
            ], 403);
        }

        $message->markAsSeen();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as seen'
        ]);
    }

    /**
     * Get unseen messages count
     */
    public function getUnseenCount()
    {
        $currentUserId = auth()->id();
        
        $unseenCount = ChatMessage::unseenFor($currentUserId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Unseen count retrieved successfully',
            'data' => [
                'unseen_count' => $unseenCount
            ]
        ]);
    }

    /**
     * Handle file attachments
     */
    private function handleAttachments($files, $messageId)
    {
        foreach ($files as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_attachments', $fileName, 'public');
            
            Attachment::create([
                'message_id' => $messageId,
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Format message for response
     */
    private function formatMessage($message)
    {
        return [
            'id' => $message->id,
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'role' => $message->sender->role,
            ],
            'receiver' => $message->receiver ? [
                'id' => $message->receiver->id,
                'name' => $message->receiver->name,
                'role' => $message->receiver->role,
            ] : null,
            'group' => $message->group ? [
                'id' => $message->group->id,
                'group_name' => $message->group->group_name,
            ] : null,
            'message' => $message->message,
            'message_type' => $message->message_type,
            'is_seen' => $message->is_seen,
            'attachments' => $message->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'file_url' => $attachment->file_url,
                    'file_type' => $attachment->file_type,
                    'original_name' => $attachment->original_name,
                    'file_size' => $attachment->formatted_file_size,
                ];
            }),
            'created_at' => $message->created_at,
            'updated_at' => $message->updated_at,
        ];
    }
}
