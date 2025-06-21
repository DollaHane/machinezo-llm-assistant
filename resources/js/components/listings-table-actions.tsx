import { Listing } from '@/types/listings';
import { EllipsisVertical } from 'lucide-react';
import ListingsTableRowDelete from './listings-table-row-delete';
import ListingsTableRowEdit from './listings-table-row-edit';
import ListingsTableRowView from './listings-table-row-view';
import { Button } from './ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuSeparator, DropdownMenuTrigger } from './ui/dropdown-menu';

interface ListingsTableActionsProps {
    row: Listing;
}

export default function ListingsTableActions({ row }: ListingsTableActionsProps) {
    return (
        <div>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" className="flex size-8 text-muted-foreground data-[state=open]:bg-muted" size="icon">
                        <EllipsisVertical />
                        <span className="sr-only">Open menu</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-32">
                    <ListingsTableRowEdit id={JSON.stringify(row.id)} />
                    <ListingsTableRowView row={row} />
                    <DropdownMenuSeparator />
                    <ListingsTableRowDelete id={JSON.stringify(row.id)} />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
}
