import { ListingData } from '@/types/listing-data';
import { Listing } from '@/types/listings';

export function dataTransformation(listing: ListingData) {
    let tags: string[] = [];
    let plant_category: string = '';
    let region: string = '';

    listing.terms.map((term) => {
        if (term.taxonomy === 'case27_job_listing_tags') {
            tags.push(term.name);
        }
        if (term.taxonomy === 'job_listing_category') {
            plant_category = term.name;
        }
        if (term.taxonomy === 'region') {
            region = term.name;
        }
    });

    const object: Listing = {
        id: listing.ID,
        title: listing.post_title,
        description: listing.post_content,
        plant_category: plant_category,
        company_name: (listing.postmeta.find((meta) => meta.meta_key === '_company-name')?.meta_value as string) || '',
        contact_email: (listing.postmeta.find((meta) => meta.meta_key === '_job_email')?.meta_value as string) || '',
        phone_number: (listing.postmeta.find((meta) => meta.meta_key === '_job_phone')?.meta_value as string) || '',
        website: (listing.postmeta.find((meta) => meta.meta_key === '_job_website')?.meta_value as string) || '',
        hire_rate_gbp: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rate_gbp')?.meta_value as string) || '',
        hire_rate_eur: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rate_eur')?.meta_value as string) || '',
        hire_rate_usd: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rate_usd')?.meta_value as string) || '',
        hire_rate_aud: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rate_aud')?.meta_value as string) || '',
        hire_rate_nzd: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rate_nzd')?.meta_value as string) || '',
        hire_rate_zar: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rate_zar')?.meta_value as string) || '',
        tags: tags,
        company_logo: (listing.postmeta.find((meta) => meta.meta_key === '_job_logo')?.meta_value as string[]) || '',
        photo_gallery: (listing.postmeta.find((meta) => meta.meta_key === '_job_gallery')?.meta_value as string[]) || '',
        attachments: (listing.postmeta.find((meta) => meta.meta_key === '_attachments-available-for-hire')?.meta_value as string[]) || '',
        social_networks: (listing.postmeta.find((meta) => meta.meta_key === '_social_networks')?.meta_value as string[]) || '',
        location: (listing.postmeta.find((meta) => meta.meta_key === '_location')?.meta_value as string) || '',
        region: region,
        related_listing: [''],
        hire_rental: (listing.postmeta.find((meta) => meta.meta_key === '_hire_rental')?.meta_value as string) || '',
        additional_1: (listing.postmeta.find((meta) => meta.meta_key === '_additional_1')?.meta_value as string) || '',
        additional_2: (listing.postmeta.find((meta) => meta.meta_key === '_additional_2')?.meta_value as string) || '',
        additional_3: (listing.postmeta.find((meta) => meta.meta_key === '_additional_3')?.meta_value as string) || '',
        additional_4: (listing.postmeta.find((meta) => meta.meta_key === '_additional_4')?.meta_value as string) || '',
        additional_5: (listing.postmeta.find((meta) => meta.meta_key === '_additional_5')?.meta_value as string) || '',
        additional_6: (listing.postmeta.find((meta) => meta.meta_key === '_additional_6')?.meta_value as string) || '',
        additional_7: (listing.postmeta.find((meta) => meta.meta_key === '_additional_7')?.meta_value as string) || '',
        additional_8: (listing.postmeta.find((meta) => meta.meta_key === '_additional_8')?.meta_value as string) || '',
        additional_9: (listing.postmeta.find((meta) => meta.meta_key === '_additional_9')?.meta_value as string) || '',
        additional_10: (listing.postmeta.find((meta) => meta.meta_key === '_additional_10')?.meta_value as string) || '',
        created_at: listing.post_date,
        updated_at: listing.post_modified,
    };

    return object;
}
