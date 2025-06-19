import DashboardDropzone from '@/components/dashboard-dropzone';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Upload CSV',
        href: '/upload-csv',
    },
];

export default function UploadCSV() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Upload CSV" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <DashboardDropzone />
            </div>
        </AppLayout>
    );
}
