import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

interface ListingTableRowEditProps {
    id: string;
}

export default function ListingsTableRowEdit({ id }: ListingTableRowEditProps) {
    return (
        <Link href={route('listings.show', { id })}>
            <Button variant="ghost" className="w-full">
                Update
            </Button>
        </Link>
    );
}
