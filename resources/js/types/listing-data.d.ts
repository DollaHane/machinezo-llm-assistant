export type ListingData = {
    ID: number;
    comment_count: number;
    comment_status: 'open' | 'closed';
    guid: string;
    locations: {
        id: number;
        listing_id: number;
        address: string;
        lat: string;
        lng: string;
    }[];
    menu_order: number;
    ping_status: 'open' | 'closed';
    pinged: string;
    post_author: number;
    post_content: string;
    post_content_filtered: string;
    post_date: string;
    post_date_gmt: string;
    post_excerpt: string;
    post_mime_type: string;
    post_modified: string;
    post_modified_gmt: string;
    post_name: string;
    post_parent: number;
    post_password: string;
    post_status: 'draft' | 'publish' | 'pending' | 'future' | 'private' | 'trash';
    post_title: string;
    post_type: 'job_listing';
    postmeta: {
        meta_key: string;
        meta_value: string | Array<any> | { [key: string]: any };
    }[];
    terms: {
        object_id: number;
        name: string;
        taxonomy: string;
    }[];
    to_ping: string;
};

export type Term = Pick<ListingData, 'terms'>;
export type Postmeta = Pick<ListingData, 'postmeta'>;
