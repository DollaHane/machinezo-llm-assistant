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
            '*.hire_rate_gbp' => 'string|min:0',
            '*.hire_rate_eur' => 'string|min:0',
            '*.hire_rate_usd' => 'string|min:0',
            '*.hire_rate_aud' => 'string|min:0',
            '*.hire_rate_nzd' => 'string|min:0',
            '*.hire_rate_zar' => 'string|min:0',
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
            Log::error('Failed to process listings: ' . $error->getMessage());
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
            '*.hire_rate_gbp' => 'string|min:0', // Meta -> NEW 
            '*.hire_rate_eur' => 'string|min:0', // Meta -> NEW 
            '*.hire_rate_usd' => 'string|min:0', // Meta -> NEW 
            '*.hire_rate_aud' => 'string|min:0', // Meta -> NEW 
            '*.hire_rate_nzd' => 'string|min:0', // Meta -> NEW 
            '*.hire_rate_zar' => 'string|min:0', // Meta -> NEW 

            '*.tags' => 'required|array', // Term -> insert functions -> check additional relationships

            '*.company_logo' => 'nullable|url|max:255', // Meta
            '*.photo_gallery' => 'nullable|array', // Meta
            '*.attachments' => 'nullable|array', // Meta
            '*.social_networks' => 'nullable|array', // Meta
            '*.location' => 'required|string|max:255', // Meta
            '*.region' => 'required|string|max:255', // Meta
            '*.related_listing' => 'nullable|array', // Meta
            '*.hire_rental' => 'nullable|string|max:255', // Meta
            '*.additional_*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Failed to validate CSV data', json_encode($validator->errors()));
            return response()->json(['message' => 'Failed to validate CSV data', 'error' => $validator->errors()], 400);
        }

        $data = $validator->validated();
        $current_date = now();
        $current_date_gmt = now()->setTimezone('GMT');

        Log::info("DATA:", $data);

        foreach ($data as $row) {


            $social_networks = $row['social_networks'];
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

            $serialized_social_media = $this->serializeSocialMedia($social_networks);
            $serialized_work_hours = serialize($work_hours_array);

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
            ]);


            Log::info("LINKS: ", ['serialized_links' => serialize($work_hours_array)], $listing_id);
        }


        /**
         * 
         

        DB::beginTransaction();
        try {
            foreach ($data as $row) {

                // __________________________________
                // CREATE SWX7neDE_posts
                try {
                    //code...
                } catch (\Exception $error) {
                    Log::error('Failed to insert post', json_encode($error->getMessage()));
                    return response()->json(['message' => 'Failed to insert post', 'error' => $error->getMessage()], 500);
                }
                $listing_id = DB::table('SWX7neDE_posts')->insertGetId([
                    'post_title' => $row['title'],
                    'post_content' => $row['description'],
                    'post_author' => 1,
                    'post_date' => $current_date,
                    'post_date_gmt' => $current_date_gmt,
                    'post_content' => '',
                    'post_status' => 'draft',
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


                // _____________________________________________________
                // SOCIAL NETWORK DATA:
                $links = array_map(function($url, $index) {
                    // Assign network names based on URL or other logic
                    $networks = ['Facebook', 'Twitter', 'Instagram']; // Example network names
                    $network = $networks[$index] ?? 'Other'; // Fallback to 'Other' if index exceeds array

                    return [
                        'network' => $network,
                        'url' => $url
                    ];
                }, $urls, array_keys($urls));



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
                    ['meta_key' => '_job_logo', 'meta_value' => serialize([])],
                    ['meta_key' => '_job_gallery', 'meta_value' => serialize([])],
                    ['meta_key' => '_attachments-available-for-hire', 'meta_value' => ''],
                    ['meta_key' => '_hire-rental', 'meta_value' => 'Operated Hire'],
                    ['meta_key' => '_location', 'meta_value' => $row['location']],
                    ['meta_key' => '_social-networks', 'meta_value' => ''],
                    ['meta_key' => '_links', 'meta_value' => serialize([
                        0 => [
                            'network' => 'Facebook',
                            'url' => 'https://www.facebook.com/people/OConnell-Plant-Hire-Groundworks-LTD/100090927452394/'
                        ]
                    ])],
                    ['meta_key' => '_work_hours', 'meta_value' => serialize([
                        'Monday' => ['status' => 'enter-hours'],
                        'Tuesday' => ['status' => 'enter-hours'],
                        'Wednesday' => ['status' => 'enter-hours'],
                        'Thursday' => ['status' => 'enter-hours'],
                        'Friday' => ['status' => 'enter-hours'],
                        'Saturday' => ['status' => 'enter-hours'],
                        'Sunday' => ['status' => 'enter-hours'],
                        'timezone' => 'UTC'
                    ])],
                    
                    
                    // ['meta_key' => '_company-name', 'meta_value' => $row['need_to_create']],
                ];

                // __________________________________
                // CREATE SWX7neDE_postmeta
                try {
                    foreach ($metadata as $meta) {
                        DB::table('SWX7neDE_postmeta')->insert([
                            'post_id' => $listing_id,
                            'meta_key' => $meta['meta_key'],
                            'meta_value' => $meta['meta_value'],
                        ]);
                    }
                } catch (\Exception $error) {
                    Log::error('Failed to ');
                }
            }
            DB::commit();

            return response()->json(['message' => 'Successfully created post with metadata'], 200);
        } catch (\Exception $error) {
            Log::error('Failed to create post with metadata', json_encode($error->getMessage()));
            return response()->json(['message' => 'Failed to create post with metadata', 'error' => $error->getMessage()], 500);
        }
         */
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
     * 
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
            Log::error('Failed to serialize social media links', $error->getMessage());
            return null;
        }
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
                            . '. Note: phone numbers should be structured correctly and include area codes. The response must not exceed 2,000 characters, including spaces and punctuation.'
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



