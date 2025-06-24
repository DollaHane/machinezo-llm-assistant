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
use App\Models\SWX7neDE_postmeta;

use function Laravel\Prompts\error;

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
            '*.title' => 'required|string|max:255', // post -> post_title -> DONE
            '*.description' => 'required|string', // post -> post_description -> DONE

            '*.plant_category' => 'required|string|max:255', // Term -> insert functions -> check additional relationships

            '*.contact_email' => 'required|email|max:255', // Meta -> DONE
            '*.phone_number' => 'required', // Meta -> DONE
            '*.website' => 'nullable|url|max:255', // Meta -> DONE
            '*.hire_rate_gbp' => 'nullable|string|min:0', // Meta -> NEW 
            '*.hire_rate_eur' => 'nullable|string|min:0', // Meta -> NEW 
            '*.hire_rate_usd' => 'nullable|string|min:0', // Meta -> NEW 
            '*.hire_rate_aud' => 'nullable|string|min:0', // Meta -> NEW 
            '*.hire_rate_nzd' => 'nullable|string|min:0', // Meta -> NEW 
            '*.hire_rate_zar' => 'nullable|string|min:0', // Meta -> NEW 

            '*.tags' => 'required|array', // Term -> insert functions -> check additional relationships

            '*.company_logo' => 'nullable|array', // Meta -> DONE
            '*.photo_gallery' => 'nullable|array', // Meta -> DONE
            '*.attachments' => 'nullable|string|max:255', // Meta -> DONE
            '*.social_networks' => 'nullable|array', // Meta -> DONE
            '*.location' => 'required|string|max:255', // Meta -> DONE
            '*.region' => 'required|string|max:255', // Meta
            '*.related_listing' => 'nullable|array', // Meta
            '*.hire_rental' => 'nullable|string|max:255', // Meta
            '*.additional_1' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_2' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_3' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_4' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_5' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_6' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_7' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_8' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_9' => 'nullable|string|max:255', // Meta -> NEW 
            '*.additional_10' => 'nullable|string|max:255', // Meta -> NEW 
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

                // TO-DO: Find which table plant_category needs to go into.

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
                    'post_type' => "https://machinezo.co.uk/?post_type=job_listing&#038;p=$listing_id",
                ]);


                // _____________________________________________________
                // TERMS FUNCTIONS:
                // step 1: foreach '$tags' as '$tag' get $term_id where name = $tag

                // step 1.1: if !$term_id -> insert into terms {
                //      name => $tag ,
                //      slug => $tag->lowcase->replace(' ', '-'),
                //      term_group => 0
                // }

                // step 1.2 insert into term_relationships {
                //      object_id => $term_id
                //      term_taxonomy_id => $listing_id
                // }

                // step 1.3 need to check relationships in term_taxonomy table


                // step 2: Duplicate for plant_category 

                // foreach ($row['tags'] as $tag) {
                //     $term_id = DB::table('SWX7neDE_terms')->where('name', $tag)->value('term_id');
                //     if (!$term_id) {
                //         $term_id = DB::table('SWX7neDE_terms')->insertGetId([
                //             'name' => $tag,
                //             'slug' => Str::slug($tag),
                //             'term_group' => 0,
                //         ]);
                //         DB::table('SWX7neDE_term_taxonomy')->insert([
                //             'term_id' => $term_id,
                //             'taxonomy' => 'job_listing_tag',
                //             'description' => '',
                //             'parent' => 0,
                //             'count' => 1,
                //         ]);
                //     }
                //     DB::table('SWX7neDE_term_relationships')->insert([
                //         'object_id' => $listing_id,
                //         'term_taxonomy_id' => $term_id,
                //     ]);
                // }


                // _____________________________________________________
                // SOCIAL NETWORK DATA:
                $social_networks = $row['social_networks'];
                $serialized_social_media = $this->serializeSocialMedia($social_networks);

                // _____________________________________________________
                // WORK HOURS DATA:
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

                // _____________________________________________________
                // COMPLETE META DATA:

                $metadata = [
                    ['meta_key' => '_case27_listing_type', 'meta_value' => 'plant-hire'], // Same for each post -> not in CSV data
                    ['meta_key' => '_job_email', 'meta_value' => $row['contact_email']],
                    ['meta_key' => '_job_phone', 'meta_value' => $row['phone_number']],
                    ['meta_key' => '_job_website', 'meta_value' => $row['website']],
                    ['meta_key' => '_hire_rate_gbp', 'meta_value' => $row['hire_rate_gbp']], // New meta to be configured in Wordpress
                    ['meta_key' => '_hire_rate_eur', 'meta_value' => $row['hire_rate_eur']], // New meta to be configured in Wordpress
                    ['meta_key' => '_hire_rate_usd', 'meta_value' => $row['hire_rate_usd']], // New meta to be configured in Wordpress
                    ['meta_key' => '_hire_rate_aud', 'meta_value' => $row['hire_rate_aud']], // New meta to be configured in Wordpress
                    ['meta_key' => '_hire_rate_nzd', 'meta_value' => $row['hire_rate_nzd']], // New meta to be configured in Wordpress
                    ['meta_key' => '_hire_rate_zar', 'meta_value' => $row['hire_rate_zar']], // New meta to be configured in Wordpress
                    ['meta_key' => '_hire-rate-pricing', 'meta_value' => ''],
                    ['meta_key' => '_job_logo', 'meta_value' => serialize($row['company_logo'])],
                    ['meta_key' => '_job_gallery', 'meta_value' => serialize($row['photo_gallery'])],
                    ['meta_key' => '_attachments-available-for-hire', 'meta_value' => $row['attachments']], // TO-DO: Needs to be serialised array in WP -> Update Laravel App
                    ['meta_key' => '_links', 'meta_value' => $serialized_social_media],
                    ['meta_key' => '_location', 'meta_value' => $row['location']], // TO-DO: Check whether these need to be co-ordinates
                    ['meta_key' => '_social-networks', 'meta_value' => ''], // TO-DO: Check where this links to
                    ['meta_key' => '_hire-rental', 'meta_value' => $row['hire_rental']],
                    ['meta_key' => '_work_hours', 'meta_value' => $serialized_work_hours],
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
