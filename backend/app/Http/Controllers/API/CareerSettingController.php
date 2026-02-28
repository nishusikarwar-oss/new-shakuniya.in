<?php

namespace App\Http\Controllers\API;

use App\Models\CareerSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CareerSettingController extends Controller
{
    /**
     * Display a listing of all career settings.
     */
    public function index(Request $request)
    {
        $query = CareerSetting::query();

        // Search by key or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('setting_key', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $settings = $query->orderBy('setting_key')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => $settings
        ]);
    }

    /**
     * Get all settings as key-value pairs (for frontend).
     */
    public function getAll()
    {
        $settings = CareerSetting::getCareerPageSettings();

        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => $settings
        ]);
    }

    /**
     * Get career page settings (public).
     */
    public function getCareerPage()
    {
        $settings = CareerSetting::getCareerPageSettings();

        return response()->json([
            'success' => true,
            'message' => 'Career page settings retrieved successfully',
            'data' => [
                'page' => [
                    'title' => $settings['page_title'],
                    'subtitle' => $settings['page_subtitle'],
                    'header_image' => $settings['header_image']
                ],
                'seo' => [
                    'meta_title' => $settings['meta_title'],
                    'meta_description' => $settings['meta_description']
                ],
                'features' => [
                    'show_stats' => $settings['show_stats'],
                    'show_perks' => $settings['show_perks'],
                    'show_testimonials' => $settings['show_testimonials']
                ],
                'application_form' => $settings['application_form']
            ]
        ]);
    }

    /**
     * Get a specific setting by key.
     */
    public function getByKey($key)
    {
        $setting = CareerSetting::byKey($key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Setting retrieved successfully',
            'data' => $setting
        ]);
    }

    /**
     * Get a specific setting value only.
     */
    public function getValue($key, Request $request)
    {
        $default = $request->get('default');
        $value = CareerSetting::getValue($key, $default);

        return response()->json([
            'success' => true,
            'message' => 'Setting value retrieved successfully',
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'setting_key' => 'required|string|max:100|unique:career_settings',
            'setting_value' => 'required',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $setting = CareerSetting::create([
                'setting_key' => $request->setting_key,
                'setting_value' => $request->setting_value,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully',
                'data' => $setting
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified setting.
     */
    public function show($id)
    {
        $setting = CareerSetting::find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Setting retrieved successfully',
            'data' => $setting
        ]);
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, $id)
    {
        $setting = CareerSetting::find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'setting_key' => 'sometimes|required|string|max:100|unique:career_settings,setting_key,' . $id,
            'setting_value' => 'sometimes|required',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $setting->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update setting value by key.
     */
    public function updateByKey($key, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $setting = CareerSetting::setValue(
                $key,
                $request->value,
                $request->description
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update multiple settings at once.
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:100',
            'settings.*.value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = [];
            
            foreach ($request->settings as $item) {
                $setting = CareerSetting::setValue(
                    $item['key'],
                    $item['value'],
                    $item['description'] ?? null
                );
                $updated[] = $setting;
            }

            return response()->json([
                'success' => true,
                'message' => count($updated) . ' settings updated successfully',
                'data' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update career page settings (specific group).
     */
    public function updateCareerPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_title' => 'nullable|string|max:255',
            'page_subtitle' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'header_image' => 'nullable|string',
            'show_stats' => 'nullable|boolean',
            'show_perks' => 'nullable|boolean',
            'show_testimonials' => 'nullable|boolean',
            'application_form.allow_cover_letter' => 'nullable|boolean',
            'application_form.allow_portfolio' => 'nullable|boolean',
            'application_form.allow_linkedin' => 'nullable|boolean',
            'application_form.require_phone' => 'nullable|boolean',
            'application_form.max_file_size' => 'nullable|integer|min:1|max:20',
            'email_notifications.applicant_confirmation' => 'nullable|boolean',
            'email_notifications.admin_notification' => 'nullable|boolean',
            'email_notifications.status_change' => 'nullable|boolean',
            'social_share.linkedin' => 'nullable|boolean',
            'social_share.twitter' => 'nullable|boolean',
            'social_share.facebook' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = [];
            
            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['application_form', 'email_notifications', 'social_share'])) {
                    // Get existing value
                    $existing = CareerSetting::getValue($key, []);
                    $newValue = array_merge($existing, $value);
                    $setting = CareerSetting::setValue($key, $newValue);
                } else {
                    $setting = CareerSetting::setValue($key, $value);
                }
                $updated[] = $setting;
            }

            return response()->json([
                'success' => true,
                'message' => 'Career page settings updated successfully',
                'data' => CareerSetting::getCareerPageSettings()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update career page settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified setting.
     */
    public function destroy($id)
    {
        $setting = CareerSetting::find($id);

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        try {
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete setting by key.
     */
    public function deleteByKey($key)
    {
        $setting = CareerSetting::byKey($key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        try {
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to defaults.
     */
    public function resetToDefaults()
    {
        try {
            // Delete all existing settings
            CareerSetting::truncate();

            // Create default settings
            $defaults = [
                'page_title' => 'Career Opportunities',
                'page_subtitle' => 'Join our team and build your future with us',
                'meta_title' => 'Careers - Join Our Team',
                'meta_description' => 'Explore exciting career opportunities and join our dynamic team',
                'show_stats' => true,
                'show_perks' => true,
                'show_testimonials' => true,
                'application_form' => [
                    'allow_cover_letter' => true,
                    'allow_portfolio' => true,
                    'allow_linkedin' => true,
                    'require_phone' => true,
                    'max_file_size' => 5,
                    'allowed_file_types' => ['pdf', 'doc', 'docx']
                ],
                'email_notifications' => [
                    'applicant_confirmation' => true,
                    'admin_notification' => true,
                    'status_change' => true
                ],
                'social_share' => [
                    'linkedin' => true,
                    'twitter' => true,
                    'facebook' => false
                ]
            ];

            foreach ($defaults as $key => $value) {
                CareerSetting::create([
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'description' => 'Default setting'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings reset to defaults successfully',
                'data' => CareerSetting::getCareerPageSettings()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application form settings.
     */
    public function getApplicationFormSettings()
    {
        $settings = CareerSetting::getValue('application_form', [
            'allow_cover_letter' => true,
            'allow_portfolio' => true,
            'allow_linkedin' => true,
            'require_phone' => true,
            'max_file_size' => 5,
            'allowed_file_types' => ['pdf', 'doc', 'docx']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application form settings retrieved successfully',
            'data' => $settings
        ]);
    }

    /**
     * Get email notification settings.
     */
    public function getEmailSettings()
    {
        $settings = CareerSetting::getValue('email_notifications', [
            'applicant_confirmation' => true,
            'admin_notification' => true,
            'status_change' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email notification settings retrieved successfully',
            'data' => $settings
        ]);
    }
}