//  $metadata = [
//     ['meta_key' => '_edit_last', 'meta_value' => '1'],
//     ['meta_key' => '_case27_listing_type', 'meta_value' => 'plant-hire'],
//     ['meta_key' => '_featured', 'meta_value' => '0'],
//     ['meta_key' => '_claimed', 'meta_value' => '0'],
//     ['meta_key' => '_edit_lock', 'meta_value' => '1750683261:1'],
//     ['meta_key' => '_job_expires', 'meta_value' => '2025-07-13'],
//     ['meta_key' => '_required-plant-hire-fields', 'meta_value' => ''],
//     ['meta_key' => '_listing-details', 'meta_value' => ''],
//     ['meta_key' => '_plant-type-eg-excavator-or-cherry-picker', 'meta_value' => ''],
//     ['meta_key' => '_contact-information', 'meta_value' => ''],
//     ['meta_key' => '_company-name', 'meta_value' => 'O\'Connell Plant'],
//     ['meta_key' => '_job_email', 'meta_value' => 'info@oconnellgroup.co.uk'],
//     ['meta_key' => '_job_phone', 'meta_value' => '+4420 7474 0109'],
//     ['meta_key' => '_job_website', 'meta_value' => 'https://www.oconnellgroup.co.uk/'],
//     ['meta_key' => '_optional-plant-hire-details', 'meta_value' => ''],
//     ['meta_key' => '_hire-rate-pricing', 'meta_value' => ''],
//     ['meta_key' => '_weekly-hire-rate', 'meta_value' => ''],
//     ['meta_key' => '_form_heading', 'meta_value' => ''],
//     ['meta_key' => '_job_logo', 'meta_value' => serialize([])],
//     ['meta_key' => '_job_gallery', 'meta_value' => serialize([])],
//     ['meta_key' => '_attachments-available-for-hire', 'meta_value' => ''],
//     ['meta_key' => '_social-networks', 'meta_value' => ''],
//     ['meta_key' => '_links', 'meta_value' => serialize([
//         0 => [
//             'network' => 'Facebook',
//             'url' => 'https://www.facebook.com/people/OConnell-Plant-Hire-Groundworks-LTD/100090927452394/'
//         ]
//     ])],
//     ['meta_key' => '_work-hours', 'meta_value' => ''],
//     ['meta_key' => '_work_hours', 'meta_value' => serialize([
//         'Monday' => ['status' => 'enter-hours'],
//         'Tuesday' => ['status' => 'enter-hours'],
//         'Wednesday' => ['status' => 'enter-hours'],
//         'Thursday' => ['status' => 'enter-hours'],
//         'Friday' => ['status' => 'enter-hours'],
//         'Saturday' => ['status' => 'enter-hours'],
//         'Sunday' => ['status' => 'enter-hours'],
//         'timezone' => 'UTC'
//     ])],
//     ['meta_key' => '_location', 'meta_value' => ''],
//     ['meta_key' => '_', 'meta_value' => ''],
//     ['meta_key' => '_hire-rental', 'meta_value' => 'Operated Hire'],
//     ['meta_key' => '_forhire', 'meta_value' => 'For Hire'],
//     ['meta_key' => '_case27_review_count', 'meta_value' => '0'],
//     ['meta_key' => '_elementor_page_assets', 'meta_value' => serialize([])],
// ];

