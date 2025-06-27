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
use Illuminate\Database\QueryException;

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

        $listings = DB::table('SWX7neDE_posts')
            ->orderBy('SWX7neDE_posts.ID', 'desc')
            ->paginate(25);

        $postIds = $listings->pluck('ID')->toArray();

        $postmeta = DB::table('SWX7neDE_postmeta')
            ->whereIn('post_id', $postIds)
            ->select('post_id', 'meta_key', 'meta_value')
            ->get()
            ->groupBy('post_id')
            ->map(function ($meta) {
                return $meta->map(function ($item) {
                    $value = @unserialize($item->meta_value) !== false ? unserialize($item->meta_value) : $item->meta_value;
                    return [
                        'meta_key' => $item->meta_key,
                        'meta_value' => $value,
                    ];
                })->toArray();
            });

        $terms = DB::table('SWX7neDE_term_relationships')
            ->leftJoin('SWX7neDE_terms', 'SWX7neDE_term_relationships.term_taxonomy_id', '=', 'SWX7neDE_terms.term_id')
            ->leftJoin('SWX7neDE_term_taxonomy', 'SWX7neDE_term_taxonomy.term_id', '=', 'SWX7neDE_terms.term_id')
            ->whereIn('SWX7neDE_term_relationships.object_id', $postIds)
            ->select('SWX7neDE_term_relationships.object_id', 'SWX7neDE_terms.name', 'SWX7neDE_term_taxonomy.taxonomy')
            ->get()
            ->groupBy('object_id');


        $listings->each(function ($post) use ($postmeta, $terms) {
            $post->postmeta = $postmeta[$post->ID] ?? [];
            $post->terms = $terms[$post->ID] ?? [];
        });

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
        if (!Auth::check()) {
            return redirect('/login');
        }

        $listing = DB::table('SWX7neDE_posts')->where('ID', $id)->first();

        $postmeta = DB::table('SWX7neDE_postmeta')
            ->where('post_id', $id)
            ->select('post_id', 'meta_key', 'meta_value')
            ->get()
            ->groupBy('post_id')
            ->map(function ($meta) {
                return $meta->map(function ($item) {
                    $value = @unserialize($item->meta_value) !== false ? unserialize($item->meta_value) : $item->meta_value;
                    return [
                        'meta_key' => $item->meta_key,
                        'meta_value' => $value,
                    ];
                })->toArray();
            });

        $terms = DB::table('SWX7neDE_term_relationships')
            ->leftJoin('SWX7neDE_terms', 'SWX7neDE_term_relationships.term_taxonomy_id', '=', 'SWX7neDE_terms.term_id')
            ->leftJoin('SWX7neDE_term_taxonomy', 'SWX7neDE_term_taxonomy.term_id', '=', 'SWX7neDE_terms.term_id')
            ->whereIn('SWX7neDE_term_relationships.object_id', [$id])
            ->select('SWX7neDE_term_relationships.object_id', 'SWX7neDE_terms.name', 'SWX7neDE_term_taxonomy.taxonomy')
            ->get()
            ->groupBy('object_id');

        $listing->postmeta = $postmeta[$id] ?? [];
        $listing->terms = $terms[$id] ?? [];

        return Inertia::render('update/listing', [
            'post' => $listing
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
            '*.description' => 'required|string|max:2000',
            '*.plant_category' => 'required|string|max:255',
            '*.contact_email' => 'required|email|max:255',
            '*.phone_number' => 'required',
            '*.website' => 'nullable|url|max:255',
            '*.hire_rate_gbp' => 'nullable|string|min:0',
            '*.hire_rate_eur' => 'nullable|string|min:0',
            '*.hire_rate_usd' => 'nullable|string|min:0',
            '*.hire_rate_aud' => 'nullable|string|min:0',
            '*.hire_rate_nzd' => 'nullable|string|min:0',
            '*.hire_rate_zar' => 'nullable|string|min:0',
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
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                $description = $this->generateLLMContent($row);
                if (!$description) {
                    throw new \Exception('Failed to generate LLM content');
                    return response()->json(['Message' => 'Failed to generate LLM content'], 465);
                }

                $insertData = [
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

                $id = DB::table('listings')->insertGetId($insertData);

                $work_hours_data = [
                    [
                        'listing_id' => $id,
                        'start' => 480,
                        'end' => 1020,
                        'timezone' => 'Europe/London',
                    ],
                    [
                        'listing_id' => $id,
                        'start' => 1920,
                        'end' => 2460,
                        'timezone' => 'Europe/London',
                    ],
                    [
                        'listing_id' => $id,
                        'start' => 3360,
                        'end' => 3900,
                        'timezone' => 'Europe/London',
                    ],
                    [
                        'listing_id' => $id,
                        'start' => 4800,
                        'end' => 5340,
                        'timezone' => 'Europe/London',
                    ],
                    [
                        'listing_id' => $id,
                        'start' => 6240,
                        'end' => 6780,
                        'timezone' => 'Europe/London',
                    ],
                ];

                DB::table('listings_workhours')->insert($work_hours_data);
            }

            DB::commit();

            return response()->json([
                'message' => 'Successfully created listings',
                'count' => count($insertData),
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('Failed to process listings: ' . json_encode($error->getMessage()));
            return response()->json([
                'message' => 'Failed to process listings',
                'error' => $error->getMessage(),
            ], 500);
        }
    }



    /**
     * ____________________________________________________________________
     * Create listings V2
     * @param Request $request
     * @return JsonReponse
     */

    public function createV2(Request $request)
    {

        $validator = Validator::make($request->json()->all(), [
            '*.title' => 'required|string|min:3|max:255',
            '*.description' => 'required|string|min:3',
            '*.plant_category' => 'required|string|min:3|max:255',
            '*.company_name' => 'required|string|min:3|max:255',
            '*.contact_email' => 'required|email|min:3|max:255',
            '*.phone_number' => 'required|min:3|max:255',
            '*.website' => 'nullable|url|max:255',
            '*.hire_rate_gbp' => 'nullable|string|max:255',
            '*.hire_rate_eur' => 'nullable|string|max:255',
            '*.hire_rate_usd' => 'nullable|string|max:255',
            '*.hire_rate_aud' => 'nullable|string|max:255',
            '*.hire_rate_nzd' => 'nullable|string|max:255',
            '*.hire_rate_zar' => 'nullable|string|max:255',
            '*.tags' => 'required|array',
            '*.company_logo' => 'nullable|array',
            '*.photo_gallery' => 'nullable|array',
            '*.attachments' => 'nullable|array',
            '*.social_networks' => 'nullable|array',
            '*.location' => 'required|string|max:255',
            '*.region' => 'required|string|max:255',
            '*.related_listing' => 'nullable|array',
            '*.hire_rental' => 'nullable|string|max:255',
            '*.additional_1' => 'nullable|string|max:255',
            '*.additional_2' => 'nullable|string|max:255',
            '*.additional_3' => 'nullable|string|max:255',
            '*.additional_4' => 'nullable|string|max:255',
            '*.additional_5' => 'nullable|string|max:255',
            '*.additional_6' => 'nullable|string|max:255',
            '*.additional_7' => 'nullable|string|max:255',
            '*.additional_8' => 'nullable|string|max:255',
            '*.additional_9' => 'nullable|string|max:255',
            '*.additional_10' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Failed to validate CSV data' . json_encode($validator->errors()));
            return response()->json(['message' => 'Failed to validate CSV data', 'error' => $validator->errors()], 400);
        }

        $data = $validator->validated();
        $current_date = now();
        $current_date_gmt = now()->setTimezone('GMT');

        DB::beginTransaction();
        try {
            foreach ($data as $row) {

                // __________________________________
                // CREATE SWX7neDE_posts
                $listing_id = DB::table('SWX7neDE_posts')->insertGetId([
                    'post_author' => 1,
                    'post_date' => $current_date,
                    'post_date_gmt' => $current_date_gmt,
                    'post_content' => $row['description'],
                    'post_title' => $row['title'],
                    'post_excerpt' => '',
                    'post_status' => 'draft',
                    'to_ping' => '',
                    'pinged' => '',
                    'post_content_filtered' => '',
                    'post_type' => 'job_listing',
                    'post_modified' => $current_date,
                    'post_modified_gmt' => $current_date_gmt,
                ]);

                DB::table('SWX7neDE_posts')->where('ID', $listing_id)->update([
                    'guid' => "https://machinezo.co.uk/?post_type=job_listing&#038;p=$listing_id",
                ]);


                // _____________________________________________________
                // DATA FUNCTIONS:
                foreach ($row['tags'] as $tag) {
                    $this->createWorpressTagTerms($tag, $listing_id);
                }

                $this->createWordpressPlantCategoryTerm($row['plant_category'], $listing_id);
                $this->createWorkHours($listing_id);
                $serialized_social_media = $this->serializeSocialMedia($row['social_networks']);
                $metadata = $this->generateMetadata($row, $serialized_social_media);

                // __________________________________
                // CREATE SWX7neDE_postmeta

                foreach ($metadata as $meta) {
                    DB::table('SWX7neDE_postmeta')->insert([
                        'post_id' => $listing_id,
                        'meta_key' => $meta['meta_key'],
                        'meta_value' => $meta['meta_value'],
                    ]);
                }
            }
            DB::commit();

            return response()->json(['message' => 'Successfully created post with metadata'], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('Failed to create post with metadata' . json_encode($error->getMessage()));
            return response()->json(['message' => 'Failed to create post with metadata', 'error' => $error->getMessage()], 500);
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
        Log::info($request->all());
        $listing_id = $request->header('listingId');
        $currentDate = now();

        // Returns status 422 if not valid
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3|max:2000',
            'plant_category' => 'required|string|min:3|max:255',
            'contact_email' => 'required|email|min:3|max:255',
            'phone_number' => 'required|min:3|max:255',
            'website' => 'nullable|url|max:255',
            'hire_rate_gbp' => 'nullable|string|min:0',
            'hire_rate_eur' => 'nullable|string|min:0',
            'hire_rate_usd' => 'nullable|string|min:0',
            'hire_rate_aud' => 'nullable|string|min:0',
            'hire_rate_nzd' => 'nullable|string|min:0',
            'hire_rate_zar' => 'nullable|string|min:0',
            'tags' => 'required|array',
            'company_logo' => 'nullable|url|max:255',
            'photo_gallery' => 'nullable|array',
            'attachments' => 'nullable|array',
            'social_networks' => 'nullable|array',
            'location' => 'required|string|min:3|max:255',
            'region' => 'required|string|min:3|max:255',
            'related_listing' => 'nullable|array',
            'hire_rental' => 'nullable|string|max:255',
            'additional_1' => 'nullable|string|max:255',
            'additional_2' => 'nullable|string|max:255',
            'additional_3' => 'nullable|string|max:255',
            'additional_4' => 'nullable|string|max:255',
            'additional_5' => 'nullable|string|max:255',
            'additional_6' => 'nullable|string|max:255',
            'additional_7' => 'nullable|string|max:255',
            'additional_8' => 'nullable|string|max:255',
            'additional_9' => 'nullable|string|max:255',
            'additional_10' => 'nullable|string|max:255',
        ]);

        try {
            DB::table('listings')->where('id', $listing_id)->update($validated);
            return response()->json(['message', 'Successfully updated listing'], 200);
        } catch (\Exception $error) {
            Log::error('Failed to update listing' . json_encode($error->getMessage()));
            return response()->json(['message' => 'Failed to update listing', 'error' => $error->getMessage()], 500);
        }
    }



    /**
     * ____________________________________________________________________
     * Update Listing V2
     * 
     * @param Request $request
     */
    public function updateV2(Request $request)
    {
        $listing_id = $request->header('listingId');

        Log::info($request->all());
        Log:
        info($listing_id);

        $validator = Validator::make($request->json()->all(), [
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3',
            'plant_category' => 'required|string|min:3|max:255',
            'company_name' => 'required|string|min:3|max:255',
            'contact_email' => 'required|email|min:3|max:255',
            'phone_number' => 'required|min:3|max:255',
            'website' => 'nullable|url|max:255',
            'hire_rate_gbp' => 'nullable|string|max:255',
            'hire_rate_eur' => 'nullable|string|max:255',
            'hire_rate_usd' => 'nullable|string|max:255',
            'hire_rate_aud' => 'nullable|string|max:255',
            'hire_rate_nzd' => 'nullable|string|max:255',
            'hire_rate_zar' => 'nullable|string|max:255',
            'tags' => 'required|array',
            'company_logo' => 'nullable|array',
            'photo_gallery' => 'nullable|array',
            'attachments' => 'nullable|array',
            'social_networks' => 'nullable|array',
            'location' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'related_listing' => 'nullable|array',
            'hire_rental' => 'nullable|string|max:255',
            'additional_1' => 'nullable|string|max:255',
            'additional_2' => 'nullable|string|max:255',
            'additional_3' => 'nullable|string|max:255',
            'additional_4' => 'nullable|string|max:255',
            'additional_5' => 'nullable|string|max:255',
            'additional_6' => 'nullable|string|max:255',
            'additional_7' => 'nullable|string|max:255',
            'additional_8' => 'nullable|string|max:255',
            'additional_9' => 'nullable|string|max:255',
            'additional_10' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Failed to validate CSV data' . json_encode($validator->errors()));
            return response()->json(['message' => 'Failed to validate CSV data', 'error' => $validator->errors()], 400);
        }

        $row = $validator->validated();
        $current_date = now();
        $current_date_gmt = now()->setTimezone('GMT');

        DB::beginTransaction();
        try {

            // UPDATE SWX7neDE_posts
            DB::table('SWX7neDE_posts')->update([
                'post_author' => 1,
                'post_content' => $row['description'],
                'post_title' => $row['title'],
                'post_excerpt' => '',
                'post_status' => 'draft',
                'to_ping' => '',
                'pinged' => '',
                'post_content_filtered' => '',
                'post_type' => 'job_listing',
                'post_modified' => $current_date,
                'post_modified_gmt' => $current_date_gmt,
            ]);

            // TERM FUNCTIONS:
            $this->updateWordpressTagTerms($row, $listing_id);
            $this->updateWordpressPlantCategoryTerms($row['plant_category'], $listing_id);

            // DATA FUNCTIONS:
            $serialized_social_media = $this->serializeSocialMedia($row['social_networks']);
            $metadata = $this->generateMetadata($row, $serialized_social_media);

            Log::info('Metadata: ', $metadata);

            // CREATE SWX7neDE_postmeta
            foreach ($metadata as $meta) {
                DB::table('SWX7neDE_postmeta')->updateOrInsert(
                    [
                        'post_id' => $listing_id,
                        'meta_key' => $meta['meta_key'],
                    ],
                    [
                        'meta_value' => $meta['meta_value'],
                    ]
                );
            }

            DB::commit();

            return response()->json(['message' => 'Successfully updated post with metadata'], 200);
        } catch (QueryException $error) {
            DB::rollBack();
            Log::error("Database error while updating post with metadata: {$error->getMessage()} at {$error->getFile()}:{$error->getLine()}");
            return response()->json(['message' => 'Failed to update post with metadata', 'error' => $error->getMessage()], 500);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('Failed to update post with metadata' . json_encode($error->getMessage()));
            return response()->json(['message' => 'Failed to update post with metadata', 'error' => $error->getMessage()], 500);
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
        try {
            DB::beginTransaction();

            DB::table('SWX7neDE_posts')->where('ID', $id)->delete();
            DB::table('SWX7neDE_postmeta')->where('post_id', $id)->delete();
            DB::table('SWX7neDE_term_relationships')->where('object_id', $id)->delete();

            DB::commit();
            redirect()->back()->with("Success", "Successfully deleted listings");
        } catch (QueryException $error) {
            DB::rollBack();
            Log::error("Database error while deleting listing: {$error->getMessage()} at {$error->getFile()}:{$error->getLine()}");
            return response()->json(['message' => 'Database error while deleting listing', 'error' => $error->getMessage()], 500);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('Failed to delete listing' . json_encode($error->getMessage()));
            return response()->json(['message' => 'Failed to delete listing', 'error' => $error->getMessage()], 500);
        }
    }









    //__________________________________________________________________________________________________________________
    //____________________________PRIVATE FUNCTIONS_____________________________________________________________________
    //__________________________________________________________________________________________________________________


    /**
     * ____________________________________________________________________
     * Create WP Tags:
     * 
     * @param string $tag
     * @param int $listing_id
     */
    private function createWorpressTagTerms($tag, $listing_id)
    {
        $tag_term_id = DB::table('SWX7neDE_terms')->where('name', $tag)->value('term_id');

        if (!$tag_term_id) {
            $tag_term_id = DB::table('SWX7neDE_terms')->insertGetId([
                'name' => $tag,
                'slug' => str_replace(' ', '-', strtolower($tag)),
                'term_group' => 0,
            ]);

            DB::table('SWX7neDE_term_taxonomy')->insert([
                'term_id' => $tag_term_id,
                'taxonomy' => 'case27_job_listing_tags',
                'description' => '',
                'parent' => 0,
                'count' => 1,
            ]);
        }

        DB::table('SWX7neDE_term_relationships')->insert([
            'object_id' => $listing_id,
            'term_taxonomy_id' => $tag_term_id,
            'term_order' => 1,
        ]);
    }

    /**
     * ____________________________________________________________________
     * Update WP Tags:
     * 
     */
    private function updateWordpressTagTerms($row, $listing_id)
    {
        $current_tags = DB::table('SWX7neDE_term_relationships')
            ->join('SWX7neDE_term_taxonomy', 'SWX7neDE_term_relationships.term_taxonomy_id', '=', 'SWX7neDE_term_taxonomy.term_taxonomy_id')
            ->join('SWX7neDE_terms', 'SWX7neDE_term_taxonomy.term_id', '=', 'SWX7neDE_terms.term_id')
            ->where('SWX7neDE_term_relationships.object_id', $listing_id)
            ->where('SWX7neDE_term_taxonomy.taxonomy', 'case27_job_listing_tags')
            ->select('SWX7neDE_term_relationships.object_id', 'SWX7neDE_terms.term_id', 'SWX7neDE_terms.name')
            ->get();

        $tags_to_delete = [];

        foreach ($current_tags as $cur_tag) {
            $remove = true;
            foreach ($row['tags'] as $tag) {
                if ($cur_tag->name === $tag) {
                    $remove = false;
                    break;
                }
            }
            if ($remove) {
                $tags_to_delete[] = $cur_tag;
            }
        }

        if (count($tags_to_delete) > 0) {
            foreach ($tags_to_delete as $tag) {
                $this->deleteWorpdressTerm($tag);
            }
        }

        $tags_to_add = [];

        foreach ($row['tags'] as $tag) {
            $add = true;
            foreach ($current_tags as $cur_tag) {
                if ($tag === $cur_tag->name) {
                    $add = false;
                    break;
                }
            }

            if ($add) {
                $tags_to_add[] = $tag;
            }
        }

        if (count($tags_to_add) > 0) {
            foreach ($tags_to_add as $tag) {
                $this->createWorpressTagTerms($tag, $listing_id);
            }
        }
    }

    /**
     * ____________________________________________________________________
     * Create WP Plant Category
     */
    private function createWordpressPlantCategoryTerm($plant_category, $listing_id)
    {
        $plant_category_term_id = DB::table('SWX7neDE_terms')->where('name', $plant_category)->value('term_id');

        if (!$plant_category_term_id) {
            $plant_category_term_id = DB::table('SWX7neDE_terms')->insertGetId([
                'name' => $plant_category,
                'slug' => str_replace(' ', '-', strtolower($plant_category)),
                'term_group' => 0,
            ]);

            DB::table('SWX7neDE_term_taxonomy')->insert([
                'term_id' => $plant_category_term_id,
                'taxonomy' => 'job_listing_category',
                'description' => '',
                'parent' => 0,
                'count' => 1,
            ]);
        }

        DB::table('SWX7neDE_term_relationships')->insert([
            'object_id' => $listing_id,
            'term_taxonomy_id' => $plant_category_term_id,
            'term_order' => 1,
        ]);
    }

    /**
     * ____________________________________________________________________
     * Update WP Plant Category:
     * 
     */
    private function updateWordpressPlantCategoryTerms($plant_category, $listing_id)
    {

        $current_plant_category = DB::table('SWX7neDE_term_relationships')
            ->join('SWX7neDE_term_taxonomy', 'SWX7neDE_term_relationships.term_taxonomy_id', '=', 'SWX7neDE_term_taxonomy.term_taxonomy_id')
            ->join('SWX7neDE_terms', 'SWX7neDE_term_taxonomy.term_id', '=', 'SWX7neDE_terms.term_id')
            ->where('SWX7neDE_term_relationships.object_id', $listing_id)
            ->where('SWX7neDE_term_taxonomy.taxonomy', 'job_listing_category')
            ->select('SWX7neDE_term_relationships.object_id', 'SWX7neDE_terms.term_id', 'SWX7neDE_terms.name')
            ->get();

        if ($current_plant_category[0]->name !== $plant_category) {
            $this->deleteWorpdressTerm($current_plant_category[0]);
            $this->createWordpressPlantCategoryTerm($plant_category, $listing_id);
        }
    }

    /**
     * ____________________________________________________________________
     * Delete WP Terms:
     * 
     */
    private function deleteWorpdressTerm($term)
    {
        DB::table('SWX7neDE_term_relationships')
            ->where('object_id', $term->object_id)
            ->where('term_taxonomy_id', $term->term_id)
            ->delete();
    }

    /**
     * ____________________________________________________________________
     * Sort and serialise social media links:
     * 
     * @param array $urls
     */
    private function serializeSocialMedia(array $urls)
    {
        try {
            $links = array_map(function ($url) {

                $parsedUrl = parse_url($url, PHP_URL_HOST);
                $domain = strtolower($parsedUrl ?: '');

                $networkMap = [
                    'facebook.com' => 'Facebook',
                    'x.com' => 'X',
                    'twitter.com' => 'Twitter',
                    'instagram.com' => 'Instagram',
                    'youtube.com' => 'YouTube',
                    'snapchat' => 'Snapchat',
                    'tumblr.com' => 'Tumblr',
                    'reddit.com' => 'Reddit',
                    'linkedin.com' => 'LinkedIn',
                    'pinterest.com' => 'Pinterest',
                    'deviantart.com' => 'DeviantArt',
                    'vkontakte.com' => 'VKontakte',
                    'tiktok.com' => 'TikTok',
                    'soundcloud.com' => 'SoundCloud',
                ];

                $network = 'Website';
                foreach ($networkMap as $domainKey => $networkName) {
                    if (strpos($domain, $domainKey) !== false) {
                        $network = $networkName;
                        break;
                    }
                }

                return [
                    'network' => $network,
                    'url' => $url
                ];
            }, $urls, array_keys($urls));

            $serializedLinks = serialize($links);
            return $serializedLinks;
        } catch (\Exception $error) {
            Log::error('Failed to serialize social media links' . json_encode($error->getMessage()));
            return null;
        }
    }

    /**
     * ____________________________________________________________________
     * Create Work Hours:
     * 
     * @param string $listing_id
     */
    private function createWorkHours($listing_id)
    {
        $work_hours_array = array(
            'Monday' => array(
                'status' => 'enter-hours',
                0 => array(
                    'from' => '07:30',
                    'to' => '18:00'
                )
            ),
            'Tuesday' => array(
                'status' => 'enter-hours'
            ),
            'Wednesday' => array(
                'status' => 'enter-hours'
            ),
            'Thursday' => array(
                'status' => 'enter-hours'
            ),
            'Friday' => array(
                'status' => 'enter-hours'
            ),
            'Saturday' => array(
                'status' => 'enter-hours'
            ),
            'Sunday' => array(
                'status' => 'enter-hours'
            ),
            'timezone' => 'UTC'
        );

        $serialized_work_hours = serialize($work_hours_array);

        DB::table('SWX7neDE_postmeta')->insert([
            'post_id' => $listing_id,
            'meta_key' => '_work_hours',
            'meta_value' => $serialized_work_hours,
        ]);
    }

    /**
     * Generate Metadata:
     */
    private function generateMetadata($row, $serialized_social_media)
    {
        $metadata = [
            ['meta_key' => '_case27_listing_type', 'meta_value' => 'plant-hire'],
            ['meta_key' => '_company-name', 'meta_value' => $row['company_name']],
            ['meta_key' => '_job_email', 'meta_value' => $row['contact_email']],
            ['meta_key' => '_job_phone', 'meta_value' => $row['phone_number']],
            ['meta_key' => '_job_website', 'meta_value' => $row['website'] ?? ''],
            ['meta_key' => '_hire_rate_gbp', 'meta_value' => $row['hire_rate_gbp'] ?? ''], // New meta to be configured in Wordpress
            ['meta_key' => '_hire_rate_eur', 'meta_value' => $row['hire_rate_eur'] ?? ''], // New meta to be configured in Wordpress
            ['meta_key' => '_hire_rate_usd', 'meta_value' => $row['hire_rate_usd'] ?? ''], // New meta to be configured in Wordpress
            ['meta_key' => '_hire_rate_aud', 'meta_value' => $row['hire_rate_aud'] ?? ''], // New meta to be configured in Wordpress
            ['meta_key' => '_hire_rate_nzd', 'meta_value' => $row['hire_rate_nzd'] ?? ''], // New meta to be configured in Wordpress
            ['meta_key' => '_hire_rate_zar', 'meta_value' => $row['hire_rate_zar'] ?? ''], // New meta to be configured in Wordpress
            ['meta_key' => '_hire-rate-pricing', 'meta_value' => ''],
            ['meta_key' => '_job_logo', 'meta_value' => serialize($row['company_logo']) ?? serialize('')],
            ['meta_key' => '_job_gallery', 'meta_value' => serialize($row['photo_gallery']) ?? serialize('')],
            ['meta_key' => '_attachments-available-for-hire', 'meta_value' => serialize($row['attachments']) ?? serialize('')], // TO-DO: Needs to be serialised array in WP -> Update Laravel App
            ['meta_key' => '_links', 'meta_value' => $serialized_social_media ?? serialize('')],
            ['meta_key' => '_location', 'meta_value' => $row['location']], // TO-DO: Check whether these need to be co-ordinates
            // TO-DO add region
            // TO-DO add related listings
            ['meta_key' => '_social-networks', 'meta_value' => ''], // TO-DO: Check where this links to
            ['meta_key' => '_hire-rental', 'meta_value' => $row['hire_rental'] ?? ''],
            ['meta_key' => '_additional_1', 'meta_value' => $row['additional_1'] ?? ''],
            ['meta_key' => '_additional_2', 'meta_value' => $row['additional_2'] ?? ''],
            ['meta_key' => '_additional_3', 'meta_value' => $row['additional_3'] ?? ''],
            ['meta_key' => '_additional_4', 'meta_value' => $row['additional_4'] ?? ''],
            ['meta_key' => '_additional_5', 'meta_value' => $row['additional_5'] ?? ''],
            ['meta_key' => '_additional_6', 'meta_value' => $row['additional_6'] ?? ''],
            ['meta_key' => '_additional_7', 'meta_value' => $row['additional_7'] ?? ''],
            ['meta_key' => '_additional_8', 'meta_value' => $row['additional_8'] ?? ''],
            ['meta_key' => '_additional_9', 'meta_value' => $row['additional_9'] ?? ''],
            ['meta_key' => '_additional_10', 'meta_value' => $row['additional_10'] ?? ''],
        ];

        return $metadata;
    }

    /**
     * ____________________________________________________________________
     * Generate LLM content for description:
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
                            . '. Note: phone numbers should be structured correctly and include area codes. The response must not exceed 2,000 characters, including spaces and punctuation.'
                    ],
                ],
            ]);

            return $response->content[0]->text;
        } catch (\Exception $error) {
            Log::error('LLM content generation failed: ' . json_encode($error->getMessage()));
            return null;
        }
    }
}
