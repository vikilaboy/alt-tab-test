<?php namespace App\Models;

use Hash;
use Cache;
use Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthContract;

/**
 * App\Models\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 */
class User extends APIModel implements AuthContract
{
    use Authenticatable;

    protected $fillable = [
        'email', 'name', 'password',
    ];

    public function messages()
    {
        return $this->belongsToMany(Message::class)->withTimestamps();
    }

    public function setPasswordAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public static function create(array $attributes = [])
    {
        $user = parent::create($attributes);

        return $user;
    }

    public function update(array $attributes = [])
    {
        $updated = parent::update($attributes);

        return $updated;
    }
}


