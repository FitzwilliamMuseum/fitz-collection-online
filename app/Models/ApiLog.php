<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Models\ApiLog
 *
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog query()
 * @mixin \Eloquent
 */
class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_full_url',
        'request_method',
        'request_body',
        'request_header',
        'request_ip',
        'request_agent',
        'response_content',
        'response_status_code',
        'user_id',
        'user_timezone',
    ];

    protected $casts = [
        'request_header' => 'array',
        'request_body' => 'array',
        'response_content' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
