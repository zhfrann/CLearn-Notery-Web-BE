<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // use HasApiTokens, HasFactory, Notifiable;
    use HasApiTokens;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'status_akun',
        'deskripsi',
        'major_id',
        'semester_id',
        'faculty_id',
        'matkul_favorit',
        'foto_profil',
        // 'rating'
        'jumlah_like',
        'qr_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    public function getFotoProfilUrlAttribute()
    {
        // return $this->foto_profil
        //     ? asset('storage/' . $this->foto_profil)
        //     : asset('storage/foto_profil/default.jpg');

        return asset('storage/' . $this->foto_profil);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id', 'major_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'semester_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'seller_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    public function savedNotes(): HasMany
    {
        return $this->hasMany(SavedNote::class, 'user_id', 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id', 'user_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(UserWallet::class, 'user_id', 'user_id');
    }

    public function payoutMethods(): HasMany
    {
        return $this->hasMany(PayoutMethod::class, 'user_id', 'user_id');
    }

    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(WithdrawRequest::class, 'user_id', 'user_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function chatRoomsAsFirstUser(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'user_one_id', 'user_id');
    }

    public function chatRoomsAsSecondUser(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'user_two_id', 'user_id');
    }

    public function allChatRooms()
    {
        return ChatRoom::query()->where('user_one_id', $this->user_id)
            ->orWhere('user_two_id', $this->user_id);
    }

    public function reviewResponses(): HasMany
    {
        return $this->hasMany(ReviewResponse::class, 'seller_id', 'user_id');
    }

    public function reviewVotes(): HasMany
    {
        return $this->hasMany(ReviewVote::class, 'user_id', 'user_id');
    }

    public function favoriteCourses(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'matkul_favorit', 'course_id');
    }
}
