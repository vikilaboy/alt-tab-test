<?php namespace App\Models;

/**
 * App\Models\Message
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $message
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Message extends APIModel
{
    protected $fillable = [
        'user_id', 'message'
    ];
}