// foreach ($metadata as $meta) {
//     WpPostmeta::create([
//         'post_id' => 40,
//         'meta_key' => $meta['meta_key'],
//         'meta_value' => $meta['meta_value'],
//     ]);
// }

// (389, 66, '_edit_last', '2'),
// (390, 66, '_case27_listing_type', 'plant-hire'),
// (391, 66, '_featured', '0'),
// (392, 66, '_claimed', '0'),
// (393, 66, '_edit_lock', '1750753613:2'),
// (405, 66, '_required-plant-hire-fields', ''),
// (406, 66, '_listing-details', ''),
// (407, 66, '_plant-type-eg-excavator-or-cherry-picker', ''),
// (408, 66, '_contact-information', ''),
// (409, 66, '_company-name', 'BuildSoftware'),
// (410, 66, '_job_email', 'shane@buildsoftware.co.za'),
// (411, 66, '_job_phone', '0604607122'),
// (412, 66, '_job_website', 'https://www.buildsoftware.co.za'),
// (413, 66, '_optional-plant-hire-details', ''),
// (414, 66, '_hire-rate-pricing', ''),
// (415, 66, '_weekly-hire-rate', ''),
// (416, 66, '_form_heading', ''),
// (417, 66, '_job_logo', 'a:0:{}'),
// (418, 66, '_job_gallery', 'a:0:{}'),
// (419, 66, '_attachments-available-for-hire', ''),
// (420, 66, '_social-networks', ''),
// (421, 66, '_links', 'a:2:{i:0;a:2:{s:7:\"network\";s:8:\"Facebook\";s:3:\"url\";s:19:\"http://url.facebook\";}i:1;a:2:{s:7:\"network\";s:8:\"LinkedIn\";s:3:\"url\";s:19:\"http://url.linkedIn\";}}'),
// (422, 66, '_work-hours', ''),
// (423, 66, '_work_hours', 'a:8:{s:6:\"Monday\";a:2:{s:6:\"status\";s:11:\"enter-hours\";i:0;a:2:{s:4:\"from\";s:5:\"07:30\";s:2:\"to\";s:5:\"18:00\";}}s:7:\"Tuesday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:9:\"Wednesday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"Thursday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:6:\"Friday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"Saturday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:6:\"Sunday\";a:1:{s:6:\"status\";s:11:\"enter-hours\";}s:8:\"timezone\";s:3:\"UTC\";}'),
// (424, 66, '_location', ''),
// (425, 66, '_', ''),
// (426, 66, '_hire-rental', 'Plant Hire'),
// (427, 66, '_forhire', ''),
