import { toast } from '@/hooks/use-toast';
import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Button } from './ui/button';

type ListingDeleteForm = {
    id: string;
};

export default function ListingsTableRowDelete({ id }: { id: string }) {
    const { delete: destroy } = useForm<Required<ListingDeleteForm>>({ id });
    const handleSubmit: FormEventHandler = (event) => {
        event.preventDefault();
        destroy(route('listings.delete', id));
        return toast({
            title: 'Deleted',
            description: 'Successfully deleted listing.',
            variant: 'success',
        });
    };
    return (
        <div>
            <form onSubmit={handleSubmit}>
                <Button variant="destructive" className="w-full">
                    Delete
                </Button>
            </form>
        </div>
    );
}
