<?php

namespace App\Http\Controllers\Listings;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Anthropic\Laravel\Facades\Anthropic;
use App\Models\Listing;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ListingsController extends Controller
{
    /**
     * ____________________________________________________________________
     * Index Page
     */
    public function index()
    {

        if (!Auth::check()) {
            return redirect('/login');
        }

        $listings = DB::table('listings')->orderBy('id', 'desc')->paginate(25);

        return Inertia::render('listings', [
            'listings' => $listings
        ]);
    }




    /**
     * ____________________________________________________________________
     * Show one listing
     * 
     * @param string $id
     */

    public function show($id)
    {
        $listing = DB::table('listings')->where('id', $id)->first();
        return Inertia::render('update/listing', [
            'listing' => $listing
        ]);
    }




    /**
     * ____________________________________________________________________
     * Create listings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {

        $validator = Validator::make($request->json()->all(), [
            '*.title' => 'required|string|max:255',
            '*.description' => 'required|string|max:1000',
            '*.plant_category' => 'required|string|max:255',
            '*.contact_email' => 'required|email|max:255',
            '*.phone_number' => 'required',
            '*.website' => 'nullable|url|max:255',
            '*.hire_rate_gbp' => 'required|string|min:0',
            '*.hire_rate_eur' => 'required|string|min:0',
            '*.hire_rate_usd' => 'required|string|min:0',
            '*.hire_rate_aud' => 'required|string|min:0',
            '*.hire_rate_nzd' => 'required|string|min:0',
            '*.hire_rate_zar' => 'required|string|min:0',
            '*.tags' => 'required|array',
            '*.company_logo' => 'nullable|url|max:255',
            '*.photo_gallery' => 'nullable|array',
            '*.attachments' => 'nullable|array',
            '*.social_networks' => 'nullable|array',
            '*.location' => 'required|string|max:255',
            '*.region' => 'required|string|max:255',
            '*.related_listing' => 'nullable|array',
            '*.hire_rental' => 'nullable|string|max:255',
            '*.additional_*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed: ' . json_encode($validator->errors()));
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $data = $validator->validated();
        $currentDate = now();
        $insertData = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {

                $description = $this->generateLLMContent($row);
                if (!$description) {
                    throw new \Exception('Failed to generate LLM content');
                    return response()->json(['Message: ' => 'Failed to generate LLM content', 465]);
                }

                $insertData[] = [
                    'title' => $row['title'],
                    'description' => $description,
                    'plant_category' => $row['plant_category'],
                    'contact_email' => $row['contact_email'],
                    'phone_number' => $row['phone_number'],
                    'website' => $row['website'] ?? '',
                    'hire_rate_gbp' => $row['hire_rate_gbp'],
                    'hire_rate_eur' => $row['hire_rate_eur'],
                    'hire_rate_usd' => $row['hire_rate_usd'],
                    'hire_rate_aud' => $row['hire_rate_aud'],
                    'hire_rate_nzd' => $row['hire_rate_nzd'],
                    'hire_rate_zar' => $row['hire_rate_zar'],
                    'tags' => json_encode($row['tags']),
                    'company_logo' => $row['company_logo'] ?? '',
                    'photo_gallery' => json_encode($row['photo_gallery'] ?? []),
                    'attachments' => json_encode($row['attachments'] ?? []),
                    'social_networks' => json_encode($row['social_networks'] ?? []),
                    'location' => $row['location'],
                    'region' => $row['region'],
                    'related_listing' => json_encode($row['related_listing'] ?? []),
                    'hire_rental' => $row['hire_rental'] ?? '',
                    'additional_1' => $row['additional_1'] ?? '',
                    'additional_2' => $row['additional_2'] ?? '',
                    'additional_3' => $row['additional_3'] ?? '',
                    'additional_4' => $row['additional_4'] ?? '',
                    'additional_5' => $row['additional_5'] ?? '',
                    'additional_6' => $row['additional_6'] ?? '',
                    'additional_7' => $row['additional_7'] ?? '',
                    'additional_8' => $row['additional_8'] ?? '',
                    'additional_9' => $row['additional_9'] ?? '',
                    'additional_10' => $row['additional_10'] ?? '',
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ];
            }

            DB::table('listings')->insert($insertData);
            DB::commit();

            return response()->json([
                'message' => 'Successfully created listings',
                'count' => count($insertData),
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('Failed to process listings: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to process listings',
                'error' => $error->getMessage(),
            ], 500);
        }
    }




    /**
     * ____________________________________________________________________
     * Update Listing
     * 
     * @param Request $request
     */

    public function update(Request $request)
    {
        $listing_id = $request->header('listingId');

        // Returns status 422 if not valid
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3|max:1000',
            'plant_category' => 'required|string|min:3|max:255',
            'contact_email' => 'required|email|min:3|max:255',
            'phone_number' => 'required|min:3|max:255',
            'website' => 'nullable|url|max:255',
            'hire_rate_gbp' => 'required|string|min:0',
            'hire_rate_eur' => 'required|string|min:0',
            'hire_rate_usd' => 'required|string|min:0',
            'hire_rate_aud' => 'required|string|min:0',
            'hire_rate_nzd' => 'required|string|min:0',
            'hire_rate_zar' => 'required|string|min:0',
            'tags' => 'required|array',
            'company_logo' => 'nullable|url|max:255',
            'photo_gallery' => 'nullable|array',
            'attachments' => 'nullable|array',
            'social_networks' => 'nullable|array',
            'location' => 'required|string|min:3|max:255',
            'region' => 'required|string|min:3|max:255',
            'related_listing' => 'nullable|array',
            'hire_rental' => 'nullable|string|max:255',
            'additional_*' => 'nullable|string|max:255',
        ]);

        try {
            DB::table('listings')->where('id', $listing_id)->update($validated);
            return response()->json(['message', 'Successfully updated listing'], 200);
        } catch (\Exception $error) {
            Log::error('Failed to update listing', $error->getMessage());
            return response()->json(['message' => 'Failed to update listing', 'error' => $error->getMessage()], 500);
        }
    }




    /**
     * ____________________________________________________________________
     * Delete
     * 
     * @param string $id
     */
    public function delete($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();
        redirect()->back()->with("Success", "Successfully deleted listings");
    }




    /**
     * ____________________________________________________________________
     * Generate LLM content for description
     *
     * @param array $row
     * @return string|null
     */
    private function generateLLMContent(array $row): ?string
    {
        try {
            $response = Anthropic::messages()->create([
                'model' => 'claude-3-opus-20240229',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Generate a concise article body (no title) based on the following description: '
                            . $row['description']
                            . '. Include relevant details from this data: '
                            . json_encode($row)
                            . '. Note: phone numbers should be structured correctly and include area codes. The response must not exceed 255 characters, including spaces and punctuation.'
                    ],
                ],
            ]);

            return $response->content[0]->text;
        } catch (\Exception $error) {
            Log::error('LLM content generation failed: ' . $error->getMessage());
            return null;
        }
    }
}
