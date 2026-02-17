<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerkResource;
use App\Models\Perk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PerkController extends Controller
{
    /**
     * Display a listing of all perks.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Perk::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where('title', 'like', "%{$search}%");
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $perPage = min(max(1, $perPage), 100);

            $perks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => PerkResource::collection($perks),
                'meta' => [
                    'total' => $perks->total(),
                    'current_page' => $perks->currentPage(),
                    'last_page' => $perks->lastPage(),
                    'per_page' => $perks->perPage(),
                    'from' => $perks->firstItem(),
                    'to' => $perks->lastItem(),
                ],
                'message' => 'Perks retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve perks.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all perks without pagination.
     * 
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        try {
            $perks = Perk::orderBy('id', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => PerkResource::collection($perks),
                'total' => $perks->count(),
                'message' => 'All perks retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve perks.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created perk.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'icon' => 'required|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = [
                'title' => $request->title
            ];

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                
                // Generate unique filename
                $filename = 'perk_' . time() . '_' . uniqid() . '.' . $icon->getClientOriginalExtension();
                
                // Store in perks directory
                $path = $icon->storeAs('perks', $filename, 'public');
                
                // Save only filename in database
                $data['icon'] = $filename;
            }

            $perk = Perk::create($data);

            return response()->json([
                'success' => true,
                'data' => new PerkResource($perk),
                'message' => 'Perk created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create perk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified perk.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $perk = Perk::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new PerkResource($perk),
                'message' => 'Perk retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perk not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve perk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified perk.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $perk = Perk::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = [];

            if ($request->has('title')) {
                $data['title'] = $request->title;
            }

            // Handle icon upload
            if ($request->hasFile('icon')) {
                // Delete old icon
                if ($perk->icon) {
                    Storage::disk('public')->delete('perks/' . $perk->icon);
                }

                $icon = $request->file('icon');
                $filename = 'perk_' . time() . '_' . uniqid() . '.' . $icon->getClientOriginalExtension();
                $icon->storeAs('perks', $filename, 'public');
                $data['icon'] = $filename;
            }

            // Handle icon removal
            if ($request->has('remove_icon') && $request->remove_icon == true) {
                if ($perk->icon) {
                    Storage::disk('public')->delete('perks/' . $perk->icon);
                    $data['icon'] = null;
                }
            }

            $perk->update($data);

            return response()->json([
                'success' => true,
                'data' => new PerkResource($perk),
                'message' => 'Perk updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perk not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update perk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified perk.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $perk = Perk::findOrFail($id);

            // Delete icon file
            if ($perk->icon) {
                Storage::disk('public')->delete('perks/' . $perk->icon);
            }

            $perk->delete();

            return response()->json([
                'success' => true,
                'message' => 'Perk deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perk not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete perk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent perks.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recent(Request $request)
    {
        try {
            $limit = min($request->get('limit', 5), 20);
            
            $perks = Perk::orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => PerkResource::collection($perks),
                'message' => 'Recent perks retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent perks.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}