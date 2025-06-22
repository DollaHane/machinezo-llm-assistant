'use client';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/text-area';
import { toast } from '@/hooks/use-toast';
import AppLayout from '@/layouts/app-layout';
import PageLayout from '@/layouts/page-layout';
import { updateValidation, UpdateValidationRequest } from '@/lib/validators/updateValidation';
import { BreadcrumbItem } from '@/types';
import { Listing } from '@/types/listings';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, Link, router } from '@inertiajs/react';
import { useMutation } from '@tanstack/react-query';
import axios, { AxiosError } from 'axios';
import { Loader2, Plus, X } from 'lucide-react';
import { FormEvent, useState } from 'react';
import { useFieldArray, useForm } from 'react-hook-form';
import { z } from 'zod';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Update Listing',
        href: '/listing',
    },
];

export default function ListingUpdate({ listing }: { listing: Listing }) {
    const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

    const form = useForm({
        resolver: zodResolver(updateValidation),
        defaultValues: {
            title: listing.title,
            description: listing.description,
            plant_category: listing.plant_category,
            contact_email: listing.contact_email,
            phone_number: listing.phone_number,
            website: listing.website! || '',
            hire_rate_gbp: listing.hire_rate_gbp,
            hire_rate_eur: listing.hire_rate_eur,
            hire_rate_usd: listing.hire_rate_usd,
            hire_rate_aud: listing.hire_rate_aud,
            hire_rate_nzd: listing.hire_rate_nzd,
            hire_rate_zar: listing.hire_rate_zar,
            tags: JSON.parse(listing.tags!) || [''],
            company_logo: listing.company_logo || '',
            photo_gallery: JSON.parse(listing.photo_gallery!) || [''],
            attachments: JSON.parse(listing.attachments!) || [''],
            social_networks: JSON.parse(listing.social_networks!) || [''],
            location: listing.location,
            region: listing.region,
            related_listing: JSON.parse(listing.related_listing!) || [''],
            hire_rental: listing.hire_rental || '',
            additional_1: listing.additional_1 || '',
            additional_2: listing.additional_2 || '',
            additional_3: listing.additional_3 || '',
            additional_4: listing.additional_4 || '',
            additional_5: listing.additional_5 || '',
            additional_6: listing.additional_6 || '',
            additional_7: listing.additional_7 || '',
            additional_8: listing.additional_8 || '',
            additional_9: listing.additional_9 || '',
            additional_10: listing.additional_10 || '',
        },
    });

    const errors = form.formState.errors;
    console.log('Form Errors:', errors);

    // @ts-expect-error
    const { fields: tagField, insert: tagInsert, remove: tagRemove } = useFieldArray({ control: form.control, name: 'tags' });
    // @ts-expect-error
    const { fields: photoField, insert: photoInsert, remove: photoRemove } = useFieldArray({ control: form.control, name: 'photo_gallery' });
    const {
        fields: attachmentsField,
        insert: attachmentsInsert,
        remove: attachmentsRemove,
        // @ts-expect-error
    } = useFieldArray({ control: form.control, name: 'attachments' });
    // @ts-expect-error
    const { fields: socialField, insert: socialInsert, remove: socialRemove } = useFieldArray({ control: form.control, name: 'social_networks' });
    // @ts-expect-error
    const { fields: relatedField, insert: relatedInsert, remove: relatedRemove } = useFieldArray({ control: form.control, name: 'related_listing' });

    if (tagField.length === 0) {
        tagInsert(Math.floor(Math.random() * 1000), '');
    }

    if (photoField.length === 0) {
        photoInsert(Math.floor(Math.random() * 1000), '');
    }

    if (attachmentsField.length === 0) {
        attachmentsInsert(Math.floor(Math.random() * 1000), '');
    }

    if (socialField.length === 0) {
        socialInsert(Math.floor(Math.random() * 1000), '');
    }

    if (relatedField.length === 0) {
        relatedInsert(Math.floor(Math.random() * 1000), '');
    }

    const { mutate: handleMutation } = useMutation({
        mutationFn: async ({
            title,
            description,
            plant_category,
            contact_email,
            phone_number,
            website,
            hire_rate_gbp,
            hire_rate_eur,
            hire_rate_usd,
            hire_rate_aud,
            hire_rate_nzd,
            hire_rate_zar,
            tags,
            company_logo,
            photo_gallery,
            attachments,
            social_networks,
            location,
            region,
            related_listing,
            hire_rental,
            additional_1,
            additional_2,
            additional_3,
            additional_4,
            additional_5,
            additional_6,
            additional_7,
            additional_8,
            additional_9,
            additional_10,
        }: UpdateValidationRequest) => {
            const payload: UpdateValidationRequest = {
                title,
                description,
                plant_category,
                contact_email,
                phone_number,
                website,
                hire_rate_gbp,
                hire_rate_eur,
                hire_rate_usd,
                hire_rate_aud,
                hire_rate_nzd,
                hire_rate_zar,
                tags,
                company_logo,
                photo_gallery,
                attachments,
                social_networks,
                location,
                region,
                related_listing,
                hire_rental,
                additional_1,
                additional_2,
                additional_3,
                additional_4,
                additional_5,
                additional_6,
                additional_7,
                additional_8,
                additional_9,
                additional_10,
            };
            console.log('Payload', payload);
            const post = await axios.put('/listings', payload, { headers: { listingId: `${listing.id}` } });
            return post;
        },
        onError: (error: AxiosError) => {
            setIsSubmitting(false);
            if (error.response?.status === 422) {
                return toast({
                    title: 'Data Validation Error.',
                    description: 'There was an error processing the data provided. Please try again.',
                    variant: 'destructive',
                });
            }
            if (error.response?.status === 401) {
                return toast({
                    title: 'Authorisation Error.',
                    description: 'Operation was not authorised, please login.',
                    variant: 'destructive',
                });
            }
            if (error.response?.status === 429) {
                return toast({
                    title: 'Too Many Requests.',
                    description: 'Please wait 30sec before trying again.',
                    variant: 'destructive',
                });
            }
            if (error.response?.status === 500) {
                return toast({
                    title: 'Server Error.',
                    description: 'Failed to complete operation due to a server error. Please try again.',
                    variant: 'destructive',
                });
            }
        },
        onSuccess: () => {
            setIsSubmitting(false);
            form.reset();
            router.visit('/listings');
            return toast({
                title: 'Success!',
                description: 'Your password was changed successfully!',
            });
        },
        onSettled: async (_, error) => {
            if (error) {
                console.log('onSettled error:', error);
            }
        },
    });

    function onSubmit(value: z.infer<typeof updateValidation>) {
        console.log('click');
        const payload: UpdateValidationRequest = {
            title: value.title,
            description: value.description,
            plant_category: value.plant_category,
            contact_email: value.contact_email,
            phone_number: value.phone_number,
            website: value.website,
            hire_rate_gbp: value.hire_rate_gbp,
            hire_rate_eur: value.hire_rate_eur,
            hire_rate_usd: value.hire_rate_usd,
            hire_rate_aud: value.hire_rate_usd,
            hire_rate_nzd: value.hire_rate_nzd,
            hire_rate_zar: value.hire_rate_zar,
            tags: value.tags,
            company_logo: value.company_logo,
            photo_gallery: value.photo_gallery,
            attachments: value.attachments,
            social_networks: value.social_networks,
            location: value.location,
            region: value.region,
            related_listing: value.related_listing,
            hire_rental: value.hire_rental,
            additional_1: value.additional_1,
            additional_2: value.additional_2,
            additional_3: value.additional_3,
            additional_4: value.additional_4,
            additional_5: value.additional_5,
            additional_6: value.additional_6,
            additional_7: value.additional_7,
            additional_8: value.additional_8,
            additional_9: value.additional_9,
            additional_10: value.additional_10,
        };
        handleMutation(payload);
        setIsSubmitting(true);
        return toast({
            title: 'Form Submitted',
            description: 'Processing request.',
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Listings" />
            <PageLayout>
                <div className="flex flex-col">
                    <Form {...form}>
                        <form onSubmit={form.handleSubmit(onSubmit)} className="mx-auto mt-5 w-full space-y-6 md:w-8/12">
                            <FormField
                                control={form.control}
                                name="title"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Title:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="description"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Description:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Textarea className="min-h-32" {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="plant_category"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Plant Category:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="contact_email"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Contact Email:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="phone_number"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Phone Number:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="website"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Website:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="location"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Location:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <FormField
                                control={form.control}
                                name="region"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>
                                            Region:<span className="text-red-500">*</span>
                                        </FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="company_logo"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Company Logo:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <hr />
                            <div className="grid w-full grid-cols-2 gap-5">
                                <FormField
                                    control={form.control}
                                    name="hire_rate_gbp"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Rate (GBP):<span className="text-red-500">*</span>
                                            </FormLabel>
                                            <FormControl>
                                                <Input {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="hire_rate_eur"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Rate (EUR):<span className="text-red-500">*</span>
                                            </FormLabel>
                                            <FormControl>
                                                <Input {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="hire_rate_usd"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Rate (USD):<span className="text-red-500">*</span>
                                            </FormLabel>
                                            <FormControl>
                                                <Input {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="hire_rate_aud"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Rate (AUD):<span className="text-red-500">*</span>
                                            </FormLabel>
                                            <FormControl>
                                                <Input {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="hire_rate_nzd"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Rate (NZD):<span className="text-red-500">*</span>
                                            </FormLabel>
                                            <FormControl>
                                                <Input {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="hire_rate_zar"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Rate (ZAR):<span className="text-red-500">*</span>
                                            </FormLabel>
                                            <FormControl>
                                                <Input {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            </div>

                            <hr />
                            <div className="flex w-full items-center justify-between">
                                <FormLabel>
                                    Tags:<span className="text-red-500">*</span>
                                </FormLabel>
                                <Button
                                    onClick={(event: FormEvent) => {
                                        event.preventDefault();
                                        tagInsert(Math.floor(Math.random() * 1000), '');
                                    }}
                                    variant="ghost"
                                    className="size-8 rounded-full hover:cursor-pointer hover:bg-background hover:text-muted-foreground"
                                >
                                    <Plus />
                                </Button>
                            </div>
                            {tagField.map((field, index) => (
                                <FormField
                                    key={field.id}
                                    control={form.control}
                                    name={`tags.${index}`}
                                    render={({ field: controllerField }) => (
                                        <FormItem>
                                            <FormControl>
                                                <div className="flex gap-2">
                                                    <Input {...controllerField} />
                                                    <Button
                                                        className="size-8 rounded-full text-red-500 hover:cursor-pointer hover:bg-background hover:text-red-300"
                                                        variant="ghost"
                                                        onClick={() => tagRemove(index)}
                                                        disabled={tagField.length === 1}
                                                    >
                                                        <X />
                                                    </Button>
                                                </div>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            ))}

                            <hr />
                            <div className="flex w-full items-center justify-between">
                                <FormLabel>Photo Gallery:</FormLabel>
                                <Button
                                    onClick={(event: FormEvent) => {
                                        event.preventDefault();
                                        photoInsert(Math.floor(Math.random() * 1000), '');
                                    }}
                                    variant="ghost"
                                    className="size-8 rounded-full hover:cursor-pointer hover:bg-background hover:text-muted-foreground"
                                >
                                    <Plus />
                                </Button>
                            </div>
                            {photoField.map((field, index) => (
                                <FormField
                                    key={field.id}
                                    control={form.control}
                                    name={`photo_gallery.${index}`}
                                    render={({ field: controllerField }) => (
                                        <FormItem>
                                            <FormControl>
                                                <div className="flex gap-2">
                                                    <Input {...controllerField} />
                                                    <Button
                                                        className="size-8 rounded-full text-red-500 hover:cursor-pointer hover:bg-background hover:text-red-300"
                                                        variant="ghost"
                                                        onClick={() => photoRemove(index)}
                                                        disabled={photoField.length === 1}
                                                    >
                                                        <X />
                                                    </Button>
                                                </div>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            ))}

                            <hr />
                            <div className="flex w-full items-center justify-between">
                                <FormLabel>Attachments:</FormLabel>
                                <Button
                                    onClick={(event: FormEvent) => {
                                        event.preventDefault();
                                        attachmentsInsert(Math.floor(Math.random() * 1000), '');
                                    }}
                                    variant="ghost"
                                    className="size-8 rounded-full hover:cursor-pointer hover:bg-background hover:text-muted-foreground"
                                >
                                    <Plus />
                                </Button>
                            </div>
                            {attachmentsField.map((field, index) => (
                                <FormField
                                    key={field.id}
                                    control={form.control}
                                    name={`attachments.${index}`}
                                    render={({ field: controllerField }) => (
                                        <FormItem>
                                            <FormControl>
                                                <div className="flex gap-2">
                                                    <Input {...controllerField} />
                                                    <Button
                                                        className="size-8 rounded-full text-red-500 hover:cursor-pointer hover:bg-background hover:text-red-300"
                                                        variant="ghost"
                                                        onClick={() => attachmentsRemove(index)}
                                                        disabled={attachmentsField.length === 1}
                                                    >
                                                        <X />
                                                    </Button>
                                                </div>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            ))}

                            <hr />
                            <div className="flex w-full items-center justify-between">
                                <FormLabel>Social Networks:</FormLabel>
                                <Button
                                    onClick={(event: FormEvent) => {
                                        event.preventDefault();
                                        socialInsert(Math.floor(Math.random() * 1000), '');
                                    }}
                                    variant="ghost"
                                    className="size-8 rounded-full hover:cursor-pointer hover:bg-background hover:text-muted-foreground"
                                >
                                    <Plus />
                                </Button>
                            </div>
                            {socialField.map((field, index) => (
                                <FormField
                                    key={field.id}
                                    control={form.control}
                                    name={`social_networks.${index}`}
                                    render={({ field: controllerField }) => (
                                        <FormItem>
                                            <FormControl>
                                                <div className="flex gap-2">
                                                    <Input {...controllerField} />
                                                    <Button
                                                        className="size-8 rounded-full text-red-500 hover:cursor-pointer hover:bg-background hover:text-red-300"
                                                        variant="ghost"
                                                        onClick={() => socialRemove(index)}
                                                        disabled={socialField.length === 1}
                                                    >
                                                        <X />
                                                    </Button>
                                                </div>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            ))}

                            <hr />
                            <div className="flex w-full items-center justify-between">
                                <FormLabel>Related Listings:</FormLabel>
                                <Button
                                    onClick={(event: FormEvent) => {
                                        event.preventDefault();
                                        relatedInsert(Math.floor(Math.random() * 1000), '');
                                    }}
                                    variant="ghost"
                                    className="size-8 rounded-full hover:cursor-pointer hover:bg-background hover:text-muted-foreground"
                                >
                                    <Plus />
                                </Button>
                            </div>
                            {relatedField.map((field, index) => (
                                <FormField
                                    key={field.id}
                                    control={form.control}
                                    name={`related_listing.${index}`}
                                    render={({ field: controllerField }) => (
                                        <FormItem>
                                            <FormControl>
                                                <div className="flex gap-2">
                                                    <Input {...controllerField} />
                                                    <Button
                                                        className="size-8 rounded-full text-red-500 hover:cursor-pointer hover:bg-background hover:text-red-300"
                                                        variant="ghost"
                                                        onClick={() => relatedRemove(index)}
                                                        disabled={relatedField.length === 1}
                                                    >
                                                        <X />
                                                    </Button>
                                                </div>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            ))}
                            <hr />
                            <FormField
                                control={form.control}
                                name="hire_rental"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Hire Rental:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <FormField
                                control={form.control}
                                name="additional_1"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #1:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_2"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #2:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_3"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #3:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_4"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #4:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_5"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #5:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_6"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #6:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_7"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #7:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_8"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #8:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_9"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #9:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                            <FormField
                                control={form.control}
                                name="additional_10"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Additional #10:</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <div className="flex flex-row gap-5">
                                <Button type="submit" onClick={() => console.log('click')} className="relative flex items-center justify-center">
                                    {isSubmitting ? <Loader2 className="absolute flex h-5 w-5 animate-spin" /> : 'Send'}
                                </Button>
                                <Link href={route('listings.index')}>
                                    <Button variant="secondary">Cancel</Button>
                                </Link>
                            </div>
                        </form>
                    </Form>
                </div>
            </PageLayout>
        </AppLayout>
    );
}
