<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('tagline', 'LIKE', "%{$search}%")
                  ->orWhere('headquarters', 'LIKE', "%{$search}%");
            });
        }

        // Filter by founded year
        if ($request->has('year')) {
            $query->where('founded_year', $request->year);
        }

        // Filter by headquarters
        if ($request->has('city')) {
            $query->where('headquarters', 'LIKE', "%{$request->city}%");
        }

        $companies = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg,webp|max:1024',
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'headquarters' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['logo', 'favicon']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('companies/logos', 'public');
            $data['logo_url'] = $path;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('companies/favicons', 'public');
            $data['favicon_url'] = $path;
        }

        $company = Company::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    /**
     * Display the specified company.
     */
    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $company
        ]);
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_name' => 'sometimes|required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg,webp|max:1024',
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'headquarters' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['logo', 'favicon']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo_url) {
                Storage::disk('public')->delete($company->logo_url);
            }
            $path = $request->file('logo')->store('companies/logos', 'public');
            $data['logo_url'] = $path;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon
            if ($company->favicon_url) {
                Storage::disk('public')->delete($company->favicon_url);
            }
            $path = $request->file('favicon')->store('companies/favicons', 'public');
            $data['favicon_url'] = $path;
        }

        $company->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'data' => $company
        ]);
    }

    /**
     * Remove the specified company.
     */
    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        // Delete associated files
        if ($company->logo_url) {
            Storage::disk('public')->delete($company->logo_url);
        }
        if ($company->favicon_url) {
            Storage::disk('public')->delete($company->favicon_url);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully'
        ]);
    }

    /**
     * Get company settings (for frontend)
     */
    public function settings()
    {
        $company = Company::first(); // Get first company (assuming single company)

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'No company found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'company_name' => $company->company_name,
                'tagline' => $company->tagline,
                'logo_url' => $company->logo_url,
                'favicon_url' => $company->favicon_url,
                'primary_color' => $company->primary_color,
                'secondary_color' => $company->secondary_color,
                'website_url' => $company->website_url
            ]
        ]);
    }

    /**
     * Get company meta data (for SEO)
     */
    public function meta()
    {
        $company = Company::first();

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'No company found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'meta_title' => $company->meta_title ?? $company->company_name,
                'meta_description' => $company->meta_description,
                'meta_keywords' => $company->meta_keywords,
                'og_image' => $company->logo_url
            ]
        ]);
    }
}