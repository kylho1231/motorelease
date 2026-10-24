<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'body_template',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Parse template tags like {customer_name}, {model}, {branch_name}, {tracking_ref}
     */
    public function render(array $placeholders): string
    {
        $text = $this->body_template;
        foreach ($placeholders as $key => $val) {
            $text = str_replace('{' . $key . '}', (string) $val, $text);
        }
        return $text;
    }
}
