'use client';

import { Listing } from '@/types/listings';
import { ColumnDef } from '@tanstack/react-table';
import ListingsTableActions from './listings-table-actions';

export const ListingsColumns: ColumnDef<Listing>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
    },
    {
        accessorKey: 'title',
        header: 'Title',
    },
    {
        accessorKey: 'description',
        header: 'Description',
    },
    {
        accessorKey: 'plant_category',
        header: 'Plant Category',
    },
    {
        accessorKey: 'contact_email',
        header: 'Contact Email',
    },
    {
        accessorKey: 'created_at',
        header: 'Created At',
    },
    {
        header: 'Action',
        cell: ({ row }) => <ListingsTableActions row={row.original} />,
    },
];
