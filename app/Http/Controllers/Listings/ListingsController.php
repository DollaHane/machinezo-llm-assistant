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
            ->where('post_type', 'job_listing')
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
     * Create listings V2
     * @param Request $request
     * @return JsonReponse
     */

    public function createV2(Request $request)
    {

        if (!Auth::check()) {
            return redirect('/login');
        }

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
                    $this->createWordpressTerm($tag, $listing_id, 'case27_job_listing_tags');
                }

                foreach ($row['attachments'] as $attachment) {
                    $this->createWordpressTerm($attachment, $listing_id, 'attachments');
                }

                $this->createWordpressTerm($row['plant_category'], $listing_id, 'job_listing_category');
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
     * Update Listing V2
     * 
     * @param Request $request
     */
    public function updateV2(Request $request)
    {
        
        if (!Auth::check()) {
            return redirect('/login');
        }

        $listing_id = $request->header('listingId');

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
            $this->updateWordpressTerms($row['tags'], $listing_id, 'case27_job_listing_tags');
            $this->updateWordpressTerms($row['attachments'], $listing_id, 'attachments');
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
     * Create WP Terms:
     * 
     * @param string $term
     * @param int $listing_id
     * @param string $taxonomy
     */
    private function createWordpressTerm($term, $listing_id, $taxonomy) {
        $term_id = DB::table('SWX7neDE_terms')->where('name', $term)->value('term_id');

        if (!$term_id) {
            $term_id = DB::table('SWX7neDE_terms')->insertGetId([
                'name' => $term,
                'slug' => str_replace(' ', '-', strtolower($term)),
                'term_group' => 0,
            ]);

            DB::table('SWX7neDE_term_taxonomy')->insert([
                'term_id' => $term_id,
                'taxonomy' => $taxonomy,
                'description' => '',
                'parent' => 0,
                'count' => 1,
            ]);
        }

        DB::table('SWX7neDE_term_relationships')->insert([
            'object_id' => $listing_id,
            'term_taxonomy_id' => $term_id,
            'term_order' => 1,
        ]);
    }

    /**
     * ____________________________________________________________________
     * Update WP Terms
     * 
     * @param string[] $terms
     * @param int $listing_id
     * @param string $taxonomy
     */
    private function updateWordpressTerms($terms, $listing_id, $taxonomy) {
        $current_terms = DB::table('SWX7neDE_term_relationships')
            ->join('SWX7neDE_term_taxonomy', 'SWX7neDE_term_relationships.term_taxonomy_id', '=', 'SWX7neDE_term_taxonomy.term_taxonomy_id')
            ->join('SWX7neDE_terms', 'SWX7neDE_term_taxonomy.term_id', '=', 'SWX7neDE_terms.term_id')
            ->where('SWX7neDE_term_relationships.object_id', $listing_id)
            ->where('SWX7neDE_term_taxonomy.taxonomy', $taxonomy)
            ->select('SWX7neDE_term_relationships.object_id', 'SWX7neDE_terms.term_id', 'SWX7neDE_terms.name')
            ->get();

        $terms_to_delete = [];

        foreach ($current_terms as $cur_term) {
            $remove = true;
            foreach ($terms as $term) {
                if ($cur_term->name === $term) {
                    $remove = false;
                    break;
                }
            }
            if ($remove) {
                $terms_to_delete[] = $cur_term;
            }
        }

        if (count($terms_to_delete) > 0) {
            foreach ($terms_to_delete as $term) {
                $this->deleteWorpdressTerm($term);
            }
        }

        $terms_to_add = [];

        foreach ($terms as $term) {
            $add = true;
            foreach ($current_terms as $cur_term) {
                if ($term === $cur_term->name) {
                    $add = false;
                    break;
                }
            }

            if ($add) {
                $terms_to_add[] = $term;
            }
        }

        if (count($terms_to_add) > 0) {
            foreach ($terms_to_add as $term) {
                $this->createWordpressTerm($term, $listing_id, $taxonomy);
            }
        }
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
            $this->createWordpressTerm($plant_category, $listing_id, 'job_listing_category');
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
            ['meta_key' => '_attachments-available-for-hire', 'meta_value' => serialize($row['attachments']) ?? serialize('')],
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
