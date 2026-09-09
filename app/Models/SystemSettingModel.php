<?php
namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['setting_key', 'setting_value', 'description'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    public function setSetting($key, $value, $description = null)
    {
        $existing = $this->where('setting_key', $key)->first();
        
        if ($existing) {
            return $this->update($existing['id'], ['setting_value' => $value]);
        } else {
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'description' => $description
            ]);
        }
    }

    public function getCurrentTerm()
    {
        return (int) $this->getSetting('current_term', 1);
    }

    public function setCurrentTerm($term)
    {
        return $this->setSetting('current_term', $term, 'Current active term for grading');
    }
}