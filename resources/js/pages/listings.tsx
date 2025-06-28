import { ListingsColumns } from '@/components/listings-columns';
import { ListingsTable } from '@/components/listings-table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page-layout';
import { dataTransformation } from '@/lib/data-transformation';
import { BreadcrumbItem } from '@/types';
import { ListingData } from '@/types/listing-data';
import { Listing } from '@/types/listings';
import { Head, Link } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Listings',
        href: '/listings',
    },
];

type Links = {
    url: string | null;
    label: string;
    active: boolean;
};

type Pagination = {
    current_page: number;
    data: ListingData[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Links[];
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
};

export default function Listings({ listings }: { listings: Pagination }) {
    const listing_data: ListingData[] = listings.data;
    let data: Listing[] = [];
    
    listing_data.map((listing: ListingData) => {
        const object = dataTransformation(listing);
        data.push(object);
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Listings" />
            <PageLayout>
                <ListingsTable columns={ListingsColumns} data={data} />
                <div className="flex items-center justify-end space-x-2 py-4">
                    <Link href={`${listings.prev_page_url}`} hidden={listings.prev_page_url === null ? true : false}>
                        <Button variant="outline" size="sm">
                            Previous
                        </Button>
                    </Link>
                    <Link href={`${listings.next_page_url}`} hidden={listings.next_page_url === null ? true : false}>
                        <Button variant="outline" size="sm">
                            Next
                        </Button>
                    </Link>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
