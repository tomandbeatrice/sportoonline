namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBranding extends Model
{
    protected $fillable = [
        'vendor_id',
        'logo_url',
        'primary_color',
        'secondary_color',
        'font_family',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}