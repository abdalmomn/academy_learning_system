<?php

namespace App\Services;

use App\DTO\CommentDTO;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CommentService
{
    public function getByVideo( $videoId)
    {
        try {
            $comments = Comment::with(['user', 'video'])
                ->where('video_id', $videoId)
                ->latest()
                ->paginate(10);

            if ($comments->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No comments found for this video'
                ];
            }

            return [
                'data' => $comments,
                'message' => 'Comments for video'
            ];
        } catch (Exception $e) {
            Log::error('Fetching comments failed', [
                'error' => $e->getMessage(),
                'video_id' => $videoId
            ]);
            return [
                'data' => null,
                'message' => 'Failed to fetch comments'
            ];
        }
    }

    public function getById($id)
    {
        try {
            $comment = Comment::with(['user', 'video'])->find($id);

            if (!$comment) {
                return [
                    'data' => null,
                    'message' => 'Comment not found'
                ];
            }

            return [
                'data' => $comment,
                'message' => 'Comment details'
            ];
        } catch (Exception $e) {
            Log::error('Fetching comment failed', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to fetch comment'
            ];
        }
    }

    public function create(CommentDTO $dto)
    {
        $user = Auth::user();
        if (!$user->hasRole(['woman', 'child'])) {
            return [
                'data' => null,
                'message' => 'only students can comment'
            ];
        }
        $video = Video::find($dto->video_id);
        if (!$video) {
            return [
                'data' => null,
                'message' => 'Video not found'
            ];
        }
        if ($video->is_comments_locked) {
            return [
                'data' => null,
                'message' => 'Comments are locked for this video'
            ];
        }

        DB::beginTransaction();
        try {
            $comment = Comment::create([
                'comment' => $dto->comment,
                'user_id' => $dto->user_id,
                'video_id' => $dto->video_id,
            ]);

            DB::commit();

            Log::info('Comment created', ['comment_id' => $comment->id]);

            return [
                'data' => $comment,
                'message' => 'Comment created successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Comment creation failed', [
                'error' => $e->getMessage(),
                'data' => $dto
            ]);
            return [
                'data' => null,
                'message' => 'Failed to create comment'
            ];
        }
    }

    public function update($id, CommentDTO $dto)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $video = Video::find($dto->video_id);
            if (!$video) {
                return [
                    'data' => null,
                    'message' => 'Video not found'
                ];
            }
            $comment = Comment::find($id);
            if (!$comment) {
                return [
                    'data' => null,
                    'message' => 'Comment not found'
                ];
            }
            if ($comment->video_id !== $dto->video_id) {
                return [
                    'data' => null,
                    'message' => 'This comment does not belong to the selected video'
                ];
            }

            if (!$user || $user->id !== $comment->user_id) {
                return [
                    'data' => null,
                    'message' => 'Unauthorized - only comment owner can edit'
                ];
            }

            $comment->update([
                'comment' => $dto->comment
            ]);

            DB::commit();

            Log::info('Comment updated', ['id' => $id]);

            return [
                'data' => $comment,
                'message' => 'Comment updated successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Comment update failed', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to update comment'
            ];
        }
    }

    public function delete($id,  $video_id)
    {

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $video = Video::find($video_id);
            if (!$video) {
                return [
                    'data' => null,
                    'message' => 'Video not found'
                ];
            }
            $comment = Comment::find($id);
            if (!$comment) {
                return [
                    'data' => null,
                    'message' => 'Comment not found'
                ];
            }
            if (!$user || !$user->hasRole( ['admin', 'supervisor'])&& $user->id !== $comment->user_id) {
                return [
                    'data' => null,
                    'message' => 'Unauthorized - only admin, supervisor, or comment owner can delete'
                ];
            }
            $comment->delete();

            DB::commit();

            Log::info('Comment deleted', ['id' => $id]);

            return [
                'data' => null,
                'message' => 'Comment deleted successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Comment deletion failed', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to delete comment'
            ];
        }
    }
    public function lockComments($videoId)
    {
        $user = Auth::user();

        if (!$user||!$user->hasRole( ['teacher', 'supervisor'])) {
            return [
                'data' => null,
                'message' => 'Unauthorized only teacher or supervisor can lock comments'
            ];
        }


        $video = Video::find($videoId);
        if (!$video) {
            return [
                'data' => null,
                'message' => 'Video not found'
            ];
        }

        $video->is_comments_locked = true;
        $video->save();

        return [
            'data' => $video,
            'message' => 'Comments locked for this video'
        ];
    }

    public function unlockComments($videoId)
    {
        $user = Auth::user();

        if (!$user ||!$user->hasRole( ['teacher', 'supervisor'])) {
            return [
                'data' => null,
                'message' => 'Unauthorized - only teacher or supervisor can unlock comments'
            ];
        }

        $video = Video::find($videoId);
        if (!$video) {
            return [
                'data' => null,
                'message' => 'Video not found'
            ];
        }

        $video->is_comments_locked = false;
        $video->save();

        return [
            'data' => $video,
            'message' => 'Comments unlocked for this video'
        ];
    }

}
