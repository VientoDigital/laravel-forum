<?php

namespace Vientodigital\LaravelForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Vientodigital\LaravelForum\Models\Discussion\User as DiscussionUser;

class Discussion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'comment_count', 'participant_count', 'post_number_index',
        'user_id', 'first_post_id', 'last_posted_at', 'last_posted_user_id', 'last_post_id',
        'is_private', 'is_approved', 'is_locked', 'is_sticky',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('laravel-forum.table_names.discussions', 'discussions'));
    }

    public function tags()
    {
        return $this->belongsToMany('\Vientodigital\LaravelForum\Models\Tag', config('laravel-forum.table_names.discussion_tags', 'discussion_tag'))->withPivot('tag_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(config('laravel-forum.models.user', 'App\Models\User'), 'user_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany('\Vientodigital\LaravelForum\Models\Post', 'discussion_id', 'id');
    }

    public function lastPost()
    {
        return  $this->hasOne('\Vientodigital\LaravelForum\Models\Post', 'discussion_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * Determines if some user has readed some post.
     *
     * @param int|string $userId if not defined it takes current user automatically from
     *                           Auth facade
     * @param mixed      $userId
     */
    public function isRead($userId = null)
    {
        if (!$userId && Auth::user()) {
            $userId = Auth::user()->id;
        }
        if (is_string($userId)) {
            $user = intval($userId);
        }
        $read = DiscussionUser::where('user_id', $userId)
            ->where('discussion_id', $this->id)
            ->first();

        return $read && $this->post_number_index === $read->last_read_post_number;
    }

    /**
     * Determines if some user can edit current discussion.
     *
     * @param int|string $userId if not defined it takes current user automatically from
     *                           Auth facade
     *
     * @return bool
     */
    public function canEdit($userId = null)
    {
        if (!$userId && Auth::user()) {
            $userId = Auth::user()->id;
        }
        if (is_string($userId)) {
            $user = intval($userId);
        }

        return $this->user_id === $userId;
    }
}
