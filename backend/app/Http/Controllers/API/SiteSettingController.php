<?php

namespace App\Http\Controllers\API;

use App\Models\SiteSetting;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SiteSettingController extends Controller
{
    /**
     * Display a listing of all settings.
     */
    public function index(Request $request)
    {
        $query = SiteSetting::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Search by key or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('setting_key', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $settings = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get all settings as key-value pairs (for frontend)
     */
    public function getAll(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $company = Company::find($companyId);
        
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $company->getAllSettings()
        ]);
    }

    /**
     * Get a specific setting by key.
     */
    public function getByKey($key, Request $request)
    {
        $companyId = $request->get('company_id', 1);
        
        $setting = SiteSetting::forCompany($companyId)
            ->byKey($key)
            ->with('company')
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Get a specific setting value only.
     */
    public function getValue($key, Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $default = $request->get('default');
        
        $setting = SiteSetting::forCompany($companyId)
            ->byKey($key)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $setting ? $setting->setting_value : $default
            ]
        ]);
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'setting_key' => 'required|string|max:255|unique:site_settings',
            'setting_value' => 'required',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        $setting = SiteSetting::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Setting created successfully',
            'data' => $setting->load('company')
        ], 201);
    }

    /**
     * Display the specified setting.
     */
    public function show($id)
    {
        $setting = SiteSetting::with('company')->find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, $id)
    {
        $setting = SiteSetting::find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'setting_key' => 'sometimes|required|string|max:255|unique:site_settings,setting_key,' . $id . ',setting_id',
            'setting_value' => 'sometimes|required',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $setting->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting->load('company')
        ]);
    }

    /**
     * Update setting value by key.
     */
    public function updateByKey($key, Request $request)
    {
        $companyId = $request->get('company_id', 1);
        
        $setting = SiteSetting::forCompany($companyId)
            ->byKey($key)
            ->first();

        if (!$setting) {
            // Create if doesn't exist
            $validator = Validator::make($request->all(), [
                'value' => 'required',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $setting = SiteSetting::create([
                'company_id' => $companyId,
                'setting_key' => $key,
                'setting_value' => $request->value,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully',
                'data' => $setting
            ], 201);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $setting->setting_value = $request->value;
        if ($request->has('description')) {
            $setting->description = $request->description;
        }
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting
        ]);
    }

    /**
     * Update multiple settings at once.
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:255',
            'settings.*.value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $request->get('company_id', 1);
        $results = [];

        foreach ($request->settings as $item) {
            $setting = SiteSetting::forCompany($companyId)
                ->byKey($item['key'])
                ->first();

            if ($setting) {
                $setting->setting_value = $item['value'];
                $setting->save();
                $results[] = $setting;
            } else {
                $setting = SiteSetting::create([
                    'company_id' => $companyId,
                    'setting_key' => $item['key'],
                    'setting_value' => $item['value']
                ]);
                $results[] = $setting;
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($results) . ' settings updated successfully',
            'data' => $results
        ]);
    }

    /**
     * Remove the specified setting.
     */
    public function destroy($id)
    {
        $setting = SiteSetting::find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully'
        ]);
    }

    /**
     * Delete setting by key.
     */
    public function deleteByKey($key, Request $request)
    {
        $companyId = $request->get('company_id', 1);
        
        $setting = SiteSetting::forCompany($companyId)
            ->byKey($key)
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully'
        ]);
    }

    /**
     * Bulk delete settings.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:site_settings,setting_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        SiteSetting::whereIn('setting_id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' settings deleted successfully'
        ]);
    }

    /**
     * Reset settings to defaults.
     */
    public function resetToDefaults(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        
        // Delete all existing settings
        SiteSetting::forCompany($companyId)->delete();
        
        // Create default settings
        $defaults = $this->getDefaultSettings();
        
        foreach ($defaults as $key => $value) {
            SiteSetting::create([
                'company_id' => $companyId,
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings reset to defaults successfully'
        ]);
    }

    /**
     * Get default settings.
     */
    private function getDefaultSettings()
    {
        return [
            'site_name' => 'Shakuniya Solutions',
            'site_description' => 'Leading digital transformation company',
            'contact_email' => 'info@shakuniyasolutions.com',
            'contact_phone' => '+91 1234567890',
            'address' => 'Mumbai, India',
            'social_media' => [
                'facebook' => 'https://facebook.com/shakuniyasolutions',
                'twitter' => 'https://twitter.com/shakuniyasol',
                'linkedin' => 'https://linkedin.com/company/shakuniyasolutions',
                'instagram' => 'https://instagram.com/shakuniyasolutions'
            ],
            'seo' => [
                'meta_title' => 'Shakuniya Solutions - Digital Transformation Company',
                'meta_description' => 'Leading digital transformation company providing innovative solutions.',
                'meta_keywords' => 'digital transformation, web development, mobile apps, seo'
            ],
            'theme' => [
                'primary_color' => '#9333ea',
                'secondary_color' => '#00d9ff',
                'font_family' => 'Inter, sans-serif'
            ],
            'features' => [
                'enable_blog' => true,
                'enable_testimonials' => true,
                'enable_contact_form' => true,
                'enable_chat' => false
            ]
        ];
    }
}