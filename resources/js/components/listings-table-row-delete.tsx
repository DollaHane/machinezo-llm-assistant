import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
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
        <AlertDialog>
            <AlertDialogTrigger asChild>
                <Button variant="destructive" className="w-full">
                    Delete
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                    <AlertDialogDescription>This action cannot be undone. This will permanently delete the listing.</AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <form onSubmit={handleSubmit}>
                        <AlertDialogAction type="submit" className="w-full">
                            Continue
                        </AlertDialogAction>
                    </form>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
