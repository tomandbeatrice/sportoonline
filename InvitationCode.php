namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationCode extends Model
{
    protected $fillable = [
        'code',
        'inviter_id',
        'used_by_id',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];

    // 🔗 Davet eden satıcı
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'inviter_id');
    }

    // 🔗 Daveti kullanan satıcı
    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'used_by_id');
    }

    // ✅ Kullanıldı mı?
    public function isUsed(): bool
    {
        return !is_null($this->used_by_id);
    }

    // 📅 Kullanım zamanı formatlı
    public function usedAtFormatted(): string
    {
        return $this->used_at ? $this->used_at->format('d.m.Y H:i') : '-';
    }
